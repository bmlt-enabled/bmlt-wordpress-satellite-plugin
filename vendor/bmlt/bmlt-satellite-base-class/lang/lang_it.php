<?php
// Italiano
/****************************************************************************************//**
*   \file   lang_it.php                                                                     *
*                                                                                           *
*   \brief  This file contains Italian localizations.                                       *
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

require_once ( dirname ( __FILE__ ) . '/BMLT_Localized_BaseClass.class.php' );

class BMLT_Localized_BaseClass_it extends BMLT_Localized_BaseClass
    {
    function __construct ()
        {
        /************************************************************************************//**
        *                           STATIC DATA MEMBERS (LOCALIZABLE)                           *
        ****************************************************************************************/
    
        /// These are all for the admin pages.
        $this->local_options_lang_prompt = 'Lingua:';                       ///< The label for the Language Selector.
        $this->local_options_title = 'Opzioni del Basic Meeting List Toolbox';    ///< This is the title that is displayed over the options.
        $this->local_menu_string = 'Opzioni BMLT';                            ///< 'BMLT Options' - The name of the menu item.
        $this->local_options_prefix = 'Seleziona impostazioni ';              ///< 'Select Setting ' - The string displayed before each number in the options popup.
        $this->local_options_add_new = 'Aggiungi una nuova impostazione';     ///< 'Add A new Setting' - The string displayed in the "Add New Option" button.
        $this->local_options_save = 'Salva le modifiche';                     ///< 'Save Changes' - The string displayed in the "Save Changes" button.
        $this->local_options_delete_option = 'Elimina questa impostazione';   ///< 'Delete This Setting' - The string displayed in the "Delete Option" button.
        $this->local_options_delete_failure = 'Eliminazione impostazione non riuscita.'; ///< 'The setting deletion failed.' - The string displayed upon unsuccessful deletion of an option page.
        $this->local_options_create_failure = 'Creazione impostazione non riuscita.'; ///< 'The setting creation failed.' - The string displayed upon unsuccessful creation of an option page.
        $this->local_options_delete_option_confirm = 'Sei sicuro di voler eliminare questa impostazione?';    ///< 'Are you sure that you want to delete this setting?' - The string displayed in the "Are you sure?" confirm.
        $this->local_options_delete_success = 'Impostazione eliminata con successo.';  ///< 'The setting was deleted successfully.' - The string displayed upon successful deletion of an option page.
        $this->local_options_create_success = 'Impostazione creata con successo.';   ///< 'The setting was created successfully.' - The string displayed upon successful creation of an option page.
        $this->local_options_save_success = 'Le impostazioni sono state aggiornate con successo.';   ///< 'The settings were created successfully.' - The string displayed upon successful update of an option page.
        $this->local_options_save_failure = 'Le impostazioni non sono state aggiornate.';     ///< 'The settings were not updated.' - The string displayed upon unsuccessful update of an option page.
        $this->local_options_url_bad = 'Questa URL root server non funzionerà per questo plugin.';        ///< The string displayed if a root server URI fails to point to a valid root server.
        $this->local_options_access_failure = 'Non hai i permessi per compiere questa operazione.';  ///< 'You are not allowed to perform this operation.' - This is displayed if a user attempts a no-no.
        $this->local_options_unsaved_message = 'Hai modifiche non salvate. Sei sicuro di voler uscire senza salvarle?';   ///< 'You have unsaved changes. Are you sure you want to leave without saving them?' - This is displayed if a user attempts to leave a page without saving the options.
        $this->local_options_settings_id_prompt = 'L\'ID per questa impostazione è ';    ///< 'The ID for this Setting is ' - This is so that users can see the ID for the setting.
        $this->local_options_settings_location_checkbox_label = 'La ricerca testuale inizia con la casella "Località" attiva.';   ///< 'Text Searches Start Off with the "Location" Checkbox On.' This is so that users can see the ID for the setting.
    
        /// These are all for the admin page option sheets.
        $this->local_options_name_label = 'Nome dell\'impostazione:';                    /// 'Setting Name:' < The Label for the setting name item.
        $this->local_options_rootserver_label = 'Root Server:';               ///< The Label for the root server item.
        $this->local_options_new_search_label = 'URL della nuova ricerca:';            ///'New Search URL:' < The Label for the new search item.
        $this->local_options_gkey_label = 'Chiave (key) delle API di Google Maps:';             /// 'Google Maps API Key:' < The Label for the Google Maps API Key item.
        $this->local_options_no_name_string = 'Inserisci il nome dell\'impostazione';           /// 'Enter Setting Name' < The Value to use for a name field for a setting with no name.
        $this->local_options_no_root_server_string = 'Inserisci un indirizzo per il Root Server';                               /// 'Enter a Root Server URL' < The Value to use for a root with no URL.
        $this->local_options_no_new_search_string = 'Inserisci un nuovo indirizzo di ricerca'; /// 'Enter a New Search URL' < The Value to use for a new search with no URL.
        $this->local_options_no_gkey_string = 'Inserisci una nuova chiave (key) API';          /// 'Enter a New API Key' < The Value to use for a new search with no URL.
        $this->local_options_test_server = 'Test';                            /// 'Test' < This is the title for the "test server" button.
        $this->local_options_test_server_success = 'Versione ';                /// 'Version ' < This is a prefix for the version, on success.
        $this->local_options_test_server_failure = 'Questo indirizzo del Root Server non è valido';                       /// 'This Root Server URL is not Valid' < This is a prefix for the version, on failure.
        $this->local_options_test_server_tooltip = 'Questo testa il Root server, per vedere se è a posto (OK).';         /// 'This tests the root server, to see if it is OK.'< This is the tooltip text for the "test server" button.
        $this->local_options_map_label = 'Scegli un punto centrale e un livello di zoom per le visualizzazioni della mappa';             /// 'Select a Center Point and Zoom Level for Map Displays'< The Label for the map.
        $this->local_options_mobile_legend = 'Questi riguardano le diverse ricerche interattive (come Mappa, Mobile e Avanzate)';  /// 'These affect the Various Interactive Searches (such as Map, Mobile and Advanced)'< This indicates that the enclosed settings are for the fast mobile lookup.
        $this->local_options_mobile_grace_period_label = 'Periodo di grazia:';     /// 'Grace Period:'< When you do a "later today" search, you get a "Grace Period."
        $this->local_options_mobile_region_bias_label = 'Discrimina regione:';       /// 'Region Bias:'< The label for the Region Bias Selector.
        $this->local_options_mobile_time_offset_label = 'Sbilanciamento (offset) temporale:';       /// 'Time Offset:'< This may have an offset (time zone difference) from the main server.
        $this->local_options_initial_view = array (                           ///< The list of choices for presentation in the popup.
                                                    'map'           => 'Mappa',
                                                    'text'          => 'Testo',
                                                    'advanced_map'  => 'Mappa (avanzata)',
                                                    'advanced_text' => 'Testo (avanzato)' /// 'map' => 'Map', 'text' => 'Text', 'advanced_map' => 'Advanced Map', 'advanced_text' => 'Advanced Text'
                                                    );
        $this->local_options_initial_view_prompt = 'Tipo iniziale di ricerca:';    /// 'Initial Search Type:' < The label for the initial view popup.
        $this->local_options_theme_prompt = 'Scegli il tema in base al colore:';          /// 'Select a Color Theme:' < The label for the theme selection popup.
        $this->local_options_more_styles_label = 'Aggiungi stili CSS al plugin:';                             ///< The label for the Additional CSS textarea.
        $this->local_options_distance_prompt = 'Unità di distanza:';             /// 'Distance Units:' < This is for the distance units select.
        $this->local_options_distance_disclaimer = 'Questo non riguarda tutte le visualizzazioni.';               /// 'This will not affect all of the displays.' < This tells the admin that only some stuff will be affected.
        $this->local_options_grace_period_disclaimer = 'Minuti rimanenti prima che una riunione sia da considerarsi passata (per ricerche veloci).';      /// 'Minutes Elapsed Before A Meeting is Considered "Past" (For the fast Lookup Searches).' < This explains what the grace period means.
        $this->local_options_time_offset_disclaimer = 'Ore di differenza dal Main server (dato di solito non necessario).';            /// 'Hours of Difference From the Main Server (This is usually not necessary).' < This explains what the time offset means.
        $this->local_options_miles = 'miglia';                                 /// 'Miles' < The string for miles.
        $this->local_options_kilometers = 'chilometri';                       /// 'Kilometers' < The string for kilometers.
        $this->local_options_selectLocation_checkbox_text = 'Mostra servizi di localizzazione solo per dispositivi mobili';  /// 'Only Display Location Services for Mobile Devices' < The label for the location services checkbox.
    
        $this->local_options_time_format_prompt = 'Formato orario:';             /// 'Time Format:' < The label for the time format selection popup.
        $this->local_options_time_format_ampm = 'Antimeridiano (HH:MM AM/PM)';    /// 'Ante Meridian (HH:MM AM/PM)' < Ante Meridian Format Option
        $this->local_options_time_format_military = 'Militare (HH:MM)';           /// 'Military (HH:MM)' < Military Time Format Option
    
        $this->local_options_google_api_label = 'Chiave (key) delle API di Google Maps:';       ///< The label for the Google Maps API Key Text Entry.
        
        $this->local_options_auto_search_radius_prompt = 'Densità automatica (della ricerca):';    ///< The label for the Auto Search Density popup.
        $this->local_options_auto_search_radius_display_names = array (             ///< The values for the auto-search density popup.
                                                                        'Minimo'    => -2,
                                                                        'Minore'    => -5,
                                                                        'Normale'   => -10,
                                                                        'Maggiore'  => -15,
                                                                        'Massimo'   => -30,
                                                                        'Super Massimo' => -100
                                                                        );
    
        $this->local_options_week_begins_on_prompt = 'La settimana inizia di:';       /// 'Weeks begin on:' < This is the label for the week start popup menu.

        $this->local_no_root_server = 'Devi fornire l\'indirizzo di un root server affinché questo funzioni.';    /// 'You need to provide a root server URI in order for this to work.' < Displayed if there was no root server provided.

        /// These are for the actual search displays
        $this->local_select_search = 'Seleziona ricerca veloce';              ///< 'Select a Quick Search'; Used for the "filler" in the quick search popup.
        $this->local_clear_search = 'Cancella i risultati della ricerca';     ///< Clear Search Results; Used for the "Clear" item in the quick search popup.
        $this->local_menu_new_search_text = 'Nuova ricerca';                  ///< For the new search menu in the old-style BMLT search.
        $this->local_cant_find_meetings_display = 'Nessuna riunione trovata con questa ricerca'; ///< 'No Meetings Found In This Search'; When the new map search cannot find any meetings.
        $this->local_single_meeting_tooltip = 'Segui questo link per i dettagli su questa riunione.'; ///< 'Follow This Link for Details About This Meeting.';The tooltip shown for a single meeting.
        $this->local_gm_link_tooltip = 'Segui questo link per visualizzare questa riunione su Google Maps.';    ///< 'Follow This Link to be Taken to A Google Maps Location for This Meeting.'; The tooltip shown for the Google Maps link.
    
        /// These are for the change display
        $this->local_change_label_date =  'Cambio data:';                     ///< 'Change Date:'; The date when the change was made.
        $this->local_change_label_meeting_name =  'Nome della riunione';            ///< 'Meeting Name:'; The name of the changed meeting.
        $this->local_change_label_service_body_name =  'Struttura di servizio:';               ///< 'Service Body:'; The name of the meeting's Service body.
        $this->local_change_label_admin_name =  'Modificato da:';             ///< 'Changed By:'; The name of the Service Body Admin that made the change.
        $this->local_change_label_description =  'Descrizione:';              ///< 'Description:'; The description of the change.
        $this->local_change_date_format = 'F j Y, \a\t g:i A';                ///< 'F j Y, \a\t g:i A' The format in which the change date/time is displayed.
    
        /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
        $this->local_noscript = 'Questo non funzionerà, perché non hai JavaScript attivo.';             ///< 'This will not work, because you do not have JavaScript active.'; The string displayed in a <noscript> element.
    
        /************************************************************************************//**
        *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
        ****************************************************************************************/
    
        /// These are all for the [[bmlt_nouveau]] shortcode.
        $this->local_nouveau_advanced_button = 'Ulteriori opzioni';                ///< The button name for the advanced search in the nouveau search.
        $this->local_nouveau_map_button = 'Mostra sulla mappa anziché come lista';    ///< The button name for the map search in the nouveau search.
        $this->local_nouveau_text_button = 'Mostra come lista anziché sulla mappa';   ///< The button name for the text search in the nouveau search.
        $this->local_nouveau_text_go_button = 'VAI';                           ///< The button name for the "GO" button in the text search in the nouveau search.
        $this->local_nouveau_text_item_default_text = 'Inserisci testo della ricerca';    ///< The text that fills an empty text item.
        $this->local_nouveau_text_location_label_text = 'Questa è una località o un CAP';         ///< The label text for the location checkbox.
        $this->local_nouveau_advanced_map_radius_label_1 = 'Cerca riunioni nel raggio di';                ///< The label text for the radius popup.
        $this->local_nouveau_advanced_map_radius_label_2 = 'dal marcatore.';             ///< The second part of the label.
        $this->local_nouveau_advanced_map_radius_value_auto = 'un raggio automaticamente scelto';   ///< The second part of the label, if Miles
        $this->local_nouveau_advanced_map_radius_value_km = 'Km';                                 ///< The second part of the popup value, if Kilometers
        $this->local_nouveau_advanced_map_radius_value_mi = 'Miglia';                              ///< The second part of the popup value, if Miles
        $this->local_nouveau_advanced_weekdays_disclosure_text = 'Giorni della settimana selezionati';             ///< The text that is used for the weekdays disclosure link.
        $this->local_nouveau_advanced_formats_disclosure_text = 'Formati selezionati';               ///< The text that is used for the formats disclosure link.
        $this->local_nouveau_advanced_service_bodies_disclosure_text = 'Aree selezionate'; ///< The text that is used for the service bodies disclosure link.
        $this->local_nouveau_select_search_spec_text = 'Specificare una nuova ricerca';                    ///< The text that is used for the link that tells you to select the search specification.
        $this->local_nouveau_select_search_results_text = "Mostra i risultati dell\'ultima ricerca";  ///< The text that is used for the link that tells you to select the search results.
        $this->local_nouveau_cant_find_meetings_display = 'Nessuna riunione trovata con questa ricerca';     ///< When the new map search cannot find any meetings.
        $this->local_nouveau_cant_lookup_display = 'Non è possibile determinare la tua posizione.';          ///< Displayed if the app is unable to determine the location.
        $this->local_nouveau_display_map_results_text = 'Mostra i risultati della ricerca sulla mappa';    ///< The text for the display map results disclosure link.
        $this->local_nouveau_display_list_results_text = 'Mostra i risultati della ricerca in una lista';  ///< The text for the display list results disclosure link.
        $this->local_nouveau_table_header_array = array ( 'Nazione', 'Regione', 'Provincia', 'Città', 'Gruppo', 'Giorno', 'Orario', 'Località', 'Formato', 'Dettagli' );
        $this->local_nouveau_weekday_long_array = array ( 'Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato' );
        $this->local_nouveau_weekday_short_array = array ( 'Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab' );
    
        $this->local_nouveau_meeting_results_count_sprintf_format = '%s riunioni trovate';
        $this->local_nouveau_meeting_results_selection_count_sprintf_format = '%s riunioni selezionate, su %s riunioni trovate';
        $this->local_nouveau_meeting_results_single_selection_count_sprintf_format = '1 riunione selezionata, su %s riunioni trovate';
        $this->local_nouveau_single_time_sprintf_format = 'La riunione si tiene ogni %s, alle %s, e dura %s.';
        $this->local_nouveau_single_duration_sprintf_format_1_hr = '1 ora';
        $this->local_nouveau_single_duration_sprintf_format_mins = '%s minuti';
        $this->local_nouveau_single_duration_sprintf_format_hrs = '%s ore';
        $this->local_nouveau_single_duration_sprintf_format_hr_mins = '1 ora e %s minuti';
        $this->local_nouveau_single_duration_sprintf_format_hrs_mins = '%s ore e %s minuti';
    
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
    
        $this->local_nouveau_location_sprintf_format_wtf = 'Nessuna località specificata';                                               ///< 'No Location Given'

        $this->local_nouveau_location_services_set_my_location_advanced_button = 'Imposta il marcatore sulla mia posizione attuale';           ///< 'Set the Marker to My Current Location'
        $this->local_nouveau_location_services_find_all_meetings_nearby_button = 'Trova riunioni vicino a me';                          ///< 'Find Meetings Near Me'
        $this->local_nouveau_location_services_find_all_meetings_nearby_later_today_button = 'Trova riunioni vicino a me oggi, più tardi';      ///< Find Meetings Near Me Later Today'
        $this->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button = 'Trova riunioni vicino a me domani';              ///< 'Find Meetings Near Me Tomorrow'

        $this->local_nouveau_location_sprintf_format_duration_title = 'Questa riunione dura % ore e %s minuti.';                  ///< 'This meeting is %s hours and %s minutes long.'
        $this->local_nouveau_location_sprintf_format_duration_hour_only_title = 'Questa riunione dura 1 ora.';                   ///< 'This meeting is 1 hour long.'
        $this->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = 'Questa riunione dura 1 ora e %s minuti.'; ///< 'This meeting is 1 hour and %s minutes long.'
        $this->local_nouveau_location_sprintf_format_duration_hours_only_title = 'Questa riunione dura %s ore.'; ///< 'This meeting is %s hours long.'
        $this->local_nouveau_lookup_location_failed = "La ricerca dell\'indirizzo non è stata completata con successo."; ///< "The address lookup was not completed successfully."
        $this->local_nouveau_lookup_location_server_error = "La ricerca dell\'indirizzo non è stata completata con successo a causa di un errore del server."; ///< "The address lookup was not completed successfully, due to a server error."
        $this->local_nouveau_time_sprintf_format = '%d:%02d %s';
        $this->local_nouveau_am = 'AM';
        $this->local_nouveau_pm = 'PM';
        $this->local_nouveau_noon = 'Mezzogiorno'; ///< 'Noon'
        $this->local_nouveau_midnight = 'Mezzanotte'; ///< 'Midnight'
        $this->local_nouveau_advanced_map_radius_value_array = "0.25, 0.5, 1.0, 2.0, 5.0, 10.0, 15.0, 20.0, 50.0, 100.0, 200.0";
        $this->local_nouveau_meeting_details_link_title = 'Ulteriori dettagli su questa riunione.'; ///< 'Get more details about this meeting.'
        $this->local_nouveau_meeting_details_map_link_uri_format = 'https://maps.google.com/maps?q=%f,%f';
        $this->local_nouveau_meeting_details_map_link_text = 'Visualizza la riunione sulla mappa'; ///< 'Map To Meeting'

        $this->local_nouveau_single_formats_label = 'Formati della riunione:'; ///< 'Meeting Formats:'
        $this->local_nouveau_single_service_body_label = 'Area:'; ///< 'Service Body:'

        $this->local_nouveau_prompt_array = array (
                                                    'weekday_tinyint' => 'Giorno', ///< Weekday'
                                                    'start_time' => 'Orario', ///< 'Start Time'
                                                    'duration_time' => 'Durata', ///< 'Duration'
                                                    'formats' => 'Formato', ///< 'Format'
                                                    'distance_in_miles' => 'Distanza in miglia', ///< 'Distance In Miles'
                                                    'distance_in_km' => 'Distanza in chilometri', ///< 'Distance In Kilometers'
                                                    'meeting_name' => 'Gruppo', /// 'Meeting Name',
                                                    'location_text' => 'Nome della struttura', /// 'Location Name'
                                                    'location_street' => 'Indirizzo', /// Street Address
                                                    'location_city_subsection' => 'Frazione', ///< 'Borough'
                                                    'location_neighborhood' => 'Quartiere', ///< 'Neighborhood'
                                                    'location_municipality' => 'Città', ///< Town'
                                                    'location_sub_province' => 'Provincia', ///< 'County'
                                                    'location_province' => 'Regione', ///< 'State'
                                                    'location_nation' => 'Nazione', ///< 'Nation'
                                                    'location_postal_code_1' => 'CAP', ///< 'Zip Code'
                                                    'location_info' => 'Informazioni extra' ///< Extra Information'
                                                    );
    
        /************************************************************************************//**
        *                   TABLE SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                    *
        ****************************************************************************************/
        $this->local_table_tab_loading_title_format       = 'Trova riunioni per %s'; /// 'Getting meetings for %s';
        $this->local_table_header_time_label              = 'Orario'; /// 'Time';
        $this->local_table_header_meeting_name_label      = 'Nome del gruppo'; /// 'Meeting Name';
        $this->local_table_header_town_label              = 'Città'; /// 'Town';
        $this->local_table_header_address_label           = 'Indirizzo'; /// 'Address';
        $this->local_table_header_format_label            = 'Formato'; /// 'Format';
        $this->local_table_header_tab_title_format        = 'Mostra riunioni per %s'; /// 'Display meetings for %s';
        $this->local_table_ante_meridian                  = '"AM","PM","Mezzogiorno","Mezzanotte"'; /// '"AM","PM","Noon","Midnight"';
        $this->local_table_no_meetings_format             = 'Nessuna riunione il %s'; /// 'No meetings on %s';
                                               
        /************************************************************************************//**
        *                      STATIC DATA MEMBERS (SPECIAL LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// This is the only localizable string that is not processed. This is because it contains HTML. However, it is also a "hidden" string that is only displayed when the browser does not support JS.
        $this->local_no_js_warning = '<noscript class="no_js">Questa ricerca non funzionerà perché il tuo browser non supporta JavaScript. Puoi, comunque, usare il <a rel="external nofollow" href="###ROOT_SERVER###">main server</a> per effettuare la ricerca.</noscript>';///< '<noscript class="no_js">This Meeting Search will not work because your browser does not support JavaScript. However, you can use the <a rel="external nofollow" href="###ROOT_SERVER###">main server</a> to do the search.</noscript>'; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                   
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
        ****************************************************************************************/
        $this->local_new_map_option_1_label = 'Opzioni di ricerca (non si applica a meno che questa sezione non sia aperta):'; // 'Search Options (Not Applied Unless This Section Is Open):'
        $this->local_new_map_weekdays = 'Le riunioni avvengono in questi giorni della settimana:'; ///< 'Meetings Gather on These Weekdays:'
        $this->local_new_map_all_weekdays = 'Tutti i giorni della settimana'; ///<  'All'
        $this->local_new_map_all_weekdays_title = 'Trova riunioni per ogni giorno.'; ///< 'Find meetings for every day.'
        $this->local_new_map_weekdays_title = 'Trova riunioni che cadano di '; ///< 'Find meetings that occur on '
        $this->local_new_map_formats = 'Le riunioni hanno questi formati:'; ///< 'Meetings Have These Formats:'
        $this->local_new_map_all_formats = 'Tutti i formati'; ///< 'All'
        $this->local_new_map_all_formats_title = 'Trova riunioni per ogni formato.'; ///< 'Find meetings for every format.'
        $this->local_new_map_js_center_marker_current_radius_1 = 'Il cerchio è circa '; ///< 'The circle is about '
        $this->local_new_map_js_center_marker_current_radius_2_km = ' chilometri di larghezza.'; ///< ' kilometers wide.'
        $this->local_new_map_js_center_marker_current_radius_2_mi = ' miglia di larghezza.';  
        $this->local_new_map_js_diameter_choices = array ( 0.25, 0.5, 1.0, 1.5, 2.0, 3.0, 5.0, 10.0, 15.0, 20.0, 25.0, 30.0, 50.0, 100.0 );
        $this->local_new_map_js_new_search = 'Nuova ricerca'; ///< 'New Search'
        $this->local_new_map_option_loc_label = 'Immetti una località:'; ///< Enter A Location:'
        $this->local_new_map_option_loc_popup_label_1 = 'Ricerca riunioni nel raggio di'; ///< 'Search for meetings within'
        $this->local_new_map_option_loc_popup_label_2 = 'dalla località.'; ///< 'of the location.'
        $this->local_new_map_option_loc_popup_km = 'Km'; ///< 'Km'
        $this->local_new_map_option_loc_popup_mi = 'Miglia'; ///< 'Miles'
        $this->local_new_map_option_loc_popup_auto = 'una distanza scelta automaticamente';///< 'an automatically chosen distance'
        $this->local_new_map_center_marker_distance_suffix = ' dal marcatore.'; ///< ' from the center marker.'
        $this->local_new_map_center_marker_description = 'Questa è la tua località prescelta.'; ///< 'This is your chosen location.'
        $this->local_new_map_text_entry_fieldset_label = 'Inserisci un indirizzo, CAP o località'; ///< 'Enter an Address, Postcode or Location'
        $this->local_new_map_text_entry_default_text = 'Inserisci un indirizzo, CAP o località'; ///<  'Enter an Address, Postcode or Location'
        $this->local_new_map_location_submit_button_text = 'Cerca riunioni vicino a questa località'; ///< 'Search for Meetings Near This Location'

        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// The units for distance.
        $this->local_mobile_kilometers = 'Chilometri'; ///< 'Kilometers'
        $this->local_mobile_miles = 'Miglia'; ///< 'Miles'
        $this->local_mobile_distance = 'Distanza';  ///< Distance (the string)
   
        /// The page titles.
        $this->local_mobile_results_page_title = 'Risultati della ricerca Vvloce'; ///< 'Quick Meeting Search Results'
        $this->local_mobile_results_form_title = 'Ricerca veloce delle riunioni vicine'; ///< 'Find Nearby Meetings Quickly'    
        /// The fast GPS lookup links.
        $this->local_GPS_banner = 'Seleziona ricerca veloce'; ///<'Select A Fast Meeting Lookup'
        $this->local_GPS_banner_subtext = 'Aggiungi questi link ai preferiti per ricerche ancora più veloci in futuro.'; ///<'Bookmark these links for even faster searches in the future.'
        $this->local_search_all = 'Cerca tutte le riunioni vicine alla mia attuale località'; ///< 'Search for all meetings near my present location.';
        $this->local_search_today = 'Oggi, più tardi'; ///< 'Later Today'
        $this->local_search_tomorrow = 'Domani';    

        /// The search for an address form.
        $this->local_list_check = 'Se stai avendo delle difficoltà con la mappa interattiva, o desideri visualizzare i risultati in una lista, spunta questa casella e inserisci un indirizzo'; ///< 'If you are experiencing difficulty with the interactive map, or wish to have the results returned as a list, check this box and enter an address.';
        $this->local_search_address_single = 'Cerca riunioni vicino a un indirizzo'; ///< 'Search for Meetings Near An Address'
    
        /// Used instead of "near my present location."
        $this->local_search_all_address = 'Cerca tutte le riunioni vicine a questo indirizzo'; ///< 'Search for all meetings near this address.';
        $this->local_search_submit_button = 'Cerca le riunioni'; ///< 'Search for Meetings'
    
        /// This is what is entered into the text box.
        $this->local_enter_an_address = 'Inserisci un indirizzo';
    
        /// Error messages.
        $this->local_mobile_fail_no_meetings = 'Nessuna riunione trovata!';
        $this->local_server_fail = 'La ricerca è fallita poiché il server ha incontrato un errore!'; ///< 'The search failed because the server encountered an error!'
        $this->local_cant_find_address = 'Non riesco a individuare la località in base all\'indirizzo fornito'; ///< 'Cannot Determine the Location From the Address Information!';
        $this->local_cannot_determine_location = 'Non riesco a individuare la localizzazione corretta!';
        $this->local_enter_address_alert = 'Per favore, inserisci un indirizzo!';
    
        /// The text for the "Map to Meeting" links
        $this->local_map_link = 'Mappa della riunione';
    
        /// Only used for WML pages
        $this->local_next_card = 'Prossima riunione >>'; ///< 'Next Meeting';
        $this->local_prev_card = '<< Riunione precedente';
    
        /// Used for the info and list windows.
        $this->local_formats = 'Formati'; ///< 'Formats';
        $this->local_noon = 'Mezzogiorno'; ///< 'Noon';
        $this->local_midnight = 'Mezzanotte'; ///<'Midnight';
    
        /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
        $this->local_weekdays = array ( 'ERRORE', 'Domenica', 'Lunedì', 'Maartedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato' );
        $this->local_weekdays_short = array ( 'ERR', 'Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab' );
    
        /************************************************************************************//**
        *                          STATIC DATA MEMBERS (QUICKSEARCH)                            *
        ****************************************************************************************/
        $this->local_quicksearch_select_option_0 = 'Cerca Ovunque';
        $this->local_quicksearch_display_too_large = 'Troppi risultati. Per favore, restringi la tua ricerca.';
        }
    };
?>
