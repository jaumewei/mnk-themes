<?php namespace CODERS;
/**
 * 
 */
final class Request{
    /**
     * @var array
     */
    private $_input = array();
    /**
     * 
     */
    public final function __construct( ) {
        
        $get = filter_input_array( INPUT_GET );
        
        $post = filter_input_array( INPUT_POST );
        
        $this->import( array_merge( 
                !is_null( $get ) ? $get : array(), 
                !is_null( $post ) ? $post : array() ) );

    }
    /**
     * Sanitiza los inputs
     * @param array $input
     * @return \CODERS\Request
     */
    private final function import( array $input ){
        foreach( $input as $var => $val ){
            $this->_input[ $var ] = sanitize_text_field( $val );
        }
        return $this;
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        return $this->get($name,'');
    }
    
    /**
     * @param string $attr
     * @param mixed $default
     * @return mixed
     */
    public final function get( $attr , $default = null ){
        
        return isset( $this->_input[$attr]) ? $this->_input[$attr] : $default;
        
    }
    /**
     * @param string $attr
     * @return int
     */
    public final function getInt( $attr ){
        return intval($this->get($attr,0));
    }
    /**
     * @param string $attr
     * @return int
     */
    public final function getFloat( $attr ){
        return floatval($this->get($attr,0.0));
    }
    /**
     * @param string $attr
     * @param mixed $separator
     * @return array
     */
    public final function getArray( $attr , $separator = '|' ){
        
        return isset( $this->_input[$attr ] ) ?
                explode($separator, $this->_input[$attr]) :
                array();
    }
    /**
     * @param string $attr
     * @return boolean
     */
    public final function has( $attr ){
        return isset( $this->_input[$attr] );
    }
}





