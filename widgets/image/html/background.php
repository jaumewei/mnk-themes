<?php defined('ABSPATH') or die; ?>
<?php if( !empty( $link ) ) : ?>
<a href="<?php
    echo $link; ?>" target="<?php
    echo $target_url; ?>" class="coders-media-widget-wrap overlay link <?php
    echo $media_ratio; ?>" style="background-image: url('<?php
    echo $media_url; ?>');">

    <h3 class="media-content-title"><?php echo $title; ?></h3>

    <?php if( !empty($text) && strlen($text)) : ?>
    
    <span class="media-content-description"><?php echo $text; ?></span>
    
    <?php endif; ?>
        
</a>
<?php else : ?>
<div class="coders-media-widget-wrap overlay <?php
    echo $media_ratio; ?>" style="background-image: url('<?php
    echo $media_url; ?>');">

    <h3 class="media-content-title"><?php echo $title; ?></h3>

    <?php if( !empty($text) && strlen($text)) : ?>
    
    <div class="media-content-description"><?php echo $text; ?></div>
    
    <?php endif; ?>
        
</div>
<?php endif; ?>