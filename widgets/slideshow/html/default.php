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
<ul class="slideshow-container <?php print $widget['orientation'] ?> <?php print $widget['theme']; ?>">
    <?php foreach( $slides as $post_id => $post_meta ) : ?>
    <li data-id="<?php echo $post_id; ?>" class="slide <?php

        print trim(sprintf('%s %s %s', 
                $post_meta['slug'],implode(' ' , $post_meta['tags']), 
                implode(' ', $post_meta['categories'])));

        ?>">
        <img class="slide-media" src="<?php echo $post_meta['image']; ?>" alt="<?php print $post_meta['title'] ?>" />
        <?php if( $mode === 'all' || $mode === 'title' || $mode === 'content' ) : ?>
            <div class="slide-content">
                <?php if( ( $mode === 'title' || $mode === 'all' ) && $post_meta['title'] ) : ?>
                <h1 class="slide-title"><?php echo $post_meta['title']; ?></h1>
                <?php endif; ?>
                <?php if( ( $mode === 'content' || $mode === 'all' ) && strlen($post_meta['description']) ) : ?>
                <div class="slide-description"><?php echo $post_meta['description']; ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
