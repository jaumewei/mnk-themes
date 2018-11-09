<?php defined('ABSPATH') or die; ?>
<div class="<?php print implode(' ', $classes) ?>">
    <ul class="post-categories">
        <!-- agregar categorías disponibles -->
    </ul>
    <ul class="post-selection post-items-<?php echo $widget['count'] ?>">
        <?php foreach( $posts as $post_id => $post_data ) : ?>
        <li class="post <?php echo $post_data['post_name']; ?>">
            <!-- THUMBNAIL -->
            <a class="thumbnail display-<?php

            echo $post_data['post_thumb_display'];

            ?>" href="<?php echo $post_data['post_url']; ?>" targe="_self">
            <?php if( $post_data['post_thumb_url'] !== false ) : ?>
                <img src="<?php

                echo $post_data['post_thumb_url'];

                ?>" alt="<?php

                echo $post_data['post_title'];

                ?>" title="<?php

                echo $post_data['post_title'];

                ?>" />
            <?php else: ?>
                <span class="no-thumbnail"><!-- NO THUMBNAIL CONTENT --></span>
            <?php endif; ?>
            </a>
            <?php if( intval($widget['display']) > CodersPostsWidget::DISPLAY_EMPTY ) : ?>
            <!-- POST CONTENT -->
            <div class="post-wrapper">
                <h4 class="post-title"><a href="<?php echo get_permalink ($post_id); ?>" target="_blank"><?php

                echo $post_data['post_title'];

                ?></a></h4>
                <?php if( intval($widget['display']) >= CodersPostsWidget::DISPLAY_CONTENT ) : ?>
                    <?php if( intval($widget['display']) === CodersPostsWidget::DISPLAY_ALL ) : ?>
                    <div class="post-meta"><?php echo $post_data['post_date']; ?></div>
                    <?php endif; ?>
                <div class="post-content">
                    <?php if( strlen( $post_data['post_excerpt'] ) ): ?>
                    <?php echo $post_data['post_excerpt']; ?>
                    <?php endif; ?>
                    <!-- READ MORE -->
                    <a class="read-more" href="<?php

                        echo $post_data['post_url']; ?>" target="_blank"><?php

                        echo __( 'Leer m&aacute;s' );
                    ?></a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>