<?php defined('ABSPATH') or die; ?>

<?php if (have_posts()) : the_post(); ?>

    <?php if ( !is_home( ) && !is_front_page( ) ) : ?>

        <!-- CABECERA -->
        <h1 class="page-title"><?php the_title( ) ?></h1>
    
    <?php endif; ?>

    <!-- CONTENIDO -->
    <?php the_content() ?>

<?php endif; ?>


