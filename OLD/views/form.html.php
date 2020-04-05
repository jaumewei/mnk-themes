<?php namespace CODERS\Html;

use \CODERS\HTML;
use \CODERS\Dictionary;

/**
 * HTML e inputs
 * 
 * Interesa más definirlo como TRAIT en lugar de clase para facilitar multi-herencia
 * 
 * @author Jaume Llopis
 */
class Form extends HTML{
    
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';
    
    const TYPE_DATA = 'multipart/form-data';
    const TYPE_PLAIN = 'text/plain';
    const TYPE_APPLICATION = 'application/x-www-form-urlencoded';
    /**
     * @var \CODERS\Dictionary
     */
    private $_model;
    /**
     * 
     * @param \CODERS\Dictionary $model
     * @param string $action
     * @param string $method
     * @param string $type
     */
    public function __construct( Dictionary $model , $action , $method = self::METHOD_POST , $type = self::TYPE_PLAIN ) {
        //inicializar siempre como array
        $this->_htmlContent = array();
        //asignar modelo de metas
        $this->_model = $model;
        
        parent::__construct( 'form', array(
            'name' => strval($model),
            'action' => $action,
            'method' => $method,
            'enctype' => $type,
        ));
    }
    /**
     * @return HTML
     */
    /*protected function __toHtml() {

        //renderizar formulario
        
        //return parent::__toHtml();
    }*/
    /**
     * @param strin $name
     * @return mixed
     */
    public function __get( $name ) {
        
        if(substr($name, 0,6) === 'input_' ){
            
            return $this->__input( substr($name, 6 , strlen($name)-6) );
        }
        
        return $name;
    }
    /**
     * Muestra un input del form
     * @param string $input
     */
    protected final function __input( $input ){
        
        if( !is_null($this->_model)){
            
            $def = $this->_model;

            $atts = $def->getDefinition( $input );
            $type = $atts['type'];
            $value = $atts['value'];
            unset( $atts['value' ] );
            unset( $atts['name' ] );
            unset( $atts['type' ] );
            
            $output = self::renderLabel($input, $def->getLabel($input) );
        
            switch( $type ){
                case Dictionary::TYPE_FILE:
                    return self::inputFile($input, $atts);
                case Dictionary::TYPE_LIST:
                    return self::inputList( $input,
                            $def->getOptions($input),
                            $value, $atts);
                case Dictionary::TYPE_SELECT:
                    return self::inputDropDown( $input,
                            $def->getOptions($input),
                            $value, $atts);
                case Dictionary::TYPE_CHECKBOX:
                    return self::inputCheckBox( $input ,
                            isset( $atts['checked'] ) && $atts['checked'],
                            $value , $atts );
                case Dictionary::TYPE_FLOAT:
                case Dictionary::TYPE_PRICE:
                    return self::inputNumber( $input, $value , $atts );
                case Dictionary::TYPE_TEXTAREA:
                    return parent::inputText($input, $value, $atts);
                default:
                    return parent::inputText($input, $value, $atts);
            }
            
            return $output;
        }
        
        return sprintf('<!-- invalid input: %s -->',$input );
    }
    /**
     * @param mixed $content
     * @return \CODERS\Htnl\Form
     */
    public final function addContent( $content ){
       
        $this->_htmlContent[] = $content;
        
        return $this;
    }
}

