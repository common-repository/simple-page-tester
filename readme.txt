=== Simple Page Tester ===
Contributors: jkohlbach, RymeraWebCo
Donate link:
Tags: split testing, split tester, split test, a/b testing, a/b tester, a/b test, a/b split tester, simple page tester, simplepagetester, page tester, split test pages
Requires at least: 3.4
Tested up to: 5.1.1
Stable tag: 1.4.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Run A/B Split Tests in WordPress without having to edit code.

== DESCRIPTION ==

Website: [http://simplepagetester.com](http://simplepagetester.com)

Premium Version: [http://simplepagetester.com/premium/](http://simplepagetester.com/premium/)

Simple Page Tester empowers website owners to run Split Tests (also called A/B Tests) in WordPress without having to edit code as required by so many of the split testing services out there.

We've made split testing simple, like it should be.

It's a simple three step process:

1. Navigate to the edit screen of the page you want to split test
1. Click "Setup Split Test" and edit your variation
1. Click "Declare Winner", in the Premium version it even tells you when!

Some features at a glance:

**SEO FRIENDLY**

Concerned about how your split test will effect SEO? We've followed the Google Webmaster guidelines exactly and follow their recommendations so your tests will be recognised as temporary.

**ANALYTICS**

In the Premium version we give you in-built conversion tracking and crazy good breakdowns of how well your split test is going along with automatic statistical confidence calculations.

The free version tracks unique visitors to each variation in your split test and is compatible with Google Analytics Goals so you can conduct sophisticated analysis of your results.

**CACHE COMPATIBLE**

The big question mark about split testing is "how does this effect my caching?"

If you're running a caching plugin, you have no need to worry with Simple Page Tester as it has been built to run side-by-side with caching solutions.

**CONVERSION TRACKING**

Full conversion tracking and statistical analysis is available in the [Premium version](http://simplepagetester.com/premium/).

== Installation ==

1. Upload the `simple-page-tester/` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the edit screen of the page you wish to split test and click the 'Setup New Split Test' button.
1. Visit [http://simplepagetester.com/](http://simplepagetester.com/) for more information on the Premium version, our blog, knowledge base and support.

== Frequently asked questions ==

For a list of frequently asked questions and more [see our knowledge base](http://simplepagetester.com/knowledge-base/).

== Screenshots ==

1. Starting a split test is easy, just click Setup New Split Test

2. Dialog for choosing what to do with the Variation. Duplicate the page, Choose and existing page or start a new page

3. The split test details screen shows view stats in the free version and conversion details in the Premium version ([read about even more features in the Premium version](http://simplepagetester.com/premium/))

== Changelog ==

= 1.4.5 =
* Bugfix: Fix various PHP notices

= 1.4.4 =
* Bugfix: Fix various PHP notices
* Bugfix: Fix chart data object not being initialised in some cases causing a JS error
* Bugfix: Don't attempt to start sessions on the admin side
* Feature: Ability to archive existing test statistics to enhance performance
* Feature: Automatic archiving statistic data on new tests
* Bugfix: Hide Add New button on split test list view on admin

= 1.4.3 =
* Bugfix: Replace usage of mysql_ function with WordPress preferred function

= 1.4.2 =
* Bugfix: Conflict fix/workaround for some plugin scripts needing default ajaxurl path inside thickboxes and causing errors which breaks our buttons.
* Bugfix: Remove forced action execution for admin_head as it's playing up on some servers and appears to be unnecessary now

= 1.4.1 =
* Bugfix: Fixing url escaping issue affecting some home page tests due to improper escaping of GET parameters, shifted the escape down lower just before the redirect occurs.

= 1.4.0 =
* Bugfix: Temporary workaround added to the Thickbox popup to fix a class with the Types plugin. Just until they fix their issue of loading Types JS files on non-required pages.
* Bugfix: If a visitor visits the test slug, redirect them to the Master
* Feature: Added a Tour to the plugin which is activated when the plugin is Activated
* Feature: Added a new metric to show "unique views" to in order to better separate the total view count from unique views. Changed existing metric to "total views"
* Feature: Allow archiving of tests so you can keep both variations after a test finishes. This is now the default behavior. The losing variation will replace the Variation's slug and the winner will replace the Master's slug. It's up to the admin to delete the second page.
* Feature: Added the ability to "Pause" a test in progress. Paused tests shuffle all traffic to the "Master" and do not record visits or conversions.
* Feature: Added a column to show the status of a test on the All Split Tests list view
* Feature: Disable the WordPress SEO meta box on the SPT edit screen automatically without having to adjust the settings in WordPress SEO

= 1.3.4 =
* Bugfix: Fix formatting on side options panel on test edit screen
* Feature: Add global notice dismissal function to mark admin notices as properly dismissed
* Feature: Add notice to inform about automatic WooCommerce tracking in Premium if WooCommerce is installed

= 1.3.3 =
* Bugfix: Fixing more logic conditional problems with force same there uncovered by the previous fix (thanks Jeong)

= 1.3.2 =
* Bugfix: Fixing logic error in force same functionality which could see the same variation being shown to all users in some cases

= 1.3.1 =
* Bugfix: Prevent undefined variable warnings on test edit screen (thanks Baden)
* Bugfix: Fixing IP address retrieval warning
* Bugfix: Check chart data exists before printing it

= 1.3 =
* Feature: Introduced a new session handler to allow us greater flexibility in support different types of handlers for tracking visitors
* Feature: Compatibility with the WP Session Manager plugin to allow for hosts that don't support native PHP sessions (like WP Engine)

= 1.2.6 =
* Feature: Provide callable function for retrieving variation IDs in an array for exclusion in the template loops

= 1.2.5 =
* Bug Fix: Exclude 'post' posts from the loop if they are involved in a test

= 1.2.4 =
* Bug Fix: Fixing php notice on force same option on back end
* Bug Fix: Fixing $post undefined notice on test edit screen
* Bug Fix: Redirection should only happen on non-archive pages (thanks Lindsey)

= 1.2.3 =
* Bug Fix: Removing debug trace accidentally left in from previous version

= 1.2.2 =
* Bug Fix: Fixing old reference to mysql filtering function, switched to using internal WP function esc_sql as it was causing issues on some server setups

= 1.2.1 =
* Feature: Adding hook after non-redirect situations on redirection routine

= 1.2 =
* Bug Fix: Force same should store session data relative to split test ID to cover cases where multiple tests are being run
* Bug Fix: Force same tests don't need to redirect if they're already on the master page (thanks Ray)
* Bug Fix: Fix for view statistics clearing during post save in some edge case scenarios
* Bug Fix: Pass the test ID to after redirect hook
* Feature: New test page to menu with instructions on how to start a new test
* Feature: Reset statistics button
* Feature: Pause test button, redirects all traffic to Master and doesn't record statistics
* Feature: Move master and variation into same graph
* Feature: Introduced types, default is Page (new test Shortcode test type in Premium)
* Feature: Display more data on the list screen (test type & statistics to start with)

= 1.1.2 =
* Adding after save hook during split test post save

= 1.1.1 =
* Correctly duplicate meta values from master post to variation
* Harden up some access points throughout the plugin (thanks Ry)

= 1.1 =
* Released free version of Simple Page Tester on WordPress.org. Let the games begin :)

= 1.0.5 =
* Compatibility with WordPress 3.8. Updating plugin to use proper ajax functions as per docs.

= 1.0.4 =
* Query args on URLs should transfer to the selected landing page (thanks Chris)

= 1.0.3 =
* Page template now copies over on duplicated pages, fix styles on new test setup screen

= 1.0.2 =
* Adding support for canonical tag on secondary page

= 1.0 =
* Initial version

== Upgrade notice ==

There is a new version of Simple Page Tester available.
