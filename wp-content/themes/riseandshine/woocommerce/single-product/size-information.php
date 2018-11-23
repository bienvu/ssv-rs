<?php
  global $product, $post;
  $size_terms = wp_get_post_terms($product->get_id(), 'pa_size');
  if (empty($size_terms)) {
    return '';
  }
?>
<div class="size-information">
  <div class="size-information__title">
    <?php print __('Select a size: ', 'riseandshine-theme'); ?><a href="measurements/#size-guide"><?php print __('Size Guide', 'riseandshine-theme'); ?></a>
  </div>
  <div class="size-information__value">
    <?php foreach ($size_terms as $key => $size_term): ?>
      <div class="item-size"><?php print $size_term->name; ?></div>
    <?php endforeach ?>
  </div>
</div>
