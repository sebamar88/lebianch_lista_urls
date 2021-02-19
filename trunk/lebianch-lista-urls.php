<?php
/**
 * Plugin Name: Lebianch Lista tus URLS
 * Plugin URI: https://lebianch.com/plugins/lista-urls/
 * Description: Crea una lista con todos los links de tu web!
 * Version: 1.0.5
 * Author: LeBianch
 * Author URI: https://lebianch.com/
 * Text Domain: list urls, number of urls, list pages urls, list posts urls, indexation
 * License: GPL v2 or higher
 * License URI: License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// See http://codex.wordpress.org/Administration_Menus for more info on the process of creating an admin page

if(!defined('ABSPATH')) die();

function lblu_menu() {
    add_menu_page(
        'Lebianch Lista tus URLS',
        'Lebianch Lista tus URLS',
        'manage_options',
        'lebianch-lista-urls',
		'lblu_manage',
        'dashicons-editor-ol',
        90
    );
}
add_action( 'admin_menu', 'lblu_menu' );

function lblu_manage() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'No tienes suficientes permisos para acceder a esta página.' ) );
	}

$i = 0;
$my_posttpe = array(); // Creating array variable to house all custom post types
$my_postname = array(); // Creating array variable to house all pretty names custom post types


// Get all Custom Post Types, and ONLY Custom Post Types. See http://codex.wordpress.org/Function_Reference/get_post_types
$args=array(
  'public'   => true,
  '_builtin' => false
);
$salida = 'objects'; // names or objects, note names is the default.
$operador = 'and'; // 'and' or 'or'
$post_types = get_post_types($args,$salida,$operador);


// Loop through get_post_types and create arrays for custom post type names and labels
foreach ($post_types  as $post_type ) {
$my_posttpe[$i] = $post_type->name; // Getting the code name for the post type. See http://codex.wordpress.org/Function_Reference/get_post_type_object
$my_postname[$i] = $post_type->labels->singular_name; // Getting the pretty name for the post type.
$i++;
}

$arrlength = count($my_posttpe); // Setting variable arrlength equal to the number of array units in $myposttpye



// Form and function called on submit sourced in part from http://stackoverflow.com/questions/6060028/call-form-submit-action-from-php-function-on-same-page
?>

<h1><strong>Selecciona las URLs que desees listar, de las siguientes opciones:</strong></h1>
<form action = "" method = "post"    id = "myform">
    <input type="radio" name="listaurls-radio" value="todas"/> Todas las URLs (paginas, entradas, y custom post types)<br>
    <input type="radio" name="listaurls-radio" value="paginas"/> Solo páginas<br>
    <input type="radio" name="listaurls-radio" value="entradas"/> Solo Entradas<br>
    <?php
		for($x=0;$x<$arrlength;$x++) {
		  echo '<input type="radio" name="listaurls-radio" value="'. $my_posttpe[$x] . '"/>Solo Entradas de ' . $my_postname[$x] . '<br>';
		  }
     ?>
    <br>
    <input type="checkbox" name="makelinks" value="makelinks"  /> Generar lista "Clickeable" (Links/Hypervinculos) <br>
	<input type="checkbox" name="numerados" value="numerados"  /> Generar lista numerada <br>
    <br>

    <input type="submit" class="button-primary" value="Enviar"/>
	
</form>


<?php
	if (isset($_POST['listaurls-radio'])) {

	    if ($_POST['listaurls-radio']=="todas") {
			$the_query = new WP_Query( array('post_type' => 'any', 'posts_per_page' => '-1', 'post_status' => 'publish' ) );
		} else if ($_POST['listaurls-radio']=="paginas") {
			$the_query = new WP_Query( array('post_type' => 'page', 'posts_per_page' => '-1', 'post_status' => 'publish' ) );
		} else if ($_POST['listaurls-radio']=="entradas") {
			$the_query = new WP_Query( array('post_type' => 'post', 'posts_per_page' => '-1', 'post_status' => 'publish' ) );
		} else {
			for($y=0;$y<$arrlength;$y++) {
			  if ($_POST['listaurls-radio'] == $my_posttpe[$y]) {
				echo '<p>Prueba</p>';
				$the_query = new WP_Query( 'post_type='.$my_posttpe[$y].'&posts_per_page=-1&post_status=publish');
				}
			}
		}

		echo '<p><strong>Abajo una lista de las URLs pedidas anteriormente:</strong></p>';
?>

<?php if (isset($_POST['numerados'])) { ?>
<ol>
<?php } else { ?>
<ol style="list-style: none">
<?php } ?>
    <?php // The Loop
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			?>
    <li>
		<?php if (isset($_POST['makelinks'])) { ?>
				<a href="<?php the_permalink();?>"><?php the_permalink(); ?></a>
                <?php } else {
					the_permalink();
					} ?>
    </li>
    <?php endwhile; ?>
</ol>





<?php } // end if

} // end generate_url_list()







