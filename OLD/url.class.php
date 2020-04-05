<?php namespace CODERS;
/**
 * Gestor de URLs
 */
final class URL{
    
    private $_url;
    
    private $_params = array();
    /**
     * @param string $url
     * @param array $args
     */
    private final function __construct( $url , array $args = array()) {
        
        $this->_url = trim( $url );
        
        foreach( $args as $var => $val ){
            $this->_params[ $var] = $val;
        }
    }
    /**
     * @param string $input
     * @return array
     */
    private static final function unserialize( $input ){
        
        if( self::match($input)){
            //eliminar URL del input
            $parts = explode('?', $input);
            if( count( $parts) > 1 ){
                $input = $parts[1];
            }
        }
        
        $output = array();
        foreach (explode('&', $input ) as $varDef) {
            $var = explode('=', $varDef);
            if (count($var) > 1) {
                $output[$var[0]] = $var[1];
            }
        }
        return $output;
    }
    /**
     * @return string
     */
    private final function serialize(){
        $output = array();
        foreach( $this->_params as $var => $val ){
            $output[ ] = sprintf('%s="%s"',$var,$val);
        }
        return implode( ' ' , $output );
    }
    /**
     * @return String
     */
    public final function __toString() {
        return $this->url();
    }
    /**
     * @param string $name
     * @return mixed
     */
    public final function __get($name) {
        return $this->get($name,'');
    }
    /**
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    public final function get( $var, $default = null ){
        return isset($this->_params[$var]) ? $this->_params[$var] : $default;
    }
    /**
     * @param string $var
     * @return int
     */
    public final function getInt( $var ){
        return intval($this->get($var,0));
    }
    /**
     * @param string $var
     * @param string $sep
     * @return array
     */
    public final function getArray( $var ,$sep = ',' ){
        return explode( $sep, $this->get($var,''));
    }
    /**
     * @return string
     */
    public final function getProtocol() {
        //return explode( '://' , $this->_url )[ 0 ];
        return self::protocol($this->_url);
    }
    /**
     * @return string
     */
    public final function getServer(){
        //return substr($this->_url, 0, strpos($this->_url, '/',8));
        return self::server($this->_url);
    }
    /**
     * @return array
     */
    public final function getArgs(){
        return $this->_params;
    }
    /**
     * @return string
     */
    public final function base(){
        return $this->_url;
    }
    /**
     * @return string
     */
    public final function url(){
        return count($this->_params) ?
                sprintf( '%s?%s' , $this->base() , implode('&', $this->serialize())) :
                $this->base();
    }
    /**
     * @param string $input
     * @return \CODERS\URL
     */
    public static final function importUrl( $input ){
        
        if( self::match($input)){
            $parts = explode('?', $input);

            return new URL($parts[0] , count( $parts ) > 1 ?
                self::unserialize( $parts[1] ) :
                array());
        }
        
        return null;
    }
    /**
     * @return \CODERS\URL
     */
    public static final function publicUrl( $input = array() ){
        
        if( !is_array($input)){
            $input = self::unserialize($input);
        }
        
        return new URL(get_site_url(),$input);
    }
    /**
     * @return \CODERS\URL
     */
    public static final function adminUrl( $input = array() ){
        
        if( !is_array($input)){
            $input = self::unserialize($input);
        }

        return new URL(get_admin_url(),$input);
    }
    /**
     * 
     * @param string $input
     * @return string
     */
    public static final function extract( $input ){
        if( self::match($input)){
            
            $parts = explode('&',$input);
            
            $dash = strpos($input, '/', 8);
            
            if( $dash !== false ){
                return substr($parts[0], 0, $dash );
            }
            
            return $parts[ 0 ];
        }
        return '';
    }
    /**
     * domain.com
     * @param string $url
     * @return string
     */
    public static final function server( $url ){
        return substr($url, 0, strpos($url, '/',8));
    }
    /**
     * http:// or https://
     * @param string $input
     * @return string
     */
    public static final function protocol( $input ){
        return explode( '://' , $input )[ 0 ] . '://';
    }
    /**
     * @param string $input
     * @return boolean
     */
    public static final function match( $input ){
        return preg_match('/^(http|https):\/\//',$input);
    }
}



