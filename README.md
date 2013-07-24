WordPress MU Domain Mapping Redux
=================================

Do you want a sub-domain based install or a sub-directory based install?
Up until now that has been the question when it comes to WordPress Multisite.
Now, with these extensions to the already excellent WordPress MU Domain Mapping 
Plugin (http://wordpress.org/plugins/wordpress-mu-domain-mapping/), you can have both!

Why Do I Need This?
-------------------

If you're only maintaining a single blog, the short answer is you don't.
However, if you are a professional developer or a company that hosts a lot of
WordPress sites, this plugin will allow you to host all your sites, regardless
of URL structure, on a single WordPress installation.  No more URL acrobatics!
Another great use case, for security reasons it is very desirable to make your
web facing servers read only and have a separate authoring domain where authorized
users can write to the site.  This plugin easily supports that - simply install
WordPress using the URL you wish to be your authoring domain, and create domain
mappings to expose your site on it's external facing URL.

How Does This Magic Work?
-------------------------

Glad you asked: Basically you go ahead and install multisite in as a normal
sub-directory based install.  After configuring the plugin using these instructions
(same as the original) http://wordpress.org/plugins/wordpress-mu-domain-mapping/ - 
you will now be able to add both sub-domain and sub-directory based domain
mappings.  That's it - no more being forced to pick one or the other.
Note, this plugin does not handle DNS registation or records, that's
up to you.