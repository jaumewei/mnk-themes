<?php defined('ABSPATH') or die;
/**
 * Widget demo para probar el entorno
 */
final class CodersTitleWidget extends \CODERS\WidgetBase {
    
    const TITLE_H1 = 'h1';
    const TITLE_H2 = 'h2';
    const TITLE_H3 = 'h3';
    const TITLE_H4 = 'h4';
    
    const TARGET_SELF = '_self';
    const TARGET_BLANK = '_blank';
    const TARGET_AUTO = '_auto';
    const TARGET_ANCHOR = 'anchor';
    
    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'T&iacute;tulo' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Widget CODERS para titular o cabecera' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersTitleWidget
     */
    protected final function registerWidgetInputs() {
        
        return parent::registerWidgetInputs()
                ->inputRegister('heading', parent::TYPE_SELECT, self::TITLE_H4,
                        __('Encabezado','coders_theme_manager'))
                ->inputRegister('link',parent::TYPE_TEXT,'',
                        __('URL','coders_theme_manager'),'',array('placeholder'=>'http:// o anchor'))
                ->inputRegister('target',parent::TYPE_SELECT,self::TARGET_AUTO,
                        __('Destino','coders_theme_manager'));
    }
    /**
     * Tipo de encabezado
     * @return array
     */
    protected final function getHeadingOptions(){
        return array(
            self::TITLE_H1 => 'H1',
            self::TITLE_H2 => 'H2',
            self::TITLE_H3 => 'H3',
            self::TITLE_H4 => 'H4',
        );
    }
    /**
     * Selección de destino
     * @return array
     */
    protected final function getTargetOptions(){
        return array(
            self::TARGET_AUTO => __('Autom&aacute;tico','coders_theme_manager'),
            self::TARGET_BLANK => __('Abrir en nueva pestaña','coders_theme_manager'),
            self::TARGET_SELF => __('Abrir en esta misma pestaña','coders_theme_manager'),
            self::TARGET_ANCHOR => __('Anchor','coders_theme_manager'),
        );
    }
    /**
     * @param array $instance
     * @param array $args
     */
    protected function display( $instance, $args = null ) {
        
        $widget = $this->inputImport($instance);
        
        if( !is_null($args) ){
            
            $before = preg_replace('/h1/', $widget['heading'],  $args['before_title'] );
            
            $after = preg_replace('/h1/', $widget['heading'],  $args['after_title'] );
            
            //printf('<!-- %s %s -->',$before,$after);
            
            if(strlen($widget['link'])){
                
                $target = $widget['target'];
                
                switch( $target ){
                    case self::TARGET_ANCHOR:
                        $link = parent::__HTML('a', array(
                            'name'=>$widget['link']),
                                $widget['title']);
                        break;
                    case self::TARGET_AUTO:
                        $target = strpos(get_site_url(), $widget['link']) !== FALSE ?
                                self::TARGET_SELF :
                                self::TARGET_BLANK;

                        $link = parent::__HTML('a', array(
                            'href'=>$widget['link'],
                            'target'=>$target),
                                $widget['title']);
                        break;
                    //case self::TARGET_BLANK:
                    //case self::TARGET_SELF:
                    default:
                        $link = parent::__HTML('a', array(
                            'href'=>$widget['link'],
                            'target'=>$target),
                                $widget['title']);
                        break;
                }
                
                print $before . $link . $after;
            }
            else{
                print $before . $widget['title'] . $after;
            }
        }
    }
}


