<?php defined('ABSPATH') or die; ?>

<?php print strlen( $widget['link'] ) ?
    sprintf('<a class="media-wrapper" href="%s" target="_self">',$widget['link']) :
    '<div class="media-wrapper">'; ?>

    <?php if( isset( $widget['media_url_medium'] ) ) : ?>
    <img class="media <?php echo $widget['media_display']; ?>" src="<?php
        echo $widget['media_url_medium']; ?>" alt="<?php
        echo $widget['title']; ?>" title="<?php
        echo $widget['title']; ?>" />
    <?php else : ?>
    <span class="empty"></span>
    <?php endif; ?>

<?php print strlen( $widget['link'] ) ? '</a>' : '</div>'; ?>

<div class="media-container">

    <h4 class="media-title">
        <?php if( !empty( $widget['link'] ) ) {
            printf('<a href="%s" target="_self">',$widget['link']);
        } ?>

        <?php echo $widget['title']; ?>

        <?php if( !empty( $widget['link'] ) ) { print '</a>'; } ?>
    </h4>

    <?php if( strlen($widget['text'])) : ?>

    <div class="media-description"><?php echo $widget['text']; ?></div>

    <?php endif; ?>
</div>