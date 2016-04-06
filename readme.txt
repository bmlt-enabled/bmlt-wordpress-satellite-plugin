=== BMLT WordPress Plugin ===
Contributors: magblogapi
Plugin URI: http://bmlt.magshare.net
Tags: na, meeting list, meeting finder, maps, recovery, addiction, webservant
Author: MAGSHARE
Requires at least: 2.6
Tested up to: 4.5
Stable tag: 3.3.0

This is a "satellite" plugin for the Basic Meeting List Toolbox (BMLT).

== Description ==

The <a href="http://magshare.org/bmlt">Basic Meeting List Toolbox (BMLT)</a> is a powerful client/server system for locating NA meetings.
The "root server" is a standalone Web site, but "satellite servers" are set up to point to the "root." This is a "satellite," set up as a WordPress plugin.
It is very easy to install and use. It has an administration panel that lets you choose a map center, designate the root, set up the map zoom, and whether or not older browsers are supported.

<strong>CAUTION:</strong> Be extremely cautious in upgrading to the 3.0 version, as it represents a MAJOR departure from the 2.X versions!
The styling, especially, has been affected. If you have customized your installation, you'll probably need to redo it. We recommend that you create a custom "theme," by modifying one of the supplied themes.

Additionally, Version 3.0 of the plugin requires a root server version of 1.10.3 or greater. After May 19, 2013, it will require version 2.0 or greater (1.10.3 will no longer work, due to Google forcefully deprecating their maps API).

== Installation ==

<a href="http://bmlt.magshare.net/wordpress/">Go to this Web page to get very detailed instructions on installing and configuring the plugin.</a>

== Screenshots ==

1. Basic Text Entry
2. Text Entry With Extra Requirements
3. Basic "One Click" Map
4. Map With Extra Requirements
5. Search Results
6. Single Meeting Marker Selected
7. Multiple Meeting Marker Selected.
8. Single Meeting Details
9. Quick Map Search.
10. Quick Map Search Results.
11. Special Preselected Search Capability (Only Available in WordPress).
12. Special Preselected Search Results.
13. Administration Screen, Showing Multiple Settings.
14. Administration screen Map.
15. Administration, Showing Multiple Themes.
16. Administration Screen, Showing Choice of Initial Search Screens.

== Changelog ==
= 3.3.1 =
* TBD
* Made the weekday tab overflow hidden for the [[bmlt_table]] shortcode.
* The format circles now float to the right for the [[bmlt_table]] shortcode.
* Made the busy throbber stay up a bit longer when loading a new weekday for the [[bmlt_table]] shortcode.

= 3.3.0 =
* April 4, 2016
* Made it so that we can have specialized themes, amied at only certain shortcodes.
* Major rewrite of the [[bmlt_table]] shortcode to improve responsive design.

= 3.2.4 =
* April 1, 2016 (Happy April Fools'!)
* Broke the table styling out into separate files that are all loaded at once. This allows a lot more flexibility when implementing the table display.
* Tweaked the GNYR style.
* The JavaScript had a fundamental error that prevented multiple instances of the table. That's been fixed.

= 3.2.3 =
* March 30, 2016
* Got rid of an undeclared variable warning.
* Fixed a bug that caused rendering issues with the new table shortcode on Internet Exploder.
* Fixed a minor style issue, where the selection triangle would flow below the text in large text situations.
* Changed the styling for the selected header triangle to make the table display a bit more responsive.

= 3.2.2 =
* March 29, 2016
* Fixed a style problem with the default search, where the map and text might be outdented by 8 pixels.
* Fixed the "Google Maps included two or more times" warning.
* Removed unnecessary new search and duration items from admin page.
* Fixed an issue where WordPress would sometimes HTML-entity the ampersand (&) character.
* Added a very significant new shortcode: [[bmlt_table]].
* Adding Italian localization.
* Adding better support for SSL/HTTPS.

= 3.0.29 =
* January 10, 2016
* Added support for a runtime language selector as a cookie. If you set a cookie named "bmlt_lang_selector," and set its simple string value to an ISO 639-1 or ISO 639-2 **SUPPORTED** language, that will select the client language.

= 3.0.28
* August 15, 2015
* Added Portuguese Translation.

= 3.0.27
* May 25, 2015
* Updated the base class (CSS fixes, mostly).

= 3.0.26
* January 31, 2015
* Fixed an issue with the extra fields display in the regular shortcode display details.
* Fixed an issue where the arbitrary fields were actually creating too many results.
* Now hide the distance_in_km/miles parameters in the meeting details (these are internal parameters).

= 3.0.25
* November 23, 2014
* Added a meta tag with the Root Server URI for troubleshooting.
* Added full support for arbitrary fields. This was an important capability that was left out after Version 3.X
* Fixed a CSS issue with the admin display map. Some themes (especially responsive ones) declare a global max-width for images. This hoses Google Maps, and has to be compensated for.

= 3.0.24 =
* July 31, 2014
* Added a user-agent to cURL, which allows the root server to be a bit more tinfoil.
* Fixed an annoying bug with the admin, in which new settings reported a bogus ID first time through.

= 3.0.23 =
* July 17, 2014
* Added Danish localization.
* Fixed a small bug in the administration.

= 3.0.21 =
* February 23, 2014
* This adds fixes for servers that run on non-standard TCP ports.

= 3.0.20 =
* December 7, 2013
* Fixed a character set issue that affected some instances of Internet Explorer.

= 3.0.19 =
* December 7, 2013
* Added French localization.

= 3.0.18 =
* September 7, 2013
* Tweaked the German localization slightly
* Fixed some JavaScript issues with the [[bmlt_mobile]] shortcode.

= 3.0.17 =
* July 1, 2013
* Fixed a couple of localization bugs in the German localization.
* Added the capability to select which day of the week will be the start day.

= 3.0.16 =
* May 22, 2013
* Added German localization.

= 3.0.15 =
* May 19, 2013
* Fixed a usability issue, where entering text into the CSS field would not immediately "dirtify" the admin screen.

= 3.0.14 =
* May 18, 2013
* Fixed an issue, where the Meeting search could have a bad AJAX URI.

= 3.0.12 =
* May 16, 2013
* Cleaned up some code to reduce notes and warnings.

= 3.0.11 =
* May 13, 2013
* Reduced the number of times that the marker redraw is called in the standard [[bmlt]] shortcode handler.
* Fixed an issue with CSS that caused displayed maps to get funky.

= 3.0.10 =
* May 5, 2013
* Fixed an issue, where the first set of results from a search would display too many red icons on the map.

= 3.0.8 =
* April 28, 2013
* Added support for display of military time.

= 3.0.7 =
* April 21, 2013
* Fixed localization issues with string searches.

= 3.0.5 =
* April 16, 2013
* There was a bug in the Swedish translation. Also, the language is now auto-detected from the blog.

= 3.0.4 =
* April 15, 2013
* Fixed a bug caused by work on the root server. This bug prevents the details window from appearing on the standard shortcode.

= 3.0.3 =
* Adds Swedish baseline localization (Still need to add localization detection).

= 3.0.1 =
* January 28, 2013
* Fixed an issue that pooched some uses, and also turned off debug mode (oops).

= 3.0 =
* January 26, 2013
* Implemented completely new default shortcode.
* NOTE: DON'T UPGRADE UNTIL YOU ARE READY! This is a MAJOR change in styling and behavior.

= 2.1.29 =
* May 13, 2012
* Fixed an old bug, where empty settings would be accidentally created.

= 2.1.28 =
* April 26, 2012
* Added some JavaScript "hooks" to allow robust customization of the new map search.

= 2.1.27 =
* March 28, 2012
* Added an alert to the new map search, if no meetings were found (originally gave no feedback).

= 2.1.26 =
* December 31, 2011
* Added the ability to localize the plugin.
* Fixed some validation issues.
* Now strip out the [[bmlt_mobile]] shortcode if the page is not a mobile page. This allows the shortcode to be used, as the comment version is stripped by "code cleaners."

= 2.1.25 =
* September 2, 2011
* Fixes an Internet Explorer JavaScript bug in the new map search.

= 2.1.24 =
* August 17, 2011
* Improves some of the styles in the info windows in the new map search.

= 2.1.23 =
* August 16, 2011
* Addresses a bug in Mozilla Firefox, that prevents the use of the popup menus in the multi-day (red) map icons.

= 2.1.22 =
* August 12, 2011
* Fixes a couple of theme/style bugs.
* Mitigates a very strange Firefox bug, where blank pages were being called when closing the location area.

= 2.1.21 =
* August 8, 2011
* This implements a powerful new shortcode: "bmlt_map"

= 2.1.20 =
* July 16, 2011
* This removes a few warnings that could come up in really anal-retentive environments.

= 2.1.19 =
* June 27, 2011
* This adds the new "bmlt_changes" shortcode. This is the first fruit of the new structure.

= 2.1.18 =
* June 20, 2011
* Fixed an "invisible" bug in the standard header output, which could interfere with the correct settings being selected.
* Changed the structure to use the new one that allows the shared projects to be "nested" more easily.

= 2.1.17 =
* June 14, 2011
* Added the location text and comments to the meeting info displays in mobile mode.

= 2.1.16 =
* May 24, 2011
* Moved the AJAX handler to the "init" phase, as 'wp' seemed to wait a bit too long.
* Added the capability to display a changelist in the upgrade notice -very cool!
* Added a settings link to the plugin listing.

= 2.1.15 =
* May 22, 2011
* Fixed a very strange bug that seems to cause error 500s on some servers. Not sure why the fix worked, but it does. This only manifested when doing an "address only" search in mobile mode.

= 2.1.14 =
* May 18, 2011
* Fixed a bug that prevents the "More Details" window from being shown.

= 2.1.13 =
* May 16, 2011
* Fixed a minor bug that prevented the popup menu and the classic interactive search from being displayed on the same page (this is OK to happen).

= 2.1.12 =
* May 8, 2011
* Fixed an error that interfered with several Advanced Search options.

= 2.1.11 =
* May 7, 2011
* Fixed a JavaScript error that prevented saves.

= 2.1.10 =
* May 6, 2011
* Added changes to the cross-CMS class and styling for the Drupal module. Won't have much effect on WordPress.

= 2.1.9 =
* May 3, 2011
* Fixed a few issues encountered while implementing the Drupal plugin (a lot of the code is cross-CMS).
* Added GPL headers to everything.

= 2.1.8 =
* April 28, 2011
* Fixed a bug, in which the bmlt_mobile shortcode was not being interpreted properly (Affected WordPress only).

= 2.1.7 =
* April 26, 2011
* Fixed a rather severe bug in the shortcode substitution, that prevented multiple shortcodes from working on the same page.

= 2.1.6 =
* April 24, 2011
* Basic code cleanup.
* Fixed a couple of minor cosmetic bugs in the admin JavaScript and CSS.
    
= 2.1.5 =
* April 23, 2011
* Oops. One more warning-spitter snuck through. It's fixed.

= 2.1.4 =
* April 23, 2011
* Fixed a minor JS bug in the option submit. It did not result in errors, per se, but caused extra text to be transmitted.

= 2.1.3 =
* April 21, 2011
* Addressed some issues that could cause problems on some servers (a screwy intval() implementation).
* Made the save settings use POST, as the size of the transaction can be too large for GET, when you have a lot of settings.
    
= 2.1.2 =
* April 20, 2011
* Fixed some warnings that interfered with the operation of 2.1.1

= 2.1.1 =
* April 20, 2011
* Sequestered the mobile stuff into a fieldset in the admin.
* Added the ability to set the mobile "grace period," as well as specify a mobile offset from the server.
* Added a number of fixes and adjustments as we debugged the Joomla plugin.
    
= 2.1 =
* April 11, 2011
* Significant refactoring to make it easier to port the plugin to other CMSes.
* Added the ability to select distance units (Km or Mi). This will only affect the mobile handler (at the moment).
* Added the ability to project a language, via the original interactive search (You can select a different language from the server's default).
    
= 2.0.2 =
* March 3, 2011
* Fixed a critical problem that appeared in 2.0.1 because of a silly error on the coders' part.
    
= 2.0.1 =
* March 3, 2011
* Fixed a JavaScript issue that prevented the options from being displayed in Firefox.
* Added null parameters to some get_page() calls to prevent warnings.

= 2.0.0 =
* February 21, 2011
* Release.
* Added the GNYR theme to the release.
    
= 2.0.0RC1 =
* February 20, 2011
* Fixed a number of issues encountered during beta testing.
    
= 2.0.0B0 =
* February 14, 2011
* Major rewrite. You can now have multiple settings, which can include different servers.
* You can "theme" the displays.
* We no longer support non-JS browsers.
* Mobile content has been woven into the plugin. It now allows the page to be replaced with the fast mobile lookup, if that was requested.
* The administration has been drastically improved.
* This provides an infrastructure for many future improvements. The 2.0 release was aimed primarily at transitioning from the old system into the new.

= 1.5.12 =
* October 2, 2010
* Very, very minor change to a text display to make the plugin easier to localize.
* Changed the CSS so that the plugin will adapt more efficiently to different environments.
	
= 1.5.11 =
* September 18, 2010
* Added support for some new initial screen modes.
	
= 1.5.10 =
* September 4, 2010
* Added a bit of default CSS to make the search specification screen more adaptable.
	
= 1.5.9 =
* August 30, 2010
* Make sure that all cURL calls are GET, as some servers don't like POST.
	
= 1.5.8 =
* July 23, 2010
* Added support for a readable-text meta tag entry ([[BMLT]]).
	
= 1.5.7 =
* July 1, 2010
* Stopped the plugin from croaking the whole shooting match if call_curl fails (Wrap in empty try block).
	
= 1.5.6 =
* June 1, 2010
* Made it so that lookups for individual meetings don't get redirected to the root server.

= 1.5.5 =
* May 30, 2010
* Added JS and Style optimizers for the linked files.
* Fixed a bug in the new selector.

= 1.5.4 =
* May 29, 2010
* Embedded the simple search feature into the plugin.

= 1.5.3 =
* May 28, 2010
* Added provision to allow the CMS direct access to the simple dump.

= 1.5.2 =
* April 23, 2010
* Fixed an old bug that could affect the way the server interaction works (curl).
		
= 1.5.1 =
* April 23, 2010
* Added some code to ensure the root server URI has a trailing slash.
	
= 1.5 =
* April 2, 2010
* Added support for the "simple" inline meeting tables.
	
= 1.4.2 =
* February 21, 2010
* Execute the PDF check earlier, as other plugins can interfere.
	
= 1.4.1 =
* February 16, 2010
* Added support for Android
	
= 1.4 =
* February 14, 2010
* Added support for iPhone
* Fixed a minor issue with the cURL caller.
	
= 1.2.19 =
* December 30, 2009
* Added the ability to switch on a "push in" method of viewing the "More Details" window.
* Added the ability for the admin to insert arbitrary CSS styles.

= 1.2.18 =
* November 24, 2009
* Added a section of documentation for administration. No code changes.
	
= 1.2.17 =
* November 8, 2009
* Fixed a bug, in which the pre-check boxes in the admin would fail to populate if there was only one Region.
	
= 1.2.16 =
* November 4, 2009
* Fixed a bug, in which advanced search criteria were ignored when printing PDFs:
* https://sourceforge.net/tracker/index.php?func=detail&aid=2892019&group_id=228122&atid=1073410
	
= 1.2.15 =
* November 3, 2009
* Added support for direct PDF printing (Requires 1.2.15 root server).
	
= 1.2.5 =
* October 3, 2009
* Fixed a slight warning issue with the way that the options are initialized.
	

= 1.2.3 =
* September 24, 2009
* Added the ability to "pre-check" Service bodies in the Advanced Search tab. This function requires that the root server also be version 1.2.3.
	
= 1.0.2 =
* July 20, 2009
* PHP 5.2.10 seems to be expecting a slightly different interpretation of explode(), so it is now simpler.

= 1.0.1 =
* June 25, 2009
* Made the inter-server communications use POST, which makes it a bit more robust.

== Installing and Administering the Plugin ==

You need to <a href="http://magshare.org/blog/bmlt-administration/">go to this Web page to get very detailed instructions on installing and configuring the plugin.</a>