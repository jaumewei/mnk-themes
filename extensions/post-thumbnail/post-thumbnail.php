<?php namespace CODERS\Extensions;

/**
 * Agrega clases personalizables a la página
 *
 * @author informatica1
 */
final class PostThumbnail extends \CODERS\Extension {
    /**
     * Imagen destacada o portada de un post
     * @param int|string $post_id
     * @return string
     */
    public static final function postThumbnail( $post_id , $size = 'thumbnail' ){
        
        if(has_post_thumbnail($post_id)){

            $media_id = get_post_thumbnail_id($post_id);

            if( $media_id > 0 ){

                $image = wp_get_attachment_image_src( $media_id , $size ); 

                if( FALSE !== $image ){

                    $text_alt = get_post_meta( $media_id, '_wp_attachment_image_alt', true);
                    $text_title = get_the_title( $media_id );

                    $width = $image[ 1 ];
                    $height = $image[ 2 ];

                    $classes = array( 'post-image' );

                    if( $width > $height ){
                        $classes[] = 'display-landscape';
                    }
                    elseif( $height > $width ){
                        $classes[] = 'display-portrait';
                    }
                    else{
                        $classes[] = 'display-default';
                    }

                    $classes[] = 'size-' . $size;

                    return sprintf('<img src="%s" class="%s" alt="%s" title="%s" />',
                            //URL de la imagen
                            $image[ 0 ],
                            //tipo de display
                            implode(' ', $classes),
                            //alt y titulo
                            $text_alt,$text_title );
                }
            }
        }
        
        return sprintf('<span class="post-image no-thumbnail post-id-%s"></span>',$post_id);
    }
}
