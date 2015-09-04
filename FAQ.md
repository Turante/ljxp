**Q: Can I add a link to the LJ post in my post or theme?**

A: Yes. See [LinkingToLJ](http://code.google.com/p/ljxp/wiki/LinkingToLJ).

**Q: Can I add more fields to the crosspost header/footer?**

A: Yes, but it requires a little PHP knowledge on your part. (Just a little.) See CustomHeaderFields.

**Q: Can I use my custom friends filters?**

A: Yes, as of version 2.3.

**Q: Can I post to more than one LiveJournal-based service?**

A: [Not yet](http://code.google.com/p/ljxp/issues/detail?id=57), at least not directly. However, if one of the services you want to use has its own crosspost feature, you can daisy-chain them. For example, if you want to send your WP entries to both LiveJournal and Dreamwidth, set up your Dreamwidth account to crosspost to LiveJournal. Then configure this plugin for Dreamwidth.

## Common Error Messages ##

**"Something went wrong - -32300 transport error"**

Explanation: LiveJournal is probably down ('could not open socket') or having some sort of server problem ('HTTP status code was not 200'). You won't be able to crosspost until it comes back up, although the WP post should be published as usual.

When LJ comes back up, you can use the Crosspost All button, or just bulk edit the last few posts that LJ missed. (See BulkEditvsCrosspostAll.)

**"Something went wrong - 302 : Client error: Can't edit post from requested journal"**

Explanation: The reason for this message to be shown is that you probably have crossposted an entry before, deleted it manually, and then tried to edit it from WP again.

Solution: Clean up all ljID fields in post custom fields, then make sure you have set the crossposting on, and save the entry again. This will result into a new entry being posted in your LiveJournal.

Prior to 2.1.1, the ljID custom field was not set to be unique. Therefore, if something went wrong the first time you crossposted, and then you updated the WP post, it could in some cases receive a second (or third, or fourth) ljID from LiveJournal. This sometimes meant that when WP tried to crosspost an update to LJ, it would do so with the wrong ID, and LJ would return the 302 error. As of 2.1.1 (July 29, 2011), these IDs are unique, and this particular problem shouldn't happen with your new posts. If you get this error while editing older posts, and you don't want to delete the LJ post (as recommended above) because it has comments, you can:

  1. write down all the ljID numbers stored in custom fields
  1. delete all the ljID custom fields but one
  1. Update the post. If you get the error, replace the ljID number with another from the list, and so on until you find the one that lets you crosspost.

Note: in WordPress 3.1, most of the boxes on the Edit Post screen are hidden by default. If you don't see your custom fields, click the Screen Options tab (in the top right corner), then check the box for custom fields.

**"Something went wrong - 208 : Client error: Invalid text encoding: The text entered is not a valid UTF-8 stream"**

Solution: Tags must only use the standard English alphabet. If you're using diagritics or a foreign alphabet, either don't post the tags or figure out a way to translate them. For example, check out the solution that people have come up with for [LJXP in Russian](http://ebroder.net/livejournal-crossposter/i18n/).

## Other Errors ##

If you get an error that wasn't mentioned above, please report it in the [Issue tracker](http://code.google.com/p/ljxp/issues/list) rather than commenting here.