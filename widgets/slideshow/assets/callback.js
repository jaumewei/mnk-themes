/**
* data contiene información del slide
* classes
* old-id
* data-id
*/
jQuery(document).ready(function(){

    (new sliderBoxController("%s", 1000, function(e, data){
    
        console.log(JSON.stringify(data));
    
    }));

});
