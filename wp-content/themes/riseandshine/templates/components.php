<?php

if( get_row_layout() == 'banner' ):
  $count = count(get_sub_field("banner_item"));
  $bannerLabel = get_sub_field("label");?>

  <div class="banner-wrap">
    <div class="banner <?php if($count > 1): ?>js-slide banner--width-slide<?php endif; ?>">
      <?php if (have_rows('banner_item')):
        while (have_rows('banner_item')): the_row();
        $title = get_sub_field('title');
        $subtitle = get_sub_field('subtitle');
        $link = get_sub_field('link');
        $images = get_sub_field('image');
        $body = get_sub_field('body');
        $type = get_sub_field('type');
        $size = 'full';
        ?>
          <div class="banner__item <?php if(!$link): ?>banner--lost-link<?php endif; ?> <?php print $type; ?>">
            <?php if( $images ): ?>
              <div class="banner__image">
                <?php echo wp_get_attachment_image( $images['ID'], $size ); ?>
              </div>
            <?php endif; ?>
            <?php if($body): ?>
              <div class="banner__wrap">
                <div class="container">
                  <div class="banner__body">
                    <?php if($subtitle): ?>
                      <h2 class="banner__subtitle"><?php print $subtitle; ?></h2>
                    <?php endif; ?>

                    <div class="banner__content">
                      <?php if($count > 1): ?>
                        <?php if($title): ?>
                          <h2 class="banner__title"><?php print $title; ?></h2>
                        <?php endif; ?>
                      <?php else: ?>
                        <?php if($title): ?>
                          <h1 class="banner__title"><?php print $title; ?></h1>
                        <?php endif; ?>
                      <?php endif; ?>
                      <div class="banner__description text--large"><?php print $body; ?></div>
                      <?php if($link): ?>
                        <div class="banner__link">
                          <a href="<?php print $link['url']; ?>" class="btn"><span><?php print('discover'); ?></span></a>
                        </div>
                      <?php endif;?>
                    </div>

                    <div class="best-advice hidden-on-mobile"><?php print $bannerLabel; ?></div>
                  </div>
                </div>
              </div>
            <?php endif;?>
          </div>
        <?php endwhile;
      endif;?>
    </div>
    <div class="scroll-element">
      <i class="icon-arrow-down js-scroll-down"></i>
    </div>
  </div>

<!-- Box Intro -->
<?php elseif( get_row_layout() == 'box_intro' ):
  $title = get_sub_field("title");
  $image = get_sub_field("image");
  $body = get_sub_field("body");
  $linkStore = get_sub_field("link_store");
  $linkShop = get_sub_field("link_shop_now"); ?>

  <div class="box-intro">
    <div class="container">
      <div class="box-intro__wrap">
        <div class="box-intro__image">
          <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
        </div>

        <div class="box-intro__content">
          <div class="box-intro__top">
            <h2 class="box-intro__title"><?php print $title; ?></h2>
            <div class="box-intro__body"><?php print $body; ?></div>
          </div>

          <div class="box-intro__multilink">
            <div class="box-intro__item"><a href="<?php print $linkStore['url'] ?>" class="btn btn--dark-blue"><span><?php print $linkStore['title'] ?></span></a></div>
            <div class="box-intro__item"><a href="<?php print $linkShop['title'] ?>" class="btn"><span><?php print $linkShop['title'] ?></span></a></div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Box Icon -->
<?php elseif( get_row_layout() == 'box_icon' ):
  $bgClass = get_sub_field("background_class");
  $iconList = get_sub_field("icon_list"); ?>

  <div class="box-icon <?php print $bgClass; ?>">
    <div class="container">
      <div class="box-icon__content">
        <?php if( have_rows('icon_list') ):
         	// loop through the rows of data
          while ( have_rows('icon_list') ) : the_row();
            $image = get_sub_field("image");
            $title = get_sub_field("title");
          ?>
            <div class="box-icon__item">
              <div class="box-icon__image">
                <?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?>
              </div>
              <h4 class="box-icon__title"><?php print $title; ?></h4>
            </div>
          <?php endwhile;
        endif;
        ?>
      </div>
    </div>
  </div>

<!-- Box ShortCodes -->
<?php elseif( get_row_layout() == 'box_shortcodes' ):
  $shortCode = get_sub_field("short_code"); ?>
  <div class="box-shortcodes">
    <?php echo do_shortcode($shortCode)?>
  </div>

<!-- Box FAQ -->
<?php elseif( get_row_layout() == 'faq' ): ?>
  <div class="box-faq">
    <div class="container">
      <?php if( have_rows('faq_item') ):
        // loop through the rows of data
        while ( have_rows('faq_item') ) : the_row();
          $question = get_sub_field("question");
          $answer = get_sub_field("answer");
        ?>
          <div class="box-faq__item">
            <h4 class="box-faq__question"><?php print $question; ?></h4>
            <div class="box-faq__answer"><?php print $answer; ?></div>
          </div>
        <?php endwhile;
      endif;
      ?>
    </div>
  </div>

<!-- Box HTML code -->
<?php elseif( get_row_layout() == 'box_html' ):
  $htmlCode = get_sub_field("html_code");
  $noSpace = get_sub_field("no_space"); ?>
  <div class="box-html <?php echo $noSpace[0]; ?>">
    <?php print $htmlCode; ?>
  </div>

<!-- Box Contact Map -->
<?php elseif( get_row_layout() == 'contact_map' ): ?>
  <div class="contact-maps">
    <div class="container">
      <div class="contact-maps__list">
        <?php if( have_rows('map_item') ):
          // loop through the rows of data
          while ( have_rows('map_item') ) : the_row();
            $title = get_sub_field("title");
            $body = get_sub_field("body");
            $link = get_sub_field("link");
            $map_url = get_sub_field("map_url");
          ?>
          <div class="contact-maps__item">
            <div class="contact-maps__content">
              <div class="contact-maps__body">
                <?php if($title): ?><h3 class="contact-maps__title"><?php print $title; ?></h3><?php endif; ?>
                <div class="contact-maps__description"><?php print $body; ?></div>
              </div>
              <?php if($link): ?>
              <div class="contact-maps__link">
                <a href="<?php print $link['url']; ?>" class="btn"><span>get directions</span></a>
              </div>
            <?php endif; ?>
            </div>
            <div class="contact-maps__image"><?php print $map_url; ?></div>
          </div>
          <?php endwhile;
        endif;
        ?>
      </div>
    </div>
  </div>

<!-- Box Form -->
<?php elseif( get_row_layout() == 'box_form' ):
  $boxForm = get_sub_field("select_form"); ?>

  <div class="box-form">
    <?php echo do_shortcode('[contact-form-7 id="' . $boxForm->ID . '" title="' . $boxForm->post_title . '"]')?>
  </div>

<!-- Box Image -->
<?php elseif( get_row_layout() == 'box_image' ):
  $bgImage = get_sub_field("background_image");
  $count = count(get_sub_field("box_image_item"));
  ?>

  <div class="box-image <?php if($count > 1):?>box-image--width-2cols<?php endif; ?><?php if($bgImage): ?>box-image--width-bg<?php endif; ?>" <?php if($bgImage): ?> style="background-image: url(<?php if($bgImage){ print $bgImage; } ?>)"<?php endif; ?>>
    <div class="container">
      <div class="box-image__wrap">
        <?php if( have_rows('box_image_item') ):
          // loop through the rows of data
          while ( have_rows('box_image_item') ) : the_row();
            $image = get_sub_field("image");
            $title = get_sub_field("title");
            $subtitle = get_sub_field("subtitle");
            $link = get_sub_field("link");
            $color = get_sub_field("color");
          ?>
            <div class="box-image__item <?php print $color; ?>">
              <div class="box-image__content">
                <h2 class="box-image__title"><?php print $title; ?></h2>
                <?php if($subtitle): ?>
                  <div class="box-image__description"><?php print $subtitle; ?></div>
                <?php endif; ?>
                <div class="box-image__link">
                  <a href="<?php print $link['url']; ?>" class="btn btn--red"><span>discover</span></a>
                </div>
              </div>
              <div class="box-image__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>
            </div>
          <?php endwhile;
        endif;
        ?>
      </div>
    </div>
  </div>

<!-- Box Video -->
<?php elseif( get_row_layout() == 'box_video' ):
  $title = get_sub_field("title");
  $videoUrl = get_sub_field("video_url");
  $videoPoster = get_sub_field("video_poster");
  $link = get_sub_field("link"); ?>
  <div class="box-video">
    <div class="container">
      <div class="box-video__wrap">
        <div class="box-video__content">
          <h3 class="box-video__title"><?php print $title; ?></h3>

          <div class="box-video__video">
            <div class="video-wrap js-play-video">
              <iframe class="youtube-embed" width="960" height="540" allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/<?php print $videoUrl; ?>?autoplay=0&amp;start=0&amp;rel=0&amp;version=3&amp;loop=1&amp;enablejsapi=1">
              </iframe>

              <div class="video-wrap__poster bg--dark-black--overlay">
                <span class="video-wrap__icon icon-play"></span>
                <?php echo wp_get_attachment_image( $videoPoster['ID'], 'full' ); ?>
              </div>
            </div>
          </div>
        </div>

        <div class="box-video__link">
          <a class="btn btn--blue btn--large" href="<?php print $link['url']; ?>"><span>buy now</span></a>
        </div>
      </div>
    </div>
  </div>

<!-- Box Trial -->
<?php elseif( get_row_layout() == 'box_trial' ):
  $imageBottom = get_sub_field("image_bottom"); ?>
  <div class="box-trial">
    <div class="container">
      <div class="box-trial__list">
        <?php if( have_rows('box_trial_item') ):
          // loop through the rows of data
          while ( have_rows('box_trial_item') ) : the_row();
            $image = get_sub_field("image");
            $title = get_sub_field("title");
            $body = get_sub_field("body"); ?>
            <div class="box-trial__item">
              <div class="box-trial__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>
              <h4 class="box-trial__title"><?php print $title; ?></h4>
              <div class="box-trial__content"><?php print $body; ?></div>
            </div>
          <?php endwhile;
        endif;
        ?>
      </div>
      <?php if($imageBottom): ?>
        <div class="box-trial__bottom"><?php echo wp_get_attachment_image( $imageBottom['ID'], 'full' ); ?></div>
      <?php endif; ?>
    </div>
  </div>

<!-- Box Image Text -->
<?php elseif( get_row_layout() == 'box_image_text' ):
  $body = get_sub_field("body");
  $image = get_sub_field("image");
  $type = get_sub_field("type"); ?>
  <div class="box-image-text <?php print $type; ?>">
    <div class="container">
      <div class="box-image-text__wrap">
        <div class="box-image-text__body"><?php print $body; ?></div>
        <div class="box-image-text__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>
      </div>
    </div>
  </div>

<!-- Box Category -->
<?php elseif( get_row_layout() == 'product_category' ):
  $categoryData = get_sub_field('category');
    if( $categoryData ): ?>
      <div class="grid-image">
        <div class="container">
          <div class="grid-image__wrap">
            <?php foreach( $categoryData as $term ): ?>
              <?php

              $termId = $term->term_id;
              $catName = $term->name;
              $catUrl = $term->slug;
              $catDescription = $term->description;
              $catImage = get_field('banner_image', 'product_cat_' . $termId);
              $color = get_field('color', 'product_cat_' . $termId);
              $thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
              $catImg = wp_get_attachment_image( $thumb_id, 'full', true );?>
                <div class="grid-image__item <?php print $color; ?>">
                  <a href="/<?php print $catUrl; ?>">
                    <?php if($catUrl != 'sale'): ?>
                      <div class="grid-image__content">
                        <h3 class="grid-image__title"><?php print $catName; ?></h3>
                        <div class="grid-image__body"><?php print $catDescription; ?></div>
                      </div>
                      <div class="grid-image__image"><?php echo $catImg; ?></div>
                    <?php else: ?>
                      <div class="grid-image__image"><?php echo $catImg; ?></div>
                    <?php endif; ?>
                  </a>
                </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>
<?php endif; ?>
