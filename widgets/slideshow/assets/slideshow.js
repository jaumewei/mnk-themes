( function(){
    /**
     * @type Object
     */
    var _controller = this;
    /**
     * @type Object
     */
    var _settings = {
        speed: 5800
    };
    /**
     * @returns {jQuery}
     */
    this.attachPaginator = function(){
        
        
        return jQuery( '<ul class="paginator"></ul>');
    };
    /**
     * @returns {jQuery}
     */
    this.attachNavigator = function(){
        
        
        return jQuery( '<ul class="navigator"></ul>');
    };
    
    
    //INICIALIZAR
    jQuery( document ).ready( function(){
        //buscar todos los slideshows (varios widdgets)
        jQuery( '.coders_slideshow_widget' ).each( function(){
            //capturar contenedor del slideshow
            var slideContainer = this;
            var slides = [];
            var custom = jQuery( this ).attr('data-handler');
            //buscar slides interiores
            jQuery( this ).find( '.slide' ).each( function(){

                //capturar slide y registrar eventos

            });
            
            //registrar eventos del paginador y navegador
            if( jQuery( slideContainer ).hasClass('paginator')){
                jQuery( slideContainer ).append( _controller.attachPaginator( ) );
            }
            if( jQuery( slideContainer ).hasClass('navigator')){
                jQuery( slideContainer ).append( _controller.attachNavigator( ) );
            }
            //registrar eventos de cambio de slide (custom)
            if( typeof custom !== 'undefined' && custom !== null ){
                //registrar evento personalizado ...
            }
        });
    });
})( /*Autoiniciador*/);


/**
 * Controlador de slides
 * @param {String} id
 * @param {Int} seconds Dejar a 0 si se desea desactivar la función automática del slideshow
 * @param {Function} customCallback
 * @param {Int} minWidth 
 * @returns {slideShowController}
 */
function sliderBoxController(id, seconds, customCallback , minWidth ) {
    /**
     * @type sliderBoxController
     */
    var _self = this;
    /**
     * @type Object Parámetros del controlador
     */
    var _settings = {
        slideBoxID: id,
        slideBoxName: '.post-slideshow#' + id,
        slideBoxEvent: 'slide' + id + 'Acivate',
        attachPaginator: true,
        attachNavigator: true,
        screenWidth: typeof minWidth === 'number' ? minWidth : 960,
        timeOut: 5000,
        enableNav: true,
        enablePag: true,
        intervalId: 0,
        //counter: 0,
        current: 0,
        themes: ['clear','dark'],
        items: []
    };
    /**
     * @param {String} setting
     * @returns {Mixed}
     */
    this.get = function( setting ){

        return typeof _settings[setting] !== 'undefined' ? _settings[setting] : '';
    };
    /**
     * @param {String} setting
     * @param {Mixed} value
     * @returns {sliderBoxController}
     */
    this.set = function( setting , value ){
        if( typeof _settings[setting] !== 'undefined' ){
            _settings[setting] = value;
        }
        return this;
    };
    /**
     * Genera los controles de navegación y los inicializa
     * @param {boolean} addPaginator
     * @param {boolean} addNavigator
     * @returns {jQuery}
     */
    this.attachNavigation = function( addPaginator , addNavigator ){
        
        var navControl = jQuery('<div class="navigation"></div>');
        
        if( typeof addNavigator === 'boolean' && addNavigator ){
            
            var prev = jQuery('<span class="nav prev"></span>');
            var next = jQuery('<span class="nav next"></span>');
            
            jQuery( prev ).on('click',function( e ){
                e.preventDefault();
                _self.prev( );
                //console.log('Prev clicked');
                return true;
            });
            jQuery( next ).on('click',function( e ){
                e.preventDefault();
                _self.next();
                //console.log('Next clicked');
                return true;
            });
            jQuery( navControl ).append(prev).append(next);
        }

        if( typeof addPaginator === 'boolean' && addPaginator ){

            var paginator = jQuery('<ul class="item-list"></ul>');
            
            for( var i = 0 ; i < _settings.items.length ; i++ ){

                jQuery( paginator ).append('<li><span class="item nav" data-id="' + i + '"></span></li>');
            }
            
            jQuery( paginator ).find('.item').on('click',function(e){

                e.preventDefault();

                var id = parseInt( jQuery( this ).attr( 'data-id' ) );

                _self.activate( id );

                return true;
            });
            
            jQuery( navControl ).append( paginator );
        }

        return navControl;
    };
    /**
     * Inicializar control
     * @param {Int} timeOut 
     * @param {Function} callBack 
     * @returns {sliderBoxController} 
     */
    this.initialize = function( timeOut , callBack ){
        
        //captura de parámetros por defecto
        if (typeof timeOut === 'numeric') {
            _settings.timeOut = timeOut * 1000;
        }
        if (typeof callBack === 'function') {
            if (typeof callBack === 'function') {
                jQuery(_settings.slideBoxName + ' .slideshow-container .slide').on(
                        _settings.slideBoxEvent,
                        callBack);
            }
        }

        _settings.items = [];
        //_settings.counter = jQuery(_settings.slideBoxName + ' > .slideshow-container .slide').length;
        jQuery(_settings.slideBoxName + ' > .slideshow-container .slide').each( function(){
            
            var slide_id = parseInt( jQuery( this ).attr('data-id') );
            
            _settings.items.push( slide_id );
        });
        
        //console.log( JSON.stringify( _settings.items ) );

        //console.log(_settings.counter);
        
        //innicializa al animación
        if( _settings.timeOut > 0 ){
            if( jQuery( window ).width() < _settings.screenWidth ){
                //console.log('Slideshow desactivado, pasado a modo responsive');
            }
            else{
                //agrega el navegador (gestionado por parámetros)
                jQuery(_settings.slideBoxName).append(this.attachNavigation(
                        _settings.attachPaginator,
                        _settings.attachNavigator));
            }
        }
        
        _self.createTransition();
        
        //control de la animación automática en hover
        jQuery( _settings.slideBoxName ).on('hover',function( e ){
            e.preventDefault();
            if( _settings.intervalId > 0 ){
                //_self.pauseTransition();
            }
            return true;
        });
        //console.log(JSON.stringify(_settings.items));
        //inicializa el slide
        return _self.activate( );
    };
    /**
     * @returns {sliderBoxController}
     */
    this.createTransition = function(){
        if( _settings.items.length > 1 ){
            //establecer intervalo de actualización automática
            _settings.intervalId = setInterval( function ( ) {

                if ( _settings.current < _settings.items.length - 1 ) {
                    _self.activate(++_settings.current);
                } else {
                    _self.activate( _settings.current = 0 );
                }
            }, _settings.timeOut);
        }
        return this;
    };
    this.pauseTransition = function(){
        return this;
    };
    this.removeTransition = function(){
        return this;
    };
    /**
     * @param {Int} slide_id Numero de slide a mostrar
     * @returns {sliderBoxController}
     */
    this.activate = function( slide_id ) {
        //console.log( slide_id );
        if ( typeof slide_id === 'undefined' ) { slide_id = 0; }

        if ( slide_id > _settings.items.length - 1) { slide_id = _settings.items.length - 1; }

        var oldSlide = jQuery(_settings.slideBoxName + ' .slideshow-container .slide.current').removeClass('current');
        //console.log( 'Anexando ' + _settings.slideBoxName + ' .slideshow-container .slide[data-id="' + slide_id + '"]' );
        var newSlide = jQuery(_settings.slideBoxName + ' .slideshow-container .slide[data-id="'
                + _settings.items[ slide_id ]
                + '"]').addClass('current');

        newSlide.trigger(_settings.slideBoxEvent, {
            /**
             * @type {Object} Array de clases de la diapositiva actual
             */
            'class': jQuery( newSlide ).attr('class').split(' '),
            /**
             * @type {Number} ID de la diapositiva actual
             */
            'slide-id': parseInt(jQuery(newSlide).attr('data-id')),
            /**
             * @type {Number} ID de la diapositiva anterior
             */
            'old-slide-id': oldSlide.length > 0 ? parseInt(jQuery(oldSlide).attr('data-id')) : 0,
            /**
             * Permite localizar una clase dentro de los parámetros de retorno
             * @param {String} cls
             * @returns {Boolean}
             */
            'hasClass': function( cls ){
                for( var i = 0 ; i < this.class.length ; i++ ){
                    if( this.class[ i ] === cls ){
                        return true;
                    }
                }
                return false;
            },
            /**
             * Cambia el tema del control de slides
             * @param {String} selected 
             * @returns {Object}
             */
            'setTheme': function( selected ){
                
                var list = _settings.themes;
                
                for( var cls = 0 ; cls < list.length ; cls++ ){
                    if( list[ cls ] === selected ){
                        if( !jQuery( _settings.slideBoxName ).hasClass( list[cls] ) ){
                            jQuery( _settings.slideBoxName ).addClass( list[cls] );
                        }
                    }
                    else if( jQuery( _settings.slideBoxName ).hasClass( list[cls] ) ){
                        jQuery( _settings.slideBoxName ).removeClass( list[cls] );
                    }
                }
                return this;
            }
        });

        return this;
    };
    /**
     * Numero de slide actual
     * @returns {Int}
     */
    this.current = function () {
        
        return _settings.current >= 0 && _settings.current < _settings.items.length ? 
                _settings.items[ _settings.current ] :
                _settings.items[ 0 ];
        //return parseInt(jQuery(_settings.slideBoxName + ' .slideshow-container .slide.current').attr('data-id'));
    };
    /**
     * Anterior slideshow de la lista
     * @returns {Int}
     */
    this.prev = function(){

        if( _settings.current > 0 ){
            
            return _self.activate( --_settings.current );
        }

        return _self.activate( _settings.current = _settings.items.length - 1);
    };
    /**
     * Siguiente slideshow de la lista
     * @returns {Int}
     */
    this.next = function(){
        
        if( _settings.current < _settings.items.length  - 1 ){
            
            return _self.activate( ++_settings.current );
        }

        return _self.activate( _settings.current = 0 );
    };
    //inicialización
    return _self.initialize( seconds , customCallback );
}
/**
 * Controlador de lista de items
 * @param {Int} id
 * @param {Function} callBack
 * @param {Array} items
 * @param {Boolean} isFormInput
 * @returns {listItemController}
 */
function listItemController(id, callBack, items , isFormInput , attributes ) {
    /**
     * @type listItemController
     */
    var _self = this;
    /**
     * @type Function
     */
    var _callBack = typeof callBack === 'function' ? callBack : null;
    /**
     * Parámetros
     * @type Object
     */
    var _settings = {
        'id': id,
        'name': '.list-item-container#' + id.toLowerCase(),
        'formInput': typeof isFormInput !== 'undefined' && isFormInput ? true : false,
        'items': typeof items !== 'undefined' ? items : []
        //'class': typeof attributes !== 'undefined' && typeof attributes.class !== 'undefined' ? attributes.class : ''
    };
    /**
     * @returns {jQuery} 
     */
    this.component = function(){ return jQuery( _settings.name ); };
    /**
     * Retorna el elemento generado para la lista
     * @param {Object} data Datos para generar el contenido: item_id, item_label, media_url (opcional)
     * @param {Boolean} isFormData Determina si se incluye un control de formulario hidden (opcional)
     * @type jQuery
     */
    this.newItem = function (data, isFormData) {

        if (typeof data.item_id === 'undefined')
            return null;
        if (typeof data.item_label === 'undefined')
            return null;

        var item = jQuery('<li class="list-item"></li>');

        if (typeof data.media_url !== 'undefined') {
            jQuery(item).append('<img src="'
                    + data.media_url + '" title="'
                    + data.item_label + '" alt="'
                    + data.item_label + '" />');
        } else {
            jQuery(item).append('<span>' + data.item_label + '</span>');
        }

        jQuery(item).append('<a class="list-item-button remove-item">Drop</a>');

        if (typeof isFormData === 'boolean' && isFormData) {
            jQuery(item).append('<input type="hidden" name="items[]" value="' + data.item_id + '" />');
        }

        jQuery(item).find('.remove-item').on('click', function (e) {
            e.preventDefault();
            jQuery(item).remove();
            return true;
        });

        return item;
    };

    //inicializar
    //console.log( jQuery( _settings.name + ' .list-item-button.add-item'));
    jQuery( _settings.name + ' .list-item-button.add-item').on('click', function (e) {
        
        e.preventDefault();
        
        var container = jQuery(this).parent(_settings.name);

        if (_callBack !== null) {

            var itemData = _callBack( );

            if (itemData !== null) {

                jQuery(container).append(_self.newItem(itemData, true));
            }
        } else {
            jQuery( this ).prop('disabled',true);
        }

        return true;
    });

    return  _self;
}


