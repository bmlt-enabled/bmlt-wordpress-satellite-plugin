<?php
// Deutsch
/****************************************************************************************//**
*   \file   lang_de.php                                                                     *
*                                                                                           *
*   \brief  This file contains German localizations.                                        *
*   \version 3.9.4                                                                          *
*                                                                                           *
*   This file is part of the BMLT Common Satellite Base Class Project. The project GitHub   *
*   page is available here: https://github.com/MAGSHARE/BMLT-Common-CMS-Plugin-Class        *
*                                                                                           *
*   This file is part of the Basic Meeting List Toolbox (BMLT).                             *
*                                                                                           *
*   Find out more at: https://bmlt.app                                              *
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

require_once ( dirname ( __FILE__ ) . '/BMLT_Localized_BaseClass.class.php' );

class BMLT_Localized_BaseClass_de extends BMLT_Localized_BaseClass
    {
    function __construct ()
        {
        /************************************************************************************//**
        *                           STATIC DATA MEMBERS (LOCALIZABLE)                           *
        ****************************************************************************************/
    
        /// These are all for the admin pages.
        $this->local_options_lang_prompt = 'Language:';                       ///< The label for the Language Selector.
        $this->local_options_title = 'Basic Meeting List Toolbox Einstellungen';    ///< This is the title that is displayed over the options.
        $this->local_menu_string = 'BMLT Einstellungen';                            ///< The name of the menu item.
        $this->local_options_prefix = 'Einstellungen auswählen ';                      ///< The string displayed before each number in the options popup.
        $this->local_options_add_new = 'Neue Einstellung hinzufügen';                   ///< The string displayed in the "Add New Option" button.
        $this->local_options_save = 'Speichern';                           ///< The string displayed in the "Save Changes" button.
        $this->local_options_delete_option = 'Diese Einstellungen löschen';           ///< The string displayed in the "Delete Option" button.
        $this->local_options_delete_failure = 'Löschen dieser Einstellungen fehlgeschlagen.'; ///< The string displayed upon unsuccessful deletion of an option page.
        $this->local_options_create_failure = 'Erstellen dieser Einstellungen fehlgeschlagen.'; ///< The string displayed upon unsuccessful creation of an option page.
        $this->local_options_delete_option_confirm = 'Sicher Einstellungen löschen?';    ///< The string displayed in the "Are you sure?" confirm.
        $this->local_options_delete_success = 'Löschen dieser Einstellungen erfolgreich.';                        ///< The string displayed upon successful deletion of an option page.
        $this->local_options_create_success = 'Erstellen dieser Einstellungen erfolgreich.';                        ///< The string displayed upon successful creation of an option page.
        $this->local_options_save_success = 'Ändern dieser Einstellungen erfolgreich.';                        ///< The string displayed upon successful update of an option page.
        $this->local_options_save_failure = 'Ändern dieser Einstellungen nicht erfolgt.';                                 ///< The string displayed upon unsuccessful update of an option page.
        $this->local_options_url_bad = 'This root server URL will not work for this plugin.';                 ///< The string displayed if a root server URI fails to point to a valid root server.
        $this->local_options_access_failure = 'Keine Berechtigung zu dieser Operation.';               ///< This is displayed if a user attempts a no-no.
        $this->local_options_unsaved_message = 'Es gibt ungespeicherte Änderungen. Verlassen ohne Speichern?';   ///< This is displayed if a user attempts to leave a page without saving the options.
        $this->local_options_settings_id_prompt = 'Die ID dieser Einstellungen ist ';                              ///< This is so that users can see the ID for the setting.
        $this->local_options_settings_location_checkbox_label = 'Die Textsuche beginnt mit "Ort" Checkbox an.';                              ///< This is so that users can see the ID for the setting.
    
        /// These are all for the admin page option sheets.
        $this->local_options_name_label = 'Name der Einstellungen:';                    ///< The Label for the setting name item.
        $this->local_options_rootserver_label = 'Root Server:';               ///< The Label for the root server item.
        $this->local_options_new_search_label = 'New Search URL:';            ///< The Label for the new search item.
        $this->local_options_gkey_label = 'Google Maps API Key:';             ///< The Label for the Google Maps API Key item.
        $this->local_options_no_name_string = 'Name der Einstellungen hinzufügen';           ///< The Value to use for a name field for a setting with no name.
        $this->local_options_no_root_server_string = 'Root Server URL eintragen';                               ///< The Value to use for a root with no URL.
        $this->local_options_no_new_search_string = 'URL für neue Suche eintragen'; ///< The Value to use for a new search with no URL.
        $this->local_options_no_gkey_string = 'Enter a New API Key';          ///< The Value to use for a new search with no URL.
        $this->local_options_test_server = 'Test';                            ///< This is the title for the "test server" button.
        $this->local_options_test_server_success = 'Version ';                ///< This is a prefix for the version, on success.
        $this->local_options_test_server_failure = 'Diese Root Server URL ist unültig.';                       ///< This is a prefix for the version, on failure.
        $this->local_options_test_server_tooltip = 'Dieses testet den root server, um zu sehen ob er OK ist.';         ///< This is the tooltip text for the "test server" button.
        $this->local_options_map_label = 'Wähle einen Mittelpunkt und Zoom Level der Kartenanzeige';             ///< The Label for the map.
        $this->local_options_mobile_legend = 'Dies beeinflusst die Various Interactive Searches (wie Map, Mobile und Advanced)';  ///< This indicates that the enclosed settings are for the fast mobile lookup.
        $this->local_options_mobile_grace_period_label = 'Frist:';     ///< When you do a "later today" search, you get a "Grace Period."
        $this->local_options_mobile_region_bias_label = 'Region Bias:';       ///< The label for the Region Bias Selector.
        $this->local_options_mobile_time_offset_label = 'Zeitverschiebung:';       ///< This may have an offset (time zone difference) from the main server.
        $this->local_options_initial_view = array (                           ///< The list of choices for presentation in the popup.
                                                    'map' => 'karte',
                                                    'text' => 'Text',
                                                    'advanced_map' => 'Erweiterte karte',
                                                    'advanced_text' => 'Erweiterter Text'
                                                    );
        $this->local_options_initial_view_prompt = 'Anfänglicher Suchtyp:';    ///< The label for the initial view popup.
        $this->local_options_theme_prompt = 'Wähle ein Farbschema:';          ///< The label for the theme selection popup.
        $this->local_options_more_styles_label = 'Füge CSS Styles zum Plugin hinzu:';                             ///< The label for the Additional CSS textarea.
        $this->local_options_distance_prompt = 'Entfernungseinheit:';             ///< This is for the distance units select.
        $this->local_options_distance_disclaimer = 'Dies wird nicht alle Anzeigen beeinflussen.';               ///< This tells the admin that only some stuff will be affected.
        $this->local_options_grace_period_disclaimer = 'Verstrichene Minuten, bevor ein Meeting als "vergangen" angesehen wird (Für schnelle Suche nach Begriffen).';      ///< This explains what the grace period means.
        $this->local_options_time_offset_disclaimer = 'Stunden an Unterschied zum Main Server (Dies ist meistens nicht erforderlich).';            ///< This explains what the time offset means.
        $this->local_options_miles = 'Milen';                                 ///< The string for miles.
        $this->local_options_kilometers = 'Kilometer';                       ///< The string for kilometers.
        $this->local_options_selectLocation_checkbox_text = 'Only Display Location Services for Mobile Devices';  ///< The label for the location services checkbox.
    
        $this->local_options_time_format_prompt = 'Zeit Format:';             ///< The label for the time format selection popup.
        $this->local_options_time_format_ampm = 'Ante Meridian (HH:MM AM/PM)';    ///< Ante Meridian Format Option
        $this->local_options_time_format_military = 'Military (HH:MM)';           ///< Military Time Format Option
    
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
    
        $this->local_options_week_begins_on_prompt = 'Wochen beginnen am:';       ///< This is the label for the week start popup menu.

        $this->local_no_root_server = 'Damit das funktioniert, muss man eine root server URI angeben.';    ///< Displayed if there was no root server provided.

        /// These are for the actual search displays
        $this->local_select_search = 'Eine achnelle Suche auswählen';                 ///< Used for the "filler" in the quick search popup.
        $this->local_clear_search = 'Suchergebnisse zurücksetzen';                   ///< Used for the "Clear" item in the quick search popup.
        $this->local_menu_new_search_text = 'Neue Suche';                     ///< For the new search menu in the old-style BMLT search.
        $this->local_cant_find_meetings_display = 'Keine Meetings in dieser Suche gefunden'; ///< When the new map search cannot find any meetings.
        $this->local_single_meeting_tooltip = 'Folge diesem Link zu Details zu diesem Meeting.'; ///< The tooltip shown for a single meeting.
        $this->local_gm_link_tooltip = 'Folge diesem Link um zu einer Google Maps Location dieses Meetings zu gelangen.';    ///< The tooltip shown for the Google Maps link.
    
        /// These are for the change display
        $this->local_change_label_date =  'Datum Ändern:';                     ///< The date when the change was made.
        $this->local_change_label_meeting_name =  'Meetingsname:';            ///< The name of the changed meeting.
        $this->local_change_label_service_body_name =  'Service Body:';       ///< The name of the meeting's Service body.
        $this->local_change_label_admin_name =  'Geändert von:';                ///< The name of the Service Body Admin that made the change.
        $this->local_change_label_description =  'Beschreibung:';              ///< The description of the change.
        $this->local_change_date_format = 'G:i, j.n.Y';                ///< The format in which the change date/time is displayed.
    
        /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
        $this->local_noscript = 'Dies funktioniert nicht, denn JavaScript ist nicht aktiviert.';             ///< The string displayed in a <noscript> element.
    
        /************************************************************************************//**
        *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
        ****************************************************************************************/
    
        /// These are all for the [[bmlt_nouveau]] shortcode.
        $this->local_nouveau_advanced_button = 'Erweitert';                ///< The button name for the advanced search in the nouveau search.
        $this->local_nouveau_map_button = 'Kartensuche anstatt Textsuche';    ///< The button name for the map search in the nouveau search.
        $this->local_nouveau_text_button = 'Textsuche anstatt Kartensuche';   ///< The button name for the text search in the nouveau search.
        $this->local_nouveau_text_go_button = 'Los';                           ///< The button name for the "GO" button in the text search in the nouveau search.
        $this->local_nouveau_text_item_default_text = 'Text zum Suchen eingeben';    ///< The text that fills an empty text item.
        $this->local_nouveau_text_location_label_text = 'Dies ist ein Ort oder eine PLZ';         ///< The label text for the location checkbox.
        $this->local_nouveau_advanced_map_radius_label_1 = 'Zeige Meetings innerhalb von';                ///< The label text for the radius popup.
        $this->local_nouveau_advanced_map_radius_label_2 = 'der Marker-Position.';             ///< The second part of the label.
        $this->local_nouveau_advanced_map_radius_value_auto = 'Ein automatisch gewählter Radius';   ///< The second part of the label, if Miles
        $this->local_nouveau_advanced_map_radius_value_km = 'Km';                                 ///< The second part of the popup value, if Kilometers
        $this->local_nouveau_advanced_map_radius_value_mi = 'Milen';                              ///< The second part of the popup value, if Miles
        $this->local_nouveau_advanced_weekdays_disclosure_text = 'Gewählte Wochentage';             ///< The text that is used for the weekdays disclosure link.
        $this->local_nouveau_advanced_formats_disclosure_text = 'Gewählte Formate';               ///< The text that is used for the formats disclosure link.
        $this->local_nouveau_advanced_service_bodies_disclosure_text = 'Gewählte Service Bodies'; ///< The text that is used for the service bodies disclosure link.
        $this->local_nouveau_select_search_spec_text = 'Neue Suche definieren';                    ///< The text that is used for the link that tells you to select the search specification.
        $this->local_nouveau_select_search_results_text = 'Zeige Ergebnisse der letzten Suche';  ///< The text that is used for the link that tells you to select the search results.
        $this->local_nouveau_cant_find_meetings_display = 'Keine Meetings in dieser Suche gefunden';     ///< When the new map search cannot find any meetings.
        $this->local_nouveau_cant_lookup_display = 'Ort nicht bestimmbar.';          ///< Displayed if the app is unable to determine the location.
        $this->local_nouveau_display_map_results_text = 'Zeige Suchergebnisse in Karte';    ///< The text for the display map results disclosure link.
        $this->local_nouveau_display_list_results_text = 'Zeige Suchergebnisse als Liste';  ///< The text for the display list results disclosure link.
        $this->local_nouveau_table_header_array = array ( 'Nation', 'Land', 'Bundesland', 'Stadt', 'Meetingsname', 'Wochentag', 'Start', 'Institution', 'Format', ' ' );
        $this->local_nouveau_weekday_long_array = array ( 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag' );
        $this->local_nouveau_weekday_short_array = array ( 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa' );
    
        $this->local_nouveau_meeting_results_count_sprintf_format = '%s Meetings gefunden';
        $this->local_nouveau_meeting_results_selection_count_sprintf_format = '%s Meetings ausgewählt, von %s gefundenen Meetings';
        $this->local_nouveau_meeting_results_single_selection_count_sprintf_format = '1 Meetings ausgewählt, von %s gefundenen Meetings';
        $this->local_nouveau_single_time_sprintf_format = 'Meeting findet jeden %s, um %s, und dauert %s.';
        $this->local_nouveau_single_duration_sprintf_format_1_hr = '1 Stunde';
        $this->local_nouveau_single_duration_sprintf_format_mins = '%s Minuten';
        $this->local_nouveau_single_duration_sprintf_format_hrs = '%s Stunden';
        $this->local_nouveau_single_duration_sprintf_format_hr_mins = '1 Stunde und %s Minuten';
        $this->local_nouveau_single_duration_sprintf_format_hrs_mins = '%s Stunden und %s minuten';
    
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
    
        $this->local_nouveau_location_sprintf_format_wtf = 'Keine Position angegeben';
    
        $this->local_nouveau_location_services_set_my_location_advanced_button = 'Setze den Marker auf meine aktuelle Position';
        $this->local_nouveau_location_services_find_all_meetings_nearby_button = 'Finde Meetings in meiner Nähe';
        $this->local_nouveau_location_services_find_all_meetings_nearby_later_today_button = 'Finde Meetings später heute';
        $this->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button = 'Finde Meetings in meiner Nähe morgen';
    
        $this->local_nouveau_location_sprintf_format_duration_title = 'Dieses Meeting dauert %s Stunden und %s Minuten.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_title = 'Dieses Meeting dauert 1 Stunde.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = 'Dieses Meeting dauert 1 Stunde und %s Minuten.';
        $this->local_nouveau_location_sprintf_format_duration_hours_only_title = 'Dieses Meeting dauert %s Stunden.';
        $this->local_nouveau_lookup_location_failed = "Die Suche nach Adresse wurde nicht erfolgreich durchgeführt.";
        $this->local_nouveau_lookup_location_server_error = "Die Suche nach Adresse wurde wegen eines Serverfehlers nicht erfolgreich durchgeführt.";
        $this->local_nouveau_time_sprintf_format = '%d:%02d %s';
        $this->local_nouveau_am = 'AM';
        $this->local_nouveau_pm = 'PM';
        $this->local_nouveau_noon = '12:00';
        $this->local_nouveau_midnight = '00:00';
        $this->local_nouveau_advanced_map_radius_value_array = "0.25, 0.5, 1.0, 2.0, 5.0, 10.0, 15.0, 20.0, 50.0, 100.0, 200.0";
        $this->local_nouveau_meeting_details_link_title = 'Mehr Details zu diesem Meeting.';
        $this->local_nouveau_meeting_details_map_link_uri_format = 'https://maps.google.com/maps?q=%f,%f';
        $this->local_nouveau_meeting_details_map_link_text = 'Karte zum Meeting';

        $this->local_nouveau_single_formats_label = 'Meetings-Formate:';
        $this->local_nouveau_single_service_body_label = 'Service Body:';

        $this->local_nouveau_prompt_array = array (
                                                    'weekday_tinyint' => 'Wochentag',
                                                    'start_time' => 'Anfangszeit',
                                                    'duration_time' => 'Dauer des Meetings',
                                                    'formats' => 'Format',
                                                    'distance_in_miles' => 'Entfernung in Meilen',
                                                    'distance_in_km' => 'Entfernung In Kilometern',
                                                    'meeting_name' => 'Meetingsname',
                                                    'location_text' => 'Institution',
                                                    'location_street' => 'Straße, Nr',
                                                    'location_city_subsection' => 'Stadtteil',
                                                    'location_neighborhood' => 'Nachbarschaft',
                                                    'location_municipality' => 'Stadt',
                                                    'location_sub_province' => 'Landkreis',
                                                    'location_province' => 'Bundesland',
                                                    'location_nation' => 'Nation',
                                                    'location_postal_code_1' => 'PLZ',
                                                    'location_info' => 'Zusätzliche Informationen'
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
        $this->local_no_js_warning = '<noscript class="no_js">Diese Meetingssuche wird nicht funktionieren, da Ihr Browser kein Javascript unterstützt. Dennoch können Sie <a rel="external nofollow" href="###ROOT_SERVER###">main server</a> zur Suche verwenden.</noscript>'; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
        ****************************************************************************************/
                                    
        $this->local_new_map_option_1_label = 'Suchoptionen (Nicht angewandt, wenn dieser Bereich nicht geöffnet ist):';
        $this->local_new_map_weekdays = 'Meetings finden an diesen Wochentagen stadt:';
        $this->local_new_map_all_weekdays = 'Alle';
        $this->local_new_map_all_weekdays_title = 'Finde Meetings an jedem Tag.';
        $this->local_new_map_weekdays_title = 'Finde Meetings, die stattfinden an ';
        $this->local_new_map_formats = 'Meetings haben dieses Format:';
        $this->local_new_map_all_formats = 'Alle';
        $this->local_new_map_all_formats_title = 'Finde Meetings für jedes Format.';
        $this->local_new_map_js_center_marker_current_radius_1 = 'Der Kreis ist etwa ';
        $this->local_new_map_js_center_marker_current_radius_2_km = ' Kilometer weit.';
        $this->local_new_map_js_center_marker_current_radius_2_mi = ' Milen weit.';
        $this->local_new_map_js_diameter_choices = array ( 0.25, 0.5, 1.0, 1.5, 2.0, 3.0, 5.0, 10.0, 15.0, 20.0, 25.0, 30.0, 50.0, 100.0 );
        $this->local_new_map_js_new_search = 'Neue Suche';
        $this->local_new_map_option_loc_label = 'Trage einen Ort ein:';
        $this->local_new_map_option_loc_popup_label_1 = 'Suche nach Meetings im Umkreis von';
        $this->local_new_map_option_loc_popup_label_2 = 'um diese Position.';
        $this->local_new_map_option_loc_popup_km = 'Km';
        $this->local_new_map_option_loc_popup_mi = 'Milen';
        $this->local_new_map_option_loc_popup_auto = 'eine automatisch gewählte Entfernung';
        $this->local_new_map_center_marker_distance_suffix = ' von dem Mittelpunkt-Marker.';
        $this->local_new_map_center_marker_description = 'Dies ist Ihre gewählte Position.';
        $this->local_new_map_text_entry_fieldset_label = 'Füge eine Adresse, PLZ oder Ort ein';
        $this->local_new_map_text_entry_default_text = 'Füge eine Adresse, PLZ oder Ort ein';
        $this->local_new_map_location_submit_button_text = 'Suche nach Meetings in der Nähe dieser Position';
    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// The units for distance.
        $this->local_mobile_kilometers = 'Kilometer';
        $this->local_mobile_miles = 'Milen';
        $this->local_mobile_distance = 'Distanz';  ///< Distance (the string)
    
        /// The page titles.
        $this->local_mobile_results_page_title = 'Schnelle Meetingssuche';
        $this->local_mobile_results_form_title = 'Finde schnell Meetings in der Nähe';
    
        /// The fast GPS lookup links.
        $this->local_GPS_banner = 'Wähle eine schnelle Meetingssuche zum Nachschlagen';
        $this->local_GPS_banner_subtext = 'Diese Links für noch schnellere zuküpnftige Suchen merken.';
        $this->local_search_all = 'Suche nach allen Meetings nahe meiner aktuellen Position.';
        $this->local_search_today = 'Später heute';
        $this->local_search_tomorrow = 'Morgen';
    
        /// The search for an address form.
        $this->local_list_check = 'Wenn Sie Probleme mit der Interaktiven Karte haben oder die Ergebnisse als Liste wünschen, haken Sie diese Box an und geben Sie eine Adresse ein.';
        $this->local_search_address_single = 'Suche nach Meetings in der Nähe einer Adresse';
    
        /// Used instead of "near my present location."
        $this->local_search_all_address = 'Suche nach allen Meetings in der Nähe einer Adresse.';
        $this->local_search_submit_button = 'Suche nach Meetings';
    
        /// This is what is entered into the text box.
        $this->local_enter_an_address = 'Geben Sie eine Adresse ein';
    
        /// Error messages.
        $this->local_mobile_fail_no_meetings = 'Keine Meetings gefunden!';
        $this->local_server_fail = 'Die Meetingssuche war nicht erfolgreich wegen eines Serverfehlers!';
        $this->local_cant_find_address = 'Kann die Position der Adressinformation nicht bestimmen!';
        $this->local_cannot_determine_location = 'Kann Ihre Position nicht bestimmen!';
        $this->local_enter_address_alert = 'Geben Sie eine Adresse ein!';
    
        /// The text for the "Map to Meeting" links
        $this->local_map_link = 'Karte zum Meeting';
    
        /// Only used for WML pages
        $this->local_next_card = 'Nächstes Meeting >>';
        $this->local_prev_card = '<< Vorheriges Meeting';
    
        /// Used for the info and list windows.
        $this->local_formats = 'Format';
        $this->local_noon = '12:00';
        $this->local_midnight = '00:00';
    
        /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
        $this->local_weekdays = array ( 'ERROR', 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag' );
        $this->local_weekdays_short = array ( 'ERR', 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa' );
    
        /************************************************************************************//**
        *                          STATIC DATA MEMBERS (QUICKSEARCH)                            *
        ****************************************************************************************/
        $this->local_quicksearch_select_option_0 = 'Suche Überall';
        $this->local_quicksearch_display_too_large = 'Too many results. Please narrow your search.';
        }
    };
?>