/**
 * Gestor multimedia coders
 * @param {String} media_id
 * @param {String} media_name
 * @param {Object} setup
 * @returns {codersMediaController}
 */
function codersMediaController( media_id , media_name , setup ){
    /**
     * @type codersMediaController
     */
    var _self = this;
    var _settings = {
        'id': typeof media_id !== 'undefined' ? media_id : null, //Id del campo input en WP
        'name': typeof media_name !== 'undefined' ? media_name : '', //nombre del campo input en WP
        'class': '.media-selector',
        'title': typeof setup === 'object' && typeof setup.title === 'string' ?
                setup.title :
                'Selector de im&aacute;genes',
        'button': typeof setup === 'object' && typeof setup.button === 'string' ?
                setup.button :
                'Seleccionar'
    };
    /**
     * @returns {String}
     */
    this.getSelectorId = function( ){
        return _settings.id !== null ? _settings.class + '#' + _settings.id : _settings.class;
    };
    /**
     * @param {String} id
     * @param {String} url
     * @param {String} name
     * @returns {codersMediaController}
     */
    this.setMedia = function( id , url , name ){
        
        jQuery( _self.getSelectorId( ) ).val( id );
        
        jQuery( _self.getSelectorId( ) ).find( 'img' ).remove( );
        
        jQuery( _self.getSelectorId( ) )
                .append( this.attachImage( id, url,name, true ))
                .remove('.empty');
                
        if( !jQuery( _self.getSelectorId() ).hasClass('selected') ){
            jQuery( _self.getSelectorId() ).addClass('selected');
        }
        
        return this;
    };
    /**
     * @param {String} id
     * @param {String} url
     * @param {String} name
     * @returns {codersMediaController}
     */
    this.addMedia = function( id, url , name ){

        var container = _self.getSelectorId();

        jQuery( container ).prepend( this.attachImage(id ,url ,name ) );
        
        return this;
    };
    /**
     * @param {String} input
     * @param {Boolean} multiSelect
     * @returns {codersMediaController}
     */
    this.openMediaSelector = function( input , multiSelect ){
        
        var mediaButton = jQuery( input );
        
        var multiple = ( typeof multiSelect === 'boolean' && multiSelect );

        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            var mediaSelector = wp.media.frames.file_frame = wp.media({
                title: _settings.title,
                button: {text: _settings.button },
                multiple: multiple
            });
            mediaSelector.on('select', function () {
                if( multiple ){
                    mediaSelector.state().get('selection').map( function( attachment ){
                        var media = attachment.toJSON();
                        _self.addMedia( media.id, media.url, media.title );
                    });
                }
                else{
                    var media = mediaSelector.state().get('selection').first().toJSON();
                    _self.setMedia( media.id , media.url , media.title );
                }
            });
            mediaSelector.open( mediaButton );
        }
        else{
            console.log('No se ha cargado correctamente la librería wp.media');
        }

        return this;
    };
    /**
     * @param {Number} mediaId
     * @param {String} mediaUrl
     * @param {String} mediaTitle
     * @param {Boolean} single
     * @returns {jQuery}
     */
    this.attachImage = function( mediaId , mediaUrl, mediaTitle , single ){
        
        var image = jQuery( '<img id="'
                + mediaId + '" src="'
                + mediaUrl + '" alt="'
                + mediaTitle + '" />' );

        if( typeof single === 'boolean' && single ){
            //es un botón, por tanto retornar la imagen únicamente
            return image;
        } 
        
        var input = jQuery( '<input type="hidden" name="'
                + _settings.name + '[]" value="'
                + mediaId + '" />' );
        
        var item = jQuery('<li class="media-item"></li>').append( input ).append( image );
        
        jQuery( item ).on('click',function(e){
            e.preventDefault();
            jQuery( this ).remove();
            return true;
        });
        
        return item;
    };
    /**
     * @param {String} mediaId
     * @returns {codersMediaController}
     */
    this.attachMediaSelector = function( ){
        
        var identifier = _self.getSelectorId( );
        
        var type = jQuery( identifier ).prop('tagName').toLowerCase();

        var multiSelect = ( type === 'ul' );

        var button = ( type === 'button' ) ?
            jQuery( identifier ) :      //referencia a sí mismo
            jQuery( '<button class="add-media"></button>' );    //crea un botón para el selector múltiple

        jQuery( button ).on( 'click' , function( e ){
            //console.log( identifier + ' clicado!!');
            if (e.handled !== true) {
                _self.openMediaSelector( this , multiSelect );
                e.preventDefault();
                e.handled = true;
                return true;
            }
        });

        if( multiSelect ){
            jQuery( identifier ).find( 'li.media-item' ).each( function(){
                jQuery( this ).on('click',function(e){
                    e.preventDefault();
                    jQuery( this ).remove();
                    return true;
                });
            });
            //anexar botón
            jQuery( identifier )
                .append( jQuery( '<li class="media-actions"></li>' )
                        .append( button ) );
        }
        else{
            //nada, ya resuelto, el botón se utiliza como base para generar
            //el evento de selector multimedia
        }

        return this;
    };
    
    return _settings.id !== null ? this.attachMediaSelector( _settings.id ) : this;
}