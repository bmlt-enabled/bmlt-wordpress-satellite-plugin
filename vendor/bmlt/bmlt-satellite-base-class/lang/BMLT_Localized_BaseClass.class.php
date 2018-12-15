<?php
// English
/****************************************************************************************//**
*   \file   BMLT_Localized_BaseClass.class.php                                              *
*                                                                                           *
*   \brief  This file contains The base localizations c;ass                                 *
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

abstract class BMLT_Localized_BaseClass
    {
    /************************************************************************************//**
    *                           STATIC DATA MEMBERS (LOCALIZABLE)                           *
    ****************************************************************************************/

    /// These are all for the admin pages.
    var $local_options_lang_prompt;                       ///< The label for the Language Selector.
    var $local_options_title;    ///< This is the title that is displayed over the options.
    var $local_menu_string;                            ///< The name of the menu item.
    var $local_options_prefix;                      ///< The string displayed before each number in the options popup.
    var $local_options_add_new;                   ///< The string displayed in the "Add New Option" button.
    var $local_options_save;                           ///< The string displayed in the "Save Changes" button.
    var $local_options_delete_option;           ///< The string displayed in the "Delete Option" button.
    var $local_options_delete_failure; ///< The string displayed upon unsuccessful deletion of an option page.
    var $local_options_create_failure; ///< The string displayed upon unsuccessful creation of an option page.
    var $local_options_delete_option_confirm;    ///< The string displayed in the "Are you sure?" confirm.
    var $local_options_delete_success;                        ///< The string displayed upon successful deletion of an option page.
    var $local_options_create_success;                        ///< The string displayed upon successful creation of an option page.
    var $local_options_save_success;                        ///< The string displayed upon successful update of an option page.
    var $local_options_save_failure;                                 ///< The string displayed upon unsuccessful update of an option page.
    var $local_options_url_bad;                 ///< The string displayed if a root server URI fails to point to a valid root server.
    var $local_options_access_failure;               ///< This is displayed if a user attempts a no-no.
    var $local_options_unsaved_message;   ///< This is displayed if a user attempts to leave a page without saving the options.
    var $local_options_settings_id_prompt;                              ///< This is so that users can see the ID for the setting.
    var $local_options_settings_location_checkbox_label;                              ///< This is so that users can see the ID for the setting.

    /// These are all for the admin page option sheets.
    var $local_options_name_label;                    ///< The Label for the setting name item.
    var $local_options_rootserver_label;               ///< The Label for the root server item.
    var $local_options_new_search_label;            ///< The Label for the new search item.
    var $local_options_gkey_label;             ///< The Label for the Google Maps API Key item.
    var $local_options_no_name_string;           ///< The Value to use for a name field for a setting with no name.
    var $local_options_no_root_server_string;                               ///< The Value to use for a root with no URL.
    var $local_options_no_new_search_string; ///< The Value to use for a new search with no URL.
    var $local_options_no_gkey_string;          ///< The Value to use for a new search with no URL.
    var $local_options_test_server;                            ///< This is the title for the "test server" button.
    var $local_options_test_server_success;                ///< This is a prefix for the version, on success.
    var $local_options_test_server_failure;                       ///< This is a prefix for the version, on failure.
    var $local_options_test_server_tooltip;         ///< This is the tooltip text for the "test server" button.
    var $local_options_map_label;             ///< The Label for the map.
    var $local_options_mobile_legend;  ///< This indicates that the enclosed settings are for the fast mobile lookup.
    var $local_options_mobile_grace_period_label;     ///< When you do a "later today" search, you get a "Grace Period."
    var $local_options_mobile_region_bias_label;       ///< The label for the Region Bias Selector.
    var $local_options_mobile_time_offset_label;       ///< This may have an offset (time zone difference) from the main server.
    var $local_options_initial_view;    ///< The list of choices for presentation in the popup.
    var $local_options_initial_view_prompt;    ///< The label for the initial view popup.
    var $local_options_theme_prompt;          ///< The label for the theme selection popup.
    var $local_options_more_styles_label;                             ///< The label for the Additional CSS textarea.
    var $local_options_distance_prompt;             ///< This is for the distance units select.
    var $local_options_distance_disclaimer;               ///< This tells the admin that only some stuff will be affected.
    var $local_options_grace_period_disclaimer;      ///< This explains what the grace period means.
    var $local_options_time_offset_disclaimer;            ///< This explains what the time offset means.
    var $local_options_miles;                                 ///< The string for miles.
    var $local_options_kilometers;                       ///< The string for kilometers.
    var $local_options_selectLocation_checkbox_text;  ///< The label for the location services checkbox.

    var $local_options_time_format_prompt;             ///< The label for the time format selection popup.
    var $local_options_time_format_ampm;    ///< Ante Meridian Format Option
    var $local_options_time_format_military;       ///< Military Time Format Option

    var $local_options_google_api_label;       ///< The label for the Google Maps API Key Text Entry.
    var $local_options_auto_search_radius_prompt;    ///< The label for the Auto Search Density popup.
    var $local_options_auto_search_radius_display_names;             ///< The values for the auto-search density popup.
    var $local_options_week_begins_on_prompt;       ///< This is the label for the week start popup menu.

    var $local_no_root_server;    ///< Displayed if there was no root server provided.

    /// These are for the actual search displays
    var $local_select_search;                 ///< Used for the "filler" in the quick search popup.
    var $local_clear_search;                   ///< Used for the "Clear" item in the quick search popup.
    var $local_menu_new_search_text;                     ///< For the new search menu in the old-style BMLT search.
    var $local_cant_find_meetings_display; ///< When the new map search cannot find any meetings.
    var $local_single_meeting_tooltip; ///< The tooltip shown for a single meeting.
    var $local_gm_link_tooltip;    ///< The tooltip shown for the Google Maps link.

    /// These are for the change display
    var $local_change_label_date;                     ///< The date when the change was made.
    var $local_change_label_meeting_name;            ///< The name of the changed meeting.
    var $local_change_label_service_body_name;       ///< The name of the meeting's Service body.
    var $local_change_label_admin_name;                ///< The name of the Service Body Admin that made the change.
    var $local_change_label_description;              ///< The description of the change.
    var $local_change_date_format;                ///< The format in which the change date/time is displayed.

    /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
    var $local_noscript;             ///< The string displayed in a <noscript> element.

    /************************************************************************************//**
    *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
    ****************************************************************************************/

    /// These are all for the [[bmlt_nouveau]] shortcode.
    var $local_nouveau_advanced_button;                ///< The button name for the advanced search in the nouveau search.
    var $local_nouveau_map_button;    ///< The button name for the map search in the nouveau search.
    var $local_nouveau_text_button;   ///< The button name for the text search in the nouveau search.
    var $local_nouveau_text_go_button;                           ///< The button name for the "GO" button in the text search in the nouveau search.
    var $local_nouveau_text_item_default_text;    ///< The text that fills an empty text item.
    var $local_nouveau_text_location_label_text;         ///< The label text for the location checkbox.
    var $local_nouveau_advanced_map_radius_label_1;                ///< The label text for the radius popup.
    var $local_nouveau_advanced_map_radius_label_2;             ///< The second part of the label.
    var $local_nouveau_advanced_map_radius_value_auto;   ///< The second part of the label, if Miles
    var $local_nouveau_advanced_map_radius_value_km;                                 ///< The second part of the popup value, if Kilometers
    var $local_nouveau_advanced_map_radius_value_mi;                              ///< The second part of the popup value, if Miles
    var $local_nouveau_advanced_weekdays_disclosure_text;             ///< The text that is used for the weekdays disclosure link.
    var $local_nouveau_advanced_formats_disclosure_text;               ///< The text that is used for the formats disclosure link.
    var $local_nouveau_advanced_service_bodies_disclosure_text; ///< The text that is used for the service bodies disclosure link.
    var $local_nouveau_select_search_spec_text;                    ///< The text that is used for the link that tells you to select the search specification.
    var $local_nouveau_select_search_results_text;  ///< The text that is used for the link that tells you to select the search results.
    var $local_nouveau_cant_find_meetings_display;     ///< When the new map search cannot find any meetings.
    var $local_nouveau_cant_lookup_display;          ///< Displayed if the app is unable to determine the location.
    var $local_nouveau_display_map_results_text;    ///< The text for the display map results disclosure link.
    var $local_nouveau_display_list_results_text;  ///< The text for the display list results disclosure link.
    var $local_nouveau_table_header_array;
    var $local_nouveau_weekday_long_array;
    var $local_nouveau_weekday_short_array;

    var $local_nouveau_meeting_results_count_sprintf_format;
    var $local_nouveau_meeting_results_selection_count_sprintf_format;
    var $local_nouveau_meeting_results_single_selection_count_sprintf_format;
    var $local_nouveau_single_time_sprintf_format;
    var $local_nouveau_single_duration_sprintf_format_1_hr;
    var $local_nouveau_single_duration_sprintf_format_mins;
    var $local_nouveau_single_duration_sprintf_format_hrs;
    var $local_nouveau_single_duration_sprintf_format_hr_mins;
    var $local_nouveau_single_duration_sprintf_format_hrs_mins;

    /// These are all variants of the text that explains the location of a single meeting (Details View).
    var $local_nouveau_location_sprintf_format_loc_street_info;
    var $local_nouveau_location_sprintf_format_loc_street;
    var $local_nouveau_location_sprintf_format_street_info;
    var $local_nouveau_location_sprintf_format_loc_info;
    var $local_nouveau_location_sprintf_format_street;
    var $local_nouveau_location_sprintf_format_loc;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_town_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_street_town_province_zip;
    var $local_nouveau_location_sprintf_format_single_street_info_town_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_info_town_province_zip;
    var $local_nouveau_location_sprintf_format_single_street_town_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_town_province_zip;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_town_province;
    var $local_nouveau_location_sprintf_format_single_loc_street_town_province;
    var $local_nouveau_location_sprintf_format_single_street_info_town_province;
    var $local_nouveau_location_sprintf_format_single_loc_info_town_province;
    var $local_nouveau_location_sprintf_format_single_street_town_province;
    var $local_nouveau_location_sprintf_format_single_loc_town_province;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_town_zip;
    var $local_nouveau_location_sprintf_format_single_loc_street_town_zip;
    var $local_nouveau_location_sprintf_format_single_street_info_town_zip;
    var $local_nouveau_location_sprintf_format_single_loc_info_town_zip;
    var $local_nouveau_location_sprintf_format_single_street_town_zip;
    var $local_nouveau_location_sprintf_format_single_loc_town_zip;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_street_province_zip;
    var $local_nouveau_location_sprintf_format_single_street_info_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_info_province_zip;
    var $local_nouveau_location_sprintf_format_single_street_province_zip;
    var $local_nouveau_location_sprintf_format_single_loc_province_zip;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_province;
    var $local_nouveau_location_sprintf_format_single_loc_street_province;
    var $local_nouveau_location_sprintf_format_single_street_info_province;
    var $local_nouveau_location_sprintf_format_single_loc_info_province;
    var $local_nouveau_location_sprintf_format_single_street_province;
    var $local_nouveau_location_sprintf_format_single_loc_province;

    var $local_nouveau_location_sprintf_format_single_loc_street_info_zip;
    var $local_nouveau_location_sprintf_format_single_loc_street_zip;
    var $local_nouveau_location_sprintf_format_single_street_info_zip;
    var $local_nouveau_location_sprintf_format_single_loc_info_zip;
    var $local_nouveau_location_sprintf_format_single_street_zip;
    var $local_nouveau_location_sprintf_format_single_loc_zip;

    var $local_nouveau_location_sprintf_format_single_loc_street_info;
    var $local_nouveau_location_sprintf_format_single_loc_street;
    var $local_nouveau_location_sprintf_format_single_street_info;
    var $local_nouveau_location_sprintf_format_single_loc_info;
    var $local_nouveau_location_sprintf_format_single_street;
    var $local_nouveau_location_sprintf_format_single_loc;

    var $local_nouveau_location_sprintf_format_wtf;

    var $local_nouveau_location_services_set_my_location_advanced_button;
    var $local_nouveau_location_services_find_all_meetings_nearby_button;
    var $local_nouveau_location_services_find_all_meetings_nearby_later_today_button;
    var $local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button;

    var $local_nouveau_location_sprintf_format_duration_title;
    var $local_nouveau_location_sprintf_format_duration_hour_only_title;
    var $local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title;
    var $local_nouveau_location_sprintf_format_duration_hours_only_title;
    var $local_nouveau_lookup_location_failed;
    var $local_nouveau_lookup_location_server_error;
    var $local_nouveau_time_sprintf_format;
    var $local_nouveau_am;
    var $local_nouveau_pm;
    var $local_nouveau_noon;
    var $local_nouveau_midnight;
    var $local_nouveau_advanced_map_radius_value_array;
    var $local_nouveau_meeting_details_link_title;
    var $local_nouveau_meeting_details_map_link_uri_format;
    var $local_nouveau_meeting_details_map_link_text;

    var $local_nouveau_single_formats_label;
    var $local_nouveau_single_service_body_label;

    var $local_nouveau_prompt_array;

    /************************************************************************************//**
    *                   TABLE SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                    *
    ****************************************************************************************/
    var $local_table_tab_loading_title_format       ;
    var $local_table_header_time_label             ;
    var $local_table_header_meeting_name_label     ;
    var $local_table_header_town_label             ;
    var $local_table_header_address_label          ;
    var $local_table_header_format_label           ;
    var $local_table_header_tab_title_format       ;
    var $local_table_ante_meridian                 ;
    var $local_table_no_meetings_format            ;
                                            
    /************************************************************************************//**
    *                      STATIC DATA MEMBERS (SPECIAL LOCALIZABLE)                        *
    ****************************************************************************************/

    /// This is the only localizable string that is not processed. This is because it contains HTML. However, it is also a "hidden" string that is only displayed when the browser does not support JS.
    var $local_no_js_warning; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                
    /************************************************************************************//**
    *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
    ****************************************************************************************/
                                
    var $local_new_map_option_1_label;
    var $local_new_map_weekdays;
    var $local_new_map_all_weekdays;
    var $local_new_map_all_weekdays_title;
    var $local_new_map_weekdays_title;
    var $local_new_map_formats;
    var $local_new_map_all_formats;
    var $local_new_map_all_formats_title;
    var $local_new_map_js_center_marker_current_radius_1;
    var $local_new_map_js_center_marker_current_radius_2_km;
    var $local_new_map_js_center_marker_current_radius_2_mi;
    var $local_new_map_js_diameter_choices;
    var $local_new_map_js_new_search;
    var $local_new_map_option_loc_label;
    var $local_new_map_option_loc_popup_label_1;
    var $local_new_map_option_loc_popup_label_2;
    var $local_new_map_option_loc_popup_km;
    var $local_new_map_option_loc_popup_mi;
    var $local_new_map_option_loc_popup_auto;
    var $local_new_map_center_marker_distance_suffix;
    var $local_new_map_center_marker_description;
    var $local_new_map_text_entry_fieldset_label;
    var $local_new_map_text_entry_default_text;
    var $local_new_map_location_submit_button_text;

    /************************************************************************************//**
    *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
    ****************************************************************************************/

    /// The units for distance.
    var $local_mobile_kilometers;
    var $local_mobile_miles;
    var $local_mobile_distance;  ///< Distance (the string)

    /// The page titles.
    var $local_mobile_results_page_title;
    var $local_mobile_results_form_title;

    /// The fast GPS lookup links.
    var $local_GPS_banner;
    var $local_GPS_banner_subtext;
    var $local_search_all;
    var $local_search_today;
    var $local_search_tomorrow;

    /// The search for an address form.
    var $local_list_check;
    var $local_search_address_single;

    /// Used instead of "near my present location."
    var $local_search_all_address;
    var $local_search_submit_button;

    /// This is what is entered into the text box.
    var $local_enter_an_address;

    /// Error messages.
    var $local_mobile_fail_no_meetings;
    var $local_server_fail;
    var $local_cant_find_address;
    var $local_cannot_determine_location;
    var $local_enter_address_alert;

    /// The text for the "Map to Meeting" links
    var $local_map_link;

    /// Only used for WML pages
    var $local_next_card;
    var $local_prev_card;

    /// Used for the info and list windows.
    var $local_formats;
    var $local_noon;
    var $local_midnight;

    /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
    var $local_weekdays;
    var $local_weekdays_short;
    };
?>