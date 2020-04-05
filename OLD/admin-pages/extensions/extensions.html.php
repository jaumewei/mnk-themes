<?php defined('ABSPATH') or die;

$optionName = CODERS\ExtensionManager::COMPONENT_LIST_OPTION;

$actionName = CODERS\AdminPage::ADMIN_PAGE_ACTION;

?>

<h1 class="wp-heading-inline"><?php print __('Extensiones', 'coders_themes') ?></h1>

<?php if( count($collection) ) : ?>

    <p><?php printf(__('<b>%s</b> de <b>%s</b> extensiones activas'), $active, $total) ?></p>

    <form name="coders-theme-manager-components" action="<?php print $form_url ?>" method="post" >

            <ul class="list-block components clearfix columns-4">

            <?php foreach( $collection as $component => $descriptor ) : ?>

            <li class="item component status-<?php print $descriptor['status'] > 0 ? 'enabled' : 'disabled'?>">

                <input class="field-input" type="checkbox" name="<?php 
                    print $optionName ?>[]" id="id-<?php
                    print $component ?>" value="<?php
                    print $component ?>" <?php
                    print $descriptor['status'] == 1 ? 'checked' : '' ?>/>

                <label for="id-<?php print $component ?>"><?php print $descriptor['name'] ?></label>
                <p><small><?php print $descriptor['description'] ?></small></p>

            </li>

            <?php endforeach; ?>

            </ul>
        
            <ul class="toolbar clearfix">
                <li class="inline">
                    <?php print \CODERS\HTML::inputSubmit(
                            $actionName, 'reset',
                            __('Desactivar todos','coders_theme'),
                            array('class'=>'button ')); ?>
                </li>

                <li class="inline right">
                    <?php print \CODERS\HTML::inputSubmit(
                            $actionName, 'save',
                            __('Guardar','coders_theme'),
                            array('class'=>'button button-primary')); ?>
                </li>
            </ul>
    </form>

<?php else : ?>

    <p><?php print __('No hay componentes disponibles', 'coders_themes') ?></p>

<?php endif; ?>
