<?php
/**
 * Class Rise_Shine_CSV_Importer_Controller file.
 *
 */

/**
 * Importer controller - handles file upload and forms in admin.
 *
 * @version     3.1.0
 */
class Rise_Shine_CSV_Importer_Controller {

  /**
   * The path to the current file.
   *
   * @var string
   */
  protected $file = '';

  /**
   * The current import step.
   *
   * @var string
   */
  protected $step = '';

  /**
   * Progress steps.
   *
   * @var array
   */
  protected $steps = array();

  /**
   * Errors.
   *
   * @var array
   */
  protected $errors = array();

  /**
   * The current delimiter for the file being read.
   *
   * @var string
   */
  protected $delimiter = ',';

  /**
   * Whether to skip existing products.
   *
   * @var bool
   */
  protected $update_existing = false;

  /**
   * Check whether a file is a valid CSV file.
   *
   * @param string $file File path.
   * @param bool   $check_path Whether to also check the file is located in a valid location (Default: true).
   * @return bool
   */
  public static function is_file_valid_csv( $file, $check_path = true ) {
    if ( $check_path && false !== stripos( $file, '://' ) ) {
      return false;
    }

    $valid_filetypes = self::get_valid_csv_filetypes();
    $filetype = wp_check_filetype( $file, $valid_filetypes );
    if ( in_array( $filetype['type'], $valid_filetypes, true ) ) {
      return true;
    }

    return false;
  }

  /**
   * Get all the valid filetypes for a CSV file.
   *
   * @return array
   */
  protected static function get_valid_csv_filetypes() {
    return array(
      'csv' => 'text/csv',
      'txt' => 'text/plain',
    );
  }

  /**
   * Constructor.
   */
  public function __construct() {
    $default_steps = array(
      'upload'  => array(
        'name'    => __( 'Upload CSV file'),
        'view'    => array( $this, 'upload_form' ),
        'handler' => array( $this, 'upload_form_handler' ),
      ),
      'import'  => array(
        'name'    => __( 'Import'),
        'view'    => array( $this, 'import'),
        'handler' => '',
      ),
      'done'    => array(
        'name'    => __( 'Done!'),
        'view'    => array( $this, 'done'),
        'handler' => '',
      ),
    );

    $this->steps = $default_steps;

    $this->step            = isset( $_REQUEST['step'] ) ? sanitize_key( $_REQUEST['step'] ) : current( array_keys( $this->steps ) );
    $this->file            = isset( $_REQUEST['file'] ) ? wc_clean( wp_unslash( $_REQUEST['file'] ) ) : '';
    $this->update_existing = isset( $_REQUEST['update_existing'] ) ? (bool) $_REQUEST['update_existing'] : false;
    $this->delimiter       = ! empty( $_REQUEST['delimiter'] ) ? wc_clean( wp_unslash( $_REQUEST['delimiter'] ) ) : ',';
  }

  /**
   * Get the URL for the next step's screen.
   *
   * @param string $step  slug (default: current step).
   * @return string       URL for next step if a next step exists.
   *                      Admin URL if it's the last step.
   *                      Empty string on failure.
   */
  public function get_next_step_link($step = '') {
    if ( ! $step ) {
      $step = $this->step;
    }

    $keys = array_keys( $this->steps );

    if ( end( $keys ) === $step ) {
      return admin_url();
    }

    $step_index = array_search( $step, $keys, true );

    if ( false === $step_index ) {
      return '';
    }

    $params = array(
      'step'            => $keys[ $step_index + 1 ],
      'file'            => str_replace( DIRECTORY_SEPARATOR, '/', $this->file ),
      'delimiter'       => $this->delimiter,
      'update_existing' => $this->update_existing,
      '_wpnonce'        => wp_create_nonce('rise-shine-importer'),
    );

    return add_query_arg($params);
  }

  /**
   * Output header view.
   */
  protected function output_header() {
    include dirname( __FILE__ ) . '/views/html-csv-import-header.php';
  }

  /**
   * Output steps view.
   */
  protected function output_steps() {
    include dirname( __FILE__ ) . '/views/html-csv-import-steps.php';
  }

  /**
   * Output footer view.
   */
  protected function output_footer() {
    include dirname( __FILE__ ) . '/views/html-csv-import-footer.php';
  }

  /**
   * Add error message.
   *
   * @param string $message Error message.
   * @param array  $actions List of actions with 'url' and 'label'.
   */
  protected function add_error( $message, $actions = array() ) {
    $this->errors[] = array(
      'message' => $message,
      'actions' => $actions,
    );
  }

  /**
   * Add error message.
   */
  protected function output_errors() {
    if ( ! $this->errors ) {
      return;
    }

    foreach ( $this->errors as $error ) {
      echo '<div class="error inline">';
      echo '<p>' . esc_html( $error['message'] ) . '</p>';

      if ( ! empty( $error['actions'] ) ) {
        echo '<p>';
        foreach ( $error['actions'] as $action ) {
          echo '<a class="button button-primary" href="' . esc_url( $action['url'] ) . '">' . esc_html( $action['label'] ) . '</a> ';
        }
        echo '</p>';
      }
      echo '</div>';
    }
  }

  /**
   * Dispatch current step and show correct view.
   */
  public function dispatch() {
    // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
    if ( ! empty( $_POST['save_step'] ) && ! empty( $this->steps[ $this->step ]['handler'] ) ) {
      call_user_func( $this->steps[ $this->step ]['handler'], $this );
    }
    $this->output_header();
    $this->output_steps();
    $this->output_errors();
    call_user_func( $this->steps[ $this->step ]['view'], $this );
    $this->output_footer();
  }

  /**
   * Output information about the uploading process.
   */
  protected function upload_form() {
    $bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
    $size       = size_format( $bytes );
    $upload_dir = wp_upload_dir();
    include dirname( __FILE__ ) . '/views/html-csv-import-form.php';
  }

  /**
   * Handle the upload form and store options.
   */
  public function upload_form_handler() {
    $file = $this->handle_upload();
    if ( is_wp_error( $file ) ) {
      $this->add_error( $file->get_error_message() );
      return;
    } else {
      $this->file = $file;
    }
    wp_redirect(esc_url_raw($this->get_next_step_link()));
    exit;
  }

  /**
   * Handles the CSV upload and initial parsing of the file to prepare for
   * displaying author import options.
   *
   * @return string|WP_Error
   */
  public function handle_upload() {
    // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification -- Nonce already verified in WC_Product_CSV_Importer_Controller::upload_form_handler()
    $file_url = isset( $_POST['file_url'] ) ? wc_clean( wp_unslash( $_POST['file_url'] ) ) : '';

    if ( empty( $file_url ) ) {
      if ( ! isset( $_FILES['import'] ) ) {
        return new WP_Error( 'rise_shine_csv_importer_upload_file_empty', __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.') );
      }

      if ( ! self::is_file_valid_csv( wc_clean( wp_unslash( $_FILES['import']['name'] ) ), false ) ) {
        return new WP_Error( 'rise_shine_csv_importer_upload_file_invalid', __( 'Invalid file type. The importer supports CSV and TXT file formats.') );
      }

      $overrides = array(
        'test_form' => false,
        'mimes'     => self::get_valid_csv_filetypes(),
      );
      $import    = $_FILES['import']; // WPCS: sanitization ok, input var ok.
      $upload    = wp_handle_upload( $import, $overrides );

      if ( isset( $upload['error'] ) ) {
        return new WP_Error( 'rise_shine_csv_importer_upload_error', $upload['error'] );
      }

      // Construct the object array.
      $object = array(
        'post_title'     => basename( $upload['file'] ),
        'post_content'   => $upload['url'],
        'post_mime_type' => $upload['type'],
        'guid'           => $upload['url'],
        'context'        => 'import',
        'post_status'    => 'private',
      );

      // Save the data.
      $id = wp_insert_attachment( $object, $upload['file'] );

      /*
       * Schedule a cleanup for one day from now in case of failed
       * import or missing wp_import_cleanup() call.
       */
      wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', array( $id ) );

      return $upload['file'];
    }
    return new WP_Error( 'rise_shine_csv_importer_upload_invalid_file', __( 'Please upload or provide the link to a valid CSV file.') );
  }

  /**
   * Import the file if it exists and is valid.
   */
  public function import() {
    if ( ! self::is_file_valid_csv( $this->file ) ) {
      $this->add_error( __( 'Invalid file type. The importer supports CSV and TXT file formats.', 'woocommerce' ) );
      $this->output_errors();
      return;
    }

    if ( ! is_file( $this->file ) ) {
      $this->add_error( __( 'The file does not exist, please try again.', 'woocommerce' ) );
      $this->output_errors();
      return;
    }

    wp_localize_script(
      'rise-shine-import', 'rise_shine_import_params', array(
        'import_nonce'    => wp_create_nonce('rise-shine-import'),
        'file'            => $this->file,
        'update_existing' => $this->update_existing,
        'delimiter'       => $this->delimiter,
      )
    );
    wp_enqueue_script('rise-shine-import');
    include_once dirname( __FILE__ ) . '/views/html-csv-import-progress.php';
  }

  /**
   * Done step.
   */
  protected function done() {
    // phpcs:disable WordPress.CSRF.NonceVerification.NoNonceVerification
    $imported = isset( $_GET['products-imported'] ) ? absint( $_GET['products-imported'] ) : 0;
    $updated  = isset( $_GET['products-updated'] ) ? absint( $_GET['products-updated'] ) : 0;
    $failed   = isset( $_GET['products-failed'] ) ? absint( $_GET['products-failed'] ) : 0;
    $skipped  = isset( $_GET['products-skipped'] ) ? absint( $_GET['products-skipped'] ) : 0;
    $errors   = array_filter( (array) get_user_option( 'product_import_error_log' ) );
    // phpcs:enable

    include_once dirname( __FILE__ ) . '/views/html-csv-import-done.php';
  }
}
