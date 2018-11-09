( function(){
    /**
     * @type Object
     */
    var _controller = this;
    /**
     * @type Object
     */
    var _component = {};
    /**
     * @type String
     */
    var _selector = 'coders_post_slideshow_widget';
    /**
     * @returns {String}
     */
    this.getSelector = function(){ return '.' + _selector; };
    /**
     * @param {String} ID
     * @param {Number} speed
     * @param {String} custom
     * @returns {post-slideshowL#1}
     */
    this.slideShow = function( ID , speed , custom ){
        /**
         * 
         * @type post-slideshowL#1
         */
        var _base = this;
        
        /**
         * @type Object
         */
        var _settings = {
            'id' : ID,
            'speed' : typeof speed !== 'undefined' ? parseFloat( speed ) : 5.8,
            'transition' : 0,
            'custom' : typeof custom !== 'undefined' ? custom : null,
            'slides' : [],
            'index' : 0
        };
        /**
         * @returns {String}
         */
        this.toString = function(){
            return _base.getSelector();
        };
        /**
         * @returns {String}
         */
        this.getSelector = function(){
            return '#' + _settings.id + '.' + _selector;
        };
        /**
         * @returns {jQuery}
         */
        this.attachPaginator = function( ){

            var paginator = jQuery( '<ul class="paginator"></ul>');

            for( var i = 0 ; i < _settings.slides.length ; i++ ){

                var item = jQuery( '<li class="page" data-id="' + (i+1) + '"></li>');

                jQuery( item ).on( 'click' , function(e){
                    var page_id = parseInt( jQuery( this ).attr('data-id') );
                    _base.activate( page_id - 1 );
                    e.preventDefault();
                    return false;
                });
                
                jQuery( paginator ).append( item );
            }

            return paginator;
        };
        /**
         * @returns {jQuery}
         */
        this.attachNavigator = function( ){

            var prev = jQuery( '<li class="nav prev"></li>' );

            var next = jQuery( '<li class="nav next"></li>' );

            jQuery( prev ).on('click',function(e){
                _base.prev();
                e.preventDefault();
                return false;
            });

            jQuery( next ).on('click',function(e){
                _base.next();
                e.preventDefault();
                return false;
            });

            return jQuery( '<ul class="navigator"></ul>').append(prev).append(next);
        };
        /**
         * @returns {post-slideshowL#1}
         */
        this.importSlides = function(){
            
            var selector = _base.getSelector();
            
            var slider = jQuery( selector + ' .slideshow-container' );

            if( typeof slider !== 'undefined' ){
                //buscar slides interiores
                jQuery( slider ).find( '.slide' ).each( function(){
                    _base.addSlide( jQuery( this ).attr('data-id') );
                });

                if( _base.countSlides() > 1 ){
                    var container = jQuery( selector );
                    //registrar eventos del paginador y navegador
                    if( jQuery( slider ).hasClass('has-paginator')){
                        jQuery( container ).append( _base.attachPaginator() );
                    }
                    if( jQuery( slider ).hasClass('has-navigator')){
                        jQuery( container ).append( _base.attachNavigator( ) );
                    }
                    //registrar eventos de cambio de slide (custom)
                    if( _base.hasCustom( ) ){
                        //registrar evento personalizado ...
                    }
                }
                //console.log( JSON.stringify( _settings.slides ) );
            }
            
            return _base;
        };
        /**
         * @param {String} id
         * @returns {post-slideshowL#1}
         */
        this.addSlide = function( id ){

            _settings.slides.push( parseInt( id ) );
            
            return _base;
        };
        /**
         * @param {Number} idx
         * @returns {Number}
         */
        this.getSlide = function( idx ){
            return idx >= 0 && idx < _settings.slides.length ? _settings.slides[ idx ] : 0;
        };
        /**
         * @returns {Array}
         */
        this.getSlides = function(){ return _settings.slides; };
        /**
         * @returns {Boolean}
         */
        this.hasSlides = function(){
            return _settings.slides.length > 0;
        };
        /**
         * @returns {Number}
         */
        this.countSlides = function(){
            return _settings.slides.length;
        };
        /**
         * @returns {Boolean}
         */
        this.hasCustom = function( ){
            return _settings.custom !== null;
        };
        /**
         * @param {Number} id
         * @returns {post-slideshowL#1}
         */
        this.activate = function( id ){

            _settings.index = id;
            
            var slider = jQuery( _base.getSelector() );
            
            if( typeof slider !== 'undefined' ){
                
                var current_slide = '.slideshow-container .slide[data-id="'
                            + _base.getSlide( _settings.index )
                            + '"]';
                
                var current_page = '.paginator .page[data-id="'
                            + ( _settings.index + 1 )
                            + '"]';

                console.log( current_slide );
                console.log( current_page );
                
                jQuery( slider )
                        .find( '.slideshow-container .slide' )
                        .removeClass('current');
                jQuery( slider )
                        .find( '.paginator .page' )
                        .removeClass( 'current' );
                jQuery( slider ).find( current_slide ).addClass('current');
                jQuery( slider ).find( current_page ).addClass('current');
            }
            
            return _base;
        };
        /**
         * @returns {post-slideshowL#1}
         */
        this.next = function(){
            
            return _base.activate( _settings.index < _settings.slides.length - 1 ? _settings.index + 1 : 0 );
        };
        /**
         * @returns {post-slideshowL#1}
         */
        this.prev = function(){
            
            return _base.activate( _settings.index > 0 ? _settings.index - 1 : _settings.slides.length - 1 );
        };
        /**
         * @returns {Number}
         */
        this.index = function(){ return _settings.index; };
        /**
         * @returns {post-slideshowL#1}
         */
        this.start = function(){
            
            _base.activate( 0 );
            
            if( _settings.speed > 0 ){
                
                _settings.transition = setInterval(function(){

                    _base.next( /*siguiente slide*/ );

                }, _settings.speed * 1000 );
            }

            return _base;
        };
        /**
         * 
         * @returns {post-slideshowL#1}
         */
        this.stop = function(){
            
            if( _settings.transition > 0 ){
                
                clearInterval( _settings.transition );
                
                _settings.transition = 0;
            }
            
            return _base;
        };
        
        //importa los slides del contenedo, inicializa y empieza la transición
        return _base.importSlides().start();
    };
    
    //INICIALIZAR
    jQuery( document ).ready( function(){
        //buscar todos los slideshows (varios widdgets)
        jQuery( _controller.getSelector( ) ).each( function(){

            var id = jQuery(this).attr('id');
            
            _component[id] = new _controller.slideShow( id );
        });
    });
})( /*Autoiniciador*/);
