<?php namespace CODERS;

//use \CodersThemes;

/**
 * Plantilla para extensiones
 * 
 * @author informatica1
 */
final class ExtensionManager {
    /**
     * 
     */
    const COMPONENT_LIST_OPTION = 'coders_widget_pack_components';
    /**
     *
     * @var \CODERS\ExtensionManager
     */
    private static $_INSTANCE = NULL;
    /**
     * @var \CODERS\Extension[]
     */
    private $_extensions = array();
    /**
     * @var boolean
     */
    private $_updated = FALSE;
    /**
     * 
     */
    private final function __construct(  ) {
        
        return $this->preload();
    }
    /**
     * 
     * @param string $extension
     * @param int $status
     * @return \CODERS\ExtensionManager
     */
    public function setStatus( $extension, $status = Extension::STATUS_ENABLED ){

        if( array_key_exists($extension, $this->_extensions) ){
            
            $this->_extensions[$extension]->setStatus($status);
            
            $this->_updated = TRUE;
        }
        
        return $this;
    }
    /**
     * @param string $extension
     * @return \CODERS\ExtensionManager
     */
    public final function toggle( $extension ){
        
        if(array_key_exists($extension, $this->_extensions)){
            
            $current = $this->_extensions[$extension]->get('status', Extension::STATUS_DISABLED);
            
            $status = $current > Extension::STATUS_DISABLED ?
                    Extension::STATUS_ENABLED  :
                    Extension::STATUS_DISABLED ;
            
            $this->_extensions[$extension]->setStatus( $status );
            
            $this->_updated = TRUE;
        }
        
        return $this;
    }
    /**
     * Resetea (desactiva) todos los widgets activados
     * @return \CODERS\ExtensionManageer
     */
    public final function reset(){

        foreach( $this->_extensions as $instance ){

            $instance->setStatus(Extension::STATUS_DISABLED);
        }

        $this->_updated = TRUE;

        return $this;
    }
    /**
     * Guarda las extensiones del tema
     * @return \CODERS\ExtensionManageer
     */
    public final function save(){

        if( $this->_updated){

            //exporta los widgets activos
            $export = json_encode($this->listExtensionSetup());
            
            $encode = base64_encode($export);

            //guarda la lista serializada en options
            update_option( self::COMPONENT_LIST_OPTION , $encode );

            $this->_updated = FALSE;
        }
        
        return $this;
    }
    /**
     * @return boolean
     */
    public final function updated(){
        return $this->_updated;
    }
    /**
     * @return array
     */
    private final function listExtensionSetup(){
        $output = array();
        foreach( $this->_extensions as $extension => $instance ){
            $output[ $extension ] = $instance->getSettings();
        }
        return $output;
    }
    /**
     * Lista las extensionses para mostrar en el adminmistrador
     * @return array
     */
    public final function listExtensionData(){
        
        $output = array();
        
        foreach( $this->_extensions as $extension => $instance ){
            $output[ $extension ] = array(
                'name' => $instance->getName(),
                'description' => $instance->getDescription(),
                'icon' => $instance->getUI(),
                'status' => $instance->getActive(),
            );
        }
        
        return $output;
    }
    /**
     * @param boolean $activeOnly
     * @return array
     */
    public final function extensions( $activeOnly = true ){
        
        if( $activeOnly ){
            $output = array();
            foreach( $this->_extensions as $extension=>$instance){
                if( $instance->status === Extension::STATUS_ENABLED ){
                    $output[] = $extension;
                }
            }
            return $output;
        }
        
        return $this->_extensions;
    }
    /**
     * @return int
     */
    public final function countAll(){
        return count( $this->extensions( FALSE ) );
    }
    /**
     * @return int
     */
    public final function countActive(){
        return count( $this->extensions());
    }
    /**
     * Comprueba si una extensión está registrada en el cargador
     * @param string $extension
     * @param boolean $activeOnly Define si la extensión está habilitada
     * @return boolean
     */
    public final function checkExtension( $extension , $activeOnly = FALSE ){

        return array_key_exists($extension, $this->_extensions)
                //comprueba si está cargada, o no see requiere como tal
                && ( $this->_extensions[ $extension ]->active || !$activeOnly );
        
    }
    /**
     * Importa las extensiones existentes en el directorio
     * @param boolean $initialize
     * @return \CODERS\ExtensionManager
     */
    private final function preload( ){
        
        $base_path = self::path();
        
        $ext_db = $this->importSetup();
        
        if ( file_exists($base_path) && $handle = opendir( $base_path ) ) {
            while (false !== ($extension = readdir($handle))){
                switch( $extension ){
                    case '.':
                    case '..':
                        break;
                    default:
                        $ext_path = self::path($extension);
                        if(file_exists($ext_path)){
                            
                            $status = array_key_exists($extension, $ext_db) ?
                                array_key_exists('status', $ext_db[$extension]) ?
                                    $ext_db[$extension]['status'] : FALSE : FALSE;

                            //registro de extensiones
                            $instance = $this->import( $extension , $status );

                            if( !is_null($instance)){
                                
                                $this->_extensions[ $extension ] = $instance;
                            }
                        }
                        break;
                }
            }
        }
        
        return $this;
    }
    /**
     * @return \CODERS\ExtensionManager
     */
    private static final function importSetup( ){

        $import = get_option( self::COMPONENT_LIST_OPTION , '' );
        
        if(strlen($import)){
            
            $extract = base64_decode($import);

            $active_list = json_decode($extract,TRUE);
            
            return is_array($active_list) ?  $active_list : array();
        }
        
        return array();
    }
    /**
     * Carga y registra la extensión
     * @param string $extension
     * @param boolean $register
     * @return \CODERS\Extension
     */
    private final function import( $extension , $register = FALSE ){
        
        $instance = Extension::import($extension);
        
        if( !is_null($instance)){
            
            if( $register ){
                
                $instance->setStatus(Extension::STATUS_ENABLED)->init();
            }
            
            return $instance;
        }
        
        return NULL;
    }
    
    /**
     * @param string $extension
     * @return string
     */
    public static final function extensionClass( $extension ){

        $classParts = explode('-', $extension);

        $class = '';

        foreach( $classParts as $chunk ){

            $class .= strtoupper( substr( $chunk , 0 , 1 ) )
                    . strtolower(substr($chunk, 1, strlen($chunk)-1));
        }
        
        return sprintf('CODERS\Extensions\%sExtension', $class );
    }
    /**
     * @param string $extension
     * @return string
     */
    public static final function path( $extension = null ){
        
        $base_path = sprintf('%s/extensions/', \CodersThemes::pluginPath());
        
        return !is_null($extension) ?
            sprintf('%s%s/%s.extension.php',$base_path,$extension,$extension) :
            $base_path;
    }
    /**
     * Comprueba que existe un widget
     * @param string $extension
     * @return boolean
     */
    public static final function checkPath( $extension ){
        
        return file_exists( self::path( $extension ) );
    }
    /**
     * @return \CODERS\ExtensionManager
     */
    public static final function instance(){
        
        if(is_null(self::$_INSTANCE)){
            
            self::$_INSTANCE = new ExtensionManager();
        }
        
        return self::$_INSTANCE;
    }
}

