# 2.1 #

  * send error back to the post edit screen when LJ is down rather than using wp\_die(), which stops all other plugins from working
  * support userpics
  * support cut text
  * switch to new meta box format so you can collapse the LJ box or move it around the post edit screen
  * fix a problem with gallery image IDs that would cause the wrong images to be shown when the [gallery](gallery.md) shortcode was crossposted
  * options page cleanup
  * get rid of has\_cap deprecated argument notice
  * less obnoxious default styling for the crosspost header/footer
  * new developer: Stephanie Leary
  * removed references to lj-xp.com, which is no longer active
  * changed directory and file names to match versions available through wordpress.org.

# 2.1.1 #

  * Fix for `<!--more-->` tags containing text (<a href='http://code.google.com/p/ljxp/issues/detail?id=76'>#76</a>)
  * Added a filter, `ljxp_pre_process_post`, applied to the post content before it's crossposted (<a href='http://code.google.com/p/ljxp/issues/detail?id=120'>#120</a>)
  * Added option to not crosspost by default (<a href='http://code.google.com/p/ljxp/issues/detail?id=67'>#67</a>)
  * Added option to crosspost the excerpt instead of the full text (<a href='http://code.google.com/p/ljxp/issues/detail?id=111'>#111</a>)
  * Added `[author]` tag for header/footer (<a href='http://code.google.com/p/ljxp/issues/detail?id=34'>#34</a>)
  * Settings API! Much better security.
  * General settings cleanup. Now using two settings instead of thirteen, and removing settings on plugin uninstall.
  * More improvements to the error handling.

# 2.1.2 #

  * Fixed category handling and a warning about arrays on line 89 that could also lead to "headers already sent" message on some servers.
  * Translations: generated new POT from wordpress.org; updated old .po/.mo files to match the new text domain.