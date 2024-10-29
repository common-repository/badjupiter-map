=== BadJupiter Map ===
Contributors: BadJupiter
Requires at least: 3.4.0
Tested up to: 5.7
Stable tag: 1.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed a BadJupiter Collection directly into your post as a map of places with a QR code to link to our BadJupiter service so you can take it on your mobile.

== Description ==
You've made your BadJupiter collection and added all your insights to all your places, you've written your in-depth post and you're about to publish it on your blog, but you'd like your visitors to have a great way of seeing where your places are and to be able to take your collection with them on the go.

Now you can embed a map of your BadJupiter collection directly in your post, complete with a QR Code or link so you follows can go directly to your BadJupiter collection and have it with them, on their mobile, all of the time.

Simply install the plugin and then add the shortcode [badjupiter-map collection="your collection slug"] to your post.

The BadJupiter Collection plugin allows you to integrate the BadJupiter Service directly within your Wordpress blog.  Collections that are publicly visible in BadJupiter can be included into your posts using the plugin and shortcode.

*Please note that we use the 3rd party MapBox GL javascript library to generate the visible map. (MapBox: https://www.mapbox.com) (TOS: https://www.mapbox.com/legal/tos/) also note that the use of this 3rd party service utilises the BadJupiter account and key and it is not for reuse outside of the BadJupiter Collection plugin. You are not required to have your own accounts at MapBox.
More information on this can be found here:
https://docs.mapbox.com/help/glossary/access-token/
https://docs.mapbox.com/accounts/overview/tokens/#default-public-access-token

*Please note that we use the 3rd party QR API hosted at https://api.qrserver.com to generate QR codes for the URLs to the BadJupiter Collection slug.


== Installation ==
INSTALL BADJUPITER MAP FROM WITHIN WORDPRESS
Visit the plugins page within your dashboard and select ‘Add New’;
Search for ‘BadJupiter Map’;
Hit the "Install Now" button;
Activate BadJupiter Map from your Plugins page;
Go to your post and enter the shortcode [badjupiter-map collection="put your BadJupiter collection slug here"]

== Frequently Asked Questions ==
WHAT IS A BADJUPITER COLLECTION SLUG
A slug is a unique 6 character code that identifies your collection in BadJupiter.

WHERE DO I FIND MY SLUG
You can find your collection slug by visiting your collection on BadJupiter and clicking the share button and copying the link.

== Screenshots ==
1. https://util.badjupiter.com/images/wp-screen-2.png
2. https://util.badjupiter.com/images/wp-screen-1.png

== Changelog ==
1.0.0

Release Date: February 1st, 2020

Initial release.

== Upgrade Notice ==

= 1.0.1 =

Switching url from bj.cards to new jupiter.link standard.

= 1.0.2 =

Fix for missing logo

= 1.0.3 =

Changing reference from bj-collection to badjupiter-map

= 1.0.4 =

Changing support for deeper response to collection request

= 1.0.5 =

Update logos

= 1.0.6 =

Updated to support Paths and Polys within the collections

= 1.0.7 =

Updated to support opacity on polys

= 1.0.8 =

Updated to draw outline on polygons

= 1.0.9 =

Updated to add the opacity control to the poly fills

= 1.0.10 =

Updated to switch the order of components on the map to push polys udner points and labels
