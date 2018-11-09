<?php defined('ABSPATH') or die; ?>

<?php if (have_posts()) : the_post(); ?>

    <!-- CABECERA -->
    <h1 class="post-title">
    
        <?php the_title(); ?>

    </h1>
    
    <!-- CONTENIDO -->
    <?php the_content(); ?>

<?php endif; ?>