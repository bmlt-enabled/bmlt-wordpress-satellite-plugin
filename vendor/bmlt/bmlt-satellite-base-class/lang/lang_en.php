<?php
// English
/****************************************************************************************//**
*   \file   lang_en.php                                                                     *
*                                                                                           *
*   \brief  This file contains English localizations.                                       *
*                                                                                           *
*   This file is part of the BMLT Common Satellite Base Class Project. The project GitHub   *
*   page is available here: https://github.com/MAGSHARE/BMLT-Common-CMS-Plugin-Class        *
*                                                                                           *
*   This file is part of the Basic Meeting List Toolbox (BMLT).                             *
*                                                                                           *
*   Find out more at: https://bmlt.app                                                      *
*                                                                                           *
*   BMLT is free software: you can redistribute it and/or modify                            *
*   it under the terms of the GNU General Public License as published by                    *
*   the Free Software Foundation, either version 3 of the License, or                       *
*   (at your option) any later version.                                                     *
*                                                                                           *
*   BMLT is distributed in the hope that it will be useful,                                 *
*   but WITHOUT ANY WARRANTY; without even the implied warranty of                          *
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                           *
*   GNU General Public License for more details.                                            *
*                                                                                           *
*   You should have received a copy of the GNU General Public License                       *
*   along with this code.  If not, see <http://www.gnu.org/licenses/>.                      *
********************************************************************************************/

require_once(dirname(__FILE__) . '/BMLT_Localized_BaseClass.class.php');
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
class BMLT_Localized_BaseClass_en extends BMLT_Localized_BaseClass
// phpcs:enable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:enable Squiz.Classes.ValidClassName.NotCamelCaps
{
    public function __construct()
    {
        /************************************************************************************//**
        *                           STATIC DATA MEMBERS (LOCALIZABLE)                           *
        ****************************************************************************************/
    
        /// These are all for the admin pages.
        $this->local_options_lang_prompt = 'Language:';                       ///< The label for the Language Selector.
        $this->local_options_title = 'Basic Meeting List Toolbox Options';    ///< This is the title that is displayed over the options.
        $this->local_menu_string = 'BMLT Options';                            ///< The name of the menu item.
        $this->local_options_prefix = 'Select Setting ';                      ///< The string displayed before each number in the options popup.
        $this->local_options_add_new = 'Add A new Setting';                   ///< The string displayed in the "Add New Option" button.
        $this->local_options_save = 'Save Changes';                           ///< The string displayed in the "Save Changes" button.
        $this->local_options_delete_option = 'Delete This Setting';           ///< The string displayed in the "Delete Option" button.
        $this->local_options_delete_failure = 'The setting deletion failed.'; ///< The string displayed upon unsuccessful deletion of an option page.
        $this->local_options_create_failure = 'The setting creation failed.'; ///< The string displayed upon unsuccessful creation of an option page.
        $this->local_options_delete_option_confirm = 'Are you sure that you want to delete this setting?';    ///< The string displayed in the "Are you sure?" confirm.
        $this->local_options_delete_success = 'The setting was deleted successfully.';                        ///< The string displayed upon successful deletion of an option page.
        $this->local_options_create_success = 'The setting was created successfully.';                        ///< The string displayed upon successful creation of an option page.
        $this->local_options_save_success = 'The settings were updated successfully.';                        ///< The string displayed upon successful update of an option page.
        $this->local_options_save_failure = 'The settings were not updated.';                                 ///< The string displayed upon unsuccessful update of an option page.
        $this->local_options_url_bad = 'This root server URL will not work for this plugin.';                 ///< The string displayed if a root server URI fails to point to a valid root server.
        $this->local_options_access_failure = 'You are not allowed to perform this operation.';               ///< This is displayed if a user attempts a no-no.
        $this->local_options_unsaved_message = 'You have unsaved changes. Are you sure you want to leave without saving them?';   ///< This is displayed if a user attempts to leave a page without saving the options.
        $this->local_options_settings_id_prompt = 'The ID for this Setting is ';                              ///< This is so that users can see the ID for the setting.
        $this->local_options_settings_location_checkbox_label = 'Text Searches Start Off with the "Location" Checkbox On.';                              ///< This is so that users can see the ID for the setting.
    
        /// These are all for the admin page option sheets.
        $this->local_options_name_label = 'Setting Name:';                    ///< The Label for the setting name item.
        $this->local_options_rootserver_label = 'Root Server:';               ///< The Label for the root server item.
        $this->local_options_new_search_label = 'New Search URL:';            ///< The Label for the new search item.
        $this->local_options_gkey_label = 'Google Maps API Key:';             ///< The Label for the Google Maps API Key item.
        $this->local_options_no_name_string = 'Enter Setting Name';           ///< The Value to use for a name field for a setting with no name.
        $this->local_options_no_root_server_string = 'Enter a Root Server URL';                               ///< The Value to use for a root with no URL.
        $this->local_options_no_new_search_string = 'Enter a New Search URL'; ///< The Value to use for a new search with no URL.
        $this->local_options_no_gkey_string = 'Enter a New API Key';          ///< The Value to use for a new search with no URL.
        $this->local_options_test_server = 'Test';                            ///< This is the title for the "test server" button.
        $this->local_options_test_server_success = 'Version ';                ///< This is a prefix for the version, on success.
        $this->local_options_test_server_failure = 'This Root Server URL is not Valid';                       ///< This is a prefix for the version, on failure.
        $this->local_options_test_server_tooltip = 'This tests the root server, to see if it is OK.';         ///< This is the tooltip text for the "test server" button.
        $this->local_options_map_label = 'Select a Center Point and Zoom Level for Map Displays';             ///< The Label for the map.
        $this->local_options_mobile_legend = 'These affect the Various Interactive Searches (such as Map, Mobile and Advanced)';  ///< This indicates that the enclosed settings are for the fast mobile lookup.
        $this->local_options_mobile_grace_period_label = 'Grace Period:';     ///< When you do a "later today" search, you get a "Grace Period."
        $this->local_options_mobile_region_bias_label = 'Region Bias:';       ///< The label for the Region Bias Selector.
        $this->local_options_mobile_time_offset_label = 'Time Offset:';       ///< This may have an offset (time zone difference) from the main server.
        $this->local_options_initial_view = array (                           ///< The list of choices for presentation in the popup.
                                                    'map' => 'Map',
                                                    'text' => 'Text',
                                                    'advanced_map' => 'Advanced Map',
                                                    'advanced_text' => 'Advanced Text'
                                                    );
        $this->local_options_initial_view_prompt = 'Initial Search Type:';    ///< The label for the initial view popup.
        $this->local_options_theme_prompt = 'Select a Color Theme:';          ///< The label for the theme selection popup.
        $this->local_options_more_styles_label = 'Add CSS Styles to the Plugin:';                             ///< The label for the Additional CSS textarea.
        $this->local_options_distance_prompt = 'Distance Units:';             ///< This is for the distance units select.
        $this->local_options_distance_disclaimer = 'This will not affect all of the displays.';               ///< This tells the admin that only some stuff will be affected.
        $this->local_options_grace_period_disclaimer = 'Minutes Elapsed Before A Meeting is Considered "Past" (For the fast Lookup Searches).';      ///< This explains what the grace period means.
        $this->local_options_time_offset_disclaimer = 'Hours of Difference From the Main Server (This is usually not necessary).';            ///< This explains what the time offset means.
        $this->local_options_miles = 'Miles';                                 ///< The string for miles.
        $this->local_options_kilometers = 'Kilometers';                       ///< The string for kilometers.
        $this->local_options_selectLocation_checkbox_text = 'Only Display Location Services for Mobile Devices';  ///< The label for the location services checkbox.
    
        $this->local_options_time_format_prompt = 'Time Format:';             ///< The label for the time format selection popup.
        $this->local_options_time_format_ampm = 'Ante Meridian (HH:MM AM/PM)';    ///< Ante Meridian Format Option
        $this->local_options_time_format_military = 'Military (HH:MM)';       ///< Military Time Format Option
    
        $this->local_options_google_api_label = 'Google Maps API Key:';       ///< The label for the Google Maps API Key Text Entry.
        
        $this->local_options_auto_search_radius_prompt = 'Auto Search Density:';    ///< The label for the Auto Search Density popup.
        $this->local_options_auto_search_radius_display_names = array (             ///< The values for the auto-search density popup.
                                                                        'Minimum'   => -2,
                                                                        'Less'      => -5,
                                                                        'Normal'    => -10,
                                                                        'More'      => -15,
                                                                        'Maximum'   => -30,
                                                                        'Super Maximum' => -100
                                                                        );
    
        $this->local_options_week_begins_on_prompt = 'Weeks begin on:';       ///< This is the label for the week start popup menu.

        $this->local_no_root_server = 'You need to provide a root server URI in order for this to work.';    ///< Displayed if there was no root server provided.

        /// These are for the actual search displays
        $this->local_select_search = 'Select a Quick Search';                 ///< Used for the "filler" in the quick search popup.
        $this->local_clear_search = 'Clear Search Results';                   ///< Used for the "Clear" item in the quick search popup.
        $this->local_menu_new_search_text = 'New Search';                     ///< For the new search menu in the old-style BMLT search.
        $this->local_cant_find_meetings_display = 'No Meetings Found In This Search'; ///< When the new map search cannot find any meetings.
        $this->local_single_meeting_tooltip = 'Follow This Link for Details About This Meeting.'; ///< The tooltip shown for a single meeting.
        $this->local_gm_link_tooltip = 'Follow This Link to be Taken to A Google Maps Location for This Meeting.';    ///< The tooltip shown for the Google Maps link.
    
        /// These are for the change display
        $this->local_change_label_date =  'Change Date:';                     ///< The date when the change was made.
        $this->local_change_label_meeting_name =  'Meeting Name:';            ///< The name of the changed meeting.
        $this->local_change_label_service_body_name =  'Service Body:';       ///< The name of the meeting's Service body.
        $this->local_change_label_admin_name =  'Changed By:';                ///< The name of the Service Body Admin that made the change.
        $this->local_change_label_description =  'Description:';              ///< The description of the change.
        $this->local_change_date_format = 'F j Y, \a\t g:i A';                ///< The format in which the change date/time is displayed.
    
        /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
        $this->local_noscript = 'This will not work, because you do not have JavaScript active.';             ///< The string displayed in a <noscript> element.
    
        /************************************************************************************//**
        *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
        ****************************************************************************************/
    
        /// These are all for the [[bmlt_nouveau]] shortcode.
        $this->local_nouveau_advanced_button = 'More Options';                ///< The button name for the advanced search in the nouveau search.
        $this->local_nouveau_map_button = 'Search By Map Instead of Text';    ///< The button name for the map search in the nouveau search.
        $this->local_nouveau_text_button = 'Search By Text instead of Map';   ///< The button name for the text search in the nouveau search.
        $this->local_nouveau_text_go_button = 'GO';                           ///< The button name for the "GO" button in the text search in the nouveau search.
        $this->local_nouveau_text_item_default_text = 'Enter Search Text';    ///< The text that fills an empty text item.
        $this->local_nouveau_text_location_label_text = 'This is a location or postcode';         ///< The label text for the location checkbox.
        $this->local_nouveau_advanced_map_radius_label_1 = 'Find Meetings Within';                ///< The label text for the radius popup.
        $this->local_nouveau_advanced_map_radius_label_2 = 'of the Marker Location.';             ///< The second part of the label.
        $this->local_nouveau_advanced_map_radius_value_auto = 'An Automatically Chosen Radius';   ///< The second part of the label, if Miles
        $this->local_nouveau_advanced_map_radius_value_km = 'Km';                                 ///< The second part of the popup value, if Kilometers
        $this->local_nouveau_advanced_map_radius_value_mi = 'Miles';                              ///< The second part of the popup value, if Miles
        $this->local_nouveau_advanced_weekdays_disclosure_text = 'Selected Weekdays';             ///< The text that is used for the weekdays disclosure link.
        $this->local_nouveau_advanced_formats_disclosure_text = 'Selected Formats';               ///< The text that is used for the formats disclosure link.
        $this->local_nouveau_advanced_service_bodies_disclosure_text = 'Selected Service Bodies'; ///< The text that is used for the service bodies disclosure link.
        $this->local_nouveau_select_search_spec_text = 'Specify A New Search';                    ///< The text that is used for the link that tells you to select the search specification.
        $this->local_nouveau_select_search_results_text = 'View the Results of the Last Search';  ///< The text that is used for the link that tells you to select the search results.
        $this->local_nouveau_cant_find_meetings_display = 'No Meetings Found In This Search';     ///< When the new map search cannot find any meetings.
        $this->local_nouveau_cant_lookup_display = 'Unable to determine your location.';          ///< Displayed if the app is unable to determine the location.
        $this->local_nouveau_display_map_results_text = 'Display the Search Results in a Map';    ///< The text for the display map results disclosure link.
        $this->local_nouveau_display_list_results_text = 'Display the Search Results in a List';  ///< The text for the display list results disclosure link.
        $this->local_nouveau_table_header_array = array ( 'Nation', 'State', 'County', 'Town', 'Meeting Name', 'Weekday', 'Start Time', 'Location', 'Format', ' ' );
        $this->local_nouveau_weekday_long_array = array ( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
        $this->local_nouveau_weekday_short_array = array ( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
    
        $this->local_nouveau_meeting_results_count_sprintf_format = '%s Meetings Found';
        $this->local_nouveau_meeting_results_selection_count_sprintf_format = '%s Meetings Selected, From %s Meetings Found';
        $this->local_nouveau_meeting_results_single_selection_count_sprintf_format = '1 Meeting Selected, From %s Meetings Found';
        $this->local_nouveau_single_time_sprintf_format = 'Meeting gathers every %s, at %s, and lasts for %s.';
        $this->local_nouveau_single_duration_sprintf_format_1_hr = '1 hour';
        $this->local_nouveau_single_duration_sprintf_format_mins = '%s minutes';
        $this->local_nouveau_single_duration_sprintf_format_hrs = '%s hours';
        $this->local_nouveau_single_duration_sprintf_format_hr_mins = '1 hour and %s minutes';
        $this->local_nouveau_single_duration_sprintf_format_hrs_mins = '%s hours and %s minutes';
    
        /// These are all variants of the text that explains the location of a single meeting (Details View).
        $this->local_nouveau_location_sprintf_format_loc_street_info = '%s, %s (%s)';
        $this->local_nouveau_location_sprintf_format_loc_street = '%s, %s';
        $this->local_nouveau_location_sprintf_format_street_info = '%s (%s)';
        $this->local_nouveau_location_sprintf_format_loc_info = '%s (%s)';
        $this->local_nouveau_location_sprintf_format_street = '%s';
        $this->local_nouveau_location_sprintf_format_loc = '%s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_town_province_zip = '%s, %s (%s), %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_town_province_zip = '%s, %s, %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_town_province_zip = '%s (%s), %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_town_province_zip = '%s (%s), %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_street_town_province_zip = '%s, %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_town_province_zip = '%s, %s, %s %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_town_province = '%s, %s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_town_province = '%s, %s, %s, %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_town_province = '%s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_town_province = '%s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_street_town_province = '%s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_town_province = '%s, %s %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_town_zip = '%s, %s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_town_zip = '%s, %s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_town_zip = '%s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_town_zip = '%s (%s), %s %s';
        $this->local_nouveau_location_sprintf_format_single_street_town_zip = '%s, %s %s';
        $this->local_nouveau_location_sprintf_format_single_loc_town_zip = '%s, %s %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_province_zip = '%s, %s (%s), %s, %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_province_zip = '%s, %s, %s, %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_province_zip = '%s (%s), %s, %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_province_zip = '%s (%s), %s, %s';
        $this->local_nouveau_location_sprintf_format_single_street_province_zip = '%s, %s, %s';
        $this->local_nouveau_location_sprintf_format_single_loc_province_zip = '%s, %s, %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_province = '%s, %s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_province = '%s, %s, %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_province = '%s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_province = '%s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_street_province = '%s, %s';
        $this->local_nouveau_location_sprintf_format_single_loc_province = '%s, %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info_zip = '%s, %s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_loc_street_zip = '%s, %s, %s';
        $this->local_nouveau_location_sprintf_format_single_street_info_zip = '%s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_loc_info_zip = '%s (%s), %s';
        $this->local_nouveau_location_sprintf_format_single_street_zip = '%s, %s';
        $this->local_nouveau_location_sprintf_format_single_loc_zip = '%s, %s';
    
        $this->local_nouveau_location_sprintf_format_single_loc_street_info = '%s, %s (%s)';
        $this->local_nouveau_location_sprintf_format_single_loc_street = '%s, %s,';
        $this->local_nouveau_location_sprintf_format_single_street_info = '%s (%s)';
        $this->local_nouveau_location_sprintf_format_single_loc_info = '%s (%s)';
        $this->local_nouveau_location_sprintf_format_single_street = '%s';
        $this->local_nouveau_location_sprintf_format_single_loc = '%s';
    
        $this->local_nouveau_location_sprintf_format_wtf = 'No Location Given';
    
        $this->local_nouveau_location_services_set_my_location_advanced_button = 'Set the Marker to My Current Location';
        $this->local_nouveau_location_services_find_all_meetings_nearby_button = 'Find Meetings Near Me';
        $this->local_nouveau_location_services_find_all_meetings_nearby_later_today_button = 'Find Meetings Near Me Later Today';
        $this->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button = 'Find Meetings Near Me Tomorrow';
    
        $this->local_nouveau_location_sprintf_format_duration_title = 'This meeting is %s hours and %s minutes long.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_title = 'This meeting is 1 hour long.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = 'This meeting is 1 hour and %s minutes long.';
        $this->local_nouveau_location_sprintf_format_duration_hours_only_title = 'This meeting is %s hours long.';
        $this->local_nouveau_lookup_location_failed = "The address lookup was not completed successfully.";
        $this->local_nouveau_lookup_location_server_error = "The address lookup was not completed successfully, due to a server error.";
        $this->local_nouveau_time_sprintf_format = '%d:%02d %s';
        $this->local_nouveau_am = 'AM';
        $this->local_nouveau_pm = 'PM';
        $this->local_nouveau_noon = 'Noon';
        $this->local_nouveau_midnight = 'Midnight';
        $this->local_nouveau_advanced_map_radius_value_array = "0.25, 0.5, 1.0, 2.0, 5.0, 10.0, 15.0, 20.0, 50.0, 100.0, 200.0";
        $this->local_nouveau_meeting_details_link_title = 'Get more details about this meeting.';
        $this->local_nouveau_meeting_details_map_link_uri_format = 'https://maps.google.com/maps?q=%f,%f';
        $this->local_nouveau_meeting_details_map_link_text = 'Map To Meeting';

        $this->local_nouveau_single_formats_label = 'Meeting Formats:';
        $this->local_nouveau_single_service_body_label = 'Service Body:';

        $this->local_nouveau_prompt_array = array (
                                                    'weekday_tinyint' => 'Weekday',
                                                    'start_time' => 'Start Time',
                                                    'duration_time' => 'Duration',
                                                    'formats' => 'Format',
                                                    'distance_in_miles' => 'Distance In Miles',
                                                    'distance_in_km' => 'Distance In Kilometers',
                                                    'meeting_name' => 'Meeting Name',
                                                    'location_text' => 'Location Name',
                                                    'location_street' => 'Street Address',
                                                    'location_city_subsection' => 'Borough',
                                                    'location_neighborhood' => 'Neighborhood',
                                                    'location_municipality' => 'Town',
                                                    'location_sub_province' => 'County',
                                                    'location_province' => 'State',
                                                    'location_nation' => 'Nation',
                                                    'location_postal_code_1' => 'Zip Code',
                                                    'location_info' => 'Extra Information'
                                                    );
    
        /************************************************************************************//**
        *                   TABLE SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                    *
        ****************************************************************************************/
        $this->local_table_tab_loading_title_format        = 'Getting meetings for %s';
        $this->local_table_header_time_label              = 'Time';
        $this->local_table_header_meeting_name_label      = 'Meeting Name';
        $this->local_table_header_town_label              = 'Town';
        $this->local_table_header_address_label           = 'Address';
        $this->local_table_header_format_label            = 'Format';
        $this->local_table_header_tab_title_format        = 'Display meetings for %s';
        $this->local_table_ante_meridian                  = '"AM","PM","Noon","Midnight"';
        $this->local_table_no_meetings_format             = 'No meetings on %s';
                                                
        /************************************************************************************//**
        *                      STATIC DATA MEMBERS (SPECIAL LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// This is the only localizable string that is not processed. This is because it contains HTML. However, it is also a "hidden" string that is only displayed when the browser does not support JS.
        $this->local_no_js_warning = '<noscript class="no_js">This Meeting Search will not work because your browser does not support JavaScript. However, you can use the <a rel="external nofollow" href="###ROOT_SERVER###">main server</a> to do the search.</noscript>'; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
        ****************************************************************************************/
                                    
        $this->local_new_map_option_1_label = 'Search Options (Not Applied Unless This Section Is Open):';
        $this->local_new_map_weekdays = 'Meetings Gather on These Weekdays:';
        $this->local_new_map_all_weekdays = 'All';
        $this->local_new_map_all_weekdays_title = 'Find meetings for every day.';
        $this->local_new_map_weekdays_title = 'Find meetings that occur on ';
        $this->local_new_map_formats = 'Meetings Have These Formats:';
        $this->local_new_map_all_formats = 'All';
        $this->local_new_map_all_formats_title = 'Find meetings for every format.';
        $this->local_new_map_js_center_marker_current_radius_1 = 'The circle is about ';
        $this->local_new_map_js_center_marker_current_radius_2_km = ' kilometers wide.';
        $this->local_new_map_js_center_marker_current_radius_2_mi = ' miles wide.';
        $this->local_new_map_js_diameter_choices = array ( 0.25, 0.5, 1.0, 1.5, 2.0, 3.0, 5.0, 10.0, 15.0, 20.0, 25.0, 30.0, 50.0, 100.0 );
        $this->local_new_map_js_new_search = 'New Search';
        $this->local_new_map_option_loc_label = 'Enter A Location:';
        $this->local_new_map_option_loc_popup_label_1 = 'Search for meetings within';
        $this->local_new_map_option_loc_popup_label_2 = 'of the location.';
        $this->local_new_map_option_loc_popup_km = 'Km';
        $this->local_new_map_option_loc_popup_mi = 'Miles';
        $this->local_new_map_option_loc_popup_auto = 'an automatically chosen distance';
        $this->local_new_map_center_marker_distance_suffix = ' from the center marker.';
        $this->local_new_map_center_marker_description = 'This is your chosen location.';
        $this->local_new_map_text_entry_fieldset_label = 'Enter an Address, Postcode or Location';
        $this->local_new_map_text_entry_default_text = 'Enter an Address, Postcode or Location';
        $this->local_new_map_location_submit_button_text = 'Search for Meetings Near This Location';
    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// The units for distance.
        $this->local_mobile_kilometers = 'Kilometers';
        $this->local_mobile_miles = 'Miles';
        $this->local_mobile_distance = 'Distance';  ///< Distance (the string)
    
        /// The page titles.
        $this->local_mobile_results_page_title = 'Quick Meeting Search Results';
        $this->local_mobile_results_form_title = 'Find Nearby Meetings Quickly';
    
        /// The fast GPS lookup links.
        $this->local_GPS_banner = 'Select A Fast Meeting Lookup';
        $this->local_GPS_banner_subtext = 'Bookmark these links for even faster searches in the future.';
        $this->local_search_all = 'Search for all meetings near my present location.';
        $this->local_search_today = 'Later Today';
        $this->local_search_tomorrow = 'Tomorrow';
    
        /// The search for an address form.
        $this->local_list_check = 'If you are experiencing difficulty with the interactive map, or wish to have the results returned as a list, check this box and enter an address.';
        $this->local_search_address_single = 'Search for Meetings Near An Address';
    
        /// Used instead of "near my present location."
        $this->local_search_all_address = 'Search for all meetings near this address.';
        $this->local_search_submit_button = 'Search For Meetings';
    
        /// This is what is entered into the text box.
        $this->local_enter_an_address = 'Enter An Address';
    
        /// Error messages.
        $this->local_mobile_fail_no_meetings = 'No Meetings Found!';
        $this->local_server_fail = 'The search failed because the server encountered an error!';
        $this->local_cant_find_address = 'Cannot Determine the Location From the Address Information!';
        $this->local_cannot_determine_location = 'Cannot Determine Your Current Location!';
        $this->local_enter_address_alert = 'Please enter an address!';
    
        /// The text for the "Map to Meeting" links
        $this->local_map_link = 'Map to Meeting';
    
        /// Only used for WML pages
        $this->local_next_card = 'Next Meeting >>';
        $this->local_prev_card = '<< Previous Meeting';
    
        /// Used for the info and list windows.
        $this->local_formats = 'Formats';
        $this->local_noon = 'Noon';
        $this->local_midnight = 'Midnight';
    
        /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
        $this->local_weekdays = array ( 'ERROR', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );
        $this->local_weekdays_short = array ( 'ERR', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
    
        /************************************************************************************//**
        *                          STATIC DATA MEMBERS (QUICKSEARCH)                            *
        ****************************************************************************************/
        $this->local_quicksearch_select_option_0 = 'Search Everywhere';
        $this->local_quicksearch_display_too_large = 'Too many results. Please narrow your search.';
    }
}
