<?php
  global $product, $post;
  $size_terms = wp_get_post_terms($product->get_id(), 'pa_size');
  if (empty($size_terms)) {
    return '';
  }
?>
<div class="size-information">
  <div class="guide-select-size">
    <?php print __('Select a size: ', 'riseandshine-theme'); ?><a href="#"><?php print __('Size Guide', 'riseandshine-theme'); ?></a>
  </div>
  <div class="sizes-available">
    <?php foreach ($size_terms as $key => $size_term): ?>
      <div class="item-size"><?php print $size_term->name; ?></div>
    <?php endforeach ?>
  </div>
</div>
