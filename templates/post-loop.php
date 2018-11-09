<?php defined('ABSPATH') or die; ?>

<?php if ( !is_front_page( ) ) : ?>
    <h1 class="category-title"><?php print get_queried_object()->name ?></h1>
<?php endif; ?>

<?php if (have_posts()) : ?>
    <ul class="loop">
        <?php while ( have_posts()) : the_post(); ?>
        <li class="post">
            <a href="<?php echo get_permalink(get_the_ID()); ?>">
                <?php print coders_thememan_image( get_the_ID() , 'medium' ); ?>
            </a>
            <h1 class="title">
                <a href="<?php print get_permalink(get_the_ID()); ?>">
                    <?php the_title(); ?>
                </a>
            </h1>
            <div class="content">
                <?php the_content(__('Leer m&aacute;s')); ?>
            </div>
        </li>
        <?php endwhile; ?>
    </ul>

    <div class="pages"><?php echo paginate_links(); ?></div>

<?php else: ?>
    <!-- no post -->
    <p><?php print __('No hay entradas que mostrar') ?></p>
<?php endif; ?>

