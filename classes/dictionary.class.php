<?php namespace CODERS;
/**
 * Descriptor de valores e información meta
 */
class Dictionary{
    const TYPE_UNDEFINED = 'undefined';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_NUMBER = 'number';
    const TYPE_FLOAT = 'float';
    const TYPE_PRICE = 'price';
    const TYPE_LIST = 'list';
    const TYPE_SELECT = 'select';
    const TYPE_FILE = 'file';
    /**
     * @var array
     */
    private $_elements = array();
    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {

        return $this->getValue( $name );

    }
    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set( $name , $value ){

        $this->__setAttribute($name, 'value', $value);

    }
    /**
     * Tiene un atributo?
     * @param string $name
     * @param string $att
     * @return boolean
     */
    public function hasAttribute( $name , $att ){
        return isset($this->_elements[$name]) && isset($this->_elements[$name][$att]);
    }
    /**
     * @param string $name
     * @param string $att
     * @param mixed $default
     * @return mixed
     */
    protected function __getAttribute( $name , $att, $default = null ){
        if( isset($this->_elements[$name])){
            return isset( $this->_elements[$name][$att]) ?
                    $this->_elements[$name][$att] :
                    $default;
        }
        return $default;
    }
    /**
     * @param string $name
     * @param string $attribute
     * @param mixed $value
     * @return \CODERS\Dictionary
     */
    protected function __setAttribute( $name , $attribute , $value ){
        if( isset( $this->_elements[$name]) ){
            $this->_elements[$name][$attribute] = $value;
        }
        return $this;
    }
    /**
     * @param string $input
     * @return array
     */
    public final function getOptions( $input ){
        
        $options = sprintf('get%sOptions' ,
                strtoupper( substr($input, 0,1) )
                . strtolower( substr($input, 1, strlen($input)-1) ) );
        
        return method_exists($this, $options) ? $this->$options : array();
    }
    /**
     * 
     * @param string $name
     * @param string $type
     * @param array $atts
     * @return \CODERS\Dictionary
     */
    public function addField( $name , $type , array $atts = array() ){
        
        if( strlen($name) > 1 && !isset( $this->_elements[$name])){

            $inputDef = array(
                'name' => $name,
                'type' => $type,
            );
            
            switch( $type ){
                case self::TYPE_NUMBER:
                    $inputDef['min'] = isset($atts['min']) ? intval($atts['min']) : 0;
                    $inputDef['max'] = isset($atts['max']) ? intval($atts['max']) : 0;
                    $inputDef['increment'] = isset($atts['increment']) ? intval($atts['increment']) : 1;
                    $inputDef['value'] = isset($atts['value']) ? intval($atts['value']) : 0;
                    break;
                case self::TYPE_FLOAT:
                case self::TYPE_PRICE:
                    $inputDef['min'] = isset($atts['min']) ? intval($atts['min']) : 0;
                    $inputDef['max'] = isset($atts['max']) ? intval($atts['max']) : 0;
                    $inputDef['increment'] = isset($atts['increment']) ? intval($atts['increment']) : 0.1;
                    $inputDef['value'] = isset($atts['value']) ? floatval($atts['value']) : 0;
                    break;
                case self::TYPE_CHECKBOX:
                    $inputDef['value'] = isset($atts['value']) ? intval($atts['value']) : 0;
                    break;
                case self::TYPE_LIST:
                    $inputDef['value'] = isset($atts['value']) ? $atts['value'] : ''; 
                    $inputDef['options'] = $this->__getOptions($name);
                    break;
                case self::TYPE_TEXTAREA:
                    $inputDef['value'] = isset($atts['value']) ? $atts['value'] : ''; 
                    break;
                default:
                    $inputDef['value'] = isset($atts['value']) ? $atts['value'] : ''; 
                    $inputDef['type'] = self::TYPE_TEXT;
                    break;
            }


            $this->_elements[$name] = $inputDef;
        }
        
        return $this;
    }
    /**
     * Lista los valores del diccionario
     * @return array
     */
    public function listValues(){
        $values = array();
        foreach( $this->listFields() as $field ){
            $values[ $field ] = $this->$field;
        }
        return $values;
    }
    /**
     * Lista los campos del diccionario
     * @return array
     */
    public function listFields(){
        return array_keys($this->_elements);
    }
    /**
     * @param string $name
     * @return string
     */
    public final function getType( $name ){
        return isset( $this->_elements[$name] ) ?
                $this->_elements[$name]['type'] :
                self::TYPE_UNDEFINED;
    }
    /**
     * Definición del tipo de datos
     * @param string $name
     * @return array
     */
    public final function getDefinition( $name ){
        return isset( $this->_elements[ $name ] ) ?
                $this->_elements[ $name ] :
                array();
    }
    /**
     * Etiqueta para mostrar
     * @param string $name
     * @return string
     */
    public function getLabel( $name ){
        return $this->__getAttribute($name, 'label' , $name );
    }
    /**
     * Captura un valor
     * @param string $name
     * @param mixed $value
     */
    public function getValue( $name ){
        return $this->__getAttribute($name, 'value');
    }
    /**
     * Establece un valor
     * @param string $name
     * @param mixed $value
     * @return \CODERS\Dictionary
     */
    public function setValue( $name , $value ){
        $this->__setAttribute($name, 'value', $value);
        return $this;
    }
    /**
     * Importa los valores dentro del diccionario
     * @param array $values
     * @return \CODERS\Dictionary
     */
    public function importValues( array $values ){
        foreach( $this->listFields() as $field ){
            if( isset( $values[$field]) ){
                $this->setValue($field,$values[$field]);
            }
        }
        return $this;
    }
}



