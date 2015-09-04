# Custom Header/Footer Fields #

Version 2.2 of LJ-XP includes a new filter, `ljxp_post_header`, that lets you add to the list of substitution strings in the custom header/footer.

This requires some ability to tinker in PHP, but it's very flexible -- you could add virtually anything you want.

## How to Use the Filter ##

Buckle up; you're about to become a plugin author! (If you aren't already.)

Download the [sample plugin](http://code.google.com/p/ljxp/downloads/detail?name=lj-xp-custom-fields-example.php.zip) from the Downloads page (code shown below) and edit it to handle the fields you want to display. The goal is to search the header for your field (`[myfield]`), tell the plugin what it should be replaced with (`'My Field Data'`), and return the altered string to LJ-XP.

The sample plugin gives a long example that's useful for adding more than one custom field. See the last line of the function for the short version you can use if you need to add only one custom field.

Upload your finished plugin to `wp-content/plugins`, activate it, and add your new `[field]` to the custom header in the LJ-XP options.

```
<?php
/*
Plugin Name: LiveJournal Header Custom Fields
Description: Lets you specify more fields for the LiveJournal Crossposter header/footer.
Version: 0.1
Author: You!
*/

function my_ljxp_custom_fields($header, $postid) {
	/*
	// First, you need to write some code to retrieve the data you want to display in the header.
	
	// Example #1: this is how the [author] tag is done in LJ-XP
	$thepost = get_post($postid);
	$userid = $thepost->post_author;
	$author = get_userdata( $userid );
	$author = $author->display_name;
	/**/
	
	// Example #2: showing the featured image (post thumbnail)
	$thumbnail = get_the_post_thumbnail($postid);
	
	// Example #3: displaying the contents of custom fields
	// third value is true for a unique field; false if the post can have more than one
	$book = get_post_meta($postid, 'currently_reading', true);  

	// Next, you need to find and replace
	$find = array('[thumbnail]', '[currently_reading]');
	$replace = array($thumbnail, $book);
	$header = str_replace($find, $replace, $header);
	
	// Finally, you need to send the text back to LJ-XP
	return $header;
	
	// Here's a one-line version of this whole function using Example #2, if you just need the one custom [field]
	// return str_replace('[thumbnail]', get_the_post_thumbnail($postid), $header);
}

// Do not edit the line below. This is what makes the whole thing go.
add_filter('ljxp_post_header', 'my_ljxp_custom_fields', 10, 2);
?>
```