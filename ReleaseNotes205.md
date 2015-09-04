This version is compatible with WP version 2.5.1, and retains compatibility with WP 2.0

**Changelog**:
  * The project is now run by new authors: corneliousjd and freeatnet
  * The project now has its own web-site: http://www.lj-xp.com/
  * Fixed compatibility issues with Wordpress 2.3 and 2.5
  * Added support for `[tags]`, `[categories]`, and `[comments_count]`
  * Added more options for LJ post tagging: none, tag with WP categories, tag with WP tags, tag with both WP tags and categories
  * Major code rewrite -- settings are now carried within one array, some duplicated code was merged
  * Russian translation updated

**Known issues**:
  * ~~Updating all posts at once may throw several warning messages, such as for array\_fill and array\_map, which also leads to 'Cannot modify header information' error. This does not affect posting itself, but I will investigate and fix it.~~ Fixed in [revision 51](https://code.google.com/p/ljxp/source/detail?r=51).
  * ~~Delayed posting (Future posts) do not cross post automatically. This has something to do with publish\_post action. Investigation to follow.~~ Fixed in [revision 51](https://code.google.com/p/ljxp/source/detail?r=51).
  * No known issues at this time