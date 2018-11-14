
			<?php
			$footerData = get_field('footer','option');

			if( $footerData ): ?>
			<div class="box-feature">
		    <div class="container">
		      <div class="box-feature__wrap">
		        <div class="box-feature__left "><?php print $footerData['footer_top_left']; ?></div>
		        <div class="box-feature__right"><?php print $footerData['footer_top_right']; ?></div>
		      </div>
		    </div>
		  </div>
			<?php endif; ?>
			<footer class="footer text--small">
		    <div class="container">
		      <div class="footer__top">
		        <div class="scroll-up">
		          <a class="icon-arrow-up text--small js-scroll-top" href="#">BACK TO TOP</a>
		        </div>

						<nav class="nav" role="navigation">
							<?php french_table_nav('main-menu','Main Menu'); ?>
						</nav>
		      </div>
					<?php if( $footerData ): ?>
			      <div class="footer__bottom">
			        <?php print $footerData['footer_bottom']; ?>
			      </div>
					<?php endif; ?>
		    </div>
				<div class="footer-sticky">
					<a href="/contact">BOOK A CONSULTATION</a>
				</div>
		  </footer>
		</div>
		<!-- /wrapper -->

		<?php wp_footer(); ?>
	</body>
</html>
