<?php
// Portugese - BR
/****************************************************************************************//**
*   \file   lang_br.php                                                                     *
*                                                                                           *
*   \brief  This file contains Brazilian Portuguese localizations.                          *
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
class BMLT_Localized_BaseClass_pt extends BMLT_Localized_BaseClass
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
        $this->local_options_title = 'Opções da Ferramenta Básica de Lista de Reuniões';        ///< This is the title that is displayed over the options.
        $this->local_menu_string = 'Opções BMLT';                                               ///< The name of the menu item.
        $this->local_options_prefix = 'Selecionar configuração';                            ///< The string displayed before each number in the options popup.
        $this->local_options_add_new = 'Adicionar uma nova Configuração';                   ///< The string displayed in the "Add New Option" button.
        $this->local_options_save = 'Salvar alterações';                                    ///< The string displayed in the "Save Changes" button.
        $this->local_options_delete_option = 'Apagar esta Configuração';                    ///< The string displayed in the "Delete Option" button.
        $this->local_options_delete_failure = 'Falha ao tentar apagar configuração.';       ///< The string displayed upon unsuccessful deletion of an option page.
        $this->local_options_create_failure = 'Falha ao tentar criar configuração.';            ///< The string displayed upon unsuccessful creation of an option page.
        $this->local_options_delete_option_confirm = 'Tem certeza que deseja apagar esta configuração?';    ///< The string displayed in the "Are you sure?" confirm.
        $this->local_options_delete_success = 'A configuração foi apagada com sucesso.';                      ///< The string displayed upon successful deletion of an option page.
        $this->local_options_create_success = 'A configuração foi criada com sucesso.';                       ///< The string displayed upon successful creation of an option page.
        $this->local_options_save_success = 'A configuração foi atualizada com sucesso.';                     ///< The string displayed upon successful update of an option page.
        $this->local_options_save_failure = 'A configuração não foi atualizada.';                             ///< The string displayed upon unsuccessful update of an option page.
        $this->local_options_url_bad = 'O endereço do servidor raiz não funciona com este plugin.';           ///< The string displayed if a root server URI fails to point to a valid root server.
        $this->local_options_access_failure = 'Voçê não tem permissão para realizar esta operação.';          ///< This is displayed if a user attempts a no-no.
        $this->local_options_unsaved_message = 'Existem alterações não salvas. Deseja sair sem salvar estas alterações?';   ///< This is displayed if a user attempts to leave a page without saving the options.
        $this->local_options_settings_id_prompt = 'O ID para esta Configuração é:';                            ///< This is so that users can see the ID for the setting.
        $this->local_options_settings_location_checkbox_label = 'Buscas por texto começam com a caixa de seleção "Local" marcada.';                              ///< This is so that users can see the ID for the setting.
    
        /// These are all for the admin page option sheets.
        $this->local_options_name_label = 'Nome da Configuração:';                    ///< The Label for the setting name item.
        $this->local_options_rootserver_label = 'Servidor Raiz:';               ///< The Label for the root server item.
        $this->local_options_new_search_label = 'New Search URL:';            ///< The Label for the new search item.
        $this->local_options_gkey_label = 'Chave API do Google Maps:';             ///< The Label for the Google Maps API Key item.
        $this->local_options_no_name_string = 'Digite o Nome da Configuração';           ///< The Value to use for a name field for a setting with no name.
        $this->local_options_no_root_server_string = 'Digite o endereço de um Servidor Raiz:';                               ///< The Value to use for a root with no URL.
        $this->local_options_no_new_search_string = 'Digite um novo endereço de Busca'; ///< The Value to use for a new search with no URL.
        $this->local_options_no_gkey_string = 'Digite uma nova chave API:';          ///< The Value to use for a new search with no URL.
        $this->local_options_test_server = 'Teste de conexão';                            ///< This is the title for the "test server" button.
        $this->local_options_test_server_success = 'Versão';                ///< This is a prefix for the version, on success.
        $this->local_options_test_server_failure = 'Este endereço de Servidor Raiz não é válido.';                       ///< This is a prefix for the version, on failure.
        $this->local_options_test_server_tooltip = 'Efetuar teste no servidor raiz, para ver se está OK.';         ///< This is the tooltip text for the "test server" button.
        $this->local_options_map_label = 'Selecione o Ponto Central e Nível de Zoom inicial do Mapa.';             ///< The Label for the map.
        $this->local_options_mobile_legend = 'Estas configurações afetam as Buscas Interativas (tais como Mapas, Dispositivos Móveis e Avançadas)';  ///< This indicates that the enclosed settings are for the fast mobile lookup.
        $this->local_options_mobile_grace_period_label = 'Período de Atraso:';     ///< When you do a "later today" search, you get a "Grace Period."
        $this->local_options_mobile_region_bias_label = 'Region Bias:';       ///< The label for the Region Bias Selector.
        $this->local_options_mobile_time_offset_label = 'Diferença de Fuso Horário para o Servidor Raiz:';       ///< This may have an offset (time zone difference) from the main server.
        $this->local_options_initial_view = array (                           ///< The list of choices for presentation in the popup.
                                                    'map' => 'Mapa',
                                                    'text' => 'Texto',
                                                    'advanced_map' => 'Mapa Avançado',
                                                    'advanced_text' => 'Texto Avançado'
                                                    );
        $this->local_options_initial_view_prompt = 'Tipo de Busca Inicial:';    ///< The label for the initial view popup.
        $this->local_options_theme_prompt = 'Selecione o Tema de Cores:';          ///< The label for the theme selection popup.
        $this->local_options_more_styles_label = 'Adicione um estilo CSS ao Plugin:';                             ///< The label for the Additional CSS textarea.
        $this->local_options_distance_prompt = 'Unidade de Distância:';             ///< This is for the distance units select.
        $this->local_options_distance_disclaimer = 'Isto não afetará todas as telas.';               ///< This tells the admin that only some stuff will be affected.
        $this->local_options_grace_period_disclaimer = 'Tempo (em minutos) de atraso antes que uma Reunião seja considerada "Terminada" (para Buscas Rápidas).';      ///< This explains what the grace period means.
        $this->local_options_time_offset_disclaimer = 'Diferença de Fuso Horário para o Servidor Raiz (Geralmente não é necessário informar).';            ///< This explains what the time offset means.
        $this->local_options_miles = 'Milhas';                                 ///< The string for miles.
        $this->local_options_kilometers = 'Quilômetros';                       ///< The string for kilometers.
        $this->local_options_selectLocation_checkbox_text = 'Mostrar Serviços de Local somente para dispositivos móveis';  ///< The label for the location services checkbox.
    
        $this->local_options_time_format_prompt = 'Formato de Horas:';             ///< The label for the time format selection popup.
        $this->local_options_time_format_ampm = 'Ante Meridian (HH:MM AM/PM)';    ///< Ante Meridian Format Option
        $this->local_options_time_format_military = 'Militar (HH:MM)';           ///< Military Time Format Option
    
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
    
        $this->local_options_week_begins_on_prompt = 'O primeiro dia da semana é:';       ///< This is the label for the week start popup menu.

        $this->local_no_root_server = 'Por favor, informar um endereço de servidor raiz para este serviço.';    ///< Displayed if there was no root server provided.

        /// These are for the actual search displays
        $this->local_select_search = 'Selecione uma Busca Rápida:';                 ///< Used for the "filler" in the quick search popup.
        $this->local_clear_search = 'Apagar os resultados da Busca';                   ///< Used for the "Clear" item in the quick search popup.
        $this->local_menu_new_search_text = 'Nova Busca';                     ///< For the new search menu in the old-style BMLT search.
        $this->local_cant_find_meetings_display = 'Nenhuma Reunião encontrada!'; ///< When the new map search cannot find any meetings.
        $this->local_single_meeting_tooltip = 'Clique no Link para Maiores Detalhes sobre esta Reunião.'; ///< The tooltip shown for a single meeting.
        $this->local_gm_link_tooltip = 'Clique no Link para ver a localização da Reunião no Google Maps.';    ///< The tooltip shown for the Google Maps link.
    
        /// These are for the change display
        $this->local_change_label_date =  'Data da alteração:';                     ///< The date when the change was made.
        $this->local_change_label_meeting_name =  'Nome da Reunião:';            ///< The name of the changed meeting.
        $this->local_change_label_service_body_name =  'Corpo de Serviço:';       ///< The name of the meeting's Service body.
        $this->local_change_label_admin_name =  'Alterado por:';                ///< The name of the Service Body Admin that made the change.
        $this->local_change_label_description =  'Descrição:';              ///< The description of the change.
        $this->local_change_date_format = 'F j Y, \a\t g:i A';                ///< The format in which the change date/time is displayed.
    
        /// A simple message for most <noscript> elements. We have a different one for the older interactive search (below).
        $this->local_noscript = 'Este recurso não funciona sem o Javascript ativado. Verifique as configurações do seu navegador';             ///< The string displayed in a <noscript> element.
    
        /************************************************************************************//**
        *                   NEW SHORTCODE STATIC DATA MEMBERS (LOCALIZABLE)                     *
        ****************************************************************************************/
    
        /// These are all for the [[bmlt_nouveau]] shortcode.
        $this->local_nouveau_advanced_button = 'Mais opções';                ///< The button name for the advanced search in the nouveau search.
        $this->local_nouveau_map_button = 'Busca pelo Mapa';    ///< The button name for the map search in the nouveau search.
        $this->local_nouveau_text_button = 'Busca por Texto';   ///< The button name for the text search in the nouveau search.
        $this->local_nouveau_text_go_button = 'Buscar';                           ///< The button name for the "GO" button in the text search in the nouveau search.
        $this->local_nouveau_text_item_default_text = 'Digite o texto da busca que deseja fazer';    ///< The text that fills an empty text item.
        $this->local_nouveau_text_location_label_text = 'Isto é um endereço ou CEP.';         ///< The label text for the location checkbox.
        $this->local_nouveau_advanced_map_radius_label_1 = 'Encontre Reuniões dentro de um raio de';                ///< The label text for the radius popup.
        $this->local_nouveau_advanced_map_radius_label_2 = 'do Local Marcado.';             ///< The second part of the label.
        $this->local_nouveau_advanced_map_radius_value_auto = 'Um raio automáticamente escolhido';   ///< The second part of the label, if Miles
        $this->local_nouveau_advanced_map_radius_value_km = 'KM';                                 ///< The second part of the popup value, if Kilometers
        $this->local_nouveau_advanced_map_radius_value_mi = 'Mi';                              ///< The second part of the popup value, if Miles
        $this->local_nouveau_advanced_weekdays_disclosure_text = 'Dias da semana selecionados';             ///< The text that is used for the weekdays disclosure link.
        $this->local_nouveau_advanced_formats_disclosure_text = 'Formatos selecionados';               ///< The text that is used for the formats disclosure link.
        $this->local_nouveau_advanced_service_bodies_disclosure_text = 'Corpos de Serviço selecionados'; ///< The text that is used for the service bodies disclosure link.
        $this->local_nouveau_select_search_spec_text = 'Fazer uma nova busca';                    ///< The text that is used for the link that tells you to select the search specification.
        $this->local_nouveau_select_search_results_text = 'Ver os resultados da última busca';  ///< The text that is used for the link that tells you to select the search results.
        $this->local_nouveau_cant_find_meetings_display = 'Nenhuma Reunião encontrada!';     ///< When the new map search cannot find any meetings.
        $this->local_nouveau_cant_lookup_display = 'Não foi possível identificar sua localização.';          ///< Displayed if the app is unable to determine the location.
        $this->local_nouveau_display_map_results_text = 'Mostrar os Resultados da Busca no Mapa';    ///< The text for the display map results disclosure link.
        $this->local_nouveau_display_list_results_text = 'Mostrar os Resultados da Busca em lista';  ///< The text for the display list results disclosure link.
        $this->local_nouveau_table_header_array = array ( 'País', 'Estado', 'Bairro', 'Cidade', 'Nome da Reunião', 'Dia da semana', 'Início', 'Local', 'Formato', ' ' );
        $this->local_nouveau_weekday_long_array = array ( 'Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado' );
        $this->local_nouveau_weekday_short_array = array ( 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb' );
    
        $this->local_nouveau_meeting_results_count_sprintf_format = '%s Reuniões encontradas';
        $this->local_nouveau_meeting_results_selection_count_sprintf_format = '%s Reuniões selecionadas das %s Reuniões encontradas';
        $this->local_nouveau_meeting_results_single_selection_count_sprintf_format = '1 Reunião selecionada das %s Reuniões encontradas';
        $this->local_nouveau_single_time_sprintf_format = 'Reuniões ocorrem às %s, às %s, com duração de %s.';
        $this->local_nouveau_single_duration_sprintf_format_1_hr = '1 hora';
        $this->local_nouveau_single_duration_sprintf_format_mins = '%s minutos';
        $this->local_nouveau_single_duration_sprintf_format_hrs = '%s horas';
        $this->local_nouveau_single_duration_sprintf_format_hr_mins = '1 hora e %s minutos';
        $this->local_nouveau_single_duration_sprintf_format_hrs_mins = '%s horas e %s minutos';
    
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
    
        $this->local_nouveau_location_sprintf_format_wtf = 'Nenhum local informado!';
    
        $this->local_nouveau_location_services_set_my_location_advanced_button = 'Definir o Marcador para meu local atual';
        $this->local_nouveau_location_services_find_all_meetings_nearby_button = 'Encontre Reuniões perto do meu local atual';
        $this->local_nouveau_location_services_find_all_meetings_nearby_later_today_button = 'Encontre Reuniões perto do meu local atual que começam hoje mais tarde ';
        $this->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button = 'Encontre Reuniões perto do meu local atual que começam amanhã  ';
    
        $this->local_nouveau_location_sprintf_format_duration_title = 'Esta Reunião tem duração de %s horas e %s minutos.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_title = 'Esta Reunião tem duração de 1 hora.';
        $this->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = 'Esta Reunião tem duração de 1 hora e %s minutos.';
        $this->local_nouveau_location_sprintf_format_duration_hours_only_title = 'Esta Reunião tem duração de %s horas.';
        $this->local_nouveau_lookup_location_failed = "A busca pelo endereço não foi realizada com sucesso.";
        $this->local_nouveau_lookup_location_server_error = "A busca pelo endereço não foi realizada com sucesso, devido a um erro no servidor.";
        $this->local_nouveau_time_sprintf_format = '%d:%02d %s';
        $this->local_nouveau_am = 'AM';
        $this->local_nouveau_pm = 'PM';
        $this->local_nouveau_noon = 'Meio-dia';
        $this->local_nouveau_midnight = 'Meia-noite';
        $this->local_nouveau_advanced_map_radius_value_array = "0.25, 0.5, 1.0, 2.0, 5.0, 10.0, 15.0, 20.0, 50.0, 100.0, 200.0";
        $this->local_nouveau_meeting_details_link_title = 'Ver mais detalhes desta Reunião.';
        $this->local_nouveau_meeting_details_map_link_uri_format = 'https://maps.google.com/maps?q=%f,%f';
        $this->local_nouveau_meeting_details_map_link_text = 'Mapa para a Reunião';

        $this->local_nouveau_single_formats_label = 'Formatos de Reunião:';
        $this->local_nouveau_single_service_body_label = 'Corpo de Serviço:';

        $this->local_nouveau_prompt_array = array (
                                                    'weekday_tinyint' => 'Dia da semana',
                                                    'start_time' => 'Início',
                                                    'duration_time' => 'Duração',
                                                    'formats' => 'Formato',
                                                    'distance_in_miles' => 'Distância em Milhas',
                                                    'distance_in_km' => 'Distância em Quilômetros',
                                                    'meeting_name' => 'Nome da Reunião',
                                                    'location_text' => 'Nome do Local',
                                                    'location_street' => 'Endereço',
                                                    'location_city_subsection' => 'Área',
                                                    'location_neighborhood' => 'Bairro',
                                                    'location_municipality' => 'Cidade',
                                                    'location_sub_province' => 'Região',
                                                    'location_province' => 'Estado',
                                                    'location_nation' => 'País',
                                                    'location_postal_code_1' => 'CEP',
                                                    'location_info' => 'Informações Adicionais'
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
        $this->local_no_js_warning = '<noscript class="no_js">Esta Busca não irá funcionar porque seu navegador não suporta JavaScript. Você pode usar <a rel="external nofollow" href="###ROOT_SERVER###">main server</a> para fazer esta busca.</noscript>'; ///< This is the noscript presented for the old-style meeting search. It directs the user to the root server, which will support non-JS browsers.
                                    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (NEW MAP LOCALIZABLE)                       *
        ****************************************************************************************/
                                    
        $this->local_new_map_option_1_label = 'Opções de Busca (Não disponíveis se este item estiver fechado):';
        $this->local_new_map_weekdays = 'Dias da semana';
        $this->local_new_map_all_weekdays = 'Todos';
        $this->local_new_map_all_weekdays_title = 'Encontre Reuniões todos os dias';
        $this->local_new_map_weekdays_title = 'Encontre Reuniões nos dias';
        $this->local_new_map_formats = 'Formatos:';
        $this->local_new_map_all_formats = 'Todos';
        $this->local_new_map_all_formats_title = 'Encontre Reuniões em todos os formatos.';
        $this->local_new_map_js_center_marker_current_radius_1 = 'O círculo é de aproximadamente ';
        $this->local_new_map_js_center_marker_current_radius_2_km = ' quilômetros.';
        $this->local_new_map_js_center_marker_current_radius_2_mi = ' milhas.';
        $this->local_new_map_js_diameter_choices = array ( 0.25, 0.5, 1.0, 1.5, 2.0, 3.0, 5.0, 10.0, 15.0, 20.0, 25.0, 30.0, 50.0, 100.0 );
        $this->local_new_map_js_new_search = 'Nova Busca';
        $this->local_new_map_option_loc_label = 'Digite um Local:';
        $this->local_new_map_option_loc_popup_label_1 = 'Buscar Reuniões no raio de';
        $this->local_new_map_option_loc_popup_label_2 = 'da localização atual.';
        $this->local_new_map_option_loc_popup_km = 'KM';
        $this->local_new_map_option_loc_popup_mi = 'Mi';
        $this->local_new_map_option_loc_popup_auto = 'uma distância automáticamente escolhida';
        $this->local_new_map_center_marker_distance_suffix = ' do marcador central.';
        $this->local_new_map_center_marker_description = 'Esta é sua localização escolhida.';
        $this->local_new_map_text_entry_fieldset_label = 'Digite um endereço, CEP ou Local';
        $this->local_new_map_text_entry_default_text = 'Digite um endereço, CEP ou Local';
        $this->local_new_map_location_submit_button_text = 'Buscar Reuniões nas proximidades deste local';
    
        /************************************************************************************//**
        *                       STATIC DATA MEMBERS (MOBILE LOCALIZABLE)                        *
        ****************************************************************************************/
    
        /// The units for distance.
        $this->local_mobile_kilometers = 'Quilômetros';
        $this->local_mobile_miles = 'Milhas';
        $this->local_mobile_distance = 'Distância';  ///< Distance (the string)
    
        /// The page titles.
        $this->local_mobile_results_page_title = 'Resultados da Busca Rápida';
        $this->local_mobile_results_form_title = 'Busca Rápida por Reuniões nas proximidades';
    
        /// The fast GPS lookup links.
        $this->local_GPS_banner = 'Selecione uma Busca Rápida';
        $this->local_GPS_banner_subtext = 'Salve este link para buscas mais rápidas.';
        $this->local_search_all = 'Buscar todas as Reuniões próximas da minha localização atual.';
        $this->local_search_today = 'Hoje mais tarde';
        $this->local_search_tomorrow = 'Amanhã';
    
        /// The search for an address form.
        $this->local_list_check = 'Se tiver dificuldades com o mapa interativo, ou desejar os resultados em lista, marque aqui e digite um endereço.';
        $this->local_search_address_single = 'Buscar Reuniões próximas a um endereço';
    
        /// Used instead of "near my present location."
        $this->local_search_all_address = 'Buscar todas as Reuniões próximas a este endereço.';
        $this->local_search_submit_button = 'Buscar Reuniões';
    
        /// This is what is entered into the text box.
        $this->local_enter_an_address = 'Digite um endereço';
    
        /// Error messages.
        $this->local_mobile_fail_no_meetings = 'Nenhuma Reunião encontrada!';
        $this->local_server_fail = 'A busca falhou porque o servidor retornou um erro!';
        $this->local_cant_find_address = 'Não é possível identificar o Local com o endereço digitado!';
        $this->local_cannot_determine_location = 'Não é possível identificar sua localização atual!';
        $this->local_enter_address_alert = 'Por favor digite um endereço!';
    
        /// The text for the "Map to Meeting" links
        $this->local_map_link = 'Mapa para a Reunião';
    
        /// Only used for WML pages
        $this->local_next_card = 'Próxima Reunião >>';
        $this->local_prev_card = '<< Reunião anterior';
    
        /// Used for the info and list windows.
        $this->local_formats = 'Formatos';
        $this->local_noon = 'Meio-dia';
        $this->local_midnight = 'Meio-noite';
    
        /// This array has the weekdays, spelled out. Since weekdays start at 1 (Sunday), we consider 0 to be an error.
        $this->local_weekdays = array ( 'ERRO', 'Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado' );
        $this->local_weekdays_short = array ( 'ER', 'Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb' );
    
        /************************************************************************************//**
        *                          STATIC DATA MEMBERS (QUICKSEARCH)                            *
        ****************************************************************************************/
        $this->local_quicksearch_select_option_0 = 'Pesquisar em Todo o Lado';
        $this->local_quicksearch_display_too_large = 'Too many results. Please narrow your search.';
    }
}
