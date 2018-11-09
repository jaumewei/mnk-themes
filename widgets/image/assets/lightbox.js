/*lightbox-sript*/
( function(){

    var _controller = this;
    /**
     * @type Object
     */
    var _settings = {
        items: [],  //lista de valores
        index: 0 ,  //indice de items
        transition: 0 , //pasa slide automáticamente, si 0, no activado
        elapsed: 3500 ,
        effectTimer: 180
    };
    /**
     * @type {Object}
     */
    var _component = {
        container : null,
        media : null ,
        switching: false
    };
    /**
     * Alternando la caja del lightbox
     * @param {Boolean} status
     * @returns {lightboxL#2}
     */
    this.toggle = function( status ){
        if( typeof status !== 'undefined' && status ){
            if( !jQuery( _component.container ).hasClass( 'active' ) ){
                jQuery( _component.container ).addClass( 'active' );
            }
        }
        else if( jQuery( _component.container ).hasClass( 'active' ) ){
            jQuery( _component.container ).removeClass( 'active' );
        }
        return this;
    };
    /**
     * 
     * @param {Number} ID
     * @returns {lightboxL#2}
     */
    this.activate = function( ID ){

        if( typeof ID === 'boolean' && !ID ){
            //error al parsear
            return this;
        }
        else if( typeof ID !== 'undefined' ){
            _settings.index = ID;
        }

        var current = _controller.current();

        if( current !== null ){
            //marcar como activo
            _controller.toggle( true ).switchImage(
                    current.id,
                    current.url,
                    current.title,
                    current.description,
                    current.cls ).setPage( _settings.index );
        }
      
        return this;
    };
    /**
     * @param {Number} item_id
     * @returns {Number|Boolean}
     */
    this.getByItemId = function( item_id ){
        for( var idx = 0 ; idx < _settings.items.length ; idx ++ ){
            if( _settings.items[ idx ].id === parseInt( item_id )  ){
                return idx;
            }
        }
        return false;
    };
    /**
     * Item actual de la galería
     * @returns {Object}
     */
    this.current = function(){

        return _settings.index >= 0 && _settings.index < _settings.items.length ?
                _settings.items[ _settings.index ] :
                null;
    };
    /**
     * Siguiente item en la galería
     * @returns {lightboxL#2}
     */
    this.next = function(){

        _controller.activate( _settings.index < _settings.items.length - 1 ?
                _settings.index + 1 :
                        0 );
        
        return this;
    };
    /**
     * Item previo en la galería
     * @returns {lightboxL#2}
     */
    this.prev = function(){
        
        _controller.activate( _settings.index > 0 ?
                _settings.index - 1 :
                        _settings.items.length -1  );

        return this;
    };
    /**
     * @param {String} id
     * @param {URL} url
     * @param {String} title  Título de la imagen
     * @param {String} description Descripción o ALT del a imagen
     * @param {String} cls Clases especiales (default|landscape|portrait)
     * @returns {Number}
     */
    this.addItem = function( id , url , title , description , cls ){
        
        var item = {
            'id': parseInt( id ),
            'url': url,
            'title': typeof title !== 'undefined' ? title : '',
            'description': typeof description !== 'undefined' ? description : '',
            'cls' : typeof cls !== 'undefined' ? cls : ''
        };

        _settings.items.push( item );

        return _settings.items.length-1;
    };
    /**
     * Cierra la caja (si está abierta)
     * @returns {lightboxL#2}
     */
    this.close = function(){
        
        return _controller.toggle( );
    };
    /**
     * Reinicia la animación
     * @param {Number} elapsed
     * @returns {lightboxL#2}
     */
    this.resetAnimation = function( elapsed ){
        
        if( _settings.transition ){
            _controller.stopAnimation().startAnimation(
                    typeof elapsed !== 'undefined' ?
                            elapsed : _settings.elapsed);
        }
        
        return this;
    };
    /**
     * Inicia la transición de imagenes en el lightbox
     * @param {Number} elapsed
     * @returns {lightboxL#2}
     */
    this.startAnimation = function( elapsed ){
        
        _settings.transition = setInterval( function(){
            
            //pasa imagenes
            _controller.next();
            
        },elapsed);
        
        return this;
    };
    /**
     * Para la animación acutal si está en curso
     * @returns {lightboxL#2}
     */
    this.stopAnimation = function(){
        
        if( _settings.transition > 0 ){
            clearInterval( _settings.transition );
            _settings.transition = 0;
        }
        
        return this;
    };
    /**
     * Cambia la página en el paginador
     * @param {Number} id
     * @returns {lightboxL#2}
     */
    this.setPage = function( id ){
        
        jQuery( _component.container ).find('.lightbox-pag .page').each( function(){
            
            var item_id = parseInt( jQuery( this ).attr('data-id') ) - 1;
            
            if( item_id === id ){
                if( !jQuery( this ).hasClass('active')){
                    jQuery( this ).addClass( 'active' );
                }
            }
            else{
                if( jQuery( this ).hasClass('active')){
                    jQuery( this ).removeClass( 'active' );
                }
            }
        });
        return this;
    };
    /**
     * @param {String} id
     * @param {URL} url
     * @param {String} title
     * @param {String} alt
     * @param {String} display
     * @returns {lightboxL#2}
     */
    this.switchImage = function( id , url , title , alt , display ){
        if( !_component.switching ){
            
        }
        if( _component.media !== null ){
            jQuery( _component.media ).fadeOut( _settings.effectTimer , function(){
                //transición de imagenes
                jQuery( this )
                        .attr('data-id',id)
                        .attr('src',url)
                        .attr('title',title)
                        .attr('alt',alt)
                        .attr( 'class' , display )
                        //.next()
                        .delay( _settings.effectTimer )
                        .fadeIn( _settings.effectTimer * 2 );
            });
        }
        else{
            //inicializando la imagen
            _component.media = _controller.attachImage( id, url, title, alt, display);
            //agregar al contenedor (interior)
            jQuery( _component.container )
                    .find( '.lightbox-wrapper' )
                    .prepend( _component.media );
        }
        return this;
    };
    /**
     * @param {Number} id
     * @param {URL} url
     * @param {String} title
     * @param {String} alt
     * @param {String} display
     * @returns {jQuery}
     */
    this.attachImage = function( id , url , title , alt , display ){

        if (typeof id !== 'undefined' && typeof url !== 'undefined') {
            if (typeof display === 'undefined') {
                display = 'default';
            }

            if (typeof title === 'undefined') {
                title = 'img-id-' + id;
            }

            if (typeof alt === 'undefined') {
                alt = title;
            }

            var media = jQuery('<img id="lightbox-media" src="'
                    + url + '" data-id="'
                    + id + '" title="'
                    + title + '" alt="'
                    + alt + '" class="'
                    + display + '" />');
            
            jQuery( media ).on( 'click' , function( e ){
                _controller.resetAnimation().next();
                e.stopPropagation();
                return  false;
            } );
            
            return media;
        }

        return null;
    };
    /**
     * 
     * @param {Array} items
     * @returns {jQuery}
     */
    this.attachPaginator = function( ){
        
        var paginator = jQuery( '<ul class="lightbox-pag"></ul>' );
        
        //agregar aqui los items
        for( var idx = 0 ; idx < _settings.items.length ; idx++ ){
            
            var page = jQuery( '<li class="page" data-id="' + (idx+1) + '"></li>');
            
            jQuery( page ).on( 'click' , function(e){
                var item_id = parseInt(jQuery( this ).attr( 'data-id' ))-1;
                console.log( item_id );
                _controller.activate( item_id );
                e.preventDefault();
                return false;
            });
            
            jQuery( paginator ).append( page );
        }
        
        return paginator;
    };
    /**
     * 
     * @param {Array} items
     * @returns {jQuery}
     */
    this.attachNavigator = function( ){
        
        var prev = jQuery( '<li class="nav prev"></li>' );
        var close_top = jQuery( '<li class="nav close top"></li>' );
        var close_down = jQuery( '<li class="nav close bottom"></li>' );
        var next = jQuery( '<li class="nav next"></li>' );
        var anim = jQuery( '<li class="nav anim"></li>' );
        //anterior
        //cerrar galería
        jQuery( close_top ).on( 'click' , function(e ){
            _controller.stopAnimation().close();
            e.preventDefault();
            return false;
        });
        jQuery( close_down ).on( 'click' , function(e ){
            _controller.stopAnimation().close();
            e.preventDefault();
            return false;
        });
        jQuery( prev ).on( 'click' , function(e ){
            //anterior
            _controller.stopAnimation().prev();
            e.preventDefault();
            return false;
        });
        //botón de siguiente
        jQuery( next ).on( 'click' , function(e ){
            //siguiente
            _controller.resetAnimation( ).next();
            e.preventDefault();
            return false;
        });
        //preparar botón diapositivas automáticas
        jQuery( anim ).on( 'click' , function(e ){
            if( _settings.transition > 0 ){
                _controller.stopAnimation();
                jQuery( this ).removeClass('paused').addClass('anim');
            }
            else{
                _controller.startAnimation( _settings.elapsed );
                jQuery( this ).removeClass('anim').addClass('paused');
            }
            e.preventDefault();
            return false;
        });
        
        return jQuery('<ul class="lightbox-nav"></ul>"')
                .append( prev )
                .append( next )
                .append( close_top )
                .append( close_down )
                .append( anim );
    };
    /**
     * Adjunta el contenedor del lightbox
     * @param {Boolean} attachNav
     * @returns {jQuery}
     */
    this.attachLightBox = function( attachNav , attachPag ){
        
        var lbContainer = jQuery( '<div id="coders-lightbox-image-widget"></div>' );
        
        var lbWrapper = jQuery( '<div class="lightbox-wrapper"><!-- IMAGEN --></div>' );
        
        if( typeof attachNav !== 'undefined' && attachNav ){

            lbWrapper.append( _controller.attachNavigator( ) );
        }
        
        if( typeof attachPag !== 'undefined' && attachPag ){
            
            lbWrapper.append( _controller.attachPaginator( /*items*/ ) );
        }
        
        jQuery( lbContainer ).on( 'click' , function( e ) {
            _controller.stopAnimation( ).close( );
            e.stopPropagation();
            return false;
        });
        
        return lbContainer.append( lbWrapper );
    };
    
    jQuery( document ).ready( function( ){
        if( _component.container === null ){
            //capturar todos los ids de galería
            jQuery( '.coders_image_widget.lightbox img.media-content' ).each( function(){

                var item_id = jQuery( this ).attr('data-item');
                
                if( item_id !== null ){
                    var item_url = jQuery( this ).attr( 'src' );
                    var item_title = jQuery( this ).attr( 'title' );
                    var item_alt = jQuery( this ).attr( 'alt' );
                    //proporciones de la imagen para mostrar un display adecuado
                    var display = jQuery( this ).hasClass('portrait') ?
                            'portrait' :
                            jQuery( this ).hasClass('landscape') ? 'landscape' :
                                    'default';
                
                    _controller.addItem( item_id , item_url , item_title , item_alt , display );
                    
                    jQuery( this ).on( 'click' , function( e ){
                        var item_id = _controller.getByItemId( jQuery( this ).attr( 'data-item' ) );
                        _controller.activate( item_id );
                        e.preventDefault();
                        return false;
                    } );
                }
            });
            //generar la galería
            _component.container = _controller.attachLightBox( true , true );
            //adjuntar al contenido
            jQuery( 'body' ).append( _component.container );
            
            jQuery( document ).keyup(function(e) {
                //cerrar al apretar ESCAPE
                if (e.keyCode == 27 && jQuery( _component.container ).hasClass('active') ) {
                    _controller.close();
                }
            });
            //testing
            //console.log( JSON.stringify( _settings.items ) );
        }
    });    
})( /*autoiniciador*/ );
