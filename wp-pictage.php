<?php
/*
Plugin Name: WP Pictage
Plugin URI: http://photographyblogsites.com/resources/wordpress-plugins/wp-pictage
Description: Allows Pictage members to list their events within a WordPress page or post. 
Version: 1.1
Author: based on "Pictage Link" by  Alfred Gutierrez - http://code.google.com/p/pictage-link/
Author URI: http://photographyblogsites.com
License: GPL2
*/

/*  Copyright 2010  Partner Interactive  (email : info@photographyblogsites.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$pictage_options = maybe_unserialize(get_option('pictage_options'));

$studio_id 		= $pictage_options['studio_id'];

$page_margin 	= $pictage_options['page_margin'];
$page_padding 	= $pictage_options['page_padding'];
$page_top 		= $pictage_options['page_top'];
$page_left 		= $pictage_options['page_left'];
$bg_image 		= $pictage_options['bg_image'];

wp_enqueue_script ('jquery');

add_shortcode	('pictage', 	'pictage_events2');

add_action		('admin_menu', 		'pictage_admin2');
add_action		('wp_head', 		'pictage_css2');

function pictage_admin2() {
	add_menu_page('Pictage Settings', 'Pictage Settings', 'administrator', __FILE__, 'pictage_options',plugins_url('/pictage.png', __FILE__));
}

function pictage_events2($studio_id="") {

	if ($studio_id == '') {
		$pictage_options = maybe_unserialize(get_option('pictage_options'));
		$studio_id = $pictage_options['studio_id'];
	};
	
	$full = "http://external.pictage.com/external/PHTINTEG?photog=$studio_id";
	$html = getPage($full);

	$html = str_replace('target="PICTAGE"', 'class="pictage_links"', $html);
	$html = str_replace("<hr>", " ", $html);
	$html = str_replace("<title>Pictage</title>", "", $html);
	$html = str_replace("<br>", "", $html);
	
	$html_final = '<div class="pictage-events">';
	$html_final .= $html;
	$html_final .= '</div>';
	
	//outputting the page
	echo $html_final; 
}

function pictage_css2() {

	$pictage_options = maybe_unserialize(get_option('pictage_options'));

	// let links take on the style of the theme.
	// maybe allow them to control padding around all the links

	$page_margin 	= $pictage_options['page_margin'];
	$page_padding 	= $pictage_options['page_padding'];
	$page_top 		= $pictage_options['page_top'];
	$page_left 		= $pictage_options['page_left'];
	$bg_image 		= $pictage_options['bg_image'];

	$css = '<style>';
	$css.= 'a.pictage_links {clear:both; display: block;}';
	$css .'</style>';
	
	echo $css;

};

//running a few curl functions to read source of EZLink's API.
function getPage($url=""){
 $ch = curl_init();
 curl_setopt($ch,CURLOPT_URL, $url);
 curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
 curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
 curl_setopt($ch,CURLOPT_TIMEOUT,10);
 $html=curl_exec($ch);
 if($html==false){
  $m=curl_error(($ch));
  error_log($m);
 }
 curl_close($ch);
 return $html;
}

function pictage_options() {
$pictage_options = maybe_unserialize(get_option('pictage_options'));

$page_margin 	= $pictage_options['page_margin'];
$page_padding 	= $pictage_options['page_padding'];
$page_top 		= $pictage_options['page_top'];
$page_left 		= $pictage_options['page_left'];

	if ($_POST['pictage-submit']) {
	
	if ( !wp_verify_nonce( $_POST[ 'pictage_admin2' ], 'pictage_admin2' ) ) : 
?>
<div class="updated">
	<p>There was a problem. Please try again.</p>
</div>
<?php 
	
	else :
		
		if (isset ($_POST['studio_id'])) { 
			$pictage_options['studio_id'] = htmlspecialchars($_POST['studio_id']);
		}
				
		update_option ('pictage_options',maybe_serialize($pictage_options));
		$pictage_options = maybe_unserialize(get_option('pictage_options'));

	?>
	
<?php if ( $warning != '' ) { ?>
<div class="error">
	<?php echo $warning; ?>
</div>
<?php } else { ?>
<div class="updated">
	<p>Your new options have been successfully saved.</p>
</div>
<?php }; 
	endif;
	};
?>
<div class="wrap">
	<h2>WP Pictage Options</h2></br>
	<form name="theform" method="post" enctype="multipart/form-data" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']);?>">
		<?php wp_nonce_field( 'pictage_admin2', 'pictage_admin2', false, true ); ?>
		<p>Studio ID:</p>
		<input type="text" name="studio_id" size ="5" value="<?php echo $pictage_options['studio_id']; ?>" /> <i>i.e. AB123 (Upper Case Only)</i>
		<input type="hidden" name="pictage-submit" value="1" />

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>
<?php
};
?>