# 2.2 #

  * New option: default LJ privacy levels for private WP posts. (<a href='http://code.google.com/p/ljxp/issues/detail?id=73'>#73</a>)
  * Added support for custom fields in the header/footer. <a href='http://code.google.com/p/ljxp/wiki/CustomHeaderFields'>See the wiki for documentation.</a> (<a href='http://code.google.com/p/ljxp/issues/detail?id=113'>#113</a>)
  * Now auto-generates excerpts from the content, if crossposting excerpts and the post doesn't have an excerpt specified (<a href='http://code.google.com/p/ljxp/issues/detail?id=139'>#139</a>)
  * Added a filter, `ljxp_pre_process_excerpt`, applied to the excerpt before it's crossposted. [Developers should use this](http://code.google.com/p/ljxp/wiki/PluginSupport) in addition to `ljxp_pre_process_post` to support both excerpt and full-text options.
  * Relative links are now converted to full URLs before the content is crossposted (<a href='http://code.google.com/p/ljxp/issues/detail?id=134'>#134</a>)
  * The LJ URL of the post is now stored in a custom field, so you can easily add the link to your WP entry. (<a href='http://code.google.com/p/ljxp/issues/detail?id=51'>#51</a>)
  * Galleries are now crossposted with inline styles, so their grid layout is maintained (<a href='http://code.google.com/p/ljxp/issues/detail?id=117'>#117</a>)
  * When posting to a community, deleted WP entries are now deleted from the community correctly.
  * New Help screen on the options page.
  * Updated POT for translators.

# 2.2.1 #

  * Fixed the new relative link function so it leaves full URLs alone. (<a href='http://code.google.com/p/ljxp/issues/detail?id=141'>#141</a>)
  * Now using WP's HTTP API rather than `curl_*()`, which means the userpic retrieval functions should work on more servers.

# 2.2.2 #

  * Fixed the userpic and comment settings on individual posts. (<a href='http://code.google.com/p/ljxp/issues/detail?id=144'>#144</a>)
  * Added some helpers to work around servers that do not support PHP's multibyte string functions.