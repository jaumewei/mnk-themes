<?php defined('ABSPATH') or die;
/**
 * Cada post debe contener esta información:
 * 
 * $item['description']; para mostrar la descripción
 * $item['image']; para mostrar la imagen destacada
 * $item['title']; para mostrar el título
 * $item['url']; para mostrar la url
 * 
 * Añadir posibilidad de seleccionar metas en el widget, por si es necesario
 * agregar enlaces personalizados o información extra.
 * 
 * Gestionar esos metas desde el post.
 */
?>
<ul class="slideshow-container <?php print $widget['classes']; ?>" data-speed="<?php print $widget['speed'] ?>" >
    <?php foreach( $widget['slides'] as $post_id => $post_meta ) : ?>
    <li data-id="<?php echo $post_id; ?>" class="slide testimonial">
        <div class="testimonial-box">
            <img class="slide-media" src="<?php
            
                echo $post_meta['image']; ?>" alt="<?php
                
                print $post_meta['title'] ?>" />
        </div>
        <?php if( !$hide_title || !$hide_content ) : ?>
        <div class="testimonial-content">
            <?php if( !$hide_content && strlen($post_meta['description']) ) : ?>
            <blockquote class="testimonial-text"><?php echo $post_meta['description']; ?></blockquote>
            <?php endif; ?>
            <?php if( !$hide_title && strlen($post_meta['title'])) : ?>
            <strong class="testimonial-name"><?php echo $post_meta['title']; ?></strong>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
