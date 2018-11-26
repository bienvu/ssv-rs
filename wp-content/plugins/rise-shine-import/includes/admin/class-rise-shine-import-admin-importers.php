<?php
/**
 * Init WooCommerce data importers.
 *
 * @package WooCommerce/Admin
 */
/**
 * WC_Admin_Importers Class.
 */
class Rise_Shine_Import_Admin_Importers {

  /**
   * Array of importer IDs.
   *
   * @var string[]
   */
  protected $importers = array();

  /**
   * Constructor.
   */
  public function __construct() {
    add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
    add_action( 'admin_init', array( $this, 'register_importers' ) );
    add_action( 'admin_head', array( $this, 'hide_from_menus' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
    add_action( 'wp_ajax_rise_shine_do_ajax_import', array( $this, 'do_ajax_import' ) );

    // Register WooCommerce importers.
    $this->importers['rise_shine_importer'] = array(
      'menu'       => 'edit.php?post_type=tantan',
      'name'       => __( 'Rise Shine Import', 'woocommerce' ),
      'capability' => 'import',
      'callback'   => array( $this, 'rise_shine_term_importer' ),
    );
  }

  /**
   * Add menu items for our custom importers.
   */
  public function add_to_menus() {
    foreach ( $this->importers as $id => $importer ) {
      add_submenu_page( $importer['menu'], $importer['name'], $importer['name'], $importer['capability'], $id, $importer['callback'] );
    }
  }

  /**
   * Hide menu items from view so the pages exist, but the menu items do not.
   */
  public function hide_from_menus() {
    global $submenu;

    foreach ( $this->importers as $id => $importer ) {
      if ( isset( $submenu[ $importer['menu'] ] ) ) {
        foreach ( $submenu[ $importer['menu'] ] as $key => $menu ) {
          if ( $id === $menu[2] ) {
            unset( $submenu[ $importer['menu'] ][ $key ] );
          }
        }
      }
    }
  }

  /**
   * Register importer scripts.
   */
  public function admin_scripts() {
    wp_register_script('rise-shine-import', RSIURL . '/assets/js/admin/rise-shine-import.js', array( 'jquery' ));
  }

  /**
   * The product importer.
   *
   * This has a custom screen - the Tools > Import item is a placeholder.
   * If we're on that screen, redirect to the custom one.
   */
  public function rise_shine_csv_importer() {
    include_once RSIPATH . '/includes/admin/importers/class-rise-shine-csv-importer-controller.php';
    $importer = new Rise_Shine_CSV_Importer_Controller();
    $importer->dispatch();
  }

  /**
   * Register WordPress based importers.
   */
  public function register_importers() {
    if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
      register_importer('rise_shine_postcode', __('Rise & Shine import Postcode'), __( 'Import <strong>PostCode</strong> to your store via a csv file.'), array( $this, 'rise_shine_csv_importer'));
    }
  }

  /**
   * Ajax callback for importing one batch of products from a CSV.
   */
  public function do_ajax_import() {
    global $wpdb;
    check_ajax_referer( 'rise-shine-import', 'security' );
    if (!isset($_POST['file'])) {
      wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import.', 'woocommerce' ) ) );
    }
    $file   = wc_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
    $params = array(
      'delimiter'       => ! empty( $_POST['delimiter'] ) ? wc_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
      'start_pos'       => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0,
      'update_existing' => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
      'lines'           => 10,
      'parse'           => true,
    );

    $error_log = array();
    $raw_data = array();

    include_once RSIPATH . '/includes/import/class-rise-shine-postcode-csv-importer.php';
    $importer         = new Rise_Shine_PostCode_CSV_Importer($file, $params);
    $results          = $importer->import();


    $percent_complete = $importer->get_percent_complete();
    $error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );



    if ( 100 === $percent_complete ) {
      // Send success.
      wp_send_json_success(
        array(
          'position'   => 'done',
          'percentage' => 100,
          'url'        => add_query_arg( array( 'nonce' => wp_create_nonce( 'product-csv' ) ), admin_url( 'admin.php?import=rise_shine_term_csv&step=done' ) ),
          'imported'   => count( $results['imported'] ),
          'failed'     => count( $results['failed'] ),
          'updated'    => count( $results['updated'] ),
          'skipped'    => count( $results['skipped'] ),
        )
      );
    } else {
      wp_send_json_success(
        array(
          'position'   => $importer->file_position,
          'percentage' => $percent_complete,
          'imported'   => count( $results['imported'] ),
          'failed'     => count( $results['failed'] ),
          'updated'    => count( $results['updated'] ),
          'skipped'    => count( $results['skipped'] ),
        )
      );
    }
  }
}

new Rise_Shine_Import_Admin_Importers();
