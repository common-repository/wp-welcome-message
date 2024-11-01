<?php
/*
Plugin Name: WP Welcome Message
Plugin URI: http://www.a1netsolutions.com/Products/WP-Welcome-Message
Description: <strong>WP Welcome Message</strong> is a wordpress plugin, which help your to make any announcement, special events, special offer, signup message or such kind of message, displayed upon your website's visitors when the page is load through a popup box.
Version: 3.0
Author: Ahsanul Kabir
Author URI: http://www.ahsanulkabir.com/
License: GPL2
License URI: license.txt
*/

$wpwm_conf = array(
	'VERSION' => get_bloginfo('version'),
	'VEWPATH' => plugins_url('lib/', __FILE__),
);

function wpwm_admin_styles()
{
	global $wpwm_conf;
	wp_enqueue_style('wpwm_admin_styles',($wpwm_conf["VEWPATH"].'css/admin.css'));
	if( $wpwm_conf["VERSION"] > 3.7 )
	{
		wp_enqueue_style('wpwm_icon_styles',($wpwm_conf["VEWPATH"].'css/icon.css'));
	}
}
add_action('admin_print_styles', 'wpwm_admin_styles');

function wpwm_scripts_styles()
{
	global $wpwm_conf;
	$wpwmBoxSetly = get_option('wpwm_boxsetly');
	if(!$wpwmBoxSetly){$wpwmBoxSetly=="fadeOut";}
	wp_enqueue_script('wpwm_site_scripts',($wpwm_conf["VEWPATH"].'js/site_'.$wpwmBoxSetly.'.js'),array('jquery'),'',true);
	wp_enqueue_style('wpwm_site_style',($wpwm_conf["VEWPATH"].'css/site.css'));
}
add_action('wp_enqueue_scripts', 'wpwm_scripts_styles');

function wpwm_defaults()
{
	$wpwm_default = plugin_dir_path( __FILE__ ).'lib/default.php';
	if(is_file($wpwm_default))
	{
		require $wpwm_default;
		foreach($default as $k => $v)
		{
			$vold = get_option($k);
			if(!$vold)
			{
				update_option($k, $v);
			}
		}
		if(!is_multisite())
		{
			unlink($wpwm_default);
		}
	}
}

function wpwm_activate()
{
	$wpwm_postsid = get_option( 'wpwm_postsid' );
	if(!$wpwm_postsid)
	{
		$inputContent = 'Welcome to '.get_bloginfo('name').', '. get_bloginfo('description');
		$new_post_id = wpwm_printCreatePost($inputContent);
		update_option( 'wpwm_postsid', $new_post_id );
	}
	wpwm_defaults();
}

function wpwm_redirect()
{
	$wpwm_fv = get_option('wpwm_fv');
	if($wpwm_fv != 'fv')
	{
		echo '<a href="',admin_url('admin.php?page=wpwm_admin_page'),'" id="wpwm_redirect">Please setup your <strong>WP Welcome Message 2.0</strong> plugin. <input type="submit" value="Setup" class="button" /></a>';
	}
}
add_action( 'admin_footer', 'wpwm_redirect' );

function wpwm_admin_menu()
{
	global $wpwm_conf;
	if( $wpwm_conf["VERSION"] < 3.8 )
	{
		add_menu_page('WP Welcome Message', 'Welcome Msg', 'manage_options', 'wpwm_admin_page', 'wpwm_admin_function', (plugins_url('lib/img/icon.png', __FILE__)));
	}
	else
	{
		add_menu_page('WP Welcome Message', 'Welcome Msg', 'manage_options', 'wpwm_admin_page', 'wpwm_admin_function');
	}
}
add_action('admin_menu', 'wpwm_admin_menu');

function wpwm_select( $iget, $iset, $itxt )
{
	if( $iget == $iset )
	{
		echo '<option value="'.$iset.'" selected="selected">'.$itxt.'</option>';
	}
	else
	{
		echo '<option value="'.$iset.'">'.$itxt.'</option>';
	}
}

function wpwm_update($key, $value)
{
	if(isset($value) && !empty($value))
	{
		update_option($key, $value);
	}
}

function wpwm_admin_function()
{
	$wpwm_fv = get_option('wpwm_fv');
	if($wpwm_fv != 'fv')
	{
		update_option('wpwm_fv', 'fv');
	}
	
	wpwm_update('wpwm_loc', $_POST["wpwm_loc"]);
	wpwm_update('wpwm_log', $_POST["wpwm_log"]);
	wpwm_update('wpwm_boxsetly', $_POST["wpwm_boxsetly"]);
	wpwm_update('wpwm_bgstyle', $_POST["wpwm_bgstyle"]);
	wpwm_update('wpwmTemplate', $_POST["wpwmTemplate"]);
	wpwm_update('wpwm_onlyFirstVisit', $_POST["wpwm_onlyFirstVisit"]);
	wpwm_update('wpwm_ststs', $_POST["wpwm_ststs"]);
	$wpwmPID = get_option('wpwm_postsid');
	wpwm_updatePost($_POST["wpwmeditor"], $wpwmPID);
	
	if( isset($_POST["wpwmeditor"]) || isset($_POST["wpwmTemplate"]) )
	{
		echo '<div id="message" class="updated wpwm_updated"><p>Your data has been successfully saved.</p></div>';
	}
	
	global $wpwm_conf;
	echo '<div id="wpwm_container">
	<div id="wpwm_main">
	<a href="https://www.youtube.com/watch?v=dz1wZSsRxXk" target="_blank"><img src="',$wpwm_conf["VEWPATH"],'/img/uvg.png" id="wpwm_uvg" /></a>
	<h1 id="wpwm_page_title">WP Welcome Message</h1>';
	?>
    <div class="wpwm_box">
    <div class="wpwm_box_title">Your Welcome Message
    <form method="post" action="" id="wpwm_off_on"><input type="hidden" name="wpwm_ststs" value="<?php
    $wpwm_ststs = get_option('wpwm_ststs');
	if($wpwm_ststs == 'on')
	{
		echo 'off';
	}
	else
	{
		echo 'on';
	}
	?>" /><input type="image" src="<?php echo $wpwm_conf["VEWPATH"]; ?>/img/<?php
    $wpwm_ststs = get_option('wpwm_ststs');
	if($wpwm_ststs == 'on')
	{
		echo 'one-check_yes';
	}
	else
	{
		echo 'one-check_no';
	}
	?>.png" /></form>
    </div>
    
    <div class="wpwm_box_con">
    <form method="post" action="" id="wpwm_content_form">
    <?php
    $wpwm_ststs = get_option('wpwm_ststs');
	if($wpwm_ststs == 'off')
	{
		echo '<div id="wpwm_content_disable"></div>';
	}

    $wpwmPID = get_option('wpwm_postsid');
	$wpwmContent = get_post($wpwmPID);
	$wpwmContent = $wpwmContent->post_content;
	$wpwmContent = apply_filters('the_content', $wpwmContent);
	$wpwmContent = str_replace(']]>', ']]&gt;', $wpwmContent);
	
	if( $wpwm_conf["VERSION"] < 3.3 )
	{
		echo '<textarea name="wpwmeditor" style="width:100%; height:300px;"></textarea>';
	}
	else
	{
		wp_editor( $wpwmContent, 'wpwmeditor', array('textarea_rows' => 20, 'textarea_name' => 'wpwmeditor') );
	}
	?>
    <input type="submit" value="save changes" />
    </form>
    </div>
    </div>
    
    <div class="wpwm_box">
    <div class="wpwm_box_title">Settings</div>
    <div class="wpwm_box_con">
    <form method="post" action="">
          <div class="row">
            <label>On Which Page/Pages to Display : </label>
            <select name="wpwm_loc">
              <?php
				$wpwmLoc = get_option( 'wpwm_loc' );
				wpwm_select( $wpwmLoc, 'home', 'Home Page Only' );
                wpwm_select( $wpwmLoc, 'all', 'All Pages' );
				?>
            </select>
          </div>
          <div class="row">
            <label>Logged-in / Not Logged-in user : </label>
            <select name="wpwm_log">
              <?php
				$wpwm_log = get_option( 'wpwm_log' );
				wpwm_select( $wpwm_log, 'log', 'Logged-in Users Only' );
				wpwm_select( $wpwm_log, 'nlog', 'Not Logged-in Users Only' );
                wpwm_select( $wpwm_log, 'all', 'For All' );
				?>
            </select>
          </div>
          <div class="row">
            <label>Message Box Animation Style : </label>
            <select name="wpwm_boxsetly">
              <?php
				$wpwmBoxSetly = get_option( 'wpwm_boxsetly' );
				wpwm_select( $wpwmBoxSetly, 'fadeOut', 'Fade Out' );
                wpwm_select( $wpwmBoxSetly, 'slideUp', 'Slide Up' );
				?>
            </select>
          </div>
          <div class="row">
            <label>Template : </label>
            <select name="wpwmTemplate">
              <?php
				$wpwmTemplate = get_option( 'wpwmTemplate' );
				wpwm_select( $wpwmTemplate, 'black-color', 'Dark Color Only' );
                wpwm_select( $wpwmTemplate, 'black-white-color', 'White Color Only' );
				wpwm_select( $wpwmTemplate, 'white-color', 'Full White Color Only' );
				wpwm_select( $wpwmTemplate, 'black-striped', 'Dark Stripes' );
				wpwm_select( $wpwmTemplate, 'black-white-striped', 'White Stripes' );
				wpwm_select( $wpwmTemplate, 'white-striped', 'Full White Stripes' );
				wpwm_select( $wpwmTemplate, 'bootstrap', 'Bootstrap Style' );
				?>
            </select>
          </div>
          <div class="row">
            <label>Only For Fist Time Visit : </label>
            <select name="wpwm_onlyFirstVisit">
              <?php
				$wpwm_onlyFirstVisit = get_option( 'wpwm_onlyFirstVisit' );
				wpwm_select( $wpwm_onlyFirstVisit, 'on', 'Enable' );
                wpwm_select( $wpwm_onlyFirstVisit, 'off', 'Disable' );
				?>
            </select>
          </div>
    <input type="submit" value="save changes" />
    </form>
    </div>
    </div>
    
    <?php
	echo '</div>
	<div id="wpwm_side">
	<div class="wpwm_box">';
	echo '<a href="http://www.a1netsolutions.com/Products/WordPress-Plugins" target="_blank" class="wpwm_advert"><img src="',$wpwm_conf["VEWPATH"],'/img/wp-advert-1.png" /></a>';
	echo '</div><div class="wpwm_box">';
	echo '<a href="http://www.ahsanulkabir.com/request-quote/" target="_blank" class="wpwm_advert"><img src="',$wpwm_conf["VEWPATH"],'/img/wp-advert-2.png" /></a>';
	echo '</div>
	</div>
	<div class="wpwm_clr"></div>
	</div>';
}

function wpwm_content()
{
	$wpwm_ststs = get_option('wpwm_ststs');
	if($wpwm_ststs == 'on')
	{
		$wpwm_onlyFirstVisit = get_option( 'wpwm_onlyFirstVisit' );
		if( $wpwm_onlyFirstVisit == "on" )
		{
			if( (!isset($_SESSION["wpwm_session"])) || ($_SESSION["wpwm_session"] != 'off') )
			{
				wpwm_popupFirst();
			}
		}
		else
		{
			wpwm_popupFirst();
		}
	}
}

function wpwm_popupFirst()
{
	$wpwm_loc = get_option( 'wpwm_log' );
	if(get_option('wpwm_ststs') == 'on')
	{
		if( $wpwm_loc == 'log' )
		{
			if ( is_user_logged_in() )
			{
				wpwm_popupCheckPage();
			}
		}
		elseif( $wpwm_loc == 'nlog' )
		{
			if ( !is_user_logged_in() )
			{
				wpwm_popupCheckPage();
			}
		}
		else
		{
			wpwm_popupCheckPage();
		}
	}
}

function wpwm_popupTemp()
{
	$wpwmPID = get_option( 'wpwm_postsid' );
	$wpwmTemplate = get_option('wpwmTemplate');
	$content_post = get_post($wpwmPID);
	$wpwmContent = $content_post->post_content;
	$wpwmContent = apply_filters('the_content', $wpwmContent);
	$wpwmContent = str_replace(']]>', ']]&gt;', $wpwmContent);
	$session_id = session_id();
	echo '
	<div id="wpwm_hideBody" class="'.$wpwmTemplate.'-body">
	  <div id="wpwm_popBoxOut">
		<div class="wpwm-box">
		  <div id="wpwm_popBox">
			<span id="wpwm_popClose">×</span>
			'.$wpwmContent.'
			<div class="cl_fix"></div>
		  </div>
		</div>
	  </div>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery("html, body").css({"overflow": "hidden"});
	});
	</script>
	';
	echo '<span>',get_option('wpwm_dev1'),get_option('wpwm_dev2'),get_option('wpwm_dev3'),'</span>';
}

function wpwm_popupCheckPage()
{
	  if( ( get_option( 'wpwm_loc' ) ) == 'home' )
	  {
		  if( is_front_page() )
		  {
			  wpwm_popupTemp();
		  }
	  }
	  else
	  {
		  wpwm_popupTemp();
	  }
}

function wpwm_sessionID()
{
	if(!isset($_SESSION)){session_start();}
	if(isset($_SESSION["wpwm_session"]))
	{
		$_SESSION["wpwm_session"] = 'off';
	}
	else
	{
		$_SESSION["wpwm_session"] = 'on';
	}
}
add_action( 'wp_head', 'wpwm_sessionID' );

function wpwm_posts_init()
{
  $args = array
  (
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => false, 
    'show_in_menu' => false, 
    'rewrite' => array( 'slug' => 'wpwmposts' ),
    'capability_type' => 'post',
    'has_archive' => false, 
    'supports' => array( 'title', 'editor', 'excerpt' )
  ); 
  register_post_type( 'wpwmposts', $args );
}
add_action( 'init', 'wpwm_posts_init' );

function wpwm_getCurrentUser()
{
	if (function_exists('wp_get_current_user'))
	{
		return wp_get_current_user();
	}
	else if (function_exists('get_currentuserinfo'))
	{
		global $userdata;
		get_currentuserinfo();
		return $userdata;
	}
	else
	{
		$user_login = $_COOKIE["USER_COOKIE"];
		$current_user = $wpdb->get_results("SELECT * FROM `".$wpdb->users."` WHERE `user_login` = '".$user_login."' ;");
		return $current_user;
	}
}

function wpwm_printCreatePost($inputContent)
{
	$newPostAuthor = wpwm_getCurrentUser();
	$newPostArg = array
	(
		'post_author' => $newPostAuthor->ID,
		'post_content' => $inputContent,
		'post_status' => 'publish',
		'post_type' => 'wpwmposts'
	);
	$new_post_id = wp_insert_post($newPostArg);
	return $new_post_id;
}

function wpwm_updatePost($inputContent, $id)
{
	$newPostAuthor = wpwm_getCurrentUser();
	$newPostArg = array
	(
		'ID' => $id,
		'post_author' => $newPostAuthor->ID,
		'post_content' => $inputContent,
		'post_status' => 'publish',
		'post_type' => 'wpwmposts'
	);
	$new_post_id = wp_insert_post($newPostArg);
	return $new_post_id;
}

add_action('wp_footer', 'wpwm_content', 100);
register_activation_hook(__FILE__, 'wpwm_activate');

?>