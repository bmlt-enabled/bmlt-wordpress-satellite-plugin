<?php
// Español 
/****************************************************************************************//**
*   \file   lang_es.php                                                                     *
*                                                                                           *
*   \brief  This file contains Spanish localizations.                                       *
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

class BMLT_Localized_BaseClass_es extends BMLT_Localized_BaseClass
    {
    function __construct ()
        {
        /************************************************************************************//**
        *                           STATIC DATA MEMBERS (LOCALIZABLE)                           *
        ****************************************************************************************/
    
        /// These are all for the admin pages.
        $this->local_options_lang_prompt = 'Language:';                       ///< The label for the Language Selector.
        $this->local_options_title = 'Opciones de la Herramienta Básica de Lista de Reuniones.';    	///< This is the title that is displayed over the options.
        $this->local_menu_string = 'Opciones BMLT.';                            					///< The name of the menu item.
        $this->local_options_prefix = 'Seleccionar configuración.';                  		    ///< The string displayed before each number in the options popup.
        $this->local_options_add_new = 'Agregar una Configuración Nueva.';                  	///< The string displayed in the "Add New Option" button.
        $this->local_options_save = 'Guardar Cambios.';                           			///< The string displayed in the "Save Changes" button.
        $this->local_options_delete_option = 'Eliminar esta Configuración.';           			///< The string displayed in the "Delete Option" button.<?php
        $this->local_options_delete_failure = 'La Eliminación de la Configuración Falló.'; 		///< The string displayed upon unsuccessful deletion of an option page.
        $this->local_options_create_failure = 'La Creación de la Configuración Falló.'; 		///< The string displayed upon unsuccessful creation of an option page.
        $this->local_options_delete_option_confirm = 'Está seguro de que desea eliminar esta Configuración?';    ///< The string displayed in the "Are you sure?" confirm.
        $this->local_options_delete_success = 'La Configuración fué Eliminada Satisfactoriamente.';        ///< The string displayed upon successful deletion of an option page.
        $this->local_options_create_success = 'La Configuración fué Creada Satisfactoriamente.';        ///< The string displayed upon successful creation of an option page.
        $this->local_options_save_success = 'La Configuración fue Actualizada Satisfactoriamente.';     ///< The string displayed upon successful update of an option page.
        $this->local_options_save_failure = 'La Configuración NO fue Actualizada.';                    ///< The string displayed upon unsuccessful update of an option page.
        $this->local_options_url_bad = 'El URL del servidor no Funciona con este plugin.';            ///< The string displayed if a root server URI fails to point to a valid root server.
        $this->local_options_access_failure = 'Usted no esta autorizado para realizar esta Operación.';               ///< This is displayed if a user attempts a no-no.
        $this->local_options_unsaved_message = 'Usted tiene cambios sin guardar. Esta seguro de que quiere salir sin Guardarlos?';   ///< This is displayed if a user attempts to leave a page without saving the options.
        $this->local_options_settings_id_prompt = 'El ID de esta Configuración es ';                              ///< This is so that users can see the ID for the setting.
        $this->local_options_settings_location_checkbox_label = 'Las Busquedas por Texto empiezan con la casilla de seleccion "Ubicación" seleccionada.';  ///< This is so that users can see the ID for the setting.
    
        /// These are all for the admin page option sheets.
        $this->local_options_name_label = 'Nombre de la Configuración:';                    ///< The Label for the setting name item.
        $this->local_options_rootserver_label = 'Root Server:';               ///< The Label for the root server item.
        $this->local_options_new_search_label = 'New Search URL:';            ///< The Label for the new search item.
        $this->local_options_gkey_label = 'Google Maps API Key:';             ///< The Label for the Google Maps API Key item.
        $this->local_options_no_name_string = 'Ingrese el Nombre de la Configuración';   ///< The Value to use for a name field for a setting with no name.
        $this->local_options_no_root_server_string = 'Ingrese el URL del Root Server';                               ///< The Value to use for a root with no URL.
        $this->local_options_no_new_search_string = 'Ingrese un nuevo URL de Búsqueda'; ///< The Value to use for a new search with no URL.
        $this->local_options_no_gkey_string = 'Ingrese un nuevo API Key';          ///< The Value to use for a new search with no URL.
        $this->local_options_test_server = 'Prueba';                            ///< This is the title for the "test server" button.
        $this->local_options_test_server_success = 'Versión ';                ///< This is a prefix for the version, on success.
        $this->local_options_test_server_failure = 'El URL del Root Server no es Válido';                       ///< This is a prefix for the version, on failure.
        $this->local_options_test_server_tooltip = 'Esto prueba el root server, para revisar si esta Bien.';         ///< This is the tooltip text for the "test server" button.
        $this->local_options_map_label = 'Seleccione un Punto Central y el nivel de Zoom inicial del Mapa';             ///< The Label for the map.
        $this->local_options_mobile_legend = 'Esto Afecta las Múltiples Búsquedas Interactivas (como Mapa, Móvil y Avanzado)';  ///< This indicates that the enclosed settings are for the fast mobile lookup.
        $this->local_options_mobile_grace_period_label = 'Periodo de Gracia:';     ///< When you do a "later today" search, you get a "Grace Period."
        $this->local_options_mobile_region_bias_label = 'Region Bias:';       ///< The label for the Region Bias Selector.
        $this->local_options_mobile_time_offset_label = 'Diferencia de Uso Horario para el Root Server:';       ///< This may have an offset (time zone difference) from the main server.
        $this->local_options_initial_view = array (                           ///< The list of choices for presentation in the popup.
                                                    'map' => 'Mapa',
                                                    'text' => 'Texto',
                                                    'advanced_map' => 'Mapa Avanzado',
                                                    'advanced_text' => 'Texto Avanzado'
                                                    );
        $this->local_options_initial_view_prompt = 'Tipo de Búsqueda Inicial:';    ///< The label for the initial view popup.
        $this->local_options_theme_prompt = 'Seleccione un Color para el Tema:';          ///< The label for the theme selection popup.
        $this->local_options_more_styles_label = 'Agregar Estilos CSS al Plugin:';                             ///< The label for the Additional CSS textarea.
        $this->local_options_distance_prompt = 'Unidades de Distancia:';             ///< This is for the distance units select.
        $this->local_options_distance_disclaimer = 'Esto no va a afectar todas las Presentaciones.';               ///< This tells the admin that only some stuff will be affected.
        $this->local_options_grace_period_disclaimer = 'Minutos que deben transcurrir Antes de que una Reunión sea considerada "Pasada"(Para las Búsquedas Rápidas).';      ///< This explains what the grace period means.
        $this->local_options_time_offset_disclaimer = 'Horas de diferecia del Root Server (Esto usualmente no es necesario).';            ///< This explains what the time offset means.
        $this->local_options_miles = 'Millas';                                 ///< The string for miles.
        $this->local_options_kilometers = 'Kilómetros';                       ///< The string for kilometers.
        $this->local_options_selectLocation_checkbox_text = 'Sólo Mostrar Servicios de Ubicación para Dispositivos Móviles';  ///< The label for the location services checkbox.
    
        $this->local_options_time_format_prompt = 'Formato de Fecha:';             ///< The label for the time format selection popup.
        $this->local_options_time_format_ampm = 'Ante Meridian (HH:MM AM/PM)';    ///< Ante Meridian Format Option
        $this->local_options_time_format_military = 'Militar (HH:MM)';       ///< Military Time Format Option
    
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
    
        $this->local_options_week_begins_on_prompt = 'Las Semanas Empiezan el día:';       ///< This is the label for the week start popup menu.

        $this->local_no_root_server = 'Usted necesita proporcionar el URI del root server para que esto funcione.';    ///< Displayed if there was no root server provided.

        /// These are for the actual search displays
        $this->local_select_search = 'Seleccione un Búsqueda Rápida';                 ///< Used for the "filler" in the quick search popup.
        $this->local_clear_search = 'Limpiar los Resultados de la Búsqueda';                   ///< Used for the "Clear" item in the quick search popup.
        $this->local_menu_new_search_text = 'Búsqueda Nueva';                     ///< For the new search menu in the old-style BMLT search.
        $this->local_cant_find_meetings_display = 'No se encontraron Reuniones en esta Búsqueda'; ///< When the new map search cannot find any meetings.
        $this->local_single_meeting_tooltip = 'Haga click en el Enlace para los detalles de esta Reunión.'; ///< The tooltip shown for a single meeting.
        $this->local_gm_link_tooltip = 'Haga click en el Enlace para ver la Ubicación de esta Reunión en Google Maps.';    ///< The tooltip shown for the Google Maps link.
    
        /// These are for the change display
        $this->local_change_label_date =  'Cambiar Fecha:';                     ///< The date when the change was made.
        $this->local_change_label_meeting_name =  'Nombre de la Reunión:';            ///< The name of the changed meeting.
        $this->local_change_label_service_body_name =  'Cuerpo de Servicio:';       ///< The name of the meeting's Service body.
        $this->local_change_label_admin_name =  'Cambiado por:';                ///< The name of the Service Body Admin that made the change.
        $this->local_change_label_description =  'Descripción:';              ///< The description of the change.
        $this->local_change_date_format = 'F j Y, \a\t g:i A';                ///< The format in which the change date/time is displayed.
    
        /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
        $this->local_noscript = 'Este recurso no va a funcionar porque JavaScript esta desactivado en su Navegador.';             ///< The string displayed in a <noscript> element.
    
        /************************************************************************************//**
        *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
        ****************************************************************************************/
    
        /// These are all for the [[bmlt_nouveau]] shortcode.
        $this->local_nouveau_advanced_button = 'Más Opciones';                ///< The button name for the advanced search in the nouveau search.
        $this->local_nouveau_map_button = 'Buscar con el mapa en lugar de Texto';    ///< The button name for the map search in the nouveau search.
        $this->local_nouveau_text_button = 'Buscar con Texto en lugar del Mapa';   ///< The button name for the text search in the nouveau search.
        $this->local_nouveau_text_go_button = 'Ir';                           ///< The button name for the "GO" button in the text search in the nouveau search.
        $this->local_nouveau_text_item_default_text = 'Ingrese el Texto para Buscar';    ///< The text that fills an empty text item.
        $this->local_nouveau_text_location_label_text = 'Este es el código postal de la Ubicación';         ///< The label text for the location checkbox.
        $this->local_nouveau_advanced_map_radius_label_1 = 'Encuentre Reuniones en un Radio de';                ///< The label text for the radius popup.
        $this->local_nouveau_advanced_map_radius_label_2 = 'la Ubicación del Marcador.';             ///< The second part of the label.
        $this->local_nouveau_advanced_map_radius_value_auto = 'Un Radio Automáticamente escogido';   ///< The second part of the label, if Miles
        $this->local_nouveau_advanced_map_radius_value_km = 'Km';                                 ///< The second part of the popup value, if Kilometers
        $this->local_nouveau_advanced_map_radius_value_mi = 'Millas';                              ///< The second part of the popup value, if Miles
        $this->local_nouveau_advanced_weekdays_disclosure_text = 'Días de la Semanda Seleccionados';             ///< The text that is used for the weekdays disclosure link.
        $this->local_nouveau_advanced_formats_disclosure_text = 'Formatos Seleccionados';               ///< The text that is used for the formats disclosure link.
        $this->local_nouveau_advanced_service_bodies_disclosure_text = 'Cuerpos de Servicio Seleccionados'; ///< The text that is used for the service bodies disclosure link.
        $this->local_nouveau_select_search_spec_text = 'Especifique una nueva Búsqueda';                    ///< The text that is used for the link that tells you to select the search specification.
        $this->local_nouveau_select_search_results_text = 'Ver los Resultados de la última Búsqueda';  ///< The text that is used for the link that tells you to select the search results.
        $this->local_nouveau_cant_find_meetings_display = 'No se encontraron reuniones en esta Búsqueda con los criterios administrados';     ///< When the new map search cannot find any meetings.
        $this->local_nouveau_cant_lookup_display = 'No se pudo determinar su Ubicación.';          ///< Displayed if the app is unable to determine the location.
        $this->local_nouveau_display_map_results_text = 'Mostrar los Resultados de la Búsqueda en un Mapa';    ///< The text for the display map results disclosure link.
        $this->local_nouveau_display_list_results_text = 'Mostrar los Resultados de la Búsqueda en una Lista';  ///< The text for the display list results disclosure link.
        $this->local_nouveau_table_header_array = array ( 'País', 'Estado/Provincia', 'Barrio', 'Ciudad', 'Nombre de la Reunión', 'Día de la Semana', 'Hora de Inicio', 'Ubicación', 'Formato', ' ' );
        $this->local_nouveau_weekday_long_array = array ( 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado' );
        $this->local_nouveau_weekday_short_array = array ( 'Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vier', 'Sáb' );
    
        $this->local_nouveau_meeting_results_count_sprintf_format = '%s Reuniones Encontradas';
        $this->local_nouveau_meeting_results_selection_count_sprintf_format = '%s Reuniones Seleccionadas, de las %s Reuniones Encontradas';
        $this->local_nouveau_meeting_results_single_selection_count_sprintf_format = '1 Reunión Seleccionada, de las %s Reuniones Encontradas';
        $this->local_nouveau_single_time_sprintf_format = 'Las reuniones ocurren cada %s, a las %s, y duran %s.';
        $this->local_nouveau_single_duration_sprintf_format_1_hr = '1 hora';
        $this->local_nouveau_single_duration_sprintf_format_mins = '%s minutos';
        $this->local_nouveau_single_duration_sprintf_format_hrs = '%s horas';
        $this->local_nouveau_single_duration_sprintf_format_hr_mins = '1 hora y %s minutos';
        $this->local_nouveau_single_duration_sprintf_format_hrs_mins = '%s horas y %s minutos';
    
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
    
        $this->local_nouveau_location_sprintf_format_wtf = 'No tenemos la Ubicación';
    
        $this->local_nouveau_location_services_set_my_location_advanced_button = 'Definir el Marcador a mi Ubicación Actual';
        $this->local_nouveau_location_services_find_all_meetings_nearby_button = 'Buscar Reuniones Cercanas';
        $this->local_nouveau_location_services_find_all_meetings_nearby_later_today_button = 'Buscar Reuniones Cercanas Hoy más tarde';
        $this->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button = 'Buscar Reuniones Cercanas para Mañana';
    
        $this->local_nouveau_location_sprintf_format_duration_title = 'Esta Reunión dura %s horas y %s minutos.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_title = 'Esta reunión dura 1 Hora.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = 'Esta reunión dura 1 Hora y %s minutos.';
        $this->local_nouveau_location_sprintf_format_duration_hours_only_title = 'Esta reunión dura %s Horas.';
        $this->local_nouveau_lookup_location_failed = "La búsqueda de la Dirección no se completo satisfactoriamente.";
        $this->local_nouveau_lookup_location_server_error = "La búsqueda de la Dirección no se completo satisfactoriamente, devido a un error con el servidor.";
        $this->local_nouveau_time_sprintf_format = '%d:%02d %s';
        $this->local_nouveau_am = 'AM';
        $this->local_nouveau_pm = 'PM';
        $this->local_nouveau_noon = 'Medio Día';
        $this->local_nouveau_midnight = 'Media Noche';
        $this->local_nouveau_advanced_map_radius_value_array = "0.25, 0.5, 1.0, 2.0, 5.0, 10.0, 15.0, 20.0, 50.0, 100.0, 200.0";
        $this->local_nouveau_meeting_details_link_title = 'Obtener más detalles de esta reunión.';
        $this->local_nouveau_meeting_details_map_link_uri_format = 'https://maps.google.com/maps?q=%f,%f';
        $this->local_nouveau_meeting_details_map_link_text = 'Mapa para la Reunión';

        $this->local_nouveau_single_formats_label = 'Formatos de Reuniones:';
        $this->local_nouveau_single_service_body_label = 'Cuerpo de Servicio:';

        $this->local_nouveau_prompt_array = array (
                                                    'weekday_tinyint' => 'Día de la Semana',
                                                    'start_time' => 'Hora de Inicio',
                                                    'duration_time' => 'Duración',
                                                    'formats' => 'Formato',
                                                    'distance_in_miles' => 'Distancia em Millas',
                                                    'distance_in_km' => 'Distancia en Kilómetros',
                                                    'meeting_name' => 'Nombre de la Reunión',
                                                    'location_text' => 'Nombre de la Ubicación',
                                                    'location_street' => 'Dirección',
                                                    'location_city_subsection' => 'Ciudad',
                                                    'location_neighborhood' => 'Barrio',
                                                    'location_municipality' => 'Pueblo',
                                                    'location_sub_province' => 'Provincia',
                                                    'location_province' => 'Estado',
                                                    'location_nation' => 'País',
                                                    'location_postal_code_1' => 'Código Postal',
                                                    'location_info' => 'Información Adiccional'
                                                    );
    
        /************************************************************************************//**
        *                   TABLE SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                    *
        ****************************************************************************************/
        $this->local_table_tab_loading_title_format        = 'Obteniendo Reuniones para %s';
        $this->local_table_header_time_label              = 'Hora';
        $this->local_table_header_meeting_name_label      = 'Nombre de la Reunión';
        $this->local_table_header_town_label              = 'Pueblo';
        $this->local_table_header_address_label           = 'Dirección';
        $this->local_table_header_format_label            = 'Formato';
        $this->local_table_header_tab_title_format        = 'Mostrar Reuniones para %s';
        $this->local_table_ante_meridian                  = '"AM","PM","Medio Día","Media Noche"';
        $this->local_table_no_meetings_format             = 'No hay reuniones para %s';
                                                
        /************************************************************************************//**
        *                      STATIC DATA MEMBERS (SPECIAL LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// This is the only localizable string that is not processed. This is because it contains HTML. However, it is also a "hidden" string that is only displayed when the browser does not support JS.
        $this->local_no_js_warning = '<noscript class="no_js">Esta Búsqueda de Reuniones no funcionara porque su Navegador no soporta JavaScript. Sin embargo puede usar el <a rel="external nofollow" href="###ROOT_SERVER###">Servidor Principal</a> para realizar la búsqueda.</noscript>'; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
        ****************************************************************************************/
                                    
        $this->local_new_map_option_1_label = 'Opciones de Búsqueda (No Aplicada a menos de que esta sección este Abierta):';
        $this->local_new_map_weekdays = 'Las reuniones se realizan estos días de la Semana:';
        $this->local_new_map_all_weekdays = 'Todos';
        $this->local_new_map_all_weekdays_title = 'Encontrar reuniones para todos los días.';
        $this->local_new_map_weekdays_title = 'Encontrar reuniones los Días ';
        $this->local_new_map_formats = 'Las Reuniones tienen estos Formatos:';
        $this->local_new_map_all_formats = 'Todos';
        $this->local_new_map_all_formats_title = 'Encontrar Reuniones de todos los formatos.';
        $this->local_new_map_js_center_marker_current_radius_1 = 'El radio es aprox ';
        $this->local_new_map_js_center_marker_current_radius_2_km = ' kilometros de radio.';
        $this->local_new_map_js_center_marker_current_radius_2_mi = ' millas de radio.';
        $this->local_new_map_js_diameter_choices = array ( 0.25, 0.5, 1.0, 1.5, 2.0, 3.0, 5.0, 10.0, 15.0, 20.0, 25.0, 30.0, 50.0, 100.0 );
        $this->local_new_map_js_new_search = 'Nueva Búsqueda';
        $this->local_new_map_option_loc_label = 'Ingresar una nueva Ubicación Enter A Location:';
        $this->local_new_map_option_loc_popup_label_1 = 'Buscar reuniones en un rango de';
        $this->local_new_map_option_loc_popup_label_2 = 'la Ubicación.';
        $this->local_new_map_option_loc_popup_km = 'Km';
        $this->local_new_map_option_loc_popup_mi = 'Millas';
        $this->local_new_map_option_loc_popup_auto = 'una distacia selecionada automáticamente';
        $this->local_new_map_center_marker_distance_suffix = ' del centro del Marcador.';
        $this->local_new_map_center_marker_description = 'Esta es la ubicación seleccionada.';
        $this->local_new_map_text_entry_fieldset_label = 'Ingrese una Dirección, Código Postal o una Ubicación';
        $this->local_new_map_text_entry_default_text = 'Ingrese una Dirección, Código Postal o una Ubicación';
        $this->local_new_map_location_submit_button_text = 'Buscar Reuniones cerca de esta Ubicación';
    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// The units for distance.
        $this->local_mobile_kilometers = 'Kilómetros';
        $this->local_mobile_miles = 'Millas';
        $this->local_mobile_distance = 'Distancia';  ///< Distance (the string)
    
        /// The page titles.
        $this->local_mobile_results_page_title = 'Resultados del la Búsqueda Rápida de Reuniones';
        $this->local_mobile_results_form_title = 'Buscar Reuniones cercanas Rápido';
    
        /// The fast GPS lookup links.
        $this->local_GPS_banner = 'Seleccione una Búsqueda de Reuniones Rápida';
        $this->local_GPS_banner_subtext = 'Guarde este enlce para encontrar reuniones más rápidamente en el futuro.';
        $this->local_search_all = 'Buscar Todas las Reuniones cercanas a mi Ubicación Actual.';
        $this->local_search_today = 'Más Tarde Hoy';
        $this->local_search_tomorrow = 'Mañana';
    
        /// The search for an address form.
        $this->local_list_check = 'Si está teniendo dificultades con el mapa interactivo, o desea tener los resultados en una lista, seleccione esta casilla e Ingrese una Dirección.';
        $this->local_search_address_single = 'Buscar Reuniones cercanas a una Dirección Específica';
    
        /// Used instead of "near my present location."
        $this->local_search_all_address = 'Buscar reuniones cercanas a esta dirección.';
        $this->local_search_submit_button = 'Buscar Reuniones';
    
        /// This is what is entered into the text box.
        $this->local_enter_an_address = 'Ingrese una Dirección';
    
        /// Error messages.
        $this->local_mobile_fail_no_meetings = 'No se encontrar reuniones!';
        $this->local_server_fail = 'La búsqueda falló debido a un error en el servidor!';
        $this->local_cant_find_address = 'No podemos determinar la Ubicacion con la información de la Dirección ingresada!';
        $this->local_cannot_determine_location = 'No pudimos determinar su ubicación Actual!';
        $this->local_enter_address_alert = 'Por favor Ingrese una dirección!';
    
        /// The text for the "Map to Meeting" links
        $this->local_map_link = 'Mapa a la Reunión';
    
        /// Only used for WML pages
        $this->local_next_card = 'Próxima Reunión >>';
        $this->local_prev_card = '<< Reunión Anterior';
    
        /// Used for the info and list windows.
        $this->local_formats = 'Formatos';
        $this->local_noon = 'Medio Día';
        $this->local_midnight = 'Media Noche';
    
        /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
        $this->local_weekdays = array ( 'ERROR', 'Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado' );
        $this->local_weekdays_short = array ( 'ERR', 'Dom', 'Lun', 'Mar', 'Mier', 'Jue', 'Vier', 'Sáb' );
    
        /************************************************************************************//**
        *                          STATIC DATA MEMBERS (QUICKSEARCH)                            *
        ****************************************************************************************/
        $this->local_quicksearch_select_option_0 = 'Buscar en todos lados';
        $this->local_quicksearch_display_too_large = 'Muchos Resultados, Porfavor filtre su búsqueda.';
        }
    };
?>