<?php defined('ABSPATH') or die; ?>
<img class="media <?php print $widget['media_display']; ?>" src="<?php
    echo $widget['media_url']; ?>" alt="<?php
    echo $widget['title']; ?>" title="<?php
    echo $widget['title']; ?>" />

<div class="media-container">
    <div class="wrap">
        <div class="media-content">
            <h3 class="media-title">
                <?php if( isset( $widget['link'] ) ) : ?>
                <a href="<?php echo $widget['link']; ?>" target="_self">
                <?php endif; ?>

                <?php echo $widget['title']; ?>

                <?php if( isset( $widget['link'] ) ) : ?>
                </a>
                <?php endif; ?>
            </h3>

            <?php if( strlen($widget['text'])) : ?>

            <div class="media-description">
                <p><?php print implode('</p><p>' , explode("\n", $widget['text'] ) ); ?></p>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>
