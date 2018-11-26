<?php
/**
 *
 */

include_once RSIPATH . '/includes/admin/importers/class-rise-shine-csv-importer-controller.php';




/**
 * WC_Product_CSV_Importer Class.
 */
class Rise_Shine_PostCode_CSV_Importer {
  public $file_position = 0;
  /**
   * Initialize importer.
   *
   * @param string $file   File to read.
   * @param array  $params Arguments for the parser.
   */
  public function __construct( $file, $params = array() ) {
    $default_args = array(
      'start_pos'        => 0, // File pointer start.
      'end_pos'          => -1, // File pointer end.
      'lines'            => -1, // Max lines to read.
      'parse'            => false, // Whether to sanitize and format data.
      'update_existing'  => false, // Whether to update existing items.
      'delimiter'        => ',', // CSV delimiter.
      'prevent_timeouts' => true, // Check memory and time usage and abort if reaching limit.
      'enclosure'        => '"', // The character used to wrap text in the CSV.
      'escape'           => "\0", // PHP uses '\' as the default escape character. This is not RFC-4180 compliant. This disables the escape character.
    );

    $this->params = wp_parse_args( $params, $default_args );
    $this->file   = $file;

    $this->read_file();
  }

  /**
   * Remove UTF-8 BOM signature.
   *
   * @param  string $string String to handle.
   * @return string
   */
  protected function remove_utf8_bom( $string ) {
    if ( 'efbbbf' === substr( bin2hex( $string ), 0, 6 ) ) {
      $string = substr( $string, 3 );
    }

    return $string;
  }

  /**
   * Read file.
   */
  protected function read_file() {
    if ( ! Rise_Shine_CSV_Importer_Controller::is_file_valid_csv( $this->file ) ) {
      wp_die( __( 'Invalid file type. The importer supports CSV and TXT file formats.') );
    }

    $handle = fopen( $this->file, 'r' );

    if ( false !== $handle ) {
      $this->raw_keys = version_compare( PHP_VERSION, '5.3', '>=' ) ? array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) ) : array_map( 'trim', fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'] ) ); // @codingStandardsIgnoreLine

      // Remove BOM signature from the first item.
      if ( isset( $this->raw_keys[0] ) ) {
        $this->raw_keys[0] = $this->remove_utf8_bom( $this->raw_keys[0] );
      }

      if ( 0 !== $this->params['start_pos'] ) {
        fseek( $handle, (int) $this->params['start_pos'] );
      }

      while ( 1 ) {
        $row = version_compare( PHP_VERSION, '5.3', '>=' ) ? fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'], $this->params['escape'] ) : fgetcsv( $handle, 0, $this->params['delimiter'], $this->params['enclosure'] );

        if ( false !== $row ) {
          $this->raw_data[]                                 = $row;
          $this->file_positions[ count( $this->raw_data ) ] = ftell( $handle );

          if ( ( $this->params['end_pos'] > 0 && ftell( $handle ) >= $this->params['end_pos'] ) || 0 === --$this->params['lines'] ) {
            break;
          }
        } else {
          break;
        }
      }

      $this->file_position = ftell( $handle );
    }
  }

  /**
   * Get file pointer position as a percentage of file size.
   *
   * @return int
   */
  public function get_percent_complete() {
    $size = filesize( $this->file );
    if ( ! $size ) {
      return 0;
    }

    return absint( min( round( ( $this->file_position / $size ) * 100 ), 100 ) );
  }

  /**
   * Process importer.
   *
   * @return array
   */
  public function import() {
    $this->start_time = time();
    $index            = 0;
    $update_existing  = $this->params['update_existing'];
    $data             = array(
      'imported' => array(),
      'failed'   => array(),
      'updated'  => array(),
      'skipped'  => array(),
    );

    foreach ($this->raw_data as $key => $raw_data) {
      $shipping_fee = $raw_data[5];
      $suburb = trim($raw_data[3]);
      $postcode = trim($raw_data[2]);
      $store_name = trim($raw_data[0]);
      $store_term = get_term_by('slug', strtolower($store_name), 'rs_store');
      $postarr = array(
        'post_title' => $store_name . '-' . $suburb . '-' . $postcode,
        'post_status' => 'publish',
        'post_type' => 'rs_shipping_fee',
        'tax_input' => array(
          'rs_store' => array($store_term->term_id), //Make like slug.
        ),
        'meta_input' => array(
          'postcode' => $postcode,
          'shipping_fee' => $shipping_fee,
          'suburb' => $suburb,
        ),
      );
      $result = wp_insert_post($postarr, true);
      if ( is_wp_error( $result ) ) {
        $result->add_data( array( 'row' => $postarr['post_title']));
        $data['failed'][] = $result;
      } elseif ( $result['updated'] ) {
        $data['updated'][] = $result['id'];
      } else {
        $data['imported'][] = $result['id'];
      }
    }
    return $data;
  }
}
