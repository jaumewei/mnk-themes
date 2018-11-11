"# mnk-themes" 
Plugin WP para la gestión de temas mediante OOP.
Herencia de temas creando el fichero nombre_tema.theme.php dentro de la carpeta nombre_tema.
La clase debe ser NombreTemaTheme y extender de la plantilla \CODERS\Theme

El tema permite generar rápidamente mediante métodos definidos la estructura de la web:

Sobrecargar la funcion defineThemeComponents() (Retornar instancia de la subclase Theme) e invocar en su interior
los siguientes métodos

    registerSidebar( ID , NAME , DESCRIPTION ) Define un sidebar para mostrar widgets
    registerMenu( ID , NAME ) Define un sidebar para mostrar widgets
    registerScript( ID , URL , DEPS ) Define un script para el tema
    registerStyle( ID , URL , DEPS ) Define un estilo para el tema
    registerGoogleFont( FONT , array( weights ) ) Define unafuente google para el tema

Sobrecargar el método defineThemeLayout() para retornar la estructura del sitio en un array multinivel.
Sobrecargar el método defineThemeIds para indicar los IDS de los contenedores del tema
Sobrecargar el método defineThemeTags para indicar que TAGS htmlse utilizan en cada contenedor
Sobrecargar el método defineThemeWrappers para registrar los contenedores con wrappers


