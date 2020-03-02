<?php namespace CODERS\AdminPages;

use \CODERS\AdminPage;

use \CODERS\ExtensionManager as Manager;

use \CODERS\HTML as HTML;

/**
 * Página principal
 *
 * @author informatica1
 */
class Extensions extends AdminPage{
    
    
    public final function getPageName() {
        return __('Extensiones','coders_themes');
    }

    public final function getMenuName() {
        return __('Extensiones','coders_themes');
    }
    /**
     * @return HHML
     */
    public final function content() {
        
        $view_path = parent::viewPath(strval($this));
        
        if(file_exists($view_path)){
            
            $collection = Manager::instance()->listExtensionData();

            $active = Manager::instance()->countActive();
            
            $total = Manager::instance()->countAll();
            
            require $view_path;
        }
    }
    /**
     * Deshabilita todos los widgets disponibles
     * @return boolean
     */
    protected final function request_reset(){
        
        $manager = Manager::instance();
        
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

        $manager = Manager::instance();

        $all_extensions = array_keys( $manager->extensions( FALSE ) );
        
        $active_extensions = filter_input( INPUT_POST ,
                Manager::COMPONENT_LIST_OPTION ,
                FILTER_DEFAULT ,
                FILTER_REQUIRE_ARRAY );
        
        if( !is_null($active_extensions)){
            //reasignar en función de los widgets marcados como activos
            foreach ($all_extensions as $extension_id) {
                $manager->setStatus($extension_id, in_array($extension_id, $active_extensions));
            }

            //guardar widgets activados
            if( $manager->updated() ){
                $manager->save( );
                return TRUE;
            }
        }

        return FALSE;
    }
}




