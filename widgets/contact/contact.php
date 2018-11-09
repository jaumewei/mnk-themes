<?php defined('ABSPATH') or die;
/**
 * Enlace de contacto
 */
final class CodersContactWidget extends \CODERS\WidgetBase {

    const WHATSAPP_API = 'https://api.whatsapp.com/send';
    
    const TYPE_EMAIL = 'mailto';

    const TYPE_TELEPHONE = 'tel';
    
    const TYPE_WHATSAPP = 'whatsapp';

    /**
     * @return string Título
     */
    public static final function defineWidgetTitle() {
        return __( 'Contacto' , 'coders_theme_manager' );
    }
    /**
     * @return string Descripción
     */
    public static final function defineWidgetDescription() {
        return __( 'Widget CODERS de enlace Email o tel&eacute;fono de contacto' , 'coders_theme_manager' );
    }
    /**
     * @return \CodersContactWidget
     */
    protected final function registerWidgetInputs() {
        return $this->inputRegister( 'contact',
                    parent::TYPE_TEXT, '',
                    __('Contacto','coders_theme_manager'))
                ->inputRegister( 'type',
                    parent::TYPE_SELECT, self::TYPE_EMAIL,
                    __('Tipo','coders_theme_manager'));
    }
    /**
     * @return array
     */
    protected final function getTypeOptions(){
        return array(
            self::TYPE_EMAIL => __( 'Email' , 'coders-contact-widget'),
            self::TYPE_TELEPHONE => __( 'Teléfono' , 'coders-contact-widget'),
            self::TYPE_WHATSAPP => __( 'Whatsapp' , 'coders-contact-widget'),
            );
    }
    /**
     * Sanitiza el input del email
     * @param string $email
     * @return string
     */
    private final function sanitizeEmail( $email ){
        
        return preg_replace('/[^0-9a-z\.\@\-\_]/', '', trim( $email ) );
    }
    /**
     * @param string $telefono
     * @return string
     */
    private final function sanitizeTelephone( $telefono ){
        
        return preg_replace('/[^0-9\s]/', '', trim( $telefono ) );
    }
    /**
     * Valida el email
     * @param string $email
     * @return boolean
     */
    private function validateEmail( $email ) {
        
        $at = strpos('@', $email );
        
        if( $at !== false ){
            
            $dom = strrpos('.', $email);
            
            return $at < $dom && $dom < strlen($email) - 1;
        }
        
        return false;
    }
    /**
     * Filtra y sanitiza los tipos de contacto (email/telefono)
     * @param array $instance
     * @param array $old
     * @return array
     */
    protected final function inputImport(array $instance, array $old = null) {
        
        $output = parent::inputImport($instance, $old);
        
        switch( $output['type'] ){
            case self::TYPE_EMAIL:
                $output['contact'] = $this->sanitizeEmail($output['contact']);
                break;
            case self::TYPE_TELEPHONE:
            case self::TYPE_WHATSAPP:
                $output['contact'] = $this->sanitizeTelephone($output['contact']);
                break;
        }
        
        return $output;
    }
    /**
     * presentación frontal
     * @param array $instance
     * @param array $args
     */
    function display($instance, $args = null) {

        $widget = $this->inputImport( $instance );
        
        switch( $widget ['type' ] ){
            case self::TYPE_WHATSAPP:
                print self::__HTML('a', array(
                    'href' => sprintf('%s?phone=%s' ,
                            self::WHATSAPP_API ,
                            preg_replace('/\s+/', '',  $widget['contact'] ) ),
                    'class' => 'icon-whatsapp',
                ), $widget['contact']);
                break;
            case self::TYPE_TELEPHONE:
                print self::__HTML('a', array(
                    'href' => 'tel:' . preg_replace('/\s+/', '',  $widget['contact'] ),
                    'class' => 'icon-telephone',
                ), $widget['contact']);
                break;
            case self::TYPE_EMAIL:
                print self::__HTML('a', array(
                    'href' => 'mailto:' . $widget['contact'],
                    'class' => 'icon-email',
                ), $widget['contact']);
                break;
            default:
                print '<!-- tipo de contacto inv&aacute;lido -->';
                break;
        }
    }
}
