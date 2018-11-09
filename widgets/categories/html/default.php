<?php defined('ABSPATH') or die; ?>
<!-- CATEGORY LIST WIDGET START -->
<ul class="category-list">
<?php foreach( $category_list as $cat_id => $cat_meta ) : ?>
    <li class="cat-item">
        <a class="category <?php
        
        echo $cat_meta['slug'];
        
        ?>" href="<?php
        
        echo get_term_link ($cat_id);
        
        ?>" target="_self"><?php
        
        echo $cat_meta['name'];
        
        ?></a>
    </li>
<?php endforeach; ?>
</ul>
<!-- CATEGORY LIST WIDGET END -->