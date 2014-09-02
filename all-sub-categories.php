<?php
/**
* Plugin Name: All Sub Categories
* Plugin URI: http://www.knoppys.co.uk/all-sub-categories-wordpress-plugin
* Description: This widget displays a list of all child categories and their associated post titles as links 
*in a list format of a single user defined Parent Category.  
* Version: V1.4
* Author: Alex Knopp Wordpress Web Develoepr
* Author URI: http://www.knoppys.co.uk
* License: GPL2
* Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : alex.knopp@knoppys.co.uk)
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License, version 2, as 
* published by the Free Software Foundation.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
 */
add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );

/**
 * Enqueue plugin style-file
 */
function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('catstyle.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}


// Creating the widget 
class wpb_widget extends WP_Widget {

function __construct() {
parent::__construct(
// Base ID of your widget
'wpb_widget', 

// Widget name will appear in UI
__('All Sub Categories', 'wpb_widget_domain'), 

// Widget description
array( 'description' => __( 'Displays a list of Child Categories including Post Titles and links of your chosen Parent Category', 'wpb_widget_domain' ), ) 
);
}

// Creating widget front-end
// This is where the action happens
	public function widget( $args, $instance ) {
		if( $c = get_category(@$instance['category_id']) ){
			
			foreach(get_categories(array(
				"child_of"	=> $c->cat_ID,
				"orderby"   =>  "count", 
				"order"		=>	"DESC",

			)) as $childCat){
				
				echo '<div class="container">';
				echo 	('<h2 class="widgettitle">'.'<a href="'.get_site_url().'/?cat='.$childCat->term_id.'">'.$childCat->name.'</a></h2>');
				echo '<ul style="float: left;">';

				foreach( get_posts('posts_per_page=-1&cat='.$childCat->term_id) as $p) {
					echo('
						<li>
							<a href="'.get_permalink($p->ID).'">'.$p->post_title.'</a>
						</li>
					');
				}  
				echo '</ul>';
				echo "</div>";

			}
		}
		

	}
		
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
	$title = $instance[ 'title' ];
}
else {
	$title = __( 'New title', 'wpb_widget_domain' );
}
// Widget admin form
		echo '<p>Choose your parent category</p>';
		wp_dropdown_categories(array(
			"id"			=> $this->get_field_id('category_id'),
			"name"			=> $this->get_field_name("category_id"),
			"selected"		=>	@$instance["category_id"],
			"children"       => 'TRUE',
			"hide_empty"    => '0',
		));
?>
<p>	
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: (if you feel you need one!)' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>

<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
	
		$instance['title'] 			= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['category_id']	= $new_instance["category_id"];

		return $instance;
	}



} // Class wpb_widget ends here

// Register and load the widget
function wpb_load_widget() {
	register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );

/* Stop Adding Functions Below this Line */



?>
