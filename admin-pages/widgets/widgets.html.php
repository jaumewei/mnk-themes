<?php defined('ABSPATH') or die;

$widget_option = \CODERS\WidgetManager::WIDGET_LIST_OPTION;

?>

<h1 class="wp-heading-inline"><?php print __('Widgets', 'coders_theme_manager') ?></h1>

<?php if (count( $collection )) : ?>

    <p><?php printf(__('<b>%s</b> de <b>%s</b> widgets activos'), $active, $total) ?></p>

    <form name="coders-theme-manager-widgets" action="<?php print $form_url ?>" method="post" >

    <ul class="list-block widget-pack clearfix columns-4">
    <?php foreach ($collection as $widget => $descriptor ) : ?>
        <li class="item widget status-<?php print $descriptor['status'] ?>">
            <input type="checkbox" class="field-input" id="id_<?php
                print $widget ?>" name="<?php
                print $widget_option ?>[]" value="<?php
                print $widget ?>" <?php
                print $descriptor['status'] == 'enabled' ? 'checked' : '' ?> />

            <label for="id_<?php print $widget ?>"><?php print $descriptor['title'] ?></label>
            <p><small><?php print $descriptor['description'] ?></small></p>
        </li>
    <?php endforeach; ?>
    </ul>

    <ul class="toolbar clearfix">
        <li class="inline">
            <button class="inline button"
                    type="submit"
                    name="<?php print \CODERS\AdminPage::ADMIN_PAGE_ACTION ?>"
                    value="reset" ><?php print __('Desactivar todos', 'coders_theme_manager') ?></button>
        </li>
        <li class="inline right">
            <button class="inline button button-primary"
                    type="submit"
                    name="<?php print \CODERS\AdminPage::ADMIN_PAGE_ACTION ?>"
                    value="save" ><?php print __('Guardar', 'coders_theme_manager') ?></button>
        </li>
    </ul>
    </form>
<?php else: ?>

    <p><?php print __('No hay widgets disponibles', 'coders_theme_manager') ?></p>

<?php endif; ?>