<?php defined('ABSPATH') or die;
/**
 * Créditos del tema
 * @author Jaume Llopis <jaume@mnkcoders.com>
 * @version 2018-11-11
 */
class CodersCreditsWidget extends \CODERS\WidgetBase{
    
    const DISPLAY_ALL = 'all';
    const DISPLAY_OWNER_DEV = 'owner_dev';
    const DISPLAY_OWNER = 'owner';
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Cr&eacute;ditos de la web' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Muestra los cr&eacute;ditos de la web definidos en la configuraci&oacute;n WP' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersCreditsWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister('display',
                parent::TYPE_SELECT, self::DISPLAY_OWNER_DEV,
                __('Vista','coders_theme_manager'));
    }
    
    /**
     * Lista de opciones  de visualización
     * @return array
     */
    protected final function getDisplayOptions(){
        return array(
            self::DISPLAY_OWNER => __('Propietario','coders_theme_manager'),
            self::DISPLAY_OWNER_DEV => __('Propietario y desarrollador','coders_theme_manager'),
            self::DISPLAY_ALL => __('Todos','coders_theme_manager'),
        );
    }
    /**
     * @param array $options
     * @return array
     */
    private final function importCredits( array $options ){

        $output = array();

        foreach( $options as $opt ){
            $output[ $opt ] = get_option($opt ,'');
        }

        return $output;
    }
    /**
     * @return array Lista los créditos a mostrar
     */
    private final function getCredits( $display = self::DISPLAY_ALL ){

        switch( $display ){
            case self::DISPLAY_OWNER:
                return array(
                    'footer_credits_firstyear',
                    //propietario
                    'footer_credits_owner',
                    'footer_credits_ownerlink',
                );
            case self::DISPLAY_OWNER_DEV:
                return array(
                    'footer_credits_firstyear',
                    //propietario
                    'footer_credits_owner',
                    'footer_credits_ownerlink',
                    //desarrolladores
                    'footer_credits_developed',
                    'footer_credits_developer',
                    'footer_credits_developerlink',
                );
            default:
                return array(
                    'footer_credits_firstyear',
                    //propietario
                    'footer_credits_owner',
                    'footer_credits_ownerlink',
                    //desarrolladores
                    'footer_credits_developed',
                    'footer_credits_developer',
                    'footer_credits_developerlink',
                    //mierda de wordpress
                    'footer_credits_powered',
                    'footer_credits_poweredcode',
                    'footer_credits_poweredcodelink',
                );
        }
    }
    /**
     * @param array $credits
     * @return array
     */
    private final function displayCredits( array $credits ){
        
        $input = $this->importCredits($credits);
        
        $output = array();
        
        if( isset( $input['footer_credits_owner'])){
            if( isset( $input['footer_credits_ownerlink'])){
                $output[] = sprintf('%s &copy; <a class="credits-owner" href="%s" target="_self">%s</a>',
                    $input['footer_credits_firstyear'],
                    $input['footer_credits_ownerlink'],
                    $input['footer_credits_owner']);
            }
            else{
                $output[] = sprintf('%s &copy; <span class="credits-owner">%s</span>',
                        $input['footer_credits_firstyear'],
                        $input['footer_credits_owner']);
            }
        }
        
        if( isset( $input['footer_credits_developer'])){

            $developer = $input['footer_credits_developer'];

            $developed_by = isset($input['footer_credits_developed']) ?
                    $input['footer_credits_developed'] : __('Desarrollado por','coders_theme_manager');

            if( isset( $input['footer_credits_developerlink'])){
                $output[] = sprintf('%s <a class="credits-dev" href="%s" target="_self">%s</a>',
                        $developed_by,
                        $input['footer_credits_developerlink'],
                        $developer);
            }
            else{
                $output[] = sprintf('%s <span class="credits-dev">%s</span>',$developed_by,$developer);
            }
        }
        
        return $output;
    }
    /**
     * Muestra los créditos
     * @param array $instance
     * @param array $args
     */
    protected function display($instance, $args = null) {
        
        $widget = $this->inputImport($instance);
        
        printf('<ul><li>%s</li></ul>', implode('</li><li>',
                $this->displayCredits(
                        $this->getCredits($widget['display']))));
    }
}