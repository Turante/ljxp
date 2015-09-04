In 2.2, the URL of the crossposted LJ entry is stored as a custom field in your WordPress post. This makes it easy to show the LJ link in your WP theme.

# In the Template File #

Add this to single.php somewhere inside the Loop:

```
<?php
$ljURL = get_post_meta($post->ID, 'ljURL', true);
if (!empty($ljURL))
   echo '<a href="'.esc_url($ljURL).'">View this post on LiveJournal</a>';
?>
```

# Adding a Filter #

If you'd rather show the link automatically, you could append the link to each post by adding a content filter.

Add this to your theme's functions.php file:

```
function show_LJ_URL($content) {
   global $post;
   $ljURL = get_post_meta($post->ID, 'ljURL', true);
   if (!empty($ljURL))
      return $content.'<a href="'.esc_url($ljURL).'">View this post on LiveJournal</a>';
   else return $content;
}
```