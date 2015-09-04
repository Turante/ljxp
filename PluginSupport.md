# Adding to the crossposted content #

As of 2.1.1, users can choose whether to crosspost the excerpt or the full content. There are two filters you can use to alter the text before it's crossposted:

`ljxp_pre_process_excerpt` -- applied after the excerpt is generated. (In 2.2.)

`ljxp_pre_process_post` -- applied after `the_content` filters, but _before_ the content is split at the `more` tag. (In 2.1.1)

# Adding to the crosspost header/footer #

As of 2.2, the header/footer supports custom replacement strings. See [CustomHeaderFields](CustomHeaderFields.md).