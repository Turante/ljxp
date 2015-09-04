# Bugs Repaired #

  * Previously, changing the LJ host or username would result in posts tied to the old information. If any post crossposted before the LJ information was changed, editing that post would produce an error. Whenever settings such as the LJ host, username, of community are changed, all posts on the old site/account/community are deleted and then reposted using the new information.

# New Features #

  * Similarly to the bug listed above, changes to settings such as the post privacy level or the header title result in re-crossposting all posts that have already been crossposted. Any changes to how the posts are displayed are reflected on all posts that have already been crossposted.
  * Posts can be crossposted to a community instead of a user's journal. The user must be a member of that community and must have posting privileges.
  * Crossposting can be limited to specific categories. This is tricky since WP allows posts to have multiple categories. Here's how it works: If the post in question has _any_ of the categories that were selected on the options page marked, then that post is crossposted. Due to some acrobatics in the backend, new categories are turned on by default for crossposting.
  * On the LJXP Options page is now a button that will crosspost all appropriate entries (that meet the category filter and such). If you have a lot of entries, there is a potential there to timeout or make LJ unhappy, so use with caution.