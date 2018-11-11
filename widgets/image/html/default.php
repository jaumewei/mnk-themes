<?php defined('ABSPATH') or die; ?>
<?php if( strlen($widget['link'] ) ) : ?>
<a class="media-wrapper link" href="<?php print $widget['link'] ?>" target="_self">
<?php else: ?><div class="media-wrapper"><?php endif; ?>
    <img class="media <?php print $widget['media_display'] . ' ' . $widget['size'] ?>" src="<?php
        print $widget['media_url'] ?>" alt="<?php
        print $widget['media_alt'] ?>" />
<?php if( strlen($widget['link'] ) ) : ?></a><?php else: ?></div><?php endif; ?>
<?php if (strlen($widget['text']) || strlen($widget['title'])) : ?>
<div class="media-content">
    <?php if (strlen($widget['title'])) : ?>
        <?php if (strlen($widget['link'])) : ?><a href="<?php print $widget['link'] ?>" target="_self"><?php endif; ?>
            <h2 class="media-title"><?php print $widget['title'] ?></h2>
            <?php if (strlen($widget['link'])) : ?></a><?php endif; ?>
        <?php endif; ?>
    <?php if (strlen($widget['text'])) : ?>
        <p class="media-text"><?php print $widget['text'] ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>
