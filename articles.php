<?php
/*
Plugin Name: Article
Plugin URI: https://www.google.com
Description: Plugin for Article custom post type
Author: xyz
Version: 1.0
Author URI: https://www.google.com
*/

function pd101_register_Article_post_type() {

	$labels = array(
		'name'               => 'Article',
		'singular_name'      => 'Articles',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Article',
		'edit_item'          => 'Edit Article',
		'new_item'           => 'New Article',
		'all_items'          => 'All Article',
		//'attributes'         => 'Item Attributes',
		'menu_icon'			 => 'dashicons-analytics',

		'view_item'          => 'View Article',
		'search_items'       => 'Search Article',
		'not_found'          =>  'No Article found',
		'not_found_in_trash' => 'No Article found in Trash',
		'parent_item_colon'  => '',
		'menu_name'          => 'Article'
	);
	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		//'taxonomies'         => array( 'category', 'post_tag' ),
		'menu_icon'			 => 'dashicons-analytics',

		'rewrite'            => array( 'slug' => 'Articles' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => 22,
		'supports'           => array( 'title', 'editor',)
	);

	register_post_type( 'article', $args );

}

add_action( 'init', 'pd101_register_article_post_type' );



add_action('admin_menu', 'add_Article_cpt_submenu_example');

//admin_menu callback function

function add_Article_cpt_submenu_example(){

     add_submenu_page(
                     'edit.php?post_type=article', //$parent_slug
                     'Article Subpage Example',  //$page_title
                     'Article Settings',        //$menu_title
                     'manage_options',           //$capability
                     'Article_subpage_example',//$menu_slug
                     'Article_subpage_example_render_page'//$function
     );

}

//add_submenu_page callback function
 
	
        function Fetch_users() {
            echo "Users fetching from the resource successfully";
			$url = 'https://jsonplaceholder.typicode.com/users';
    
				$arguments = array(
					'method' => 'GET'
				);

				$response = wp_remote_get( $url, $arguments );
				$user_arr=json_decode( wp_remote_retrieve_body( $response ) );
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					return "Something went wrong: $error_message";
				} else {
					echo '<pre>';
					
					// var_dump( $user_arr  );
					echo '</pre>';
				}
				foreach($user_arr as $u_ser){
				$password='abc';
				$user_id = username_exists( $u_ser->username );
				if ( !$user_id && email_exists($u_ser->email) == false ) {
					$user_id = wp_create_user( $u_ser->username, $password, $u_ser->email );
					if( !is_wp_error($user_id) ) {
						$user = get_user_by( 'id', $user_id );
						$user->set_role( 'author' );
					}
				}
				update_user_meta( $user_id, 'user_nicename', $u_ser->username );
				update_user_meta( $user_id, 'phone', $u_ser->phone );
				update_user_meta( $user_id, 'website', $u_ser->website );
				update_user_meta( $user_id, 'name', $u_ser->company->name );
				update_user_meta( $user_id, 'bs', $u_ser->company->bs );
				update_user_meta( $user_id, 'street', $u_ser->address->street );
				update_user_meta( $user_id, 'suite', $u_ser->address->suite );
				update_user_meta( $user_id, 'city', $u_ser->address->city );
				update_user_meta( $user_id, 'zipcode', $u_ser->address->zipcode );
				}
        }
        function Fetch_posts() {
            echo "Posts fetching from the resource successfully";
			global $user_ID;
			$url = 'https://jsonplaceholder.typicode.com/posts';
    
				$arguments = array(
					'method' => 'GET'
				);

				$response = wp_remote_get( $url, $arguments );
				$post_arr=json_decode( wp_remote_retrieve_body( $response ) );
				if ( is_wp_error( $response ) ) {
					$error_message = $response->get_error_message();
					return "Something went wrong: $error_message";
				} else {
					echo '<pre>';
					  
					 //var_dump( $post_arr  );
					echo '</pre>';
				}
				//$users = get_users(array(role => 'author'));

	foreach($post_arr as $p_ost){
			
	global $wpdb;
	$post_id = post_exists( $p_ost->title );
	if (!$post_id)
	 {
				$new_post = array(
			'post_title' => $p_ost->title,
			'post_content' => $p_ost->body,
			'post_status' => 'publish',
			'post_date' => date('Y-m-d H:i:s'),
			//'post_author' => $users->id,
			'post_type' => 'article',
			'post_category' => array(0)
			);
			$post_id = wp_insert_post($new_post);
			
		}else{
					echo 'Posts already exists';

		}
	}
}
function Article_subpage_example_render_page() {
if(array_key_exists('Fetch_users', $_POST)) {
            Fetch_users();
        }
        else if(array_key_exists('Fetch_posts', $_POST)) {
            Fetch_posts();
        }
?>
<form method="post">
<br><br><br><br>
        <input type="submit" name="Fetch_users"
                class="button" value="Fetch_users" />
          
        <input type="submit" name="Fetch_posts"
                class="button" value="Fetch_posts" />
    </form>
<?php
}
?>



















