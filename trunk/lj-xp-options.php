<?php
// set defaults
function ljxp_get_options() {
	$defaults = array(
			'host'				=> 'www.livejournal.com',
			'username'			=> '',
			'password'			=> '',
			'custom_name_on'	=> 0,
			'custom_name'		=> '',
			'crosspost'			=> 1,
			'content'			=> 'full',
			'privacy'			=> 'public',
			'comments'			=> 0,
			'tag'				=> '1',
			'more'				=> 'link',
			'community'			=> '',
			'skip_cats'			=> array(),
			'header_loc'		=> 0,		// 0 means top, 1 means bottom
			'custom_header'		=> '',
			'delete_private'	=> 1,
			'userpics'			=> array(),
			'cut_text'			=> __('Read the rest of this entry &raquo;', 'lj-xp'),
	);
	
	$options = get_option('ljxp');
	if (!is_array($options)) $options = array();
	
	// clean up options from old versions
	$old_options = get_option('ljxp_username');
	if (!empty($old_options)) {
		$old_option_list = array(	
					'ljxp_host',
					'ljxp_username',
					'ljxp_password',
					'ljxp_custom_name_on',
					'ljxp_custom_name',
					'ljxp_privacy',
					'ljxp_comments',
					'ljxp_tag',
					'ljxp_more',
					'ljxp_community',
					'ljxp_skip_cats',
					'ljxp_header_loc',
					'ljxp_custom_header',
					'ljxp_delete_private',
					'ljxp_userpics',
					'ljxp_cut_text',
					);
		$old_options = array();
		foreach ($old_option_list as $_opt ) {
			$newkey = str_replace('ljxp_', '', (string)$_opt);
			$old_options[$newkey] = get_option($_opt);
			delete_option($_opt);
		} 
		if (is_array($old_options))
			$options = array_merge( $old_options, $options );
	}
	
	// still need to get the defaults for the new settings, so we'll merge again
	return array_merge( $defaults, $options );
}

// Validation/sanitization. Add errors to $msg[].
function ljxp_validate_options($input) {
	$msg = array();
	$linkmsg = '';
	$msgtype = 'error';
		
	// trim
	if (!empty($input['host']))			$input['host'] = 			trim($input['host']);
	if (!empty($input['username']))		$input['username'] = 		trim($input['username']);
	if (!empty($input['custom_name']))	$input['custom_name'] = 	trim($input['custom_name']);
	if (!empty($input['community']))	$input['community'] = 		trim($input['community']);
	if (!empty($input['custom_header'])) $input['custom_header'] = 	trim($input['custom_header']);
		
	
	// If we're handling a submission, save the data
	if(isset($input['update_ljxp_options']) || isset($input['crosspost_all'])) {
		// Grab a list of all entries that have been crossposted
		$beenposted = get_posts(array('meta_key' => 'ljID', 'post_type' => 'any', 'post_status' => 'any', 'numberposts' => '-1'));
		foreach ($beenposted as $post) {
			$repost_ids[] = $post->ID;
		}
		
		// Set the update flag
		$need_update = 0;

		// compare to old options
		$options = ljxp_get_options();
		foreach ($input as $key => $val) {
			if ($val != $options[$key]) { // the option has changed
				
				// And then the custom actions
				switch ($key) { // this is kinda harsh, I guess
					case 'post' :
					case 'username' :
					case 'comments' :
					case 'community' :
							ljxp_delete_all($repost_ids);
					case 'custom_name_on' :
					case 'privacy' :
					case 'tag' :
					case 'more' :
					case 'custom_header' :
							$need_update = 1;
						break;
					case 'custom_name' :
							if (!empty($options['custom_name'])) {
								$need_update = 1;
							}
						break;
					default:
							continue;
						break;
				}
			}
		}

		if (!isset($input['post_category'])) $input['post_category'] = array();
		sort($options['skip_cats']);
		$new_skip_cats = array_diff(get_all_category_ids(), $input['post_category']);
		sort($new_skip_cats);
		if($options['skip_cats'] != $new_skip_cats) {
			$input['skip_cats'] = $new_skip_cats;
		}

		unset($new_skip_cats);
		unset($input['post_category']);

		if (!empty($input['password'])) {
			$input['password'] = md5($input['password']);
		}

		if ($need_update && isset($input['update_lj_options'])) {
			ljxp_post_all($repost_ids);
		}

		if(isset($input['crosspost_all'])) {
			ljxp_post_all();
		}
		
	} // if updated
	unset($input['crosspost_all']);
	unset($input['update_ljxp_options']);
	
	// If we are clearing the userpics, then get a new list of userpics from the server.
	if (isset($input['clear_userpics'])) {
		$input['userpics'] = array();
		$msg[] .= __('Userpic list cleared.', 'lj-xp');
		$msgtype = 'updated';
	}
	unset($input['clear_userpics']);

	if (isset($input['update_userpics'])) {
		$pics = ljxp_update_userpics($input['username']);
		$input['userpics'] = $pics['userpics'];
		$msg[] .= $pics['msg'];
		$msgtype = $pics['msgtype'];
	}
	unset($input['update_userpics']);
		
	// Send custom updated message
	$msg = implode('<br />', $msg);
	
	if (empty($msg)) {
		$msg = __('Settings saved.', 'lj-xp');
		$msgtype = 'updated';
	}
	
	add_settings_error( 'ljxp', 'ljxp', $msg, $msgtype );
	return $input;
}

// ---- Options Page -----

function ljxp_add_pages() {
	$pg = add_options_page("LiveJournal", "LiveJournal", 'manage_options', basename(__FILE__), 'ljxp_display_options');
	add_action("admin_head-$pg", 'ljxp_settings_css');
	// register setting
	add_action( 'admin_init', 'register_ljxp_settings' );	
}

// Add link to options page from plugin list
add_action('plugin_action_links_' . plugin_basename(__FILE__), 'lj_xp_plugin_actions');
function lj_xp_plugin_actions($links) {
	$new_links = array();
	$new_links[] = '<a href="options-general.php?page=lj_crosspost.php">' . __('Settings', 'google-analyticator') . '</a>';
	return array_merge($new_links, $links);
}

// Display the options page
function ljxp_display_options() {
?>
<div class="wrap">
	<form method="post" id="ljxp" action="options.php">
		<?php 
		settings_fields('ljxp');
		get_settings_errors( 'ljxp' );	
		settings_errors( 'ljxp' );
		$options = ljxp_get_options();
		?>
		<h2><?php _e('LiveJournal Crossposter Options', 'lj-xp'); ?></h2>
		<!-- <pre><?php //print_r($options); ?></pre>  -->
		<table class="form-table ui-tabs-panel">
			<tr valign="top">
				<th scope="row"><?php _e('LiveJournal-compliant host:', 'lj-xp') ?></th>
				<td><input name="ljxp[host]" type="text" id="host" value="<?php esc_attr_e($options['host']); ?>" size="40" /><br />
				<span class="description">
				<?php
				_e('If you are using a LiveJournal-compliant site other than LiveJournal (like DeadJournal), enter the domain name here. LiveJournal users can use the default value', 'lj-xp');
				?>
				</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('LJ Username', 'lj-xp'); ?></th>
				<td><input name="ljxp[username]" type="text" id="username" value="<?php esc_attr_e($options['username']); ?>" size="40" /></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('LJ Password', 'lj-xp'); ?></th>
				<td><input name="ljxp[password]" type="password" id="password" value="" size="40" /><br />
				<span  class="description"><?php
				_e('Only enter a value if you wish to change the stored password. Leaving this field blank will not erase any passwords already stored.', 'lj-xp');
				?></span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e('Community', 'lj-xp'); ?></th>
				<td><input name="ljxp[community]" type="text" id="community" value="<?php esc_attr_e($options['community']); ?>" size="40" /><br />
				<span class="description"><?php
				_e("If you wish your posts to be copied to a community, enter the community name here. Leaving this space blank will copy the posts to the specified user's journal instead", 'lj-xp');
				?></span>
				</td>
			</tr>
		</table>
		<fieldset class="options">
			<legend><h3><?php _e('Crosspost Default', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('If no crosspost setting is specified for an individual post:', 'lj-xp'); ?></th>
					<td>
					<label>
						<input name="ljxp[crosspost]" type="radio" value="1" <?php checked($options['crosspost'], 1); ?>/>
						<?php _e('Crosspost', 'lj-xp'); ?>
					</label>
					<br />
					<label>
						<input name="ljxp[crosspost]" type="radio" value="0" <?php checked($options['crosspost'], 0); ?>/>
						<?php _e('Do not crosspost', 'lj-xp'); ?>
					</label>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Content to crosspost:', 'lj-xp'); ?></th>
					<td>
					<label>
						<input name="ljxp[content]" type="radio" value="full" <?php checked($options['content'], 'full'); ?>/>
						<?php _e('Full text', 'lj-xp'); ?>
					</label>
					<br />
					<label>
						<input name="ljxp[content]" type="radio" value="excerpt" <?php checked($options['content'], 'excerpt'); ?>/>
						<?php _e('Excerpt only', 'lj-xp'); ?>
					</label>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('Blog Header', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('Crosspost header/footer location', 'lj-xp'); ?></th>
					<td>
					<label>
						<input name="ljxp[header_loc]" type="radio" value="0" <?php checked($options['header_loc'], 0); ?>/>
						<?php _e('Top of post', 'lj-xp'); ?>
					</label>
					<br />
					<label>
						<input name="ljxp[header_loc]" type="radio" value="1" <?php checked($options['header_loc'], 1); ?> /> 
						<?php _e('Bottom of post', 'lj-xp'); ?>
					</label></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Set blog name for crosspost header/footer', 'lj-xp'); ?></th>
					<td>
						<label>
							<input name="ljxp[custom_name_on]" type="radio" value="0" <?php checked($options['custom_name_on'], 0); ?>
							onclick="javascript: jQuery('#custom_name_row').hide('fast');"/>
							<?php printf(__('Use the title of your blog (%s)', 'lj-xp'), get_option('blogname')); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[custom_name_on]" type="radio" value="1" <?php checked($options['custom_name_on'], 1); ?> 
							onclick="javascript: jQuery('#custom_name_row').show('fast');"/>
							<?php _e('Use a custom title', 'lj-xp'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top" id="custom_name_row" <?php if ($options['custom_name_on']) echo 'style="display: table-row"'; else echo 'style="display: none"'; ?>>
					<th scope="row"><?php _e('Custom blog title', 'lj-xp'); ?></th>
					<td><input name="ljxp[custom_name]" type="text" id="custom_name" value="<?php esc_attr_e($options['custom_name']); ?>" size="40" /><br />
					<span class="description"><?php
					_e('If you chose to use a custom title above, enter the title here. This will be used in the header which links back to this site at the top of each post on the LiveJournal.', 'lj-xp');
					?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Custom crosspost header/footer', 'lj-xp'); ?></th>
					<td><textarea name="ljxp[custom_header]" id="custom_header" rows="3" cols="40"><?php echo esc_textarea($options['custom_header']); ?></textarea><br />
					<span  class="description"><?php
					_e("If you wish to use LJXP's dynamically generated post header/footer, you can ignore this setting. If you don't like the default crosspost header/footer, specify your own here. For flexibility, you can choose from a series of case-sensitive substitution strings, listed below:", 'lj-xp');
					?></span>
					<dl>
						<dt>[blog_name]</dt>
						<dd><?php _e('The title of your blog, as specified above', 'lj-xp'); ?></dd>

						<dt>[blog_link]</dt>
						<dd><?php _e("The URL of your blog's homepage", 'lj-xp'); ?></dd>

						<dt>[permalink]</dt>
						<dd><?php _e('A permanent URL to the post being crossposted', 'lj-xp'); ?></dd>

						<dt>[comments_link]</dt>
						<dd><?php _e('The URL for comments. Generally this is the permalink URL with #comments on the end', 'lj-xp'); ?></dd>

						<dt>[tags]</dt>
						<dd><?php _e('Tags with links list for the post', 'lj-xp'); ?></dd>

						<dt>[categories]</dt>
						<dd><?php _e('Categories with links list for the post', 'lj-xp'); ?></dd>

						<dt>[comments_count]</dt>
						<dd><?php _e('An image containing a comments counter', 'lj-xp'); ?></dd>

						<dt>[author]</dt>
						<dd><?php _e('The display name of the post\'s author', 'lj-xp'); ?></dd>
					</dl>
					</td>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('Post Privacy', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('Privacy level for all posts to LiveJournal', 'lj-xp'); ?></th>
					<td>
						<label>
							<input name="ljxp[privacy]" type="radio" value="public" <?php checked($options['privacy'], 'public'); ?>/>
							<?php _e('Public', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[privacy]" type="radio" value="private" <?php checked($options['privacy'], 'private'); ?> />
							<?php _e('Private', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[privacy]" type="radio" value="friends" <?php checked($options['privacy'], 'friends'); ?>/>
							<?php _e('Friends only', 'lj-xp'); ?>
						</label>
						<br />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('LiveJournal Comments', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('Should comments be allowed on LiveJournal?', 'lj-xp'); ?></th>
					<td>
					<label>
						<input name="ljxp[comments]" type="radio" value="0" <?php checked($options['comments'], 0); ?>/>
						<?php _e('Require users to comment on WordPress', 'lj-xp'); ?>
					</label>
					<br />
					<label>
						<input name="ljxp[comments]" type="radio" value="1" <?php checked($options['comments'], 1); ?>/>
						<?php _e('Allow comments on LiveJournal', 'lj-xp'); ?>
					</label>
					<br />
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('LiveJournal Tags', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('Tag entries on LiveJournal?', 'lj-xp'); ?></th>
					<td>
						<?php
							/* PHP-only comment:
							 *
							 * Yes, 1 -> 3 -> 2 -> 0 is a wierd order, but
							 * if categories = 1 and tags = 2,
							 * nothing would equal 0
							 * and
							 * tags+categories = 3
							 */
						?>
						<label>
							<input name="ljxp[tag]" type="radio" value="1" <?php checked($options['tag'], 1); ?>/>
							<?php _e('Tag LiveJournal entries with WordPress categories only', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[tag]" type="radio" value="3" <?php checked($options['tag'], 3); ?>/>
							<?php _e('Tag LiveJournal entries with WordPress categories and tags', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[tag]" type="radio" value="2" <?php checked($options['tag'], 2); ?>/>
							<?php _e('Tag LiveJournal entries with WordPress tags only', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[tag]" type="radio" value="0" <?php checked($options['tag'], 0); ?>/>
							<?php _e('Do not tag LiveJournal entries', 'lj-xp'); ?>
						</label>
						<br />
						<span class="description">
						<?php
						_e('You may with to disable this feature if you are posting in an alphabet other than the Roman alphabet. LiveJournal does not seem to support non-Roman alphabets in tag names.', 'lj-xp');
						?>
						</span>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('Handling of &lt;!--More--&gt;', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('How should LJXP handle More tags?', 'lj-xp'); ?></th>
					<td>
						<label>
							<input name="ljxp[more]" type="radio" value="link" <?php checked($options['more'], 'link'); ?>/>
							<?php _e('Link back to WordPress', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[more]" type="radio" value="lj-cut" <?php checked($options['more'], 'lj-cut'); ?>/>
							<?php _e('Use an lj-cut', 'lj-xp'); ?>
						</label>
						<br />
						<label>
							<input name="ljxp[more]" type="radio" value="copy" <?php checked($options['more'], 'copy'); ?>/>
							<?php _e('Copy the entire entry to LiveJournal', 'lj-xp'); ?>
						</label>
						<br />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('Category Selection', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('Select which categories should be crossposted', 'lj-xp'); ?></th>
					<td>
						<ul id="category-children">
							<?php
							$selected = array_diff(get_all_category_ids(), $options['skip_cats']);
							wp_category_checklist(0, 0, $selected, false, 0, false);
							?>
						</ul>
					<span class="description">
					<?php _e('Any post that has <em>at least one</em> of the above categories selected will be crossposted.'); ?>
					</span>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="options">
			<legend><h3><?php _e('Userpics', 'lj-xp'); ?></h3></legend>
			<table class="form-table ui-tabs-panel">
				<tr valign="top">
					<th scope="row"><?php _e('The following userpics are currently available', 'lj-xp'); ?></th>
					<td>
					<?php
						$userpics = $options['userpics'];
						if (!$userpics)
							_e('<p>No userpics have been downloaded, only the default will be available.</p>');
						else
							echo implode(', ', $userpics);
					?>
					<br/>
					<br/>
					<input type="submit" name="ljxp[update_userpics]" value="<?php esc_attr_e('Update Userpics', 'lj-xp'); ?>" class="button-secondary" />

					<?php if (count($options['userpics'])) { ?>
						<input type="submit" name="ljxp[clear_userpics]" value="<?php printf(esc_attr('Clear %d Userpics', 'lj-xp'), count($options['userpics'])); ?>" class="button-secondary" />
					<?php } ?>
					</td>
				</tr>
			</table>
		</fieldset>
		<p class="submit">
			<input type="submit" name="ljxp[crosspost_all]" value="<?php esc_attr_e('Update Options and Crosspost All WordPress entries', 'lj-xp'); ?>" />
			<input type="submit" name="ljxp[update_ljxp_options]" value="<?php esc_attr_e('Update Options'); ?>" class="button-primary" />
		</p>
	</form>
</div>
<?php
}

function ljxp_update_userpics($username) {
	$msgtype = 'error';
	
	// We keep a flag if we should keep processing since there are multiple steps here
	$keep_going = 1;

	// Userpics can be found from the user's domain, in an atom feed
	if (empty($username)) {
		// Report what we did to the user
		$msg[] .= __('Cannot update userpic list unless username is set.', 'lj-xp');
		$keep_going = 0;
	}

	// Download the Atom feed from the server.
	if ($keep_going) {
		try {
			// Download the data into a string from LiveJournal
			// SCL: there are better built-in WP functions for this
			$curl = curl_init('http://' . $username . '.livejournal.com/data/userpics');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			$atom_data = curl_exec($curl);
			curl_close($curl);
			$keep_going = 1;
		} catch (Exception $e) {
			$msg[] .= __('Cannot download Atom feed of userpics.', 'lj-xp');
			$keep_going = 0;
		}
	}

	// Parse the Atom feed and pull out the keywords
	if ($keep_going) {
		$new_userpics = array();

		try {
			// Parse the data as an XML string. The atom feed has many fields, but the category/@term
			// contains the name that is placed in the post metadata
			$atom_doc = new SimpleXmlElement($atom_data, LIBXML_NOCDATA);

			foreach($atom_doc->entry as $entry) {
				$attributes = $entry->category->attributes();
				$term = $attributes['term'];
				$new_userpics[] = html_entity_decode($term);
			}
		} catch (Exception $e) {
			$msg[] .= __('Cannot parse Atom data from LiveJournal.', 'lj-xp');
			$keep_going = 0;
		}
	}

	// Finally, we have new userpics, so we save it in our array
	if ($keep_going) {
		// Sort these so they come in a consistent format.
		sort($new_userpics);

		// Report our success
		$msg[] .= sprintf(__('Found %d userpics on LiveJournal.', 'lj-xp'), count($new_userpics));
		$msgtype = 'updated';
	}
	$msg = implode('<br />', $msg);
	return array('userpics' => $new_userpics, 'msg' => $msg, 'msgtype' => $msgtype);
}
?>