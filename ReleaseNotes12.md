# Bugs Resolved #

  * Non-ASCII (standard English alphabet) characters were misencoded. The two-byte Unicode characters were treated as two one-byte ISO-8859-1 characters.
> > To solve this problem, any non-ASCII characters are converted to HTML entities. I would appreciate help from anyone with experience in character sets who can offer a better solution.
  * Post status changes were not handled intelligently. If a post’s status was changed from Publish to Draft or Private, the most recent copy still remained crossposted on LiveJournal.
> > To solve this, extra hooks were added in that captured when a post’s status was changed away from Publish.

# Features Added #

  * Commenting on LiveJournal can be enabled or disabled on the Options page.
  * Password-protected posts are not copied in their entirety; instead only a link back to WordPress is crossposted.
  * The categories marked on WordPress are copied to LiveJournal as tags
  * To prevent non-recent entries from showing up on others’ friendlists, any entry that is not the most recent is marked as backdated.