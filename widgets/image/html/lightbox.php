<?php defined('ABSPATH') or die; ?>
<div class="coders-media-widget-wrap lightbox">
    <div class="media-content-wrap">
        <img class="media-content <?php echo $widget['media_display']; ?>" data-item="<?php
            echo $widget['media']; ?>" src="<?php
            echo $widget['media_url']; ?>" alt="<?php
            echo strip_tags( $widget['text'] ); ?>" title="<?php
            echo strip_tags( $widget['title'] ); ?>" />
    </div>
</div>