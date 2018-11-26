<?php
/**
 * Admin View: Product import form
 *
 */
?>
<form class="rise-shine-importer-progress-form-content rise-shine-importer" enctype="multipart/form-data" method="post">
	<header>
		<p><?php esc_html_e( 'This tool allows you to import (or merge) data to your site from a CSV file.'); ?></p>
	</header>
	<section>
		<table class="form-table rise-shine-importer-options">
			<tbody>
				<tr>
					<th scope="row">
						<label for="upload">
							<?php esc_html_e( 'Choose a CSV file from your computer:' ); ?>
						</label>
					</th>
					<td>
						<?php
						if ( ! empty( $upload_dir['error'] ) ) {
							?>
							<div class="inline error">
								<p><?php esc_html_e( 'Before you can upload your import file, you will need to fix the following error:' ); ?></p>
								<p><strong><?php echo esc_html( $upload_dir['error'] ); ?></strong></p>
							</div>
							<?php
						} else {
							?>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo esc_attr( $bytes ); ?>" />
							<br>
							<small>
								<?php
								printf(
									/* translators: %s: maximum upload size */
									esc_html__( 'Maximum size: %s' ),
									esc_html( $size )
								);
								?>
							</small>
							<?php
						}
					?>
					</td>
				</tr>
				<tr>
					<th><label for="rise-shine-importer-update-existing"><?php esc_html_e( 'Update existing PostCode' ); ?></label><br/></th>
					<td>
						<input type="hidden" name="update_existing" value="0" />
						<input type="checkbox" id="rise-shine-importer-update-existing" name="update_existing" value="1" />
						<label for="rise-shine-importer-update-existing"><?php esc_html_e( 'Existing Postcode that match by ID or Slug will be updated. Postcode that do not exist will be skipped.'); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
	</section>
	<div class="wc-actions">
		<button type="submit" class="button button-primary button-next" value="<?php esc_attr_e( 'Continue'); ?>" name="save_step"><?php esc_html_e( 'Continue'); ?></button>
		<?php wp_nonce_field( 'rise-shine-csv-importer' ); ?>
	</div>
</form>
