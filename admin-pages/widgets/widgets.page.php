<?php namespace CODERS\AdminPages;

defined('ABSPATH') or die;

use \CODERS\AdminPage;
use \CODERS\WidgetManager;

/**
 * Gestor de widgets del admin
 */
final class Widgets extends AdminPage{

    public final function getPageName() {
        return __('Paquete de Widgets','coders_theme_manager');
    }

    public final function getMenuName() {
        return __('Widgets','coders_theme_manager');
    }
    /**
     * Mostrar form de widgets
     */
    public final function content(){
       
        if( $this->request() ){
            $this->notify( __('Cambios realizados','coders_theme_manager') );
        }
        
        $view_path = self::viewPath(strval($this));
        
        if(file_exists($view_path)){

            $manager = WidgetManager::instance();
            
            $collection = $manager->listWidgetData();

            $active = count( $manager->widgets());

            $total = count( $manager->widgets(false));
            
            $form_url = $this->getUrl();

            require $view_path;
        }
    }
    /**
     * Deshabilita todos los widgets disponibles
     * @return boolean
     */
    protected final function request_reset(){
        $manager = WidgetManager::instance();
        
        if( !is_null($manager)){
            $manager->reset()->save();
            return TRUE;
        }

        return FALSE;
    }
    /**
     * Procesa los cambios en la lista de widgets
     */
    protected final function request_save(){
        
        $manager = WidgetManager::instance();
        
        //listar  todos los widgets disponibles
        $widget_list = array_keys($manager->widgets(FALSE));
        
        $active_widgets = filter_input( INPUT_POST ,
                WidgetManager::WIDGET_LIST_OPTION ,
                FILTER_DEFAULT ,
                FILTER_REQUIRE_ARRAY );
        
        if(is_array($active_widgets) && count( $active_widgets ) ){
            //reasignar en función de los widgets marcados como activos
            foreach ($widget_list as $widget_id) {
                $manager->set($widget_id, in_array($widget_id, $active_widgets));
            }
        }
        //guardar widgets activados
        $manager->save( );
        
        return TRUE;
    }
}


