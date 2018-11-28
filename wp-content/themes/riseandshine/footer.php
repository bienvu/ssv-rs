
      <?php
        $sitewideData = get_field('sitewide','option');
        $socialData = $sitewideData['social'];
        $footer_bottom = $sitewideData['footer_bottom'];
        $footer_form = $sitewideData['footer_form'];
      ?>
      <footer class="footer">
        <div class="footer__top">
          <div class="container">
            <div class="footer__wrap">
              <div class="menu-basic">
                <?php rs_nav('footer-menu','Footer menu'); ?>
              </div>

              <div class="footer__icons">
                <h6 class="footer__title"><?php _e( 'follow us', 'ssvtheme' ); ?></h6>

                <ul class="list-icons">
                  <?php foreach ( $socialData as $value ) : ?>
                    <li><a href="<?php print $value['link']['url']; ?>" class="<?php print $value['class']; ?>" target="<?php print $value['link']['target']; ?>"><?php print $value['link']['title']; ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </div>

              <div class="footer__form">
                <?php echo do_shortcode('[contact-form-7 id="' . $footer_form->ID . '" title="' . $footer_form->post_title . '"]')?>
              </div>
            </div>
          </div>
        </div>

        <div class="footer__bottom">
          <div class="container">
            <div class="footer__wrap">
              <?php print $footer_bottom; ?>
            </div>
          </div>
        </div>
        
        <div class="form-fixed">
          <?php echo do_shortcode('[contact-form-7 id="210" title="request a call back"]'); ?>
        </div>
      </footer>
    </div>
    <!-- /wrapper -->

    <?php wp_footer(); ?>
  </body>
</html>
