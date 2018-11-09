jQuery( document ).ready(function(){
    /**
     *  Dado un contenedor donde su amplitud es el 100%
     *  el wraper interno en posición absoluta es del 200% para facilitar la reinserción de imagenes...
     *  
     *  Por jQuery preparar un efecto de transición hozigontal de todas las imágenes hacia la izquierda
     *  
     *  La primera imagen acaba por desaparecer desde el margen izquierdo en posición negativa
     *      posición = - amplitud de la imágen con respecto al contenedor padre.
     *      
     *  Por jQuery, extraer esa imagen del slide y agregarla (append) al final de la lista de nuevo
     *  de manera continuada
     *      
     */

    var _selfHorizontal = jQuery( '.widget_coders_mediascroll .image-scroller.horizontal .wrapper' );

    window.setInterval(function(){

        var _item = _selfHorizontal.children('img').first();

        var _offset = _item.width();

        console.log('animating ' + jQuery(_item).attr('src') + ' (' + _offset + ') ...');

        jQuery(_selfHorizontal).append(jQuery(_item).clone(false));

        jQuery.when(
                jQuery(_selfHorizontal).children('img').animate( {left:-_offset} , 1600 )
            ).done(function(){
                //console.log('detaching ' + jQuery(_item).attr('src') + ' ...');
                //_item.detach();
                jQuery(_item).remove();
                jQuery(_selfHorizontal).children('img').removeAttr('style');
            });
    }, 4000 );
    
    
    var _selfVertical = jQuery( '.widget_coders_mediascroll .image-scroller.vertical .wrapper' );

    window.setInterval(function(){

        var _item = _selfVertical.children('img').first();

        var _offset = _item.height();

        console.log('animating ' + jQuery(_item).attr('src') + ' (' + _offset + ') ...');

        jQuery(_selfVertical).append(jQuery(_item).clone(false));

        jQuery.when(
                jQuery(_selfVertical).children('img').animate( {top:-_offset} , 1600 )
            ).done(function(){
                jQuery(_item).remove();
                jQuery(_selfVertical).children('img').removeAttr('style');
            });
    }, 4000 );
});
