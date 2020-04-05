/**
 * Coders Admin Widget Manager
 */
( function(){
    /**
     * @returns {String|URL}
     */
    this.url = function(){
        
        var output = [
            window.location.protocol + '/' ,
            window.location.hostname ];

        
        var path = window.location.pathname;
        var fixed_path = path.substr(0,path.indexOf('wp-admin')).split('/');
        
        for( var i = 0 ; i < fixed_path.length ; i++ ){
            if( fixed_path[i].length ){
                output.push( fixed_path[ i ] );
            }
        }
        
        output.push('wp-admin');
        output.push('admin-ajax.php');

        return output.join('/');
    };
    /**
     * @param {String|URL} url
     * @param {Object} data
     * @param {Function} callback
     * @returns {widget-managerL#4}
     */
    this.ajax = function( request , callback ){

        console.log( this.url() );
        jQuery.ajax({
            'type': 'POST',
            'url': this.url(),
            'data': request,
            'success': callback,
            'error': function( error ){
                console.log( error );
            }
        });
        return this;
    };
    /**
     * @param {String} widget
     * @returns {widget-managerL#4}
     */
    this.toggle = function( widget ){
        
        var button = jQuery('#coder-widget-manager button[data-widget-id="' + widget + '"]');
        
        jQuery( button ).removeClass( 'active inactive' ).addClass('disabled');

        return this.ajax(
            {'action': 'coder_widget_toggle','widget': widget },
            function( response ){
                var status = response.hasOwnProperty('status') ?
                    response[ 'status' ] :
                    'disabled';
                if( status !== 'disabled' ){
                    jQuery( button ).removeClass('disabled').addClass( status );
                }
                //console.log( response );
            });
    };
    /**
     * @returns {widget-managerL#4}
     */
    this.import = function(){
        var _self = this;
        return this.ajax(
            {'action': 'coder_widget_list'},
            function( response ){
                var widgets = JSON.parse(response) ;
                _self.populate( widgets );
            });
    };
    /**
     * @param {Array} items
     * @returns {widget-managerL#4}
     */
    this.populate = function( items ){
        
        var _self = this;
        var widgetList = jQuery( '<ul class="widget-list"></ul>' );
        
        Object.keys( items ).forEach( function( id ){

            var item = jQuery('<li class="widget inline"></li>');
            var widget = jQuery('<button type="button" class="button"></button>');
            var name = items[ id ].name || 'undefined';
            var status = items[ id ].status || 'invalid';

            jQuery( widget ).attr( 'data-widget-id' , id );
            jQuery( widget ).html( name );
            jQuery( widget ).addClass( status );

            jQuery( widget ).on( 'click' , function(e){
                e.preventDefault();
                var id = jQuery( this ).attr('data-widget-id');
                _self.toggle( id );
                return true;
            });
            jQuery( item ).append( widget );
            jQuery( widgetList ).append( item );
        } );
        jQuery('#coder-widget-manager').append( widgetList );
        return this;
    };
    /**
     * @returns {widget-managerL#4}
     */
    this.setup = function(){
        var _self = this;
        jQuery( document ).ready( function(e){
            _self.import();
        });
        return this;
    };
    
    return this.setup();
})( /* autoloader*/ );



