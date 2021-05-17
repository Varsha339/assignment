<?php
/*
Plugin Name: Article
Plugin URI: https://www.google.com
Description: Plugin for Article custom post type
Author: xyz
Version: 1.0
Author URI: https://www.google.com
*/

/*create custom post type here*/
function register_article_post_type() {
	$labels = array(
		'name'               => 'Article',
		'singular_name'      => 'Articles',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Article',
		'edit_item'          => 'Edit Article',
		'new_item'           => 'New Article',
		'all_items'          => 'All Article',
		'menu_icon'			 => 'dashicons-analytics', /*icon for custom post type*/
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
add_action( 'init', 'register_article_post_type' );

add_action('admin_menu', 'add_article_cpt_submenu_example');
/*create sub-menu as Fetch Api*/
function add_article_cpt_submenu_example(){

     add_submenu_page(
                     'edit.php?post_type=article', //$parent_slug
                     'Article Subpage Example',  //$page_title
                     'Fetch Api',        //$menu_title
                     'manage_options',           //$capability
                     'Article_subpage_example',//$menu_slug
                     'Article_subpage_example_render_page'//$function
     );

}

/*Sub-menu page callback function for fetch users */
function Fetch_users() {
    $url = 'https://jsonplaceholder.typicode.com/users';
    $arguments = array('method' => 'GET');
    $response = wp_remote_get($url, $arguments);
    $user_arr = json_decode(wp_remote_retrieve_body($response));
    
    foreach ($user_arr as $u_ser) {
        $password = 'abc';
        $user_id = username_exists($u_ser->username);
        if (!$user_id && email_exists($u_ser->email) == false) {
            $user_id = wp_create_user($u_ser->username, $password, $u_ser->email);
            if (!is_wp_error($user_id)) {
                $user = get_user_by('id', $user_id);
                $user->set_role('author');
            }
        }
        update_user_meta($user_id, 'user_nicename', $u_ser->username);
        update_user_meta($user_id, 'phone', $u_ser->phone);
        update_user_meta($user_id, 'website', $u_ser->website);
        update_user_meta($user_id, 'name', $u_ser->company->name);
        update_user_meta($user_id, 'catchPhrase', $u_ser->company->catchPhrase);
        update_user_meta($user_id, 'bs', $u_ser->company->bs);
        update_user_meta($user_id, 'street', $u_ser->address->street);
        update_user_meta($user_id, 'suite', $u_ser->address->suite);
        update_user_meta($user_id, 'city', $u_ser->address->city);
        update_user_meta($user_id, 'zipcode', $u_ser->address->zipcode);
    }
}
/* function for fetch Posts into Article custom post type */

function Fetch_posts() {
    global $user_ID;
    $url = 'https://jsonplaceholder.typicode.com/posts';
    $arguments = array('method' => 'GET');
    $response = wp_remote_get($url, $arguments);
    $post_arr = json_decode(wp_remote_retrieve_body($response));
    $users = get_users();
    foreach ($post_arr as $p_ost) {
	/* rand(0, count($users) - 1) function used to assign random author to posts */
        $index = rand(0, count($users) - 1);
        $roles = $users[$index]->roles[0];
        if ($roles == "administrator") {
            $index = rand(0, count($users) - 1);
        }
        global $wpdb;
        $post_id = post_exists($p_ost->title);
        if (!$post_id) {
        $new_post = array(
		'post_title' => $p_ost->title,
		'post_content' => $p_ost->body,
		'post_status' => 'publish',
		'post_date' => date('Y-m-d H:i:s'),
		'post_author' => $users[$index]->ID,
		'post_type' => 'article',
		'post_category' => array(0));
        $post_id = wp_insert_post($new_post);           
        }
    }
	
}
/* s2 buttons on ub-menu page For fetching resources users and posts*/
function Article_subpage_example_render_page() {
    if (array_key_exists('Fetch_users', $_POST)) {
        Fetch_users();
		echo 'Fetching successfully done..';
    } else if (array_key_exists('Fetch_posts', $_POST)) {
        Fetch_posts();
		echo 'Fetching successfully done..';

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



















