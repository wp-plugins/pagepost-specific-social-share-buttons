<?php
$prefix = 'ab_';
$meta_box = array(
    'id' => 'ppss-meta-box',
    'title' => 'Page/Post Specific Social Share',
    'page' =>  'post',
    'context' => 'side',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => 'Checkbox',
            'id' => $prefix . 'checkbox',
            'type' => 'checkbox',
			'value' => 'yes'
        )
    )
);

add_action('add_meta_boxes', 'ppss_add_box');
function ppss_add_box() {
    global $meta_box;

    add_meta_box($meta_box['id'], '<span style="color:#720DAA;">'.$meta_box['title'].'</span>', 'ppss_show_box', $meta_box['page'], $meta_box['context'], $meta_box['priority']);
	add_meta_box($meta_box['id'], '<span style="color:#720DAA;">'.$meta_box['title'].'</span>', 'ppss_show_box', 'page', $meta_box['context'], $meta_box['priority']);
}
function ppss_show_box() {
    global $meta_box, $post;
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo '<table class="form-table">';

    foreach ($meta_box['fields'] as $field) {
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>',
                '<th style=""><label for="', $field['id'], '">Hide social share buttons on this post / page:</label></th>',
                '<td>';
	                echo '<input type="checkbox" value="',$field['value'],'" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
        echo     '<td>',
            '</tr>';
    }
    echo '</table>';
}

add_action('save_post', 'mytheme_save_data');

function mytheme_save_data($post_id) {
    global $meta_box;

    if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];

        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

function ppss_twitter_facebook_admin_menu() {
add_options_page('Page/Post Specific Social Share', 'Page/Post Specific Social Share', 'manage_options',
'ppss-social-share', 'ppss_twitter_facebook_admin_page');
}

function ppss_twitter_facebook_admin_page() {

	$option_name = 'ppss_social_share';
if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

$active_buttons = array(
		'facebook_like'=>'Facebook like',
		'twitter'=>'Twitter',
		'stumbleupon'=>'Stumbleupon',
		'Google_plusone'=>'Google PlusOne',
		'linkedin'=>'LinkedIn',
		'pinterest'=>'Pinterest'
	);	

$show_in = array(
		'posts'=>'Single posts',
		'pages'=>'Pages',
		'home_page'=>'Home page',
		'tags'=>'Tags',
		'categories'=>'Categories',
		'authors'=>'Author archives',
		'search'=>'Search results',
		'date_arch'=>'Archives'
	);
	
	$out = '';
	
	if( isset($_POST['ppss_social_share_position'])) {
		$option = array();
		
		$option['auto'] = (isset($_POST['ppss_social_share_auto_display']) and $_POST['ppss_social_share_auto_display']=='on') ? true : false;

		foreach (array_keys($active_buttons) as $item) {
			$option['active_buttons'][$item] = (isset($_POST['ppss_social_share_active_'.$item]) and $_POST['ppss_social_share_active_'.$item]=='on') ? true : false;
		}	
		foreach (array_keys($show_in) as $item) {
			$option['show_in'][$item] = (isset($_POST['ppss_social_share_show_'.$item]) and $_POST['ppss_social_share_show_'.$item]=='on') ? true : false;
		}
		$option['position'] = esc_html($_POST['ppss_social_share_position']);
		$option['border'] = esc_html($_POST['ppss_social_share_border']);
		$option['bkcolor'] = (isset($_POST['ppss_social_share_background_color']) and $_POST['ppss_social_share_background_color']=='on') ? true : false;
		
		$option['enabler'] = (isset($_POST['ppss_social_share_enabler']) and $_POST['ppss_social_share_enabler']=='on') ? true : false;
		
		$option['bkcolor_value'] = esc_html($_POST['ppss_social_share_bkcolor_value']);
		$option['jsload'] = (isset($_POST['ppss_social_share_javascript_load']) and $_POST['ppss_social_share_javascript_load']=='on') ? true : false;
		$option['mobdev'] = (isset($_POST['ppss_social_share_mobile_device']) and $_POST['ppss_social_share_mobile_device']=='on') ? true : false;
		$option['twitter_id'] = esc_html($_POST['ppss_social_share_twitter_id']);
		$option['custom_code'] = stripslashes($_POST['ppss_social_share_custom_code']);
		$option['left_space'] = esc_html($_POST['ppss_social_share_left_space']);
		$option['bottom_space'] = esc_html($_POST['ppss_social_share_bottom_space']);
		$option['float_position'] = esc_html($_POST['ppss_social_share_float_position']);
		$option['twitter_count'] = (isset($_POST['ppss_social_share_twitter_count']) and $_POST['ppss_social_share_twitter_count']=='on') ? true : false;
		$option['google_count'] = (isset($_POST['ppss_social_share_google_count']) and $_POST['ppss_social_share_google_count']=='on') ? true : false;
		$option['linkedin_count'] = (isset($_POST['ppss_social_share_linkedin_count']) and $_POST['ppss_social_share_linkedin_count']=='on') ? true : false;
		$option['pinterest_count'] = (isset($_POST['ppss_social_share_pinterest_count']) and $_POST['ppss_social_share_pinterest_count']=='on') ? true : false;
		$option['google_width'] = esc_html($_POST['ppss_social_share_google_width']);
		$option['facebook_like_width'] = esc_html($_POST['ppss_social_share_facebook_like_width']);
		$option['twitter_width'] = esc_html($_POST['ppss_social_share_twitter_width']);
		$option['linkedin_width'] = esc_html($_POST['ppss_social_share_linkedin_width']);
		$option['pinterest_width'] = esc_html($_POST['ppss_social_share_pinterest_width']);
		$option['stumbleupon_width'] = esc_html($_POST['ppss_social_share_stumbleupon_width']);
		update_option($option_name, $option);
		// Put a settings updated message on the screen
		$out .= '<div class="updated"><p><strong>'.__('Settings saved.', 'menu-test' ).'</strong></p></div>';
	}

	$option = ppss_social_share_get_options_stored();
	
	$sel_above = ($option['position']=='above') ? 'selected="selected"' : '';
	$sel_below = ($option['position']=='below') ? 'selected="selected"' : '';
	$sel_both  = ($option['position']=='both' ) ? 'selected="selected"' : '';
	
	$sel_flat = ($option['border']=='flat') ? 'selected="selected"' : '';
	$sel_round = ($option['border']=='round') ? 'selected="selected"' : '';
	$sel_none  = ($option['border']=='none' ) ? 'selected="selected"' : '';
	
	$sel_fixed = ($option['float_position']=='fixed') ? 'selected="selected"' : '';
	$sel_absolute = ($option['float_position']=='absolute') ? 'selected="selected"' : '';
	
	$bkcolor = ($option['bkcolor']) ? 'checked="checked"' : '';
	
	$enabler = ($option['enabler']) ? 'checked="checked"' : '';
	
	$jsload =  ($option['jsload']) ? 'checked="checked"' : '';
	$mobdev =  ($option['mobdev']) ? 'checked="checked"' : '';
	$auto =    ($option['auto']) ? 'checked="checked"' : '';
	$google_count = ($option['google_count']) ? 'checked="checked"' : '';
	$twitter_count = ($option['twitter_count']) ? 'checked="checked"' : '';
	$linkedin_count = ($option['linkedin_count']) ? 'checked="checked"' : '';
	$pinterest_count = ($option['pinterest_count']) ? 'checked="checked"' : '';
	
	$enablerColor = $enabler ? '#0B932D' : '#ff0000';
	
	$out .= '
	<div class="wrap">

	<h2 style="background:#EDF4F7;padding:7px; font-family:Tahoma;">'.__( 'Page/Post Specific Social Share Buttons', 'menu-test' ).'</h2>
	<div id="poststuff" style="padding-top:10px; position:relative;">
		<div style="float:left; width:74%; padding-right:1%;">
	<form name="form1" method="post" action="">
	<div class="postbox">
	<h3 style="background:#E1F4A2;">'.__("Page/Post Specific Social Share Options", 'menu-test' ).'</h3>
	<div class="inside">
	<table>

	<tr><td style="padding:15px 0 20px 0; font-weight:bold; font-size:15px; color:'.$enablerColor.'" valign="top">'.__("Enable share buttons", 'menu-test' ).':</td>
	<td style="padding:15px 0 20px 0;">
		<input type="checkbox" name="ppss_social_share_enabler" '.$enabler.' />
	</td></tr>

	<tr><td valign="top" style="width:180px;">'.__("Active share buttons", 'menu-test' ).':</td>
	<td style="padding-bottom:30px;">';
	
	foreach ($active_buttons as $name => $text) {
		$checked = ($option['active_buttons'][$name]) ? 'checked="checked"' : '';
		$out .= '<div style="width:150px; float:left;">
				<input type="checkbox" name="ppss_social_share_active_'.$name.'" '.$checked.' /> '
				. __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';

	}
	
	$out .= '</td></tr>
			<tr><td valign="top" style="width:180px;">'.__("Show buttons in these pages", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">';

			foreach ($show_in as $name => $text) {
				$checked = ($option['show_in'][$name]) ? 'checked="checked"' : '';
				$out .= '<div style="width:150px; float:left;">
						<input type="checkbox" name="ppss_social_share_show_'.$name.'" '.$checked.' /> '
						. __($text, 'menu-test' ).' &nbsp;&nbsp;</div>';
			}

	$out .= '</td></tr>';
	$out .= '<tr><td style="padding-bottom:20px;" valign="top">'.__("Position", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;"><select name="ppss_social_share_position">
		<option value="above" '.$sel_above.' > '.__('Above the post', 'menu-test' ).'</option>
		<option value="below" '.$sel_below.' > '.__('Below the post', 'menu-test' ).'</option>
		<option value="both"  '.$sel_both.'  > '.__('Above and Below the post', 'menu-test' ).'</option>
		</select>
	</td></tr>
	
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Border Style", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;"><select name="ppss_social_share_border">
		<option value="flat"  '.$sel_flat.' > '.__('Flat Border', 'menu-test' ).'</option>
		<option value="round" '.$sel_round.' > '.__('Round Border', 'menu-test' ).'</option>
		<option value="none"  '.$sel_none.'  > '.__('No Border', 'menu-test' ).'</option>
		</select>
	</td></tr>
	
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Show Background Color", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
		<input type="checkbox" name="ppss_social_share_background_color" '.$bkcolor.' />
	</td></tr>
	
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Background Color", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
	<input type="text" name="ppss_social_share_bkcolor_value" value="'.$option['bkcolor_value'].'" size="10">  
		 <span class="description">'.__("Default Color wont disappoint you :-)", 'menu-test' ).'</span>
	</td></tr> 
	
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Load Javascript in Footer", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
		<input type="checkbox" name="ppss_social_share_javascript_load" '.$jsload.' />
		<span class="description">'.__("(Recommended, else loaded in header)", 'menu-test' ).'</span>
	</td></tr>
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Disable on Mobile Device", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
		<input type="checkbox" name="ppss_social_share_mobile_device" '.$mobdev.' />
		<span class="description">'.__("(Disable on iPad,iPhone,Blackberry,Nokia,Opera Mini and Android)", 'menu-test' ).'</span>
	</td></tr>
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Your Twitter ID", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
	<input type="text" name="ppss_social_share_twitter_id" value="'.$option['twitter_id'].'" size="30">  
		 <span class="description">'.__("Specify your twitter id without @", 'menu-test' ).'</span>
	</td></tr> 
	<tr><td style="padding-bottom:20px;" valign="top">'.__("Your Custom code", 'menu-test' ).':</td>
	<td style="padding-bottom:20px;">
	<textarea name="ppss_social_share_custom_code" rows="6" cols="60">'.$option['custom_code'].'</textarea>
	</td></tr> 
	</table>
	</div>
	</div>
	<div class="postbox">
	<h3 style="background:#E1F4A2;">'.__("Adjust Width and Count Display", 'menu-test' ).'</h3>
	<div class="inside">
		<table>
		<tr><td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Facebook Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_facebook_like_width" value="'.stripslashes($option['facebook_like_width']).'" size="5">px<br />
			</td>
			<td style="padding-bottom:20px; padding-left:50px; padding-right:10px;" valign="top">'.__("Google +1 Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_google_width" value="'.stripslashes($option['google_width']).'" size="5">px<br />
			</td>
			<td style="padding-bottom:20px; padding-left:5px; padding-right:10px;" valign="top">'.__("Stumbleupon Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_stumbleupon_width" value="'.stripslashes($option['stumbleupon_width']).'" size="5"> px <br />
			</td>	
		</tr>
		<tr><td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Twitter Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_twitter_width" value="'.stripslashes($option['twitter_width']).'" size="5"> px <br />
			</td>
			<td style="padding-bottom:20px; padding-left:50px; padding-right:10px;" valign="top">'.__("Linkedin Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_linkedin_width" value="'.stripslashes($option['linkedin_width']).'" size="5"> px <br />
			</td>	
			<td style="padding-bottom:20px; padding-left:5px; padding-right:10px;" valign="top">'.__("Pinterest Button width", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="text" name="ppss_social_share_pinterest_width" value="'.stripslashes($option['pinterest_width']).'" size="5"> px <br />
			</td>	
		</tr>
		<tr><td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Google +1 counter", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="checkbox" name="ppss_social_share_google_count" '.$google_count.' />
			</td>
			<td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Pinterest counter", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="checkbox" name="ppss_social_share_pinterest_count" '.$pinterest_count.' />
			</td>	
		</tr>
		<tr><td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("Twitter counter", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="checkbox" name="ppss_social_share_twitter_count" '.$twitter_count.' />
			</td>
			<td style="padding-bottom:20px; padding-right:10px;" valign="top">'.__("LinkedIn counter", 'menu-test' ).':</td>
			<td style="padding-bottom:20px;">
				<input type="checkbox" name="ppss_social_share_linkedin_count" '.$linkedin_count.' />
			</td>	
		</tr>
		</table>
	</div>
	</div>
	
	<tr><td valign="top" colspan="2">
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="'.esc_attr('Save Changes').'" />
	</p>
	</td></tr>
	</form>
	</div>
	<div style="float:right; width:25%;">
	
	<div class="postbox">
	<h3 style="background:#E1F4A2;">'.__("Message", 'menu-test' ).'</h3>
	<div class="inside">
	<table>
	<tr><td  align="justify">
	<ul>
	<li>Thank you for using <strong>Page/Post Specific Social Share</strong> plugin.</li> 
	<li style="font-weight:bold;">Visit: <a href="http://www.completewebresources.com/page-post-specific-social-share-wp-plugin/" target="_blank">Completewebresources.com</a></li>
	</ul>
	</td></tr>
	</tr>
	</table>
	</div>
	</div>
	</div>
	';
	echo $out; 
}

function ppss_social_share_get_options_stored () {
	$option = get_option('ppss_social_share');
	 
	if ($option===false) {
		$option = ppss_social_share_get_options_default();
		add_option('ppss_social_share', $option);
	} else if ($option=='above' or $option=='below') {
		$option = ppss_social_share_get_options_default($option);
	} else if(!is_array($option)) {
		$option = json_decode($option, true);
	}

	if (!isset($option['bkcolor'])) {
		$option['bkcolor'] = true;
	}
	
	if (!isset($option['auto'])) {
		$option['auto'] = true;
	}
	if (!isset($option['bkcolor_value'])) {
		$option['bkcolor_value'] = '#F0F4F9';
	}
	if (!isset($option['left_space'])) {
		$option['left_space'] = '60px';
	}
	if (!isset($option['bottom_space'])) {
		$option['bottom_space'] = '20%';
	}
	
	if (!isset($option['jsload'])) {
		$option['jsload'] = true;
	}
	if (!isset($option['mobdev'])) {
		$option['mobdev'] = true;
	}
	
	if (!isset($option['facebook_like_width'])) {
		$option['facebook_like_width'] = '85';
	}
	if (!isset($option['twitter_width'])) {
		$option['twitter_width'] = '95';
	}
	if (!isset($option['google_width'])) {
		$option['google_width'] = '80';
	}
	if (!isset($option['linkedin_width'])) {
		$option['linkedin_width'] = '105';
	}
	if (!isset($option['pinterest_width'])) {
		$option['pinterest_width'] = '105';
	}
	if (!isset($option['stumbleupon_width'])) {
		$option['stumbleupon_width'] = '85';
	}
	if (!isset($option['twitter_count'])) {
		$option['twitter_count'] = true;
	}
	if (!isset($option['linkedin_count'])) {
		$option['linkedin_count'] = true;
	}
	if (!isset($option['pinterest_count'])) {
		$option['pinterest_count'] = true;
	}
	if (!isset($option['google_count'])) {
		$option['google_count'] = true;
	}	
	return $option;
}

function ppss_social_share_get_options_default ($position='above', $border='none', $color='transparent',$left_space='60px',$bottom_space='40%', $float_position='fixed') {
	$option = array();
	$option['auto'] = true;
	$option['active_buttons'] = array('facebook_like'=>true, 'twitter'=>true, 'stumbleupon'=>true, 'Google_plusone'=>true, 'linkedin'=>true,'pinterest'=>false);
	$option['show_in'] = array('posts'=>true, 'pages'=>true, 'home_page'=>true, 'tags'=>true, 'categories'=>true,  'authors'=>true, 'search'=>true,'date_arch'=>true);
	$option['position'] = $position;
	$option['border'] = $border;
	$option['bkcolor'] = true;
	$option['enabler'] = true;
	$option['bkcolor_value'] = $color;
	$option['jsload'] = true;
	$option['mobdev'] = true;
	$option['left_space'] = $left_space;
	$option['bottom_space'] = $bottom_space;
	$option['float_position'] = $float_position;
	$option['facebook_like_width'] = '85';
	$option['twitter_width'] = '95';
	$option['linkedin_width'] = '105';
	$option['pinterest_width'] = '105';
	$option['stumbleupon_width'] = '85';
	$option['google_width'] = '80';
	$option['google_count'] = true;
	$option['twitter_count'] = true;
	$option['linkedin_count'] = true;
	$option['pinterest_count'] = true;
	return $option;
}
?>