/*global ajaxurl, wc_product_import_params */
;(function ( $, window ) {

  /**
   * riseShineImportForm handles the import process.
   */
  var riseShineImportForm = function( $form ) {
    this.$form           = $form;
    this.xhr             = false;
    this.position        = 0;
    this.file            = rise_shine_import_params.file;
    this.update_existing = rise_shine_import_params.update_existing;
    this.delimiter       = rise_shine_import_params.delimiter;
    this.security        = rise_shine_import_params.import_nonce;

    // Number of import successes/failures.
    this.imported = 0;
    this.failed   = 0;
    this.updated  = 0;
    this.skipped  = 0;

    // Initial state.
    this.$form.find('.woocommerce-importer-progress').val( 0 );

    this.run_import = this.run_import.bind( this );

    // Start importing.
    this.run_import();
  };

  /**
   * Run the import in batches until finished.
   */
  riseShineImportForm.prototype.run_import = function() {
    var $this = this;
    $.ajax( {
      type: 'POST',
      url: ajaxurl,
      data: {
        action          : 'rise_shine_do_ajax_import',
        position        : $this.position,
        file            : $this.file,
        update_existing : $this.update_existing,
        delimiter       : $this.delimiter,
        security        : $this.security
      },
      dataType: 'json',
      success: function( response ) {
        if ( response.success ) {
          $this.position  = response.data.position;
          $this.imported += response.data.imported;
          $this.failed   += response.data.failed;
          $this.updated  += response.data.updated;
          $this.skipped  += response.data.skipped;
          $this.$form.find('.rise-shine-importer-progress').val( response.data.percentage );

          if ( 'done' === response.data.position ) {
            window.location = response.data.url + '&products-imported=' + parseInt( $this.imported, 10 ) + '&products-failed=' + parseInt( $this.failed, 10 ) + '&products-updated=' + parseInt( $this.updated, 10 ) + '&products-skipped=' + parseInt( $this.skipped, 10 );
          } else {
            $this.run_import();
          }
        }
      }
    } ).fail( function( response ) {
      window.console.log( response );
    } );
  };

  /**
   * Function to call productImportForm on jQuery selector.
   */
  $.fn.rise_shine_importer = function() {
    new riseShineImportForm(this);
    return this;
  };
  $('.rise-shine-importer').rise_shine_importer();

})( jQuery, window );
