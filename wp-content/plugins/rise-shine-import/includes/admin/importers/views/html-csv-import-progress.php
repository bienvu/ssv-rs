<?php
/**
 * Admin View: Importer - CSV import progress
 */
?>
<div class="rise-shine-progress-form-content rise-shine-importer rise-shine-importer__importing">
	<header>
		<span class="spinner is-active"></span>
		<h2><?php esc_html_e( 'Importing'); ?></h2>
		<p><?php esc_html_e( 'Your items are now being imported...' ); ?></p>
	</header>
	<section>
		<progress class="rise-shine-importer-progress" max="100" value="0"></progress>
	</section>
</div>
