<?php defined('ABSPATH') or die; ?>
<div class="image-scroller <?php echo $widget['orientation']; ?>">
    <div class="wrapper">
        <?php if( count($gallery)) : ?>
        <?php foreach( $gallery as $item_id => $item_meta ) : ?>
        <img class="item" src="<?php

            echo $item_meta['url'];

            ?>" alt="<?php

            echo $item_meta['alt'];

            ?>" title="<?php

            echo $item_meta['title'];

            ?>"/>
        <?php endforeach; ?>
        <?php else: ?>
        <span class="empty"><?php echo __('Lista vac&iacute;a','coders_theme_manager'); ?></span>
        <?php endif; ?>
    </div>
</div>