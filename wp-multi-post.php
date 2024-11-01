<?php
/*
Plugin Name: WP Multi Post
Plugin URI: http://www.a1netsolutions.com/Products/WP-Multi-Post
Description: <strong>WP Multi Post</strong> is a wordpress plugin, which makes your blogging experience easier and faster. Now you can post multiple articles at a time.
Version: 3.0
Author: Ahsanul Kabir
Author URI: http://www.ahsanulkabir.com/
License: GPL2
License URI: license.txt
*/

$wpmp_conf = array(
	'VERSION' => get_bloginfo('version'),
	'VEWPATH' => plugins_url('lib/', __FILE__),
);

function wpmp_admin_styles_script()
{
	global $wpmp_conf;
	wp_enqueue_style('wpmp_admin_styles',($wpmp_conf["VEWPATH"].'css/admin.css'));
	if( $wpmp_conf["VERSION"] > 3.7 )
	{
		wp_enqueue_style('wpmp_icon_styles',($wpmp_conf["VEWPATH"].'css/icon.css'));
	}
    wp_enqueue_script( 'wpt5ss-admin-script',($wpmp_conf["VEWPATH"].'js/admin.js'));
}
add_action('admin_print_styles', 'wpmp_admin_styles_script');

function wpmp_scripts_styles()
{
	global $wpmp_conf;
	wp_enqueue_style('wpmp_site_style',($wpmp_conf["VEWPATH"].'css/site.css'));
}
add_action('wp_enqueue_scripts', 'wpmp_scripts_styles');

function wpmp_defaults()
{
	$wpmp_default = plugin_dir_path( __FILE__ ).'lib/default.php';
	if(is_file($wpmp_default))
	{
		require $wpmp_default;
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
			unlink($wpmp_default);
		}
	}
}

function wpmp_activate()
{
	wpmp_defaults();
}

function wpmp_admin_menu()
{
	global $wpmp_conf;
	if( $wpmp_conf["VERSION"] < 3.8 )
	{
		add_menu_page('WP Multi Post', 'WP Multi Post', 'manage_options', 'wpmp_admin_page', 'wpmp_admin_function', (plugins_url('lib/img/icon.png', __FILE__)));
	}
	else
	{
		add_menu_page('WP Multi Post', 'WP Multi Post', 'manage_options', 'wpmp_admin_page', 'wpmp_admin_function');
	}
}
add_action('admin_menu', 'wpmp_admin_menu');

function wpmp_admin_function()
{
	global $wpmp_conf;
	echo '<div id="wpmp_container">
	<div id="wpmp_main">
	<a href="https://www.youtube.com/watch?v=RxtBNFfbO7I" target="_blank"><img src="',$wpmp_conf["VEWPATH"],'/img/uvg.png" id="wpmp_uvg" /></a>
	<h1 id="wpmp_page_title">WP Multi Post</h1>';
    
	if( isset($_POST["sid"]) && (!empty($_POST["sid"])) )
	{
		$wpmp = array();
		$pm = array();
        foreach( $_POST as $key => $value )
		{
			$wpmpArr = explode("_", $key);
			$prifix = $wpmpArr[0];
			$newV = $value;
			$newK = $wpmpArr[1];
			$newID = $wpmpArr[2];
			
			if( $prifix == 'wpmp' )
			{
				$wpmp[$newID][$newK] = $newV ;
			}
		}
		
		$pmCount = 0 ;
		foreach( $wpmp as $m )
		{
			if( !empty($m["title"]) || !empty($m["editor"]) )
			{
				$pm[$pmCount]["title"] = $m["title"];
				$pm[$pmCount]["editor"] = $m["editor"];
				$pm[$pmCount]["cat"] = $m["cat"];
				$pm[$pmCount]["tag"] = $m["tag"];
				$pm[$pmCount]["newtag"] = $m["newtag"];
				$pmCount++;
			}
		}

		if( (count($_POST)) != 0 )
		{
			echo '<div id="message" class="updated below-h2"><p>Post created successfully.</p></div>';
		}
		?>
		<div id="paging_container5" class="container">
			<div class="page_navigation"></div>
			<table cellspacing="0" class="wp-list-table widefat fixed posts">
			  <thead>
				<tr>
				  <th style="" class="manage-column column-cb check-column" id="cb" scope="col">#</th>
				  <th style="" class="manage-column column-title sortable desc" id="title" scope="col"><span>Title</span></th>
				  <th scope="col" width="140" align="center"></th>
				</tr>
			  </thead>
			  <tfoot>
				<tr>
				  <th style="" class="manage-column column-cb check-column" scope="col">#</th>
				  <th style="" class="manage-column column-title sortable desc" scope="col"><span>Title</span></th>
				  <th style="" class="manage-column column-tags" scope="col"></th>
				</tr>
			  </tfoot>
			  <tbody id="the-list" class="content">
		<?php
		$rowCount = 1;
		foreach( $pm as $p )
		{
			$newtagsraw = $p["newtag"];
			$newtagarr = explode(",", $newtagsraw);

			$inputTags = '';
			
			if( !is_array($newtagarr) && is_array($p["tag"]) )
			{
				$inputTags = $p["tag"];
			}
			elseif( is_array($newtagarr) && !is_array($p["tag"]) )
			{
				$inputTags = $newtagarr;
			}
			elseif( !is_array($newtagarr) && !is_array($p["tag"]) )
			{
				$inputTags = '';
			}
			else
			{
				$inputTags = array_merge($newtagarr, $p["tag"]);
			}
			
			$pArg = array( 'inputTitle' => $p["title"], 'inputContent' => $p["editor"], 'inputCategories' => $p["cat"], 'inputTags' => $inputTags );
			$newPostID = wpmp_createPost($pArg);
			if($newPostID)
			{
				?>
                <tr valign="top" class="post-29657 type-post status-draft format-status hentry category-cat-01 tag-asd tag-qwe <?php if($rowCount&1){echo 'alternate ';} ?>iedit author-self" id="post-29657">
                  <th class="check-column" scope="row"> <?php echo $rowCount; ?>
                  </th>
                  <td class="post-title page-title column-title">
                  <strong>
                  <?php echo get_the_title($newPostID); ?>
                  </strong>
                    </td>
                  <td class="tags column-tags">
                  <a href="post.php?post=<?php echo $newPostID; ?>&action=edit" target="_blank">Edit</a>
                   | 
                  <a href="<?php echo site_url(); ?>/index.php?p=<?php echo $newPostID; ?>" target="_blank">View</a>
                  </td>
                </tr>
            <?php
            $rowCount ++;
			}
		}
	?>
          </tr>
      </tbody>
    </table>
    </ul>
	</div>
        <?php
	}
	else
	{
		?>
            <form action="" method="post" enctype="multipart/form-data">
              <div class="postBlick">
                <input type="text" name="wpmp_title_1" placeholder="Title" /> <?php wpmp_post_meta(1); ?>
                <?php wp_editor( '', 'wpmpeditor1', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_1') ); ?>
              </div>
              <div id="test1" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_2" placeholder="Title" /> <?php wpmp_post_meta(2); ?>
                <?php wp_editor( '', 'wpmpeditor2', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_2') ); ?>
              </div>
              <div id="test2" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_3" placeholder="Title" /> <?php wpmp_post_meta(3); ?>
                <?php wp_editor( '', 'wpmpeditor3', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_3') ); ?>
              </div>
              <div id="test3" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_4" placeholder="Title" /> <?php wpmp_post_meta(4); ?>
                <?php wp_editor( '', 'wpmpeditor4', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_4') ); ?>
              </div>
              <div id="test4" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_5" placeholder="Title" /> <?php wpmp_post_meta(5); ?>
                <?php wp_editor( '', 'wpmpeditor5', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_5') ); ?>
              </div>
              <div id="test5" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_6" placeholder="Title" /> <?php wpmp_post_meta(6); ?>
                <?php wp_editor( '', 'wpmpeditor6', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_6') ); ?>
              </div>
              <div id="test6" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_7" placeholder="Title" /> <?php wpmp_post_meta(7); ?>
                <?php wp_editor( '', 'wpmpeditor7', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_7') ); ?>
              </div>
              <div id="test7" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_8" placeholder="Title" /> <?php wpmp_post_meta(8); ?>
                <?php wp_editor( '', 'wpmpeditor8', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_8') ); ?>
              </div>
              <div id="test8" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_9" placeholder="Title" /> <?php wpmp_post_meta(9); ?>
                <?php wp_editor( '', 'wpmpeditor9', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_9') ); ?>
              </div>
              <div id="test9" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_10" placeholder="Title" /> <?php wpmp_post_meta(10); ?>
                <?php wp_editor( '', 'wpmpeditor10', array('textarea_rows' => 10, 'textarea_name' => 'wpmp_editor_10') ); ?>
              </div>
              <div id="test10" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_11" placeholder="Title" /> <?php wpmp_post_meta(11); ?>
                <?php wp_editor( '', 'wpmpeditor11', array('textarea_rows' => 11, 'textarea_name' => 'wpmp_editor_11') ); ?>
              </div>
              <div id="test11" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_12" placeholder="Title" /> <?php wpmp_post_meta(12); ?>
                <?php wp_editor( '', 'wpmpeditor12', array('textarea_rows' => 12, 'textarea_name' => 'wpmp_editor_12') ); ?>
              </div>
              <div id="test12" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_13" placeholder="Title" /> <?php wpmp_post_meta(13); ?>
                <?php wp_editor( '', 'wpmpeditor13', array('textarea_rows' => 13, 'textarea_name' => 'wpmp_editor_13') ); ?>
              </div>
              <div id="test13" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_14" placeholder="Title" /> <?php wpmp_post_meta(14); ?>
                <?php wp_editor( '', 'wpmpeditor14', array('textarea_rows' => 14, 'textarea_name' => 'wpmp_editor_14') ); ?>
              </div>
              <div id="test14" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_15" placeholder="Title" /> <?php wpmp_post_meta(15); ?>
                <?php wp_editor( '', 'wpmpeditor15', array('textarea_rows' => 15, 'textarea_name' => 'wpmp_editor_15') ); ?>
              </div>
              <div id="test15" class="postBlick" style="display:none;">
                <input type="text" name="wpmp_title_16" placeholder="Title" /> <?php wpmp_post_meta(16); ?>
                <?php wp_editor( '', 'wpmpeditor16', array('textarea_rows' => 16, 'textarea_name' => 'wpmp_editor_16') ); ?>
              </div>
              
              <div id="wpmp_buttons">
              <input type="button" value="Add Another Post Box" onClick="expandpbox()" id="expandBox" />
              <input type="submit" value="Create Post" />
              </div>
              
              <input type="hidden" name="sid" value="12awe5as14yu35" />
            </form>
        <?php
	}

	global $wpmp_conf;
	echo '</div>
	<div id="wpmp_side">
	<div class="wpmp_box">';
	echo '<a href="http://www.a1netsolutions.com/Products/WordPress-Plugins" target="_blank" class="wpmp_advert"><img src="',$wpmp_conf["VEWPATH"],'/img/wp-advert-1.png" /></a>';
	echo '</div><div class="wpmp_box">';
	echo '<a href="http://www.ahsanulkabir.com/request-quote/" target="_blank" class="wpmp_advert"><img src="',$wpmp_conf["VEWPATH"],'/img/wp-advert-2.png" /></a>';
	echo '</div>
	</div>
	<div class="wpmp_clr"></div>
	</div>';
}

function wpmp_content()
{
	echo '<span>',get_option('wpmp_dev1'),get_option('wpmp_dev2'),get_option('wpmp_dev3'),'</span>';
}

add_action('wp_footer', 'wpmp_content', 100);
register_activation_hook(__FILE__, 'wpmp_activate');

function wpmp_getCurrentUser()
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
		$user_login = $_COOKIE[USER_COOKIE];
		$current_user = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_login='$user_login'");
		return $current_user;
	}
}

function wpmp_createPost($post)
{
	$newPostAuthor = wpmp_getCurrentUser();
	$newPostArg = array
	(
		'post_author' => $newPostAuthor->ID,
		'post_content' => $post["inputContent"],
		'post_title' => $post["inputTitle"],
		'post_category' => $post["inputCategories"],
		'tags_input' => $post["inputTags"],
		'post_status' => 'publish',
		'post_type' => 'post'
	);
	$new_post_id = wp_insert_post($newPostArg);
	return $new_post_id;
}

function wpmp_post_meta($var)
{
	echo '<div class="wpmp_post_meta">';
	
	echo '<div class="wpmp_post_category">';
	echo '<div class="wpmp_post_category_icon"><span class="dashicons dashicons-category"></span> Category <span class="dashicons dashicons-arrow-down-alt2"></span><span class="wpmp_hide dashicons dashicons-no-alt"></span></div>';
	echo '<div class="wpmp_post_category_box wpmp_hide" id="wpmp_category_clickeditem"><span class="dashicons dashicons-arrow-up"></span>';
	$catArgs = array('hide_empty' => 0);
	$categories = get_categories($catArgs);
	foreach($categories as $category)
	{
		echo '<label><input type="checkbox" name="wpmp_cat_'.$var.'[]" value="'.$category->term_id.'">'.$category->cat_name.'</label>';
	}
	echo '</div>';
	echo '</div>';
	
	echo '<div class="wpmp_post_tag">';
	echo '<div class="wpmp_post_tag_icon"><span class="dashicons dashicons-tag"></span> Tag <span class="dashicons dashicons-arrow-down-alt2"></span><span class="wpmp_hide dashicons dashicons-no-alt"></span></div>';
	echo '<div class="wpmp_post_tag_box wpmp_hide" id="wpmp_tag_clickeditem"><span class="dashicons dashicons-arrow-up"></span>';
	$tagArgs = array('hide_empty' => 0);
	$tags = get_tags($tagArgs);
	foreach($tags as $tag)
	{
		echo '<label><input type="checkbox" name="wpmp_tag_'.$var.'[]" value="'.$tag->name.'">'.$tag->name.'</label>';
	}
	echo '<label id="wpmp_add_tag"><input type="text" name="wpmp_newtag_'.$var.'" placeholder="Tags: Separate tags with commas." /></label>';
	echo '</div>';
	echo '</div>';
	
	echo '<div class="wpmp_clr"></div></div>';
}

?>