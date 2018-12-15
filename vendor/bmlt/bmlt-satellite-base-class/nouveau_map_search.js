/****************************************************************************************//**
* \file nuveau_map_search.js																*
* \brief Javascript functions for the new default implementation.                           *
*                                                                                           *
*   This class implements the entire new default search algorithm (basic/advanced/text/map) *
*   in a manner that exports all the functionality to the client. It uses the JSON API      *
*   to communicate with the root server.                                                    *
*   This builds almost the entire BMLT search as a dynamic, DOM-constructed page. Very      *
*   little is done before execution time. A great deal of care has been taken to allow      *
*   robust, complete CSS presentation management.                                           *
*                                                                                           *
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

/****************************************************************************************//**
*	\brief  This class implements our bmlt_nouveau instance as an entirely DOM-generated    *
*           JavaScript Web app.                                                             *
********************************************************************************************/
function NouveauMapSearch ( in_unique_id,           ///< The UID of the container (will be used to get elements)
                            in_initial_view,        ///< This contains the initial view, as specified in the settings.
                            in_initial_lat,         ///< The initial latitude for the map.
                            in_initial_long,        ///< The initial longitude for the map.
                            in_initial_zoom,        ///< The initial zoom level for the map.
                            in_distance_units,      ///< The distance units (km or mi).
                            in_theme_dir,           ///< The selected theme directory (HTTP path).
                            in_root_server_uri,     ///< The base root server URI,
                            in_initial_text,        ///< If there is any initial text to be displayed, it should be here.
                            in_checked_location,    ///< If the "Location" checkbox should be checked, this should be TRUE.
                            in_show_location,       ///< If this is true, then the location services will be available.
                            in_single_meeting_id,   ///< If this has an integer number in it, it will display the details for a single meeting.
                            in_grace_period         ///< This is the number of minutes "grace" to give meetings in the location services ("Find Later Today").
                            )
{
	/****************************************************************************************
	*									  CLASS DATA MEMBERS								*
	****************************************************************************************/
    
    /// These are the state variables.
    var m_uid = null;                       ///< The unique identifier. This won't be changed after the construction.
    var m_current_view = null;              ///< One of 'map', 'text', 'advanced', 'advanced_map', 'advanced_text'. It will change as the object state changes.
    var m_current_long = null;              ///< The current map longitude. It will change as the map state changes.
    var m_current_lat = null;               ///< The current map latitude. It will change as the map state changes.
    var m_initial_zoom = null;              ///< This saves the original zoom, passed in.
    var m_current_zoom = null;              ///< The current map zoom. It will change as the map state changes.
    var m_distance_units = null;            ///< The distance units for this instance (km or mi)
    var m_theme_dir = null;                 ///< An HTTP path to the selected theme for this instance. Used to get images.
    var m_root_server_uri = null;           ///< A string, containing the URI of the root server. It will not change after construction.
    var m_initial_text = null;              ///< This will contain any initial text for the search text box.
    var m_checked_location = null;          ///< This is set at construction. If true, then the "Location" checkbox will be checked at startup.
    var m_show_location;                    ///< If this is true, then the location services will be available.
    var m_single_meeting_id = null;         ///< This will contain the ID of any single meeting being displayed.
    var m_grace_period = null;              ///< This is the number of minutes "grace" to give meetings in the location services ("Find Later Today").
    
    var m_default_duration = null;          ///< The default meeting length.
    
	var m_icon_image_single = null;         ///< The blue icon image.
	var m_icon_image_multi = null;          ///< The red icon image.
	var m_icon_image_selected = null;       ///< The selected (green) icon.
	var m_icon_shadow = null;               ///< The standard icon shadow.
	
	/// These describe the "You are here" icon.
	var m_center_icon_image = null;
	var m_center_icon_shadow = null;
	var m_center_icon_shape = null;
	
    /// These variables hold quick references to the various elements of the screen.
    var m_container_div = null;             ///< This is the main outer container. It also contains the script.
    var m_display_div = null;               ///< This is the div where everything happens.
    
    var m_search_spec_switch_div = null;    ///< This holds the switch between the spec and the results.
    var m_search_spec_switch_a = null;      ///< This holds the switch anchor element for the spec..
    var m_search_results_switch_a = null;   ///< This holds the switch anchor element for the results.
    
    var m_search_spec_div = null;           ///< This holds the search specification.
    var m_search_results_div = null;        ///< This holds the search results.
    
    var m_basic_advanced_switch_div = null; ///< This will contain the "basic and "advanced" switch links.
    var m_map_text_switch_div = null;       ///< This will contain the 'Map' and 'Text' switch links.
    var m_advanced_switch_a = null;         ///< This is the "advanced" disclosure switch
    var m_map_switch_a = null;              ///< This is the "map" anchor
    var m_text_switch_a = null;             ///< This is the "text" anchor
    var m_advanced_section_div = null;      ///< This is the advanced display section
    
    var m_advanced_go_a = null;             ///< This will be a "GO" button in the advanced search section.
    var m_advanced_map_distance_popup = null;   ///< This is the popup menu that selects the search radius.
    var m_advanced_map_distance_popup_label_1 = null;   ///< The left of the popup menu label.
    var m_advanced_map_distance_popup_label_2 = null;   ///< The left of the popup menu label.
    
    var m_map_div = null;                   ///< This will contain the map.
    var m_main_map = null;                  ///< This is the actual Google Maps instance.
    var m_search_radius = null;             ///< This is the chosen search radius (if the advanced search is open and the map is open).
    
    var m_text_div = null;                  ///< This will contain the text div.
    var m_text_inner_div = null;            ///< This will be an inner container, allowing more precise positioning.
    var m_text_item_div = null;             ///< This contains the text item.
    var m_text_input = null;                ///< This is the text search input element.
    var m_text_input_label = null;          ///< This is the text input label.
    var m_text_loc_checkbox_div = null;     ///< This contains the location checkbox item.
    var m_location_checkbox = null;         ///< This is the "This is a Location" checkbox.
    var m_location_checkbox_label = null;   ///< This is the "This is a Location" checkbox label.
    var m_text_go_button_div = null;        ///< This contains the go button item.
    var m_text_go_a = null;                 ///< This is the text div "GO" button (Anchor element).
    
    var m_location_services_panel = null;   ///< This displays the various location services.
    
    /// These all contain the various Advanced sub-sections
    
    /// The Map Options
    var m_advanced_map_options_div = null;
    var m_results_map_loaded = null;

    /// Weekdays
    var m_advanced_weekdays_div = null;
    var m_advanced_weekdays_header_div = null;
    var m_advanced_weekdays_disclosure_a = null;
    var m_advanced_weekdays_content_div = null;
    var m_advanced_weekdays_array = null;
    var m_advanced_weekdays_shown = null;
    
    /// Meeting Formats
    var m_advanced_formats_div = null;
    var m_advanced_formats_header_div = null;
    var m_advanced_formats_disclosure_a = null;
    var m_advanced_formats_content_div = null;
    var m_advanced_formats_shown = null;
    var m_advanced_format_checkboxes_array = null;  ///< This will contain all the formats checkboxes.
    
    /// Service Bodies
    var m_advanced_service_bodies_div = null;
    var m_advanced_service_bodies_header_div = null;
    var m_advanced_service_bodies_disclosure_a = null;
    var m_advanced_service_bodies_content_div = null;
    var m_advanced_service_bodies_shown = null;
    var m_advanced_service_bodies_checkboxes_array = null;
    
    /// The GO Button
    var m_advanced_go_button_div = null;
    
    /// The dynamic map search results
    var m_map_search_results_disclosure_div = null;
    var m_map_search_results_disclosure_a = null;
    var m_map_search_results_container_div = null;
    var m_map_search_results_inner_container_div = null;
    var m_map_search_results_map_div = null;
    var m_map_search_results_map = null;
    var m_mapResultsDisplayed = null;
    
    /// The dynamic list search results
    var m_list_search_results_disclosure_div = null;
    var m_list_search_results_disclosure_a = null;
    var m_list_search_results_container_div = null;
    var m_list_search_results_table = null;
    var m_list_search_results_table_head = null;
    var m_list_search_results_table_body = null;
    var m_listResultsDisplayed = null;
    
    var m_throbber_div = null;                  ///< This will show the throbber.
    var m_details_div = null;                   ///< This will hold the meeting details.
    var m_details_inner_div = null;             ///< This will show the meeting details.
    var m_single_meeting_display_div = null;    ///< This is the div that will be used to display the details of a single meeting.
    
    var m_details_meeting_name_div = null;      ///< This will hold the meeting name in the details window.
    var m_details_meeting_time_div = null;      ///< The day and time display for the details page.
    var m_details_meeting_location_div = null;  ///< This will hold the meeting location in the details window.
    var m_details_map_container_div = null;     ///< This is the outer container for the details map.
    var m_details_map_div = null;               ///< The div that implements the details map.
    var m_details_map = null;                   ///< This contains the map displayed in the details page.
    var m_details_service_body_div = null;      ///< The div that will show the meeting Service body.
    var m_details_service_body_span = null;     ///< This holds the actual text for the Service body.
    var m_details_comments_div = null;          ///< The div that will show the meeting comments.
    var m_details_formats_div = null;           ///< The div that will list the meeting formats.
    var m_details_formats_contents_div = null;  ///< The div that will list the meeting formats.
    var m_details_observer_only_div = null;     ///< The div that will list the items only visible to logged-in Observers.
    
    var m_search_results = null;                ///< If there are any search results, they are kept here (JSON object).
    var m_selected_search_results = null;       ///< This contains the number of meetings selected in the list.
    var m_long_lat_northeast = null;            ///< This will contain the long/lat for the maximum North and West coordinate to show all the meetings in the search.
    var m_long_lat_southwest = null;            ///< This will contain the long/lat for the maximum South and East coordinate to show all the meetings in the search.
    var m_search_results_shown = null;          ///< If this is true, then the results div is displayed.
    var m_map_search_results_display_result_text_div = null;            ///< This will display a count of returned meetings.
    var m_map_search_results_display_result_print_text_div = null;      ///< THis is a version that shows up in printed results.
    var m_location_services_panel_advanced_marker_button_div = null;    ///< This will hold the button that allows the advanced marker position to be set to the user's location.
    var m_location_services_panel_date_button_div = null;               ///< This will hold the buttons that do a date-sensitive lookup of the user location..
    
    var m_ajax_request = null;                  ///< This is used to handle AJAX calls.
    
    var m_search_sort_key = null;               ///< This can be 'time', 'town', 'name', or 'distance'.
    
    var m_format_descriptions = null;           ///< This will contain our formats.
    var m_service_bodies = null;                ///< This will contain our Service bodies.
    var m_geocoder = null;                      ///< This will hold any active address lookup.
    var m_g_geo = null;                         ///< This will hold any Google Gears geo lookup.
    
    var m_pre_search_lat = null;                ///< This will hold the main latitude, prior to a search (used to replace it if the search fails).
    var m_pre_search_long = null;               ///< The same for longitude.
    
    var m_semaphore_lookup_day = null;                  ///< If we are going to look up today or tomorrow, this holds that value.
    var m_semaphore_lookup_location_services = null;    ///< This is a flag, stating that we are in a location services search (pretends to be a map search).
    var m_semaphore_lookup_set_marker = null;           ///< This will indicate that the advanced marker is to be positioned.
    var m_semaphore_lookup_retry_count = null;          ///< This counts cache retries.
    
    /****************************************************************************************
    *								  INTERNAL CLASS FUNCTIONS							    *
    ****************************************************************************************/
    /****************************************************************************************
    *################################# INITIAL SETUP ROUTINES ##############################*
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief Sets up all the various DOM elements that comprise the search screen.        *
    ****************************************************************************************/
    this.buildDOMTree = function ()
        {
        var this_object = this;
        document.body.onkeyup = function(e){if (e.keyCode == 27) { this_object.hideDetails(); }};
        // First, create and set up the entire screen.
        this.m_display_div = document.createElement ( 'div' );
        this.m_display_div.className = 'bmlt_nouveau_div';
        this.m_display_div.id = this.m_uid;
        var id = this.m_uid;
        this.m_display_div.onkeypress = function () { NouveauMapSearch.prototype.sKeyDown ( id ); };
        
        // Next, create the spec/results switch.
        this.buildDOMTree_ResultsSpec_Switch();
        
        // Next, create the search specification div.
        this.m_search_spec_div = document.createElement ( 'div' );
        this.m_search_spec_div.className = 'bmlt_nouveau_search_spec_div';
        
        this.buildDOMTree_Location_Services_Panel();

        this.buildDOMTree_Map_Text_Switch();
        
        this.buildDOMTree_Spec_Map_Div();
        this.buildDOMTree_Text_Div();
        
        this.buildDOMTree_Basic_Advanced_Switch();
        this.buildDOMTree_AdvancedSection();
        
        this.setBasicAdvancedSwitch();
        this.setMapTextSwitch();
        
        this.m_display_div.appendChild ( this.m_search_spec_div );
        
        // Next, create the search results div.
        this.buildDOMTree_SearchResults_Section();
        
        this.setDisplayedSearchResults();   // Make sure that the proper div is displayed.
        this.validateGoButtons();
        
        this.buildDOMTree_CreateThrobberDiv();
        this.hideThrobber();
        
        this.buildDOMTree_CreateDetailsDiv();
        this.hideDetails();
        
        // Finally, set everything into the container.
        this.m_container_div.appendChild ( this.m_display_div );
        };
    
    /****************************************************************************************
    *######################## SET UP SEARCH SPEC AND RESULTS SWITCH ########################*
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief This sets up the "MAP/TEXT" tab switch div.                                  *
    ****************************************************************************************/
    this.buildDOMTree_ResultsSpec_Switch = function ()
        {
        this.m_search_spec_switch_div = document.createElement ( 'div' );   // Create the switch container.
        
        this.m_search_spec_switch_a = document.createElement ( 'a' );      // Create the basic switch anchor element.
        this.m_search_spec_switch_a.appendChild ( document.createTextNode(g_Nouveau_select_search_spec_text) );
        this.m_search_spec_switch_div.appendChild ( this.m_search_spec_switch_a );
        
        this.m_search_results_switch_a = document.createElement ( 'a' );      // Create the advanced switch anchor element.
        this.m_search_results_switch_a.appendChild ( document.createTextNode(g_Nouveau_select_search_results_text) );
        this.m_search_spec_switch_div.appendChild ( this.m_search_results_switch_a );
        
        this.m_search_spec_switch_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.searchSpecButtonHit()' );
        this.m_search_results_switch_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.searchResultsButtonHit()' );
        
        this.m_display_div.appendChild ( this.m_search_spec_switch_div );
        };
        
    /****************************************************************************************
    *################################### SET UP SEARCH SPEC ################################*
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief This sets up the "MAP/TEXT" tab switch div.                                  *
    ****************************************************************************************/
    this.buildDOMTree_Map_Text_Switch = function ( in_container_node   ///< This holds the node that will contain the switch.
                                                  )
        {
        this.m_map_text_switch_div = document.createElement ( 'div' );   // Create the switch container.
        this.m_map_text_switch_div.className = 'bmlt_nouveau_switcher_div bmlt_nouveau_text_map_switcher_div';
        
        this.m_map_switch_a = document.createElement ( 'a' );      // Create the basic switch anchor element.
        this.m_map_switch_a.appendChild ( document.createTextNode(g_NouveauMapSearch_map_name_string) );
        this.m_map_text_switch_div.appendChild ( this.m_map_switch_a );
        
        this.m_text_switch_a = document.createElement ( 'a' );      // Create the advanced switch anchor element.
        this.m_text_switch_a.appendChild ( document.createTextNode(g_NouveauMapSearch_text_name_string) );
        this.m_map_text_switch_div.appendChild ( this.m_text_switch_a );
        
        this.m_text_switch_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.textButtonHit()' );
        this.m_map_switch_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.mapButtonHit()' );
        
        this.m_search_spec_div.appendChild ( this.m_map_text_switch_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setMapTextSwitch = function()
        {
        if ( (this.m_current_view == 'map') || (this.m_current_view == 'advanced_map') )
            {
            this.m_map_switch_a.className = 'bmlt_nouveau_switch_a_selected';
            this.m_text_switch_a.className = 'bmlt_nouveau_switch_a';
            this.m_text_div.className = 'bmlt_nouveau_text_div text_div_hidden';
            this.m_map_div.className = 'bmlt_nouveau_map_div';
            this.m_advanced_map_options_div.className = 'bmlt_nouveau_advanced_map_options_div';
            if ( m_location_services_panel_advanced_marker_button_div )
                {
                this.m_location_services_panel_advanced_marker_button_div.className = (this.m_current_view == 'advanced_map') ? 'bmlt_nouveau_advanced_marker_button_div' : 'bmlt_nouveau_advanced_marker_button_hidden_div';
                };
            }
        else
            {
            this.m_map_switch_a.className = 'bmlt_nouveau_switch_a';
            this.m_text_switch_a.className = 'bmlt_nouveau_switch_a_selected';
            this.m_map_div.className = 'bmlt_nouveau_map_div map_div_hidden';
            this.m_text_div.className = 'bmlt_nouveau_text_div';
            this.m_advanced_map_options_div.className = 'bmlt_nouveau_advanced_map_options_div bmlt_nouveau_advanced_map_options_div_hidden';
            this.m_text_input.select();
            if ( m_location_services_panel_advanced_marker_button_div )
                {
                this.m_location_services_panel_advanced_marker_button_div.className = 'bmlt_nouveau_advanced_marker_button_hidden_div';
                };
            };
        };
    
    /************************************************************************************//**
    *	\brief This constructs the map div (used by the map search).                        *
    ****************************************************************************************/
    this.buildDOMTree_Spec_Map_Div = function ()
        {
        this.m_map_div = document.createElement ( 'div' );   // Create the map container.
        this.m_map_div.className = 'bmlt_nouveau_map_div';
        if ( ((this.m_current_view == 'map') || (this.m_current_view == 'advanced_map')) && !this.m_search_results_shown )
            {
            this.loadSpecMap();
            };
        this.m_search_spec_div.appendChild ( this.m_map_div );
        };
    
    /************************************************************************************//**
    *	\brief This creates the map for the search spec.                                    *
    ****************************************************************************************/
	this.loadSpecMap = function ( )
	    {
	    if ( !this.m_search_results_shown && ((this.m_current_view == 'advanced_map') || this.m_current_view == 'map') ) // This can only happen when the spec map is shown.
	        {
            if ( this.m_map_div && !this.m_main_map )
                {
                var myOptions = {
                                'center': new google.maps.LatLng ( this.m_current_lat, this.m_current_long ),
                                'zoom': this.m_current_zoom,
                                'mapTypeId': google.maps.MapTypeId.ROADMAP,
                                'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                                'zoomControl': true,
                                'mapTypeControl': true,
                                'disableDoubleClickZoom' : true,
                                'draggableCursor': "crosshair",
                                'scaleControl' : true
                                };

                myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };

                this.m_main_map = new google.maps.Map ( this.m_map_div, myOptions );
            
                if ( this.m_main_map )
                    {
                    this.m_main_map.setOptions({'scrollwheel': false});   // For some reason, it ignores setting this in the options.
                    this.m_main_map.map_marker = null;
                    this.m_main_map.geo_width = null;
                    this.m_main_map._circle_overlay = null;
                
                    var id = this.m_uid;
                
                    google.maps.event.addListener ( this.m_main_map, 'click', function(in_event) { NouveauMapSearch.prototype.sMapClicked( in_event, id ); } );
                    };
                };
        
            if ( this.m_map_search_results_map && this.m_main_map )
                {
                this.m_main_map.setCenter ( this.m_map_search_results_map.getCenter() );
                this.m_main_map.setZoom ( this.m_map_search_results_map.getZoom() );
                }
            else if ( this.m_main_map )
                {
                this.m_main_map.setCenter ( new google.maps.LatLng ( this.m_current_lat, this.m_current_long ) );
                this.m_main_map.setZoom ( this.m_current_zoom );
                };
            
            if ( this.m_current_view == 'advanced_map' )
                {
                this.displayMarkerInAdvancedMap();
                };
            };
	    };
    
    /************************************************************************************//**
    *	\brief This creates the map for the search spec.                                    *
    ****************************************************************************************/
	this.loadResultsMap = function ( )
	    {
        if ( this.m_map_search_results_map_div && !this.m_map_search_results_map )
            {
            this.m_results_map_loaded = false;
            var myOptions = {
                            'center': new google.maps.LatLng ( this.m_current_lat, this.m_current_long ),
                            'zoom': this.m_current_zoom,
                            'mapTypeId': google.maps.MapTypeId.ROADMAP,
                            'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                            'zoomControl': true,
                            'mapTypeControl': true,
                            'disableDoubleClickZoom' : true,
                            'scaleControl' : true
                            };

            var	pixel_width = this.m_map_search_results_map_div.offsetWidth;
            var	pixel_height = this.m_map_search_results_map_div.offsetHeight;
            
            myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };

            this.m_map_search_results_map = new google.maps.Map ( this.m_map_search_results_map_div, myOptions );
            
            if ( this.m_map_search_results_map )
                {
                this.m_map_search_results_map.setOptions({'scrollwheel': false});   // For some reason, it ignores setting this in the options.
                
                google.maps.event.addListener ( this.m_map_search_results_map, 'click', function(in_event) { NouveauMapSearch.prototype.sResultMapClicked( in_event, id ); } );

                this.m_map_search_results_map.meeting_marker_array = new Array;;
                this.m_map_search_results_map.meeting_marker_object_array = null;
                this.m_map_search_results_map.main_marker = null;
                
                var id = this.m_uid;
                
                google.maps.event.addListener ( this.m_map_search_results_map, 'zoom_changed', function(in_event) { NouveauMapSearch.prototype.sMapZoomChanged( in_event, id ); } );
                google.maps.event.addListenerOnce ( this.m_map_search_results_map, 'tilesloaded', function() { NouveauMapSearch.prototype.sMapTilesLoaded( id ); } );

                this.m_map_search_results_map.fitBounds ( new google.maps.LatLngBounds ( this.m_long_lat_southwest, this.m_long_lat_northeast ) );
                this.m_current_zoom = this.m_map_search_results_map.getZoom();
                };
            }
        else if ( this.m_map_search_results_map )
            {
            this.m_map_search_results_map.setCenter ( new google.maps.LatLng ( this.m_current_lat, this.m_current_long ) );
            this.m_map_search_results_map.setZoom ( this.m_current_zoom );
            this.redrawResultMapMarkers();
            };
        
        if ( this.m_main_map )
            {
            this.m_main_map.setCenter ( this.m_map_search_results_map.getCenter() );
            this.m_main_map.setZoom ( this.m_map_search_results_map.getZoom() );
            };
	    };
    
    /************************************************************************************//**
    *	\brief This constructs the text div (used by the text search).                      *
    ****************************************************************************************/
    this.buildDOMTree_Text_Div = function ()
        {
        this.m_text_div = document.createElement ( 'div' );
        this.m_text_div.className = 'bmlt_nouveau_text_div';
        
        this.m_text_inner_div = document.createElement ( 'div' );
        this.m_text_inner_div.className = 'bmlt_nouveau_text_inner_div';
        
        this.m_text_item_div = document.createElement ( 'div' );
        this.m_text_item_div.className = 'bmlt_nouveau_text_item_div';
        
        this.m_text_input = document.createElement ( 'input' );
        this.m_text_input.type = "text";
        this.m_text_input.defaultValue = g_Nouveau_text_item_default_text;
        this.m_text_input.className = 'bmlt_nouveau_text_input_empty';
        this.m_text_input.value = this.m_text_input.defaultValue;

        // If we have any initial text, we enter that.
        if ( this.m_initial_text )
            {
            this.m_text_input.value = this.m_initial_text;
            this.m_text_input.className = 'bmlt_nouveau_text_input';
            };

        // We just call the global handlers (since callbacks are in their own context, no worries).
        this.m_text_input.uid = this.m_uid; // Used to establish context in the callbacks.
        this.m_text_input.onfocus = function () {NouveauMapSearch.prototype.sCheckTextInputFocus(this);};
        this.m_text_input.onblur = function () {NouveauMapSearch.prototype.sCheckTextInputBlur(this);};
        this.m_text_input.onkeyup = function () {NouveauMapSearch.prototype.sCheckTextInputKeyUp(this);};
        
        this.m_text_item_div.appendChild ( this.m_text_input );
        this.m_text_inner_div.appendChild ( this.m_text_item_div );
        
        this.m_text_go_button_div = document.createElement ( 'div' );
        this.m_text_go_button_div.className = 'bmlt_nouveau_text_go_button_div';
        
        this.m_text_go_a = document.createElement ( 'a' );
        this.m_text_go_a.appendChild ( document.createTextNode(g_Nouveau_text_go_button_string) );
        
        this.m_text_go_button_div.appendChild ( this.m_text_go_a );
        this.m_text_inner_div.appendChild ( this.m_text_go_button_div );
        
        this.m_text_loc_checkbox_div = document.createElement ( 'div' );
        this.m_text_loc_checkbox_div.className = 'bmlt_nouveau_text_checkbox_div';
        
        this.m_location_checkbox = document.createElement ( 'input' );
        this.m_location_checkbox.type = 'checkbox';
        this.m_location_checkbox.id = this.m_uid + '_location_checkbox';
        this.m_location_checkbox.className = 'bmlt_nouveau_text_loc_checkbox';
        this.m_location_checkbox.checked = this.m_checked_location;
        this.m_location_checkbox.defaultChecked = this.m_location_checkbox.checked; // For IE9.
        var uid = this.m_uid;
        this.m_location_checkbox.onchange = function () { NouveauMapSearch.prototype.sLocationCheckboxHit ( uid ); };
                
        this.m_location_checkbox_label = document.createElement ( 'label' );
        this.m_location_checkbox_label.className = 'bmlt_nouveau_text_checkbox_label';
        this.m_location_checkbox_label.setAttribute ( 'for', this.m_uid + '_location_checkbox' );
        
        this.m_location_checkbox_label.appendChild ( document.createTextNode(g_Nouveau_text_location_label_text) );

        this.m_text_loc_checkbox_div.appendChild ( this.m_location_checkbox );
        this.m_text_loc_checkbox_div.appendChild ( this.m_location_checkbox_label );

        this.m_text_inner_div.appendChild ( this.m_text_loc_checkbox_div );
        this.m_text_div.appendChild ( this.m_text_inner_div );
        
        var elem = document.createElement ( 'div' );
        elem.className = 'bmlt_nouveau_breaker_div';
        this.m_text_div.appendChild ( elem );
        
        this.m_search_spec_div.appendChild ( this.m_text_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets up the Location Services panel. It will only be displayed if they  *
    *          are available in the client browser.                                         *
    ****************************************************************************************/
    this.buildDOMTree_Location_Services_Panel = function ()
        {
        if ( this.m_show_location )
            {
            this.m_location_services_panel = document.createElement ( 'div' );
            this.m_location_services_panel.className = (this.hasNavCapability()) ? 'bmlt_nouveau_location_services_div' : 'bmlt_nouveau_location_services_hidden_div';
        
            var inner_div = document.createElement ( 'div' );
            inner_div.className = 'bmlt_nouveau_location_services_inner_div';
        
            var button_div = document.createElement ( 'div' );
            button_div.className = 'bmlt_nouveau_find_nearby_meetings_button_div';
        
            var regular_button = document.createElement ( 'a' );
            regular_button.appendChild ( document.createTextNode(g_Nouveau_location_services_find_all_meetings_nearby_button) );
            regular_button.className = 'bmlt_nouveau_location_button_a bmlt_nouveau_find_nearby_meetings_button_a';
            regular_button.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.handleFindNearbyMeetingsByDay(null)' );
            button_div.appendChild ( regular_button );
            inner_div.appendChild ( button_div );
        
            this.m_location_services_panel_date_button_div = document.createElement ( 'div' );
            this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_div';
        
            button_div = document.createElement ( 'div' );
            button_div.className = 'bmlt_nouveau_find_nearby_meetings_today_button_div';
        
            regular_button = document.createElement ( 'a' );
            regular_button.appendChild ( document.createTextNode(g_Nouveau_location_services_find_all_meetings_nearby_later_today_button) );
            regular_button.className = 'bmlt_nouveau_location_button_a bmlt_nouveau_find_nearby_meetings_today_button_a';
            regular_button.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.handleFindNearbyMeetingsByDay(\'today\')' );
            button_div.appendChild ( regular_button );
        
            this.m_location_services_panel_date_button_div.appendChild ( button_div );
                
            button_div = document.createElement ( 'div' );
            button_div.className = 'bmlt_nouveau_find_nearby_meetings_tomorrow_button_div';
        
            regular_button = document.createElement ( 'a' );
            regular_button.appendChild ( document.createTextNode(g_Nouveau_location_services_find_all_meetings_nearby_tomorrow_button) );
            regular_button.className = 'bmlt_nouveau_location_button_a bmlt_nouveau_find_nearby_meetings_tomorrow_button_a';
            regular_button.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.handleFindNearbyMeetingsByDay(\'tomorrow\')' );
            button_div.appendChild ( regular_button );
        
            this.m_location_services_panel_date_button_div.appendChild ( button_div );
        
            inner_div.appendChild ( this.m_location_services_panel_date_button_div );
        
            this.m_location_services_panel.appendChild ( inner_div );
            this.m_search_spec_div.appendChild ( this.m_location_services_panel );
            };
        };
    
    /************************************************************************************//**
    *	\brief This sets up the "MAP/TEXT" tab switch div.                                  *
    ****************************************************************************************/
    this.buildDOMTree_Basic_Advanced_Switch = function ()
        {
        this.m_basic_advanced_switch_div = document.createElement ( 'div' );   // Create the switch container.
        this.m_basic_advanced_switch_div.className = 'bmlt_nouveau_switcher_div bmlt_nouveau_advanced_switcher_div';
        
        this.m_advanced_switch_a = document.createElement ( 'a' );      // Create the advanced switch anchor element.
        this.m_advanced_switch_a.appendChild ( document.createTextNode(g_NouveauMapSearch_advanced_name_string) );
        this.m_advanced_switch_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.toggleAdvanced()' );
        this.m_basic_advanced_switch_div.appendChild ( this.m_advanced_switch_a );
        
        this.m_search_spec_div.appendChild ( this.m_basic_advanced_switch_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets up the "BASIC/ADVANCED" tab switch div.                            *
    ****************************************************************************************/
    this.buildDOMTree_AdvancedSection = function ()
        {
        this.m_advanced_section_div = document.createElement ( 'div' );
        this.m_advanced_section_div.className = 'bmlt_nouveau_advanced_section_div';
        
        this.buildDOMTree_Advanced_MapOptions();
        this.buildDOMTree_Advanced_Weekdays();
        this.buildDOMTree_Advanced_Formats();
        this.buildDOMTree_Advanced_Service_Bodies();
        this.buildDOMTree_Advanced_GoButton();
        
        this.setAdvancedWeekdaysDisclosure();
        this.setAdvancedFormatsDisclosure();
        this.setAdvancedServiceBodiesDisclosure();
        
        this.m_search_spec_div.appendChild ( this.m_advanced_section_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setBasicAdvancedSwitch = function()
        {
        if ( (this.m_current_view == 'advanced_map') || (this.m_current_view == 'advanced_text') )
            {
            this.m_advanced_switch_a.className = 'bmlt_nouveau_advanced_switch_disclosure_open_a';
            this.m_advanced_section_div.className = 'bmlt_nouveau_advanced_section_div';
            this.m_text_go_button_div.className = 'bmlt_nouveau_text_go_button_div text_go_a_hidden';
            if ( this.m_location_services_panel_advanced_marker_button_div )
                {
                this.m_location_services_panel_advanced_marker_button_div.className = (this.m_current_view == 'advanced_map') ? 'bmlt_nouveau_advanced_marker_button_div' : 'bmlt_nouveau_advanced_marker_button_hidden_div';
                };
            }
        else
            {
            this.m_advanced_switch_a.className = 'bmlt_nouveau_advanced_switch_disclosure_a';
            this.m_advanced_section_div.className = 'bmlt_nouveau_advanced_section_div advanced_div_hidden';
            this.m_text_go_button_div.className = 'bmlt_nouveau_text_go_button_div';
            if ( this.m_location_services_panel_advanced_marker_button_div )
                {
                this.m_location_services_panel_advanced_marker_button_div.className = 'bmlt_nouveau_advanced_marker_button_hidden_div';
                };
            };
            
        if ( this.m_location_services_panel_date_button_div )
            {
            if ( this.m_advanced_weekdays_shown && ((this.m_current_view == 'advanced_map') || (this.m_current_view == 'advanced_text')) )
                {
                this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_hidden_div';
                }
            else
                {
                this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_div';
                };
            };
        
        this.displayMarkerInAdvancedMap();
        };
    
    /************************************************************************************//**
    *	\brief Build the Advanced Map Options section.                                      *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_MapOptions = function ()
        {
        if ( this.m_show_location )
            {
            this.m_location_services_panel_advanced_marker_button_div = document.createElement ( 'div' );
            this.m_location_services_panel_advanced_marker_button_div.className = 'bmlt_nouveau_advanced_marker_button_div';
        
            var marker_button = document.createElement ( 'a' );
            marker_button.appendChild ( document.createTextNode(g_Nouveau_location_services_set_my_location_advanced_button) );
            marker_button.className = 'bmlt_nouveau_location_button_a bmlt_nouveau_advanced_marker_button_a';
            marker_button.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.setLocationOfMainMarker()' );
            this.m_location_services_panel_advanced_marker_button_div.appendChild ( marker_button );
        
            this.m_advanced_section_div.appendChild ( this.m_location_services_panel_advanced_marker_button_div );
            };
            
        this.m_advanced_map_options_div = document.createElement ( 'div' );
        this.buildDOMTree_Advanced_DistancePopup();
        this.m_advanced_section_div.appendChild ( this.m_advanced_map_options_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the Advanced Weekdays section.                                         *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_DistancePopup = function ()
        {
        var container_div = document.createElement ( 'div' );
        container_div.className = 'bmlt_nouveau_advanced_map_options_container_div';
        
        this.m_advanced_map_distance_popup_label_1 = document.createElement ( 'label' );
        this.m_advanced_map_distance_popup_label_1.className = 'bmlt_nouveau_advanced_map_popup_label_left';
        this.m_advanced_map_distance_popup_label_1.appendChild ( document.createTextNode(g_Nouveau_advanced_map_radius_label_1) );
        
        this.m_advanced_map_distance_popup = document.createElement ( 'select' );
        this.m_advanced_map_distance_popup.className = 'bmlt_nouveau_advanced_map_popup_select';
        
        var option_1 = document.createElement ( 'option' );
        option_1.className = 'bmlt_nouveau_advanced_map_popup_select_option_auto';
        option_1.value = g_Nouveau_default_geo_width;
        option_1.appendChild ( document.createTextNode(g_Nouveau_advanced_map_radius_value_auto) );
        this.m_advanced_map_distance_popup.appendChild ( option_1 );
        
        option = document.createElement ( 'option' );
        option.disabled = true;
        option.className = 'bmlt_nouveau_advanced_map_popup_select_option_null';
        this.m_advanced_map_distance_popup.appendChild ( option );
        
        var selected = false;
        var option = null;
        
        for ( var c = 0; c < g_Nouveau_advanced_map_radius_value_array.length; c++ )
            {
            var distance = parseFloat(g_Nouveau_advanced_map_radius_value_array[c]);
            option = document.createElement ( 'option' );
            option.value = distance;
            option.className = 'bmlt_nouveau_advanced_map_popup_select_option_' + distance;
            this.m_advanced_map_distance_popup.appendChild ( option );
            var text = distance + ' ' + ((this.m_distance_units == 'mi') ? g_Nouveau_advanced_map_radius_value_2_mi : g_Nouveau_advanced_map_radius_value_2_km);
            if ( this.m_search_radius == distance )
                {
                option.selected = true;
                selected = true;
                };
            option.appendChild ( document.createTextNode(text) );
            };
        
        if ( !selected )
            {
            option_1.selected = true;
            };
        
        var id = this.m_uid;
        this.m_advanced_map_distance_popup.onchange = function(in_event) { NouveauMapSearch.prototype.sRadiusChanged( id ); };

        this.m_advanced_map_distance_popup_label_2 = document.createElement ( 'label' );
        this.m_advanced_map_distance_popup_label_2.className = 'bmlt_nouveau_advanced_map_popup_label_right';
        this.m_advanced_map_distance_popup_label_2.appendChild ( document.createTextNode(g_Nouveau_advanced_map_radius_label_2) );
        
        container_div.appendChild ( this.m_advanced_map_distance_popup_label_1 );
        container_div.appendChild ( this.m_advanced_map_distance_popup );
        container_div.appendChild ( this.m_advanced_map_distance_popup_label_2 );
        this.m_advanced_map_options_div.appendChild ( container_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the Advanced Weekdays section.                                         *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Weekdays = function ()
        {
        this.m_advanced_weekdays_div = document.createElement ( 'div' );
        this.m_advanced_weekdays_div.className = 'bmlt_nouveau_advanced_weekdays_div';
        
        this.buildDOMTree_Advanced_Weekdays_Header();
        this.buildDOMTree_Advanced_Weekdays_Content();
        
        this.m_advanced_section_div.appendChild ( this.m_advanced_weekdays_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the disclosure link for the Advanced Weekdays section.                 *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Weekdays_Header = function ()
        {
        this.m_advanced_weekdays_disclosure_a = null;
        
        this.m_advanced_weekdays_header_div = document.createElement ( 'div' );
        this.m_advanced_weekdays_header_div.className = 'bmlt_nouveau_advanced_weekdays_header_div';
        
        this.m_advanced_weekdays_disclosure_a = document.createElement ( 'a' );
        this.m_advanced_weekdays_disclosure_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.toggleWeekdaysDisclosure()' );
        this.m_advanced_weekdays_disclosure_a.appendChild ( document.createTextNode(g_Nouveau_advanced_weekdays_disclosure_text) );
        this.m_advanced_weekdays_header_div.appendChild ( this.m_advanced_weekdays_disclosure_a );
        
        this.m_advanced_weekdays_div.appendChild ( this.m_advanced_weekdays_header_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the content for the Advanced Weekdays section.                         *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Weekdays_Content = function ()
        {
        this.m_advanced_weekdays_content_div = document.createElement ( 'div' );
        this.m_advanced_weekdays_content_div.className = 'bmlt_nouveau_advanced_weekdays_content_div';
        
        var inner = document.createElement ( 'div' );
        inner.className = 'bmlt_nouveau_advanced_weekdays_content_inner_div';
        
        this.m_advanced_weekdays_array = new Array;
        
        for ( var c = 0; c < 7; c++ )
            {
            var weekday_index = c + g_Nouveau_start_week;
            
            if ( weekday_index > 7 )
                {
                weekday_index -= 7;
                }
            
            var enclosure = document.createElement ( 'div' );
            enclosure.className = 'bmlt_nouveau_advanced_weekdays_content_one_weekday_enclosure_div';

            this.m_advanced_weekdays_array[weekday_index - 1] = document.createElement ( 'input' );
            this.m_advanced_weekdays_array[weekday_index - 1].type = 'checkbox';
            this.m_advanced_weekdays_array[weekday_index - 1].className = 'bmlt_nouveau_advanced_weekdays_checkbox bmlt_nouveau_advanced_weekdays_checkbox_' + weekday_index;
            this.m_advanced_weekdays_array[weekday_index - 1].id = this.m_uid + '_weekdays_checkbox_' + weekday_index;
            enclosure.appendChild ( this.m_advanced_weekdays_array[weekday_index - 1] );

            var weekday_text = g_Nouveau_weekday_long_array[c];
            var label = document.createElement ( 'label' );
            label.className = 'bmlt_nouveau_advanced_weekdays_label bmlt_nouveau_advanced_weekdays_label_' + weekday_index;
            label.setAttribute ( 'for', this.m_uid + '_weekdays_checkbox_' + weekday_index );
            label.appendChild ( document.createTextNode ( g_Nouveau_weekday_long_array[weekday_index - 1] ) );
            enclosure.appendChild ( label );
            
            inner.appendChild ( enclosure );
            };
        
        var closure = document.createElement ( 'div' );
        closure.className = 'bmlt_nouveau_clear_both';
        inner.appendChild ( closure );
        
        this.m_advanced_weekdays_content_div.appendChild ( inner );
        
        this.m_advanced_weekdays_div.appendChild ( this.m_advanced_weekdays_content_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setAdvancedWeekdaysDisclosure = function()
        {
        if ( this.m_advanced_weekdays_shown )
            {
            this.m_advanced_weekdays_disclosure_a.className = 'bmlt_nouveau_advanced_weekdays_disclosure_open_a';
            this.m_advanced_weekdays_content_div.className = 'bmlt_nouveau_advanced_weekdays_content_div';
            if ( this.m_location_services_panel_date_button_div )
                {
                if ( (this.m_current_view == 'advanced_map') || (this.m_current_view == 'advanced_text') )
                    {
                    this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_hidden_div';
                    }
                else
                    {
                    this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_div';
                    };
                };
            }
        else
            {
            this.m_advanced_weekdays_disclosure_a.className = 'bmlt_nouveau_advanced_weekdays_disclosure_a';
            this.m_advanced_weekdays_content_div.className = 'bmlt_nouveau_advanced_weekdays_content_div bmlt_nouveau_advanced_weekdays_content_div_hidden';
            if ( this.m_location_services_panel_date_button_div )
                {
                this.m_location_services_panel_date_button_div.className = 'bmlt_nouveau_location_services_panel_date_button_div';
                };
            };
        };
    
    /************************************************************************************//**
    *	\brief Build the Formats section.                                                   *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Formats = function ()
        {
        this.m_advanced_formats_div = document.createElement ( 'div' );
        this.m_advanced_formats_div.className = 'bmlt_nouveau_advanced_formats_div';
        
        this.buildDOMTree_Advanced_Formats_Header();
        
        this.m_advanced_section_div.appendChild ( this.m_advanced_formats_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the disclosure link for the Formats section.                           *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Formats_Header = function ()
        {
        this.m_advanced_formats_disclosure_a = null;
        
        this.m_advanced_formats_header_div = document.createElement ( 'div' );
        this.m_advanced_formats_header_div.className = 'bmlt_nouveau_advanced_formats_header_div';
        
        this.m_advanced_formats_disclosure_a = document.createElement ( 'a' );
        this.m_advanced_formats_disclosure_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.toggleFormatsDisclosure()' );
        this.m_advanced_formats_disclosure_a.appendChild ( document.createTextNode(g_Nouveau_advanced_formats_disclosure_text) );
        this.m_advanced_formats_header_div.appendChild ( this.m_advanced_formats_disclosure_a );
        
        this.m_advanced_formats_div.appendChild ( this.m_advanced_formats_header_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the contents for the Formats section.                                  *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Formats_Content = function ()
        {
        this.m_advanced_formats_content_div = document.createElement ( 'div' );
        this.m_advanced_formats_content_div.className = 'bmlt_nouveau_advanced_formats_content_div';
        
        var inner = document.createElement ( 'div' );
        inner.className = 'bmlt_nouveau_advanced_formats_content_inner_div';
        
        this.m_advanced_format_checkboxes_array = new Array;
        
        for ( var c = 0; this.m_format_descriptions && (c < this.m_format_descriptions.length); c++ )
            {
            var format_id = this.m_format_descriptions[c].id;
            var format_code = this.m_format_descriptions[c].key_string;
            var format_name = this.m_format_descriptions[c].name_string;
            var format_description = this.m_format_descriptions[c].description_string;
            
            var enclosure = document.createElement ( 'div' );
            enclosure.className = 'bmlt_nouveau_advanced_format_content_one_format_enclosure_div';

            this.m_advanced_format_checkboxes_array[c] = document.createElement ( 'input' );
            this.m_advanced_format_checkboxes_array[c].type = 'checkbox';
            this.m_advanced_format_checkboxes_array[c].className = 'bmlt_nouveau_advanced_format_checkbox bmlt_nouveau_advanced_format_checkbox_' + format_code;
            this.m_advanced_format_checkboxes_array[c].id = this.m_uid + '_format_checkbox_' + format_code;
            this.m_advanced_format_checkboxes_array[c].setAttribute ( 'title', format_description );
            this.m_advanced_format_checkboxes_array[c].value = format_id;
            enclosure.appendChild ( this.m_advanced_format_checkboxes_array[c] );

            var label = document.createElement ( 'label' );
            label.className = 'bmlt_nouveau_advanced_format_label bmlt_nouveau_advanced_format_label_' + format_code;
            label.setAttribute ( 'for', this.m_uid + '_format_checkbox_' + format_code );
            label.setAttribute ( 'title', format_description );
            label.appendChild ( document.createTextNode ( format_code ) );
            enclosure.appendChild ( label );
            
            inner.appendChild ( enclosure );
            };
        
        var closure = document.createElement ( 'div' );
        closure.className = 'bmlt_nouveau_clear_both';
        inner.appendChild ( closure );
        
        this.m_advanced_formats_content_div.appendChild ( inner );
        
        this.m_advanced_formats_div.appendChild ( this.m_advanced_formats_content_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setAdvancedFormatsDisclosure = function()
        {
        if ( this.m_advanced_formats_shown )
            {
            this.m_advanced_formats_disclosure_a.className = 'bmlt_nouveau_advanced_formats_disclosure_open_a';
            if ( this.m_advanced_formats_content_div )
                {
                this.m_advanced_formats_content_div.className = 'bmlt_nouveau_advanced_formats_content_div';
                };
            }
        else
            {
            this.m_advanced_formats_disclosure_a.className = 'bmlt_nouveau_advanced_formats_disclosure_a';
            if ( this.m_advanced_formats_content_div )
                {
                this.m_advanced_formats_content_div.className = 'bmlt_nouveau_advanced_formats_content_div bmlt_nouveau_advanced_formats_content_div_hidden';
                };
            };
        };
    
    /************************************************************************************//**
    *	\brief Build the Advanced Service Bodies section.                                   *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Service_Bodies = function ()
        {
        this.m_advanced_service_bodies_div = document.createElement ( 'div' );
        this.m_advanced_service_bodies_div.className = 'bmlt_nouveau_advanced_service_bodies_div';
        
        this.buildDOMTree_Advanced_Service_Bodies_Header();
        
        this.m_advanced_section_div.appendChild ( this.m_advanced_service_bodies_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the disclosure link for the Advanced Service Bodies.                   *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Service_Bodies_Header = function ()
        {
        this.m_advanced_service_bodies_disclosure_a = null;
        
        this.m_advanced_service_bodies_header_div = document.createElement ( 'div' );
        this.m_advanced_service_bodies_header_div.className = 'bmlt_nouveau_advanced_service_bodies_header_div';
        
        this.m_advanced_service_bodies_disclosure_a = document.createElement ( 'a' );
        this.m_advanced_service_bodies_disclosure_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.toggleServiceBodiesDisclosure()' );
        this.m_advanced_service_bodies_disclosure_a.appendChild ( document.createTextNode(g_Nouveau_advanced_service_bodies_disclosure_text) );
        this.m_advanced_service_bodies_header_div.appendChild ( this.m_advanced_service_bodies_disclosure_a );
        
        this.m_advanced_service_bodies_div.appendChild ( this.m_advanced_service_bodies_header_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the content for the Advanced Service Bodies section.                   *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_Service_Bodies_Content = function ()
        {
        
        this.m_advanced_service_bodies_content_div = document.createElement ( 'div' );
        
        var inner = document.createElement ( 'div' );
        inner.className = 'bmlt_nouveau_advanced_formats_content_inner_div';
        
        this.m_advanced_service_bodies_checkboxes_array = new Array;
        
        this.populate_Advanced_Service_Bodies_Content ( 0, inner );
        
        var closure = document.createElement ( 'div' );
        closure.className = 'bmlt_nouveau_clear_both';
        inner.appendChild ( closure );
        
        this.m_advanced_service_bodies_content_div.appendChild ( inner );
        
        this.m_advanced_service_bodies_div.appendChild ( this.m_advanced_service_bodies_content_div );
        };
    
    /************************************************************************************//**
    *	\brief Build the content for the Advanced Service Bodies section.                   *
    ****************************************************************************************/
    this.populate_Advanced_Service_Bodies_Content = function (  in_owner_id,
                                                                in_container
                                                            )
        {
        var main_dl = null;
        
        for ( var c = 0; c < this.m_service_bodies.length; c++ )
            {
            if ( in_owner_id == this.m_service_bodies[c].parent_id )
                {
                if ( !main_dl )
                    {
                    main_dl = document.createElement ( 'dl' );
                    main_dl.className = 'bmlt_nouveau_advanced_service_bodies_dl';
                    };
        
                var index = this.m_advanced_service_bodies_checkboxes_array.length;
                var name = this.m_service_bodies[c].name;
                var description = this.m_service_bodies[c].description;
                var id = this.m_service_bodies[c].id;
                
                var checkbox_dt = document.createElement ( 'dt' );
                checkbox_dt.className = 'bmlt_nouveau_advanced_service_bodies_dt bmlt_nouveau_advanced_service_bodies_dt_' + id;
                
                var cb_wrapper = document.createElement ( 'div' );
                cb_wrapper.className = 'bmlt_nouveau_advanced_service_bodies_checkbox_wrapper_div';
                
                this.m_advanced_service_bodies_checkboxes_array[index] = document.createElement ( 'input' );
                this.m_advanced_service_bodies_checkboxes_array[index].type = 'checkbox';
                this.m_advanced_service_bodies_checkboxes_array[index].className = 'bmlt_nouveau_advanced_service_bodies_checkbox bmlt_nouveau_advanced_service_body_checkbox_' + id;
                this.m_advanced_service_bodies_checkboxes_array[index].id = this.m_uid + '_service_body_checkbox_' + id;
                this.m_advanced_service_bodies_checkboxes_array[index].setAttribute ( 'title', description );
                this.m_advanced_service_bodies_checkboxes_array[index].value = id;
                this.m_advanced_service_bodies_checkboxes_array[index].parent_service_body_id = in_owner_id;
                
                cb_wrapper.appendChild ( this.m_advanced_service_bodies_checkboxes_array[index] );
                
                checkbox_dt.appendChild ( cb_wrapper );
                
                var label = document.createElement ( 'label' );
                label.className = 'bmlt_nouveau_advanced_service_bodies_label bmlt_nouveau_advanced_service_bodies_label_' + id;
                label.setAttribute ( 'for', this.m_uid + '_service_body_checkbox_' + id );
                label.setAttribute ( 'title', description );
                label.appendChild ( document.createTextNode ( name ) );
                checkbox_dt.appendChild ( label );

                main_dl.appendChild ( checkbox_dt );
                
                var next_level_dd = document.createElement ( 'dd' );
                next_level_dd.className = 'bmlt_nouveau_advanced_service_bodies_container_dd bmlt_nouveau_advanced_service_bodies_container_dd_' + id;
                
                var uid = this.m_uid;
                
                if ( this.populate_Advanced_Service_Bodies_Content ( id, next_level_dd ) )
                    {
                    main_dl.appendChild ( next_level_dd );
                    label.className = 'bmlt_nouveau_advanced_service_bodies_container_label bmlt_nouveau_advanced_service_bodies_container_label_' + id;
                    this.m_advanced_service_bodies_checkboxes_array[index].onchange = function() { NouveauMapSearch.prototype.sServiceBodyContainerCheckHit ( this, uid ); };
                    }
                };
            };
            
        if ( main_dl )
            {
            in_container.appendChild ( main_dl );
            var closure = document.createElement ( 'div' );
            closure.className = 'bmlt_nouveau_clear_both';
            in_container.appendChild ( closure );
            };
            
        return (main_dl != null);
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setAdvancedServiceBodiesDisclosure = function()
        {
        if ( this.m_advanced_service_bodies_shown )
            {
            this.m_advanced_service_bodies_disclosure_a.className = 'bmlt_nouveau_advanced_service_bodies_disclosure_open_a';
            if ( this.m_advanced_service_bodies_content_div )
                {
                this.m_advanced_service_bodies_content_div.className = 'bmlt_nouveau_advanced_service_bodies_content_div';
                };
            }
        else
            {
            this.m_advanced_service_bodies_disclosure_a.className = 'bmlt_nouveau_advanced_service_bodies_disclosure_a';
            if ( this.m_advanced_service_bodies_content_div )
                {
                this.m_advanced_service_bodies_content_div.className = 'bmlt_nouveau_advanced_service_bodies_content_div bmlt_nouveau_advanced_service_bodies_content_div_hidden';
                };
            };
        };
    
    /************************************************************************************//**
    *	\brief Build the GO button for the Advanced section.                                *
    ****************************************************************************************/
    this.buildDOMTree_Advanced_GoButton = function ()
        {
        this.m_advanced_go_button_div = document.createElement ( 'div' );
        this.m_advanced_go_button_div.className = 'bmlt_nouveau_advanced_go_button_div';
        
        this.m_advanced_go_a = document.createElement ( 'a' );
        this.m_advanced_go_a.className = 'bmlt_nouveau_advanced_go_button_a';
        this.m_advanced_go_a.appendChild ( document.createTextNode(g_Nouveau_text_go_button_string) );
        this.m_advanced_go_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.goButtonHit()' );
        
        this.m_advanced_go_button_div.appendChild ( this.m_advanced_go_a );
        this.m_advanced_section_div.appendChild ( this.m_advanced_go_button_div );
        };
    
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.clearSearchResults = function ()
        {
        this.m_long_lat_northwest = null;
        this.m_long_lat_southeast = null;
        this.m_search_results = null;
        this.m_selected_search_results = 0;
        this.m_search_results_shown = false;
        this.m_map_search_results_map = null;
        this.m_mapResultsDisplayed = false;
        this.m_listResultsDisplayed = false;

        if ( this.m_search_results_div )
            {
            this.m_display_div.removeChild ( this.m_search_results_div );
            this.m_search_results_div.innerHTML = "";
            };
        
        this.m_map_search_results_disclosure_div = null;
        this.m_map_search_results_disclosure_a = null;
        this.m_map_search_results_container_div = null;
        this.m_map_search_results_map_div = null;
        this.m_list_search_results_disclosure_div = null;
        this.m_list_search_results_disclosure_a = null;
        this.m_list_search_results_container_div = null;
        this.m_list_search_results_table = null;
        this.m_list_search_results_table_head = null;
        this.m_list_search_results_table_body = null;
        this.m_search_results_div = null;
        };
    
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_Section = function ()
        {
        this.m_search_results_div = document.createElement ( 'div' );
        this.m_search_results_div.className = 'bmlt_nouveau_search_results_div';

        this.m_map_search_results_display_result_text_div = document.createElement ( 'div' );
        this.m_map_search_results_display_result_text_div.className = 'bmlt_nouveau_search_results_display_text_div';
        
        this.m_search_results_div.appendChild ( this.m_map_search_results_display_result_text_div );
        
        this.m_map_search_results_display_result_print_text_div = document.createElement ( 'div' );
        this.m_map_search_results_display_result_print_text_div.className = 'bmlt_nouveau_search_results_print_only_display_text_div';
        
        this.m_search_results_div.appendChild ( this.m_map_search_results_display_result_print_text_div );
        
        if ( this.m_search_results && this.m_search_results.length )
            {
            this.setMeetingResultCountText();
            this.buildDOMTree_SearchResults_Map();
            this.buildDOMTree_SearchResults_List();
            };
        
        this.m_display_div.appendChild ( this.m_search_results_div );
        };
    
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.setMeetingResultCountText = function ()
        {
        if ( this.m_map_search_results_display_result_text_div && this.m_map_search_results_display_result_print_text_div )
            {
            this.m_map_search_results_display_result_text_div.innerHTML = '';
            this.m_map_search_results_display_result_print_text_div.innerHTML = '';
            
            if ( this.m_search_results.length )
                {
                this.m_map_search_results_display_result_print_text_div.innerHTML = sprintf ( g_Nouveau_meeting_results_count_sprintf_format, this.m_search_results.length );
                
                if ( this.m_selected_search_results == 1 )
                    {
                    this.m_map_search_results_display_result_text_div.innerHTML = sprintf ( g_Nouveau_meeting_results_single_selection_count_sprintf_format, this.m_search_results.length );
                    }
                else if ( this.m_selected_search_results )
                    {
                    this.m_map_search_results_display_result_text_div.innerHTML = sprintf ( g_Nouveau_meeting_results_selection_count_sprintf_format, this.m_selected_search_results, this.m_search_results.length );
                    }
                else
                    {
                    this.m_map_search_results_display_result_text_div.innerHTML = sprintf ( g_Nouveau_meeting_results_count_sprintf_format, this.m_search_results.length );
                    };
                };
            };
        };
        
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_Map = function ()
        {
        this.m_map_search_results_disclosure_div = document.createElement ( 'div' );
        this.m_map_search_results_disclosure_div.className = 'bmlt_nouveau_search_results_map_disclosure_div';
        this.m_map_search_results_disclosure_div.id = 'bmlt_nouveau_search_results_map_disclosure_div_' + this.m_uid;
        
        this.m_map_search_results_disclosure_a = document.createElement ( 'a' );
        this.m_map_search_results_disclosure_a.appendChild ( document.createTextNode(g_Nouveau_display_map_results_text) );
        this.m_map_search_results_disclosure_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.displayMapResultsDiscolsureHit()' );
        
        this.m_map_search_results_disclosure_div.appendChild ( this.m_map_search_results_disclosure_a );
        
        this.m_search_results_div.appendChild ( this.m_map_search_results_disclosure_div );

        this.m_map_search_results_container_div = document.createElement ( 'div' );
        this.m_map_search_results_container_div.className = 'bmlt_nouveau_search_results_map_container_div';
        
        this.m_map_search_results_inner_container_div = document.createElement ( 'div' );
        this.m_map_search_results_inner_container_div.className = 'bmlt_nouveau_search_results_map_inner_container_div';
        
        this.m_map_search_results_map_div = document.createElement ( 'div' );
        this.m_map_search_results_map_div.className = 'bmlt_nouveau_search_results_map_div';
        
        this.m_map_search_results_inner_container_div.appendChild ( this.m_map_search_results_map_div );
        this.m_map_search_results_container_div.appendChild ( this.m_map_search_results_inner_container_div );
        
        this.setMapResultsDisclosure();
        
        this.m_search_results_div.appendChild ( this.m_map_search_results_container_div );
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setMapResultsDisclosure = function()
        {
        if ( this.m_mapResultsDisplayed )
            {
            this.m_map_search_results_disclosure_a.className = 'bmlt_nouveau_search_results_map_disclosure_a bmlt_nouveau_search_results_map_disclosure_open_a';
            this.m_map_search_results_container_div.className = 'bmlt_nouveau_search_results_map_container_div';
            }
        else
            {
            this.m_map_search_results_disclosure_a.className = 'bmlt_nouveau_search_results_map_disclosure_a';
            this.m_map_search_results_container_div.className = 'bmlt_nouveau_search_results_map_container_div bmlt_nouveau_search_results_map_container_div_hidden';
            };
        };
        
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_List = function ()
        {
        this.m_list_search_results_disclosure_div = document.createElement ( 'div' );
        this.m_list_search_results_disclosure_div.className = 'bmlt_nouveau_search_results_list_disclosure_div';
        
        this.m_list_search_results_disclosure_a = document.createElement ( 'a' );
        this.m_list_search_results_disclosure_a.appendChild ( document.createTextNode(g_Nouveau_display_list_results_text) );
        this.m_list_search_results_disclosure_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.displayListResultsDiscolsureHit()' );
        
        this.m_list_search_results_disclosure_div.appendChild ( this.m_list_search_results_disclosure_a );
        
        this.m_search_results_div.appendChild ( this.m_list_search_results_disclosure_div );

        this.m_list_search_results_container_div = document.createElement ( 'div' );
        this.m_list_search_results_container_div.className = 'bmlt_nouveau_search_results_list_container_div';
        this.buildDOMTree_SearchResults_List_Table();
        
        this.setListResultsDisclosure();
        
        this.m_search_results_div.appendChild ( this.m_list_search_results_container_div );
        };
        
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_List_Table = function ()
        {
        this.m_list_search_results_table = document.createElement ( 'table' );
        this.m_list_search_results_table.className = 'bmlt_nouveau_search_results_list_table';
        this.m_list_search_results_table.setAttribute ( 'cellpadding', 0 );
        this.m_list_search_results_table.setAttribute ( 'cellspacing', 0 );
        this.m_list_search_results_table.setAttribute ( 'border', 0 );
        
        this.m_list_search_results_table_head = document.createElement ( 'thead' );
        this.m_list_search_results_table_head.className = 'bmlt_nouveau_search_results_list_thead';
        this.buildDOMTree_SearchResults_List_Table_Header();
        
        this.m_list_search_results_table.appendChild ( this.m_list_search_results_table_head );
        
        this.m_list_search_results_table_body = document.createElement ( 'tbody' );
        this.m_list_search_results_table_body.className = 'bmlt_nouveau_search_results_list_tbody';
        this.buildDOMTree_SearchResults_List_Table_Contents();
        
        this.m_list_search_results_table.appendChild ( this.m_list_search_results_table_body );
        
        this.m_list_search_results_container_div.appendChild ( this.m_list_search_results_table );
        };
        
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_List_Table_Header = function ()
        {
        // The header has one row.
        var tr_element = document.createElement ( 'tr' );
        tr_element.className = 'bmlt_nouveau_search_results_list_header_tr';
        
        for ( var i = 0; i < g_Nouveau_array_header_text.length; i++ )
            {
            var td_element = document.createElement ( 'td' );
            td_element.className = 'bmlt_nouveau_search_results_list_header_td';
        
            switch ( i )
                {
                case    0:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_nation';
                break;
            
                case    1:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_state';
                break;
            
                case    2:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_county';
                break;
            
                case    3:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_town';
                break;
            
                case    4:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_name';
                break;
            
                case    5:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_weekday';
                break;
            
                case    6:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_start';
                break;
            
                case    7:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_location';
                break;
            
                case    8:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_format';
                break;
            
                case    9:
                    td_element.className += ' bmlt_nouveau_search_results_list_header_td_more';
                break;

                default:
                    td_element.className += ' bmlt_nouveau_hidden';
                break;
                };
        
            td_element.appendChild ( document.createTextNode(g_Nouveau_array_header_text[i]) );
            tr_element.appendChild ( td_element );
            };
        
        this.m_list_search_results_table_head.appendChild ( tr_element );
        };
        
    /************************************************************************************//**
    *	\brief This populates the results list (a table).                                   *
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_List_Table_Contents = function ()
        {
        // Each meeting gets a row.
        var uid = this.m_uid;
        for ( var i = 0; i < this.m_search_results.length; i++ )
            {
            var tr_element = document.createElement ( 'tr' );
            tr_element.className = 'bmlt_nouveau_search_results_list_body_tr ' + 'bmlt_nouveau_search_results_list_body_tr_' + (((i % 2) == 0) ? 'even' : 'odd');
            
            // These are used to allow a "highlight" of meetings represented by map markers.
            tr_element.classNameNormal = tr_element.className;
            tr_element.classNameHighlight = tr_element.className + 'bmlt_nouveau_search_results_list_body_highlight_tr';
            tr_element.id = this.m_uid + '_meeting_list_item_' + this.m_search_results[i]['id_bigint'] + '_tr';
            tr_element.meeting_id = this.m_search_results[i].id_bigint;

            tr_element.onmouseup = function () { NouveauMapSearch.prototype.sRowClick (this.meeting_id, uid) };
        
            for ( var c = 0; c < g_Nouveau_array_header_text.length; c++ )
                {
                this.buildDOMTree_SearchResults_List_Table_Contents_Node_TD(this.m_search_results[i], c, tr_element);
                };
            
            var td_element = document.createElement ( 'td' );
            td_element.className = 'bmlt_nouveau_search_results_list_body_td bmlt_nouveau_search_results_list_body_td_more';
            
            var more_details_a = document.createElement ( 'a' );      // Create the basic switch anchor element.
            more_details_a.className = 'bmlt_nouveau_search_results_list_body_td_more_a';
            var id = this.m_uid;
            more_details_a.setAttribute ( 'href', "javascript:NouveauMapSearch.prototype.sDetailsButtonHit('" + id + "','" + tr_element.meeting_id + "')" );
            more_details_a.setAttribute ( 'title', g_Nouveau_meeting_details_link_title );
            
            var more_details_a_span = document.createElement ( 'span' );      // Create the basic switch anchor element.
            more_details_a_span.appendChild ( document.createTextNode('MORE') );
            more_details_a_span.className = 'bmlt_nouveau_search_results_list_body_td_more_a_span';

            more_details_a.appendChild ( more_details_a_span );
            td_element.appendChild ( more_details_a );            
            var breaker_div = document.createElement ( 'div' );
            breaker_div.className = "bmlt_nouveau_clear_both";
            td_element.appendChild ( breaker_div );            
            tr_element.appendChild ( td_element );
        
            this.m_list_search_results_table_body.appendChild ( tr_element );
            };
        };
    
    /************************************************************************************//**
    *	\brief This creates a single node for one meeting in the table.                     *
    ****************************************************************************************/
    this.buildDOMTree_SearchResults_List_Table_Contents_Node_TD = function (    in_meeting_object,  ///< The meeting data line object.
                                                                                index,              ///< Which column it will be using.
                                                                                tr_element          ///< The tr element that will receive this line.
                                                                            )
        {
        var td_element = document.createElement ( 'td' );
        td_element.className = 'bmlt_nouveau_search_results_list_body_td';
        
        switch ( index )
            {
            case    0:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_nation';
                td_element.appendChild ( this.buildDOMTree_ConstructNationName ( in_meeting_object ) );
            break;
            
            case    1:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_state';
                td_element.appendChild ( this.buildDOMTree_ConstructStateName ( in_meeting_object ) );
            break;
            
            case    2:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_county';
                td_element.appendChild ( this.buildDOMTree_ConstructCountyName ( in_meeting_object ) );
            break;
            
            case    3:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_town';
                td_element.appendChild ( this.buildDOMTree_ConstructTownName ( in_meeting_object ) );
            break;
            
            case    4:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_name';
                td_element.appendChild ( this.buildDOMTree_ConstructMeetingName ( in_meeting_object ) );
            break;
            
            case    5:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_weekday';
                td_element.appendChild ( this.buildDOMTree_ConstructWeekday ( in_meeting_object ) );
            break;
            
            case    6:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_start';
                td_element.appendChild ( this.buildDOMTree_ConstructStartTime( in_meeting_object ) );
            break;
            
            case    7:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_location';
                td_element.appendChild ( this.buildDOMTree_ConstructLocation( in_meeting_object ) );
            break;
            
            case    8:
                td_element.className += ' bmlt_nouveau_search_results_list_body_td_format';
                td_element.appendChild ( this.buildDOMTree_ConstructFormat( in_meeting_object ) );
            break;
            
            default:
                td_element.className += ' bmlt_nouveau_hidden';
            break;
            };
        
        tr_element.appendChild ( td_element );
        };
        
    /************************************************************************************//**
    *	\brief This gets the nation name as a div.                                          *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructNationName = function ( in_meeting_object  ///< The meeting data line object.
                                                     )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_nation_name_div';

        if ( in_meeting_object['location_nation'] )
            {
            container_element.appendChild ( document.createTextNode ( in_meeting_object['location_nation'] ) );
            };
        
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the state name as a div.                                           *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructStateName = function ( in_meeting_object  ///< The meeting data line object.
                                                    )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_state_name_div';
        
        if ( in_meeting_object['location_province'] )
            {
            container_element.appendChild ( document.createTextNode ( in_meeting_object['location_province'] ) );
            };
        
        return container_element;
        };
    
    /************************************************************************************//**
    *	\brief This gets the county name as a div.                                          *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructCountyName = function ( in_meeting_object  ///< The meeting data line object.
                                                     )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_county_name_div';
        
        if ( in_meeting_object['location_city_subsection'] )
            {
            container_element.className += ' bmlt_nouveau_search_results_list_town_name_county_has_borough_div';
            };

        container_element.appendChild ( document.createTextNode ( in_meeting_object['location_sub_province'] ) );
        
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the town name as a div. It will do a "smart" build, where it uses  *
    *          special dynamic CSS classes to allow the implementor to control things like  *
    *          a borough being displayed instead of a town, etc.                            *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructTownName = function ( in_meeting_object  ///< The meeting data line object.
                                                   )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_town_name_div';
        
        if ( in_meeting_object['location_sub_province'] )
            {
            container_element.className += ' bmlt_nouveau_search_results_list_town_name_has_county_div';
            };
        
        var span_element = null;

        if ( in_meeting_object['location_municipality'] )
            {
            span_element = document.createElement ( 'span' );
            span_element.className = 'bmlt_nouveau_search_results_list_town_name_town_span';
            
            if ( in_meeting_object['location_city_subsection'] )
                {
                span_element.className += ' bmlt_nouveau_search_results_list_town_name_town_has_borough_span';
                };
                
            span_element.appendChild ( document.createTextNode ( in_meeting_object['location_municipality'] ) );
            container_element.appendChild ( span_element );
            };
        
        if ( in_meeting_object['location_city_subsection'] )
            {
            span_element = document.createElement ( 'span' );
            span_element.className = 'bmlt_nouveau_search_results_list_town_name_borough_span';
            
            if ( in_meeting_object['location_municipality'] )
                {
                span_element.className += ' bmlt_nouveau_search_results_list_town_name_borough_has_town_span';
                };

            span_element.appendChild ( document.createTextNode ( in_meeting_object['location_city_subsection'] ) );
            container_element.appendChild ( span_element );
            };
        
        if ( in_meeting_object['location_neighborhood'] )
            {
            span_element = document.createElement ( 'span' );
            span_element.className = 'bmlt_nouveau_search_results_list_town_name_neighborhood_span';
            span_element.appendChild ( document.createTextNode ( in_meeting_object['location_neighborhood'] ) );
            container_element.appendChild ( span_element );
            };

        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the meeting name as a div.                                         *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructMeetingName = function ( in_meeting_object  ///< The meeting data line object.
                                                        )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_meeting_name_div';
        
        if ( in_meeting_object['meeting_name'] )
            {
            container_element.appendChild ( document.createTextNode ( in_meeting_object['meeting_name'] ) );
            };
                    
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the weekday name as a div.                                         *
    *   \returns a new DOM div, with the name in it.                                        *
    ****************************************************************************************/
    this.buildDOMTree_ConstructWeekday = function ( in_meeting_object  ///< The meeting data line object.
                                                    )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_weekday_div';
        container_element.appendChild ( document.createTextNode(g_Nouveau_weekday_long_array[in_meeting_object['weekday_tinyint'] - 1] ) );
                    
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the start time name as a div.                                      *
    *   \returns a new DOM div, with the time in it. It will use "noon" and "midnight."     *
    ****************************************************************************************/
    this.buildDOMTree_ConstructStartTime = function ( in_meeting_object  ///< The meeting data line object.
                                                    )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_start_time_div';
        
        var text_element = document.createElement ( 'div' );
        text_element.className = 'bmlt_nouveau_search_results_list_start_time_text_div';

        var time = (in_meeting_object['start_time'].toString()).split(':');

        time[0] = parseInt ( time[0], 10 );
        time[1] = parseInt ( time[1], 10 );
        
        var st = null;
        
        if ( (time[0] == 12) && (time[1] == 0) )
            {
            st = g_Nouveau_noon;
            }
        else if ( ((time[0] == 23) && (time[1] >= 55)) || (((time[0] == 0) && (time[1] == 0))) )
            {
            st = g_Nouveau_midnight;
            }
        else
            {
            var hours = (time[0] > 12) ? time[0] - 12 : time[0];
            var minutes = time[1];
            var a = ((time[0] > 12) || ((time[0] == 12) && (time[1] > 0))) ? g_Nouveau_pm : g_Nouveau_am;
            
            if ( g_Nouveau_military_time )
                {
                st = sprintf ( "%d:%02d", parseInt ( time[0], 10 ), parseInt ( time[1], 10 ) );
                }
            else
                {
                st = sprintf ( g_Nouveau_time_sprintf_format, hours, time[1], a );
                };
            };
        
        text_element.appendChild ( document.createTextNode( st ) );
        container_element.appendChild ( document.createTextNode( st ) );
        
        if ( in_meeting_object['duration_time'] )
            {
            var duration_element = document.createElement ( 'div' );
            duration_element.className = 'bmlt_nouvea_duration_container_div';
            time = (in_meeting_object['duration_time'].toString()).split(':');

            time[0] = parseInt ( time[0], 10 );
            time[1] = parseInt ( time[1], 10 );
            
            var title_string = '';
            
            if ( (time[0] > 1) && (time[1] > 1) )
                {
                title_string = sprintf ( g_Nouveau_location_sprintf_format_duration_title, time[0], time[1] );
                }
            else if ( (time[0] > 1) && (time[1] == 0) )
                {
                title_string = sprintf ( g_Nouveau_location_sprintf_format_duration_hours_only_title, time[0] );
                }
            else if ( (time[0] == 1) && (time[1] == 0) )
                {
                title_string = sprintf ( g_Nouveau_location_sprintf_format_duration_hour_only_title, time[0] );
                }
            else if ( (time[0] == 1) && (time[1] > 0) )
                {
                title_string = sprintf ( g_Nouveau_location_sprintf_format_duration_hour_only_and_minutes_title, time[1] );
                };

            var container_img_div = null;
            var inner_img = null;
            
            var t = time[0];
            
            for ( var c = 0; c  < t; c++ )
                {
                container_img_div = document.createElement ( 'div' );
                container_img_div.className = 'bmlt_nouveau_clock_div bmlt_nouvea_duration_60_div';
                container_img_div.setAttribute ( 'title', title_string );
                inner_img = document.createElement ( 'img' );
                inner_img.className = 'bmlt_nouveau_clock_img bmlt_nouvea_duration_60_img';
                inner_img.src = this.m_theme_dir + '/images/Clock60.png';
                container_img_div.appendChild ( inner_img );
                duration_element.appendChild ( container_img_div );
                };
            
            if ( time[1] > 0 )
                {
                container_img_div = document.createElement ( 'div' );
                container_img_div.className = 'bmlt_nouveau_clock_div';
                container_img_div.setAttribute ( 'title', title_string );
                
                inner_img = document.createElement ( 'img' );
                inner_img.className = 'bmlt_nouveau_clock_img';

                if ( (time[1] > 0) && (time[1] < 6) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_05_img';
                    container_img_div.className += ' bmlt_nouvea_duration_05_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock05.png';
                    }
                else if ( (time[1] > 5) && (time[1] < 11) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_10_img';
                    container_img_div.className += ' bmlt_nouvea_duration_10_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock10.png';
                    }
                else if ( (time[1] > 10) && (time[1] < 16) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_15_img';
                    container_img_div.className += ' bmlt_nouvea_duration_15_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock15.png';
                    }
                else if ( (time[1] > 15) && (time[1] < 21) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_20_img';
                    container_img_div.className += ' bmlt_nouvea_duration_20_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock20.png';
                    }
                else if ( (time[1] > 20) && (time[1] < 26) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_25_img';
                    container_img_div.className += ' bmlt_nouvea_duration_25_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock25.png';
                    }
                else if ( (time[1] > 25) && (time[1] < 31) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_30_img';
                    container_img_div.className += ' bmlt_nouvea_duration_30_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock30.png';
                    }
                else if ( (time[1] > 30) && (time[1] < 36) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_35_img';
                    container_img_div.className += ' bmlt_nouvea_duration_35_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock35.png';
                    }
                else if ( (time[1] > 35) && (time[1] < 41) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_40_img';
                    container_img_div.className += ' bmlt_nouvea_duration_40_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock40.png';
                    }
                else if ( (time[1] > 40) && (time[1] < 46) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_45_img';
                    container_img_div.className += ' bmlt_nouvea_duration_45_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock45.png';
                    }
                else if ( (time[1] > 45) && (time[1] < 51) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_50_img';
                    container_img_div.className += ' bmlt_nouvea_duration_50_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock50.png';
                    }
                else if ( (time[1] > 50) && (time[1] < 56) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_55_img';
                    container_img_div.className += ' bmlt_nouvea_duration_55_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock55.png';
                    }
                else if ( (time[1] > 55) && (time[1] <= 60) )
                    {
                    inner_img.className += ' bmlt_nouvea_duration_60_img';
                    container_img_div.className += ' bmlt_nouvea_duration_60_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock60.png';
                    }
                else
                    {
                    inner_img.className += ' bmlt_nouvea_duration_00_img';
                    container_img_div.className += ' bmlt_nouvea_duration_00_div';
                    inner_img.src = this.m_theme_dir + '/images/Clock00.png';
                    };
                    
                inner_img.setAttribute ( 'alt', '' );
           
                container_img_div.appendChild ( inner_img );
                duration_element.appendChild ( container_img_div );
                };
            
            container_element.appendChild ( duration_element );
            };
        
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the location address as a div. It uses a formatted output to make  *
    *          a string that provides a readable, useful location.                          *
    *   \returns a new DOM div, with the location in it.                                    *
    ****************************************************************************************/
    this.buildDOMTree_ConstructLocation = function ( in_meeting_object  ///< The meeting data line object.
                                                    )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_location_div';
        
        var loc_text = '';
        
        if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_loc_street_info, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'] );
            }
        else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_loc_street, in_meeting_object['location_text'], in_meeting_object['location_street'] );
            }
        else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_street_info, in_meeting_object['location_street'], in_meeting_object['location_info'] );
            }
        else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_loc_info, in_meeting_object['location_text'], in_meeting_object['location_info'] );
            }
        else if ( in_meeting_object['location_street'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_street, in_meeting_object['location_street'] );
            }
        else if ( in_meeting_object['location_text'] )
            {
            loc_text = sprintf ( g_Nouveau_location_sprintf_format_loc, in_meeting_object['location_text'] );
            }
        else
            {
            loc_text = g_Nouveau_location_sprintf_format_wtf;
            };

        container_element.appendChild ( document.createTextNode( loc_text ) );
        return container_element;
        };
        
    /************************************************************************************//**
    *	\brief This gets the format codes as a div.                                         *
    *   \returns a new DOM div, with the format codes in it.                                *
    ****************************************************************************************/
    this.buildDOMTree_ConstructFormat = function ( in_meeting_object  ///< The meeting data line object.
                                                    )
        {
        var container_element = document.createElement ( 'div' );
        container_element.className = 'bmlt_nouveau_search_results_list_format_div';
        
        var loc_array = in_meeting_object.formats.split ( ',');
        
        if ( loc_array && loc_array.length )
            {
            for ( var c = 0; c < loc_array.length; c++ )
                {
                var loc_text = loc_array[c];
        
                var format_element = document.createElement ( 'span' );
                format_element.className = 'bmlt_nouveau_advanced_formats_element_span';
                format_element.appendChild ( document.createTextNode( loc_text ) );
                format_element.setAttribute ( 'title', this.getFormatDescription ( loc_text ) );
                container_element.appendChild ( format_element );
                };
            };
                
        return container_element;
        };
    
    /************************************************************************************//**
    *	\brief This returns the format description for the given format code.               *
    *   \returns A string, containing the description.                                      *
    ****************************************************************************************/
    this.getFormatDescription = function( in_code_string    ///< This is the code string, and will be used to look up the description
                                        )
        {
        var ret = '';
        
        for ( var c = 0; c < this.m_format_descriptions.length;  c++ )
            {
            if ( this.m_format_descriptions[c].key_string == in_code_string )
                {
                ret = this.m_format_descriptions[c].description_string;
                break;
                };
            };
        
        return ret;
        };
    
    /************************************************************************************//**
    *	\brief This returns the format name for the given format code.                      *
    *   \returns A string, containing the description.                                      *
    ****************************************************************************************/
    this.getFormatName = function( in_code_string    ///< This is the code string, and will be used to look up the description
                                        )
        {
        var ret = '';
        
        for ( var c = 0; c < this.m_format_descriptions.length;  c++ )
            {
            if ( this.m_format_descriptions[c].key_string == in_code_string )
                {
                ret = this.m_format_descriptions[c].name_string;
                break;
                };
            };
        
        return ret;
        };
    
    /************************************************************************************//**
    *	\brief This returns the Service Body name for the given Service body ID.            *
    *   \returns A string, containing the name.                                             *
    ****************************************************************************************/
    this.getServiceBodyName = function( in_id    ///< This is the ID, and will be used to look up the description
                                        )
        {
        var ret = '';
        for ( var c = 0; c < this.m_service_bodies.length;  c++ )
            {
            if ( this.m_service_bodies[c].id == in_id )
                {
                ret = this.m_service_bodies[c].name;
                break;
                };
            };
        
        return ret;
        };
    
    /************************************************************************************//**
    *	\brief This returns the Service Body URL for the given Service body ID.             *
    *   \returns A string, containing the name.                                             *
    ****************************************************************************************/
    this.getServiceBodyURL = function( in_id    ///< This is the ID, and will be used to look up the description
                                        )
        {
        var ret = '';
        for ( var c = 0; c < this.m_service_bodies.length;  c++ )
            {
            if ( this.m_service_bodies[c].id == in_id )
                {
                ret = this.m_service_bodies[c].url;
                break;
                };
            };
        
        return ret;
        };
    
    /************************************************************************************//**
    *	\brief This returns the Service Body description for the given Service body ID.     *
    *   \returns A string, containing the description.                                      *
    ****************************************************************************************/
    this.getServiceBodyDescription = function( in_id    ///< This is the ID, and will be used to look up the description
                                            )
        {
        var ret = '';
        
        for ( var c = 0; c < this.m_service_bodies.length;  c++ )
            {
            if ( this.m_service_bodies[c].id == in_id )
                {
                ret = this.m_service_bodies[c].description;
                break;
                };
            };
        
        return ret;
        };
    
    /************************************************************************************//**
    *	\brief This sets the state of the "MAP/TEXT" tab switch div. It actually changes    *
    *          the state of the anchors, so it is more than just a CSS class change.        *
    ****************************************************************************************/
    this.setListResultsDisclosure = function()
        {
        if ( this.m_listResultsDisplayed )
            {
            this.m_list_search_results_disclosure_a.className = 'bmlt_nouveau_search_results_list_disclosure_a bmlt_nouveau_search_results_list_disclosure_open_a';
            this.m_list_search_results_container_div.className = 'bmlt_nouveau_search_results_list_container_div';
            }
        else
            {
            this.m_list_search_results_disclosure_a.className = 'bmlt_nouveau_search_results_list_disclosure_a';
            this.m_list_search_results_container_div.className = 'bmlt_nouveau_search_results_list_container_div bmlt_nouveau_search_results_list_container_div_hidden';
            };
        };
        
    /************************************************************************************//**
    *	\brief This establishes the (usually invisible) throbber display.                   *
    ****************************************************************************************/
    this.buildDOMTree_CreateThrobberDiv = function ()
        {
        this.m_throbber_div = document.createElement ( 'div' );
        this.m_throbber_div.className = 'bmlt_nouveau_throbber_div bmlt_nouveau_throbber_div_hidden';
        this.m_throbber_div.id = this.m_uid + '_throbber_div';
        
        var inner_div = document.createElement ( 'div' );
        inner_div.className = 'bmlt_nouveau_throbber_mask_div';

        this.m_throbber_div.appendChild ( inner_div );
        
        var inner_div = document.createElement ( 'div' );
        inner_div.className = 'bmlt_nouveau_throbber_inner_container_div';

        var inner_img = document.createElement ( 'img' );
        inner_img.className = 'bmlt_nouveau_throbber_img';
        inner_img.src = this.m_theme_dir + '/images/Throbber.gif';
        inner_img.setAttribute ( 'alt', 'Busy Throbber' );
        
        inner_div.appendChild ( inner_img );
        
        this.m_throbber_div.appendChild ( inner_div );
        
        this.m_display_div.appendChild ( this.m_throbber_div );
        };
        
    /************************************************************************************//**
    *	\brief This establishes the (usually invisible) throbber display.                   *
    ****************************************************************************************/
    this.buildDOMTree_CreateDetailsDiv = function ()
        {
        this.m_details_div = document.createElement ( 'div' );
        this.m_details_div.className = 'bmlt_nouveau_details_div bmlt_nouveau_details_div_hidden';
        this.m_details_div.id = this.m_uid + '_details_div';
        
        var mask_div = document.createElement ( 'div' );
        mask_div.className = 'bmlt_nouveau_details_mask_div';
        var ev = null;
        eval ( 'ev = function () { g_instance_' + this.m_uid + '_js_handler.closeSingle(); };' );
        mask_div.onclick = ev;
        this.m_details_div.appendChild ( mask_div );
        this.m_details_div.my_mask_div = mask_div;

        this.m_details_inner_div = document.createElement ( 'div' );
        this.m_details_inner_div.className = 'bmlt_nouveau_details_inner_container_div';
        
        var closer_a = document.createElement ( 'a' );
        closer_a.className = 'bmlt_nouveau_details_closer_a';
        closer_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.closeSingle()' );

        this.m_details_div.appendChild ( closer_a );
        this.m_details_div.appendChild ( this.m_details_inner_div );
        this.m_display_div.appendChild ( this.m_details_div );
        };
    
    /************************************************************************************//**
    *	\brief  Look up an address in the text
    ****************************************************************************************/
    this.lookupLocation = function ()
        {
        if ( this.m_text_input.value && (this.m_text_input.value != this.m_text_input.defaultValue) )
            {
            this.m_geocoder = new google.maps.Geocoder;
            
            if ( this.m_geocoder )
                {
                var id = this.m_uid;
                
                // Save this, in case the lookup goes walkabout
                this.m_pre_search_lat = this.m_current_lat;
                this.m_pre_search_long = this.m_current_long;

                var	status = this.m_geocoder.geocode ( { 'address' : this.m_text_input.value }, function ( in_geocode_response ) { NouveauMapSearch.prototype.sGeoCallback ( in_geocode_response, id ); } );
                
                this.m_text_input.select();
                
                if ( google.maps.OK != status )
                    {
                    if ( google.maps.INVALID_REQUEST != status )
                        {
                        alert ( g_Nouveau_lookup_location_failed );
                        this.hideThrobber();
                        }
                    else
                        {
                        if ( google.maps.ZERO_RESULTS != status )
                            {
                            alert ( g_Nouveau_lookup_location_failed );
                            this.hideThrobber();
                            }
                        else
                            {
                            alert ( g_Nouveau_lookup_location_server_error );
                            this.hideThrobber();
                            };
                        };
                    };
                }
            else	// None of that stuff is defined if we couldn't create the geocoder.
                {
                alert ( g_Nouveau_lookup_location_server_error );
                this.hideThrobber();
                };
            }
        else
            {
			alert ( g_Nouveau_lookup_location_failed );
            this.hideThrobber();
            };
        };
	
    /****************************************************************************************//**
    *	\brief This catches the AJAX response, and fills in the response form.				    *
    ********************************************************************************************/
    this.lookupCompleteHandler = function ( in_geocode_response ///< The JSON object.
                                            )
        {
        this.m_current_long = in_geocode_response[0].geometry.location.lng();
        this.m_current_lat = in_geocode_response[0].geometry.location.lat();
        this.beginSearch();
        };
    
    /****************************************************************************************
    *#################################### MAP HANDLERS #####################################*
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief This starts a search immediately, for a basic map click.                     *
    ****************************************************************************************/
    this.basicMapClicked = function ()
        {
        this.beginSearch();
        };

    /************************************************************************************//**
    *	\brief This moves the marker, in response to a map click.                           *
    ****************************************************************************************/
    this.advancedMapClicked = function ()
        {
        this.displayMarkerInAdvancedMap();
        };

    /************************************************************************************//**
    *	\brief This displays the marker and overlay for the advanced map.                   *
    ****************************************************************************************/
    this.displayMarkerInAdvancedMap = function ()
        {
        if ( this.m_current_view == 'advanced_map' && this.m_main_map )
            {
            var position = new google.maps.LatLng ( this.m_current_lat, this.m_current_long );
            
            if ( !this.m_main_map.map_marker )
                {
		        this.m_main_map.map_marker = new google.maps.Marker (
                                                                    {
                                                                    'position':     position,
                                                                    'map':		    this.m_main_map,
                                                                    'shadow':		this.m_center_icon_shadow,
                                                                    'icon':			this.m_center_icon_image,
                                                                    'shape':		this.m_center_icon_shape,
                                                                    'clickable':	false,
                                                                    'cursor':		'pointer',
                                                                    'draggable':    true
                                                                    } );
                var id = this.m_uid;
                google.maps.event.addListener ( this.m_main_map.map_marker, 'dragend', function(in_event) { NouveauMapSearch.prototype.sAdvancedMapDragEnd( in_event, id ); } );
                }
            else
                {
                this.m_main_map.map_marker.setPosition ( position );
                this.m_main_map.panTo ( position );
                };
            
            if ( this.m_search_radius > 0 )
                {
                var circle_radius = this.m_search_radius * ( (this.m_distance_units == 'mi') ? 1694.4 : 1000 );
            
                // Options for circle overlay object
                if ( !this.m_main_map._circle_overlay )
                    {
                    var circle_options = {
                                        'center': this.m_main_map.map_marker.getPosition(),
                                        'fillColor': "#999",
                                        'radius':circle_radius,
                                        'fillOpacity': 0.25,
                                        'strokeOpacity': 0.0,
                                        'map': this.m_main_map,
                                        'clickable': false
                                        };

                    this.m_main_map._circle_overlay = new google.maps.Circle ( circle_options );
                    this.m_main_map._circle_overlay.bindTo ( 'center', this.m_main_map.map_marker, 'position' );
                    }
                else
                    {
                    this.m_main_map._circle_overlay.setRadius(circle_radius);
                    this.m_main_map._circle_overlay.setCenter(position);
                    };
                }
            else if ( this.m_main_map._circle_overlay )
                {
                this.m_main_map._circle_overlay.setMap(null);
                this.m_main_map._circle_overlay = null;
                };
            }
        else if ( this.m_main_map )
            {
            if ( this.m_main_map.map_marker )
                {
                this.m_main_map.map_marker.setMap(null);
                this.m_main_map.map_marker = null;
                };
                
            if ( this.m_main_map._circle_overlay )
                {
                this.m_main_map._circle_overlay.setMap(null);
                this.m_main_map._circle_overlay = null;
                };
            };
        };
    
    /************************************************************************************//**
    *	\brief Redraws the meeting result map markers.                                      *
    ****************************************************************************************/
    this.redrawResultMapMarkers = function()
        {
        if ( this.m_results_map_loaded )
            {
            this.clearMarkerHighlight();    // Get rid of selected meetings.
        
            // First, get rid of the old ones.
            if ( this.m_map_search_results_map.main_marker )
                {
                this.m_map_search_results_map.main_marker.setMap(null);
                this.m_map_search_results_map.main_marker = null;
                };

            // Next, get rid of all the meeting markers.
            for ( var c = 0; this.m_map_search_results_map.meeting_marker_array && (c < this.m_map_search_results_map.meeting_marker_array.length); c++ )
                {
                if ( this.m_map_search_results_map.meeting_marker_array[c] )
                    {
                    this.m_map_search_results_map.meeting_marker_array[c].setMap(null);
                    this.m_map_search_results_map.meeting_marker_array[c] = null;
                    };
                };
        
            this.m_map_search_results_map.meeting_marker_array = new Array;
        
            this.displayMainMarkerInResults();
 
            // Recalculate the new batch.
            this.m_map_search_results_map.meeting_marker_object_array = NouveauMapSearch.prototype.sMapOverlappingMarkers ( this.m_search_results, this.m_map_search_results_map );

            for ( var c = 0; this.m_map_search_results_map.meeting_marker_object_array && (c < this.m_map_search_results_map.meeting_marker_object_array.length); c++ )
                {
                this.displayMeetingMarkerInResults ( this.m_map_search_results_map.meeting_marker_object_array[c] );
                };
            };
        };

    /************************************************************************************//**
    *	\brief This displays the "Your Position" marker in the results map.                 *
    ****************************************************************************************/
    this.displayMainMarkerInResults = function ()
        {
		this.m_map_search_results_map.main_marker = new google.maps.Marker (
		                                                                    {
                                                                            'position':     new google.maps.LatLng ( this.m_current_lat, this.m_current_long ),
                                                                            'map':		    this.m_map_search_results_map,
                                                                            'shadow':		this.m_center_icon_shadow,
                                                                            'icon':			this.m_center_icon_image,
                                                                            'shape':		this.m_center_icon_shape,
                                                                            'clickable':	false,
                                                                            'cursor':		'pointer',
                                                                            'draggable':    true
                                                                            } );
        var id = this.m_uid;
        google.maps.event.addListener ( this.m_map_search_results_map.main_marker, 'dragend', function(in_event) { NouveauMapSearch.prototype.sResultsMapDragend( in_event, id ); } );
        };

    /************************************************************************************//**
    *	\brief This displays the "Your Position" marker in the results map.                 *
    ****************************************************************************************/
    this.displayMeetingMarkerInResults = function ( in_mtg_obj_array   ///< The array of meeting objects meeting to be marked.
                                                    )
        {
        var displayed_image = (in_mtg_obj_array.length == 1) ? this.m_icon_image_single : this.m_icon_image_multi;
        
		var main_point = new google.maps.LatLng ( in_mtg_obj_array[0].latitude, in_mtg_obj_array[0].longitude );

		var new_marker = new google.maps.Marker (
                                                    {
                                                    'position':     main_point,
                                                    'map':		    this.m_map_search_results_map,
                                                    'shadow':		this.m_icon_shadow,
                                                    'icon':			displayed_image,
                                                    'clickable':	true,
                                                    'cursor':		'pointer',
                                                    'draggable':    false
                                                    } );
        
        var id = this.m_uid;
        new_marker.oldImage = displayed_image;
        new_marker.meeting_id_array = new Array;
        new_marker.meeting_obj_array = in_mtg_obj_array;
        
        // We save all the meetings represented by this marker.
        for ( var c = 0; c < in_mtg_obj_array.length; c++ )
            {
            new_marker.meeting_id_array[c] = in_mtg_obj_array[c]['id_bigint'];
            };
        
        google.maps.event.addListener ( new_marker, 'click', function(in_event) { NouveauMapSearch.prototype.sResultsMarkerClicked( new_marker, id ); } );

        this.m_map_search_results_map.meeting_marker_array[this.m_map_search_results_map.meeting_marker_array.length] = new_marker;
        };
        
    /************************************************************************************//**
    *	\brief This function resets all the highlighting for a selected marker.             *
    ****************************************************************************************/
    this.clearMarkerHighlight = function()
        {
        for ( var i = 0; i < this.m_search_results.length; i++ )
            {
            var id = this.m_uid + '_meeting_list_item_' + this.m_search_results[i]['id_bigint'] + '_tr';
            var tr_element = document.getElementById ( id );
            tr_element.className = tr_element.classNameNormal
            };
            
        for ( var c = 0; this.m_map_search_results_map.meeting_marker_array && (c < this.m_map_search_results_map.meeting_marker_array.length); c++ )
            {
            if ( this.m_map_search_results_map.meeting_marker_array[c] )
                {
                this.m_map_search_results_map.meeting_marker_array[c].setIcon(this.m_map_search_results_map.meeting_marker_array[c].oldImage);
                };
            };
        
        this.m_selected_search_results = 0;
        this.setMeetingResultCountText();
        };
        
    /************************************************************************************//**
    *	\brief This returns the marker (on the result map) for the given meeting ID.        *
    *   \returns a marker object.                                                           *
    ****************************************************************************************/
    this.getMarkerForMeetingId = function(  in_meeting_id
                                        )
        {
        if ( this.m_map_search_results_map && this.m_map_search_results_map.meeting_marker_array && this.m_map_search_results_map.meeting_marker_array.length )
            {
            for ( var c = 0; c < this.m_map_search_results_map.meeting_marker_array.length; c++ )
                {
                var marker = this.m_map_search_results_map.meeting_marker_array[c];
                var id_array = marker.meeting_id_array;
                for ( i = 0; i < id_array.length; i++ )
                    {
                    if ( in_meeting_id == id_array[i] )
                        {
                        return marker;
                        };
                    };
                };
            };
        return null;
        };
                
    /************************************************************************************//**
    *	\brief Returns a meeting object from a given meeting ID.                            *
    *   \returns the instance of the meeting object corresponding to the given ID.          *
    ****************************************************************************************/
    this.getMeetingObjectFromId = function ( in_meeting_id    ///< The ID of the meeting.
                                            )
        {
        var ret = null;
        
        if ( this.m_search_results && this.m_search_results.length )
            {
            for ( var c = 0; c < this.m_search_results.length; c++ )
                {
                if ( this.m_search_results[c].id_bigint == in_meeting_id )
                    {
                    ret = this.m_search_results[c];
                    break;
                    };
                };
            };
        
        return ret;
        };
        
    /************************************************************************************//**
    *	\brief This is called to handle a blue marker being clicked.                        *
    ****************************************************************************************/
    this.respondToSingleClick = function (  in_marker,  ///< The marker object that was clicked.
                                            in_no_hash  ///< If true, the hash will be skipped.
                                            )
        {
        this.clearMarkerHighlight();
        
        var in_mtg_obj = in_marker.meeting_obj_array[0];
        
        in_marker.setIcon ( this.m_icon_image_selected );
        
        tr_element_id = this.m_uid + '_meeting_list_item_' + in_mtg_obj['id_bigint'] + '_tr';
        
        var tr_element = document.getElementById ( tr_element_id );
        
        tr_element.className = tr_element.classNameNormal + '_single';
        
        if ( !this.m_listResultsDisplayed )
            {
            this.m_listResultsDisplayed = true;
            this.setListResultsDisclosure();
            };
        
        if ( !in_no_hash )
            {
            location.hash = "#" + tr_element.id;
            };
        
        this.m_selected_search_results = 1;
        this.setMeetingResultCountText();
        };

    /************************************************************************************//**
    *	\brief This is called to handle a red marker being clicked.                         *
    ****************************************************************************************/
    this.respondToMultiClick = function (   in_marker,  ///< The marker object that was clicked.
                                            in_no_hash  ///< If true, the hash will be skipped.
                                            )
        {
        this.clearMarkerHighlight();
        
        var in_mtg_obj_array = in_marker.meeting_obj_array;

        in_marker.setIcon ( this.m_icon_image_selected );
        
        var top_element = null;
        
        for ( var c = 0; c < in_mtg_obj_array.length; c++ )
            {
            tr_element_id = this.m_uid + '_meeting_list_item_' + in_mtg_obj_array[c]['id_bigint'] + '_tr';
            
            var tr_element = document.getElementById ( tr_element_id );
        
            tr_element.className = tr_element.classNameNormal + '_multi';
            
            if ( !top_element || (tr_element.offsetTop < top_element.offsetTop) )
                {
                top_element = tr_element;
                };
            };
        
        if ( !this.m_listResultsDisplayed )
            {
            this.m_listResultsDisplayed = true;
            this.setListResultsDisclosure();
            };
            
        if ( !in_no_hash )
            {
            location.hash = "#" + tr_element.id;
            };
        
        this.m_selected_search_results = in_mtg_obj_array.length;
        this.setMeetingResultCountText();
        };
        
    /************************************************************************************//**
    *	\brief 
    ****************************************************************************************/
    this.handleRadiusChange = function()
        {
        this.m_search_radius = this.m_advanced_map_distance_popup.value;

        this.displayMarkerInAdvancedMap();
        };

    /****************************************************************************************
    *################################### PERFORM SEARCH ####################################*
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief This function constructs a URI to the root server that reflects the search   *
    ****************************************************************************************/
    this.beginSearch = function ()
        {
        this.displayThrobber();
        this.m_search_results = null;
        this.m_selected_search_results = 0;
        this.setDisplayedSearchResults();
        this.clearSearchResults();
        var uri = this.createSearchURI();
        this.m_ajax_request = BMLTPlugin_AjaxRequest ( uri, NouveauMapSearch.prototype.sMeetingsCallback, 'get', this.m_uid );
        };
        
    /************************************************************************************//**
    *	\brief This shows our "busy throbber."                                              *
    ****************************************************************************************/
    this.displayThrobber = function ()
        {
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.m_throbber_div.className = 'bmlt_nouveau_throbber_div';
        };

    /************************************************************************************//**
    *	\brief This hides our "busy throbber."                                              *
    ****************************************************************************************/
    this.hideThrobber = function ()
        {
        this.m_throbber_div.className = 'bmlt_nouveau_throbber_div bmlt_nouveau_throbber_div_hidden';
        };
        
    /************************************************************************************//**
    *	\brief This shows our "busy throbber."                                              *
    ****************************************************************************************/
    this.displayDetails = function ()
        {
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.m_display_div.className = 'bmlt_nouveau_div bmlt_nouveau_div_details_displayed';   // Used for pretty printing.
        this.m_details_div.className = 'bmlt_nouveau_details_div';
        this.m_details_div.my_mask_div.className = 'bmlt_nouveau_details_mask_div_displayed';
        };

    /************************************************************************************//**
    *	\brief This hides our "busy throbber."                                              *
    ****************************************************************************************/
    this.hideDetails = function ()
        {
        this.m_display_div.className = 'bmlt_nouveau_div';
        this.m_details_div.className = 'bmlt_nouveau_details_div bmlt_nouveau_details_div_hidden';
        this.m_details_div.my_mask_div.className = 'bmlt_nouveau_details_mask_div';
        };
                
    /************************************************************************************//**
    *	\brief  Closes the single results detail page.                                      *
    ****************************************************************************************/
    this.closeSingle = function()
        {
        if ( this.m_details_observer_only_div )
            {
            this.m_details_observer_only_div.innerHTML = '';
            };
        
        this.hideDetails();
        if ( this.m_single_meeting_id )
            {
            this.searchSpecButtonHit();
            this.m_single_meeting_id = 0;
            };
        };

    /************************************************************************************//**
    *	\brief This function constructs a URI to the root server that reflects the search   *
    *          parameters, as specified by the search specification section.                *
    *   \returns a string, containing the complete URI.                                     *
    ****************************************************************************************/
    this.createSearchURI = function ()
        {
        var is_advanced = (this.m_current_view == 'advanced_map') || (this.m_current_view == 'advanced_text') ;
        
        var ret = this.m_root_server_uri; // We append a question mark, so all the rest can be added without worrying about this.
        
        ret += encodeURIComponent ( 'switcher=GetSearchResults&sort_keys=weekday_tinyint,start_time' );
        
        // These will all be appended to the URI (or not).
        var uri_elements = new Array;
        var index = 0;
        
        uri_elements[index] = new Array;
        uri_elements[index][0] = 'long_val';
        uri_elements[index++][1] = this.m_current_long;
        
        uri_elements[index] = new Array;
        uri_elements[index][0] = 'lat_val';
        uri_elements[index++][1] = this.m_current_lat;
        // First, if we have a map up, we use the specified width. (not done if the search is specified using text).
        // This restricts the search area.
        if ( this.m_semaphore_lookup_location_services || (this.m_location_checkbox.checked && this.m_text_input.value && (this.m_text_input.value != this.m_text_input.defaultValue)) || (this.m_current_view == 'map') || (this.m_current_view == 'advanced_map') )
            {
            // In the case of the advanced map, we will also have a radius value. Otherwise, we use the default auto.
            this.m_search_radius = (this.m_current_view == 'advanced_map') ? this.m_search_radius : g_Nouveau_default_geo_width;

            uri_elements[index] = new Array;
            uri_elements[index][0] = 'geo_width';
            uri_elements[index++][1] = this.m_search_radius;
            }
        else if ( !this.m_location_checkbox.checked )   // Otherwise, we use whatever is in the text box.
            {
            var search_text = this.m_text_input.value;
            
            if ( search_text && (search_text != this.m_text_input.defaultValue) )
                {
                uri_elements[index] = new Array;
                uri_elements[index][0] = 'SearchString';
                uri_elements[index++][1] = search_text;
                
                // Make sure that all the text is used.
                uri_elements[index] = new Array;
                uri_elements[index][0] = 'SearchStringAll';
                uri_elements[index++][1] = 1;
                };
            };
        
        if ( this.m_single_meeting_id )
            {
            uri_elements[index] = new Array;
            uri_elements[index][0] = 'meeting_ids[]';
            uri_elements[index++][1] = this.m_single_meeting_id;
            }
        else
            {
            if ( is_advanced && this.m_advanced_weekdays_shown )
                {
                for ( var c = 0; c < this.m_advanced_weekdays_array.length; c++ )
                    {
                    if ( this.m_advanced_weekdays_array[c].checked )
                        {
                        uri_elements[index] = new Array;
                        uri_elements[index][0] = 'weekdays[]';
                        uri_elements[index++][1] = c + 1;
                        };
                    };
                };
        
            if ( is_advanced && this.m_advanced_formats_shown )
                {
                for ( var c = 0; c < this.m_advanced_format_checkboxes_array.length; c++ )
                    {
                    if ( this.m_advanced_format_checkboxes_array[c].checked )
                        {
                        uri_elements[index] = new Array;
                        uri_elements[index][0] = 'formats[]';
                        uri_elements[index++][1] = this.m_advanced_format_checkboxes_array[c].value;
                        };
                    };
                };
            
            if ( is_advanced && this.m_advanced_service_bodies_shown )
                {
                for ( var c = 0; c < this.m_advanced_service_bodies_checkboxes_array.length; c++ )
                    {
                    if ( this.m_advanced_service_bodies_checkboxes_array[c].checked )
                        {
                        uri_elements[index] = new Array;
                        uri_elements[index][0] = 'services[]';
                        uri_elements[index++][1] = this.m_advanced_service_bodies_checkboxes_array[c].value;
                        };
                    };
                };
            
            if ( this.m_semaphore_lookup_day )
                {
                var todays_date = new Date ();
            
                var today_weekday = parseInt(todays_date.getDay()) + 1;
                var today_hour = parseInt(todays_date.getHours());
                var today_minute = parseInt(todays_date.getMinutes());
            
                today_minute -= parseInt ( this.m_grace_period );
            
                if ( today_minute < 0 )
                    {
                    today_hour--;
                    today_minute += 60;
                
                    if ( today_hour < 0 )   // Can't go earlier than midnight.
                        {
                        today_hour = 0;
                        today_minute = 0;
                        };
                    };
            
                // If today, then we look for this weekday, and a time after now.
                if ( this.m_semaphore_lookup_day == 'today' )
                    {
                    uri_elements[index] = new Array;
                    uri_elements[index][0] = 'StartsAfterH';
                    uri_elements[index++][1] = today_hour.toString();
                
                    uri_elements[index] = new Array;
                    uri_elements[index][0] = 'StartsAfterM';
                    uri_elements[index++][1] = today_minute.toString();
                    }
                else if ( this.m_semaphore_lookup_day == 'tomorrow' )
                    {
                    today_weekday = (today_weekday < 7) ? today_weekday + 1 : 1;
                    };
            
                uri_elements[index] = new Array;
                uri_elements[index][0] = 'weekdays[]';
                uri_elements[index++][1] = today_weekday.toString();
            
                this.m_semaphore_lookup_day = null;
                };	
            };
        
        // Concatenate all the various parameters we gathered.
        for ( var i = 0; i < index; i++ )
            {
            ret += '&' + uri_elements[i][0] + '=' + uri_elements[i][1];
            };
        
        // Belt and suspenders.
        this.m_semaphore_lookup_location_services = false;
        
        // Return the complete URI for a JSON response.
        return ret;
        };

    /************************************************************************************//**
    *	\brief This function constructs a URI to the root server that reflects the search   *
    *          parameters, as specified by the search specification section.                *
    *   \returns a string, containing the complete URI.                                     *
    ****************************************************************************************/
    this.createSearchURI_Formats = function ()
        {
        var ret = this.m_root_server_uri; // We append a question mark, so all the rest can be added without worrying about this.
        
        ret += encodeURIComponent ( 'switcher=GetFormats' );
        return ret;
        };

    /************************************************************************************//**
    *	\brief This function constructs a URI to the root server that reflects the search   *
    *          parameters, as specified by the search specification section.                *
    *   \returns a string, containing the complete URI.                                     *
    ****************************************************************************************/
    this.createSearchURI_ServiceBodies = function ()
        {
        var ret = this.m_root_server_uri; // We append a question mark, so all the rest can be added without worrying about this.
        
        ret += encodeURIComponent ( 'switcher=GetServiceBodies' );
        return ret;
        };
	
	/************************************************************************************//**
	*	\brief  Does an AJAX call for a JSON response, based on the given criteria and      *
	*           callback function.                                                          *
	*           The callback will be a function in the following format:                    *
	*               function ajax_callback ( in_json_obj )                                  *
	*           where "in_json_obj" is the response, converted to a JSON object.            *
	*           it will be null if the function failed.                                     *
	****************************************************************************************/
	this.callRootServer = function ( in_uri ///< The URI to call (with all the parameters).
	                                )
	    {
        this.displayThrobber();
	    if ( this.m_ajax_request )   // This prevents the requests from piling up. We are single-threaded.
	        {
	        this.m_ajax_request.abort();
	        this.m_ajax_request = null;
	        };
	    
        this.m_ajax_request = BMLTPlugin_AjaxRequest ( in_uri, NouveauMapSearch.prototype.sFormatCallback, 'get', this.m_uid );
	    };
	
	/************************************************************************************//**
	*	\brief  Does an AJAX call for a JSON response, based on the given criteria and      *
	*           callback function.                                                          *
	*           The callback will be a function in the following format:                    *
	*               function ajax_callback ( in_json_obj )                                  *
	*           where "in_json_obj" is the response, converted to a JSON object.            *
	*           it will be null if the function failed.                                     *
	****************************************************************************************/
	this.getFormats = function ()
	    {
        this.displayThrobber();
	    if ( this.m_ajax_request )   // This prevents the requests from piling up. We are single-threaded.
	        {
	        this.m_ajax_request.abort();
	        this.m_ajax_request = null;
	        };
	        
	    var uri = this.createSearchURI_Formats();
	    
        this.m_ajax_request = BMLTPlugin_AjaxRequest ( uri, NouveauMapSearch.prototype.sFormatCallback, 'get', this.m_uid );
	    };
	
	/************************************************************************************//**
	*	\brief  Does an AJAX call for a JSON response, based on the given criteria and      *
	*           callback function.                                                          *
	*           The callback will be a function in the following format:                    *
	*               function ajax_callback ( in_json_obj )                                  *
	*           where "in_json_obj" is the response, converted to a JSON object.            *
	*           it will be null if the function failed.                                     *
	****************************************************************************************/
	this.getServiceBodies = function ()
	    {
        this.displayThrobber();
	    if ( this.m_ajax_request )   // This prevents the requests from piling up. We are single-threaded.
	        {
	        this.m_ajax_request.abort();
	        this.m_ajax_request = null;
	        };
	    
	    var uri = this.createSearchURI_ServiceBodies();
	    
        this.m_ajax_request = BMLTPlugin_AjaxRequest ( uri, NouveauMapSearch.prototype.sServiceBodiesCallback, 'get', this.m_uid );
	    };
    
    /************************************************************************************//**
    *	\brief  This handles looking up the location, then setting the marker in the        *
    *           advanced map display to that position.                                      *
    ****************************************************************************************/
    this.setLocationOfMainMarker = function ()
        {
        this.m_semaphore_lookup_set_marker = true;
        this.handleFindNearbyMeetingsByDay ( null );
        };
    
    /************************************************************************************//**
    *	\brief  This locates the user's position, then looks up meetings around that. The   *
    *           variants are default (null passed in), which just does a "one-click" search *
    *           around the position, 'today', which looks for meetings that are nearby, and *
    *           start later today, and 'tomorrow', in which we look for nearby meetings     *
    *           tomorrow.                                                                   *
    ****************************************************************************************/
    this.handleFindNearbyMeetingsByDay = function ( in_day  ///< This is null, 'today', or 'tomorrow'
                                                    )
        {
        this.m_semaphore_lookup_location_services = true;
        this.m_semaphore_lookup_day = in_day;
        this.m_semaphore_lookup_retry_count = 0;
        this.lookupMyLocation();
        };

	/************************************************************************************//**
	*	\brief  Looks up the users's location, using the browser's JavaScript API
	****************************************************************************************/
    this.lookupMyLocation = function()
        {
        this.displayThrobber();
        
        // Save this, in case the lookup goes walkabout
        this.m_pre_search_lat = this.m_current_lat;
        this.m_pre_search_long = this.m_current_long;
        
        var uid = this.m_uid;
        
        navigator.geolocation.getCurrentPosition (  function (in_position) { NouveauMapSearch.prototype.sWhereAmI_CallBack(in_position,uid) },
                                                    function(in_error) { NouveauMapSearch.prototype.sWhereAmI_Fail_Final(in_error, uid); },
                                                    {enableHighAccuracy:true, maximumAge:600000});
        };
    
    /********************************************************************************************
    *	\brief Handles a successful location result.                                            *
    ********************************************************************************************/
    this.handleWhereAmI_CallBack = function (   in_position ///< The found position
                                            )
        {
        var move_marker = this.m_semaphore_lookup_set_marker;
        
        this.m_semaphore_lookup_set_marker = false;
        
		if ( in_position.coords )
			{
            this.m_current_lat = in_position.coords.latitude;
            this.m_current_long = in_position.coords.longitude;
			if ( move_marker && (this.m_current_view == 'advanced_map') )
			    {
                this.displayMarkerInAdvancedMap();
                this.hideThrobber();
			    }
			else
			    {
                this.beginSearch();
                };
			}
		else
		    {
            alert ( g_Nouveau_cant_lookup_display );
            this.hideThrobber();
		    };
        
        this.m_semaphore_lookup_location_services = false;
        };

    /********************************************************************************************
    *	\brief Handles failure to locate.                                                       *
    ********************************************************************************************/
    this.handleWhereAmI_Fail_Final = function ( in_error    ///< The error that caused the failure.
                                                )
        {
        switch ( in_error.code )
            {
            case in_error.TIMEOUT:
                var uid = this.m_uid;
                navigator.geolocation.getCurrentPosition (  function (in_position) { NouveauMapSearch.prototype.sWhereAmI_CallBack(in_position,uid) },
                                                            function(in_error) { NouveauMapSearch.prototype.sWhereAmI_Fail_Final(in_error, uid); },
                                                            {enableHighAccuracy:true, maximumAge:600000, timeout:100});
            break;
            
            default:
                this.m_semaphore_lookup_set_marker = false;
                this.m_semaphore_lookup_location_services = false;
                alert ( g_Nouveau_cant_lookup_display );
                this.hideThrobber();
            break;
            };
        };
    
    /********************************************************************************************
    *	\brief Test to see if the browser supports location services.                           *
    *   \returns a Boolean. TRUE, if the browser supports location services.                    *
    ********************************************************************************************/
    this.hasNavCapability = function()
        {
        return      ( ( typeof ( google ) == 'object' && typeof ( google.gears ) == 'object' ) )
                ||  ( typeof ( navigator ) == 'object' && typeof ( navigator.geolocation ) == 'object' );
        };
    
    /****************************************************************************************
    *################################# SET UP SEARCH RESULTS ###############################*
    ****************************************************************************************/    
    /************************************************************************************//**
    *	\brief This either hides or shows the search results.                               *
    ****************************************************************************************/
    this.processSearchResults = function( in_search_results_json_object ///< The search results, as a JSON object.
                                        )
        {
        this.m_search_results = in_search_results_json_object;
        this.m_selected_search_results = 0;
        this.analyzeSearchResults();
        this.m_search_results_shown = true;
        this.buildDOMTree_SearchResults_Section();
        this.m_mapResultsDisplayed = true;
        this.m_listResultsDisplayed = true;
        this.setDisplayedSearchResults();
        this.loadResultsMap();
        this.validateGoButtons();
        if ( this.m_single_meeting_id )
            {
            this.detailsButtonHit(this.m_single_meeting_id);
            };
        };
    
    /************************************************************************************//**
    *	\brief This sorts through all of the search results, and builds an array of fields  *
    *          for their display.                                                           *
    *          The principal reason for this function is to create a "box" that contains    *
    *          all of the meetings. This will be used to select an initial projection on    *
    *          the map display.                                                             *
    *          TODO: This needs some work to make it effective for the antimeridian.        *
    ****************************************************************************************/
    this.analyzeSearchResults = function ()
        {
        // These will be the result of this function.
        this.m_long_lat_northeast = new google.maps.LatLng ( this.m_current_lat, this.m_current_long );
        this.m_long_lat_southwest = new google.maps.LatLng ( this.m_current_lat, this.m_current_long );

        // We loop through the whole response.
		for ( var c = 0; c < this.m_search_results.length; c++ )
		    {
		    this.m_search_results[c].uid = this.m_uid;    // This will be used to anchor context in future callbacks. This is a convenient place to set it.
		    
            var time_array = (this.m_search_results[c]['duration_time'].toString()).split(':');
            
            var time_full = parseInt ( ((time_array && (time_array.length > 1)) ? ((parseInt ( time_array[0], 10 ) * 100) + parseInt ( time_array[1], 10 )) : 0), 10 );

		    // We give the meeting a duration, if none is provided.
		    if ( time_full == 0 )
		        {
		        this.m_search_results[c].duration_time = this.m_default_duration;
		        };
		    
		    if ( this.m_search_results[c].longitude > this.m_long_lat_northeast.lng() )
		        {
                this.m_long_lat_northeast = new google.maps.LatLng ( this.m_long_lat_northeast.lat(), this.m_search_results[c].longitude );
		        };
		    
		    if ( this.m_search_results[c].latitude > this.m_long_lat_northeast.lat() )
		        {
                this.m_long_lat_northeast = new google.maps.LatLng ( this.m_search_results[c].latitude, this.m_long_lat_northeast.lng() );
		        };
		    
		    if ( this.m_search_results[c].longitude < this.m_long_lat_southwest.lng() )
		        {
                this.m_long_lat_southwest = new google.maps.LatLng ( this.m_long_lat_southwest.lat(), this.m_search_results[c].longitude );
		        };
		    
		    if ( this.m_search_results[c].latitude < this.m_long_lat_southwest.lat() )
		        {
                this.m_long_lat_southwest = new google.maps.LatLng ( this.m_search_results[c].latitude, this.m_long_lat_southwest.lng() );
		        };
		    };

		this.sortSearchResults();
        };
    
    /************************************************************************************//**
    *	\brief This either hides or shows the search results.                               *
    ****************************************************************************************/
    this.sortSearchResults = function()
        {
        if ( this.m_search_results && this.m_search_results.length )    // Make sure we have something to sort.
            {
            this.m_search_results.sort ( NouveauMapSearch.prototype.sSortCallback );
            };
        };
        
    /************************************************************************************//**
    *	\brief This either hides or shows the search results.                               *
    ****************************************************************************************/
    this.setDisplayedSearchResults = function()
        {
        if ( !this.m_search_results )
            {
            this.m_search_results_shown = false;    // Can't show what doesn't exist.
            if ( this.m_search_results_div )
                {
                this.m_search_spec_switch_div.className = 'bmlt_nouveau_search_spec_switch_div bmlt_nouveau_search_spec_switch_div_hidden';
                this.m_search_results_div.className = 'bmlt_nouveau_search_results_div bmlt_nouveau_results_hidden';
                this.m_search_spec_div.className = 'bmlt_nouveau_search_spec_div';
                };
            }
        else
            {
            this.m_search_spec_switch_div.className = 'bmlt_nouveau_search_spec_switch_div';
            
            if ( this.m_search_results_shown )
                {
                this.m_search_spec_switch_a.className = 'bmlt_search_spec_switch_a';
                this.m_search_results_switch_a.className = 'bmlt_search_results_switch_a bmlt_search_results_switch_hidden';
                this.m_search_results_div.className = 'bmlt_nouveau_search_results_div';
                this.m_search_spec_div.className = 'bmlt_nouveau_search_spec_div bmlt_nouveau_spec_hidden';
                }
            else
                {
                this.m_search_spec_switch_a.className = 'bmlt_search_spec_switch_a bmlt_search_spec_switch_hidden';
                this.m_search_results_switch_a.className = 'bmlt_search_spec_switch_a';
                this.m_search_results_div.className = 'bmlt_nouveau_search_results_div bmlt_nouveau_results_hidden';
                this.m_search_spec_div.className = 'bmlt_nouveau_search_spec_div';
                };

            this.setMapResultsDisclosure();
            this.setListResultsDisclosure();
            };
        };
        
    /************************************************************************************//**
    *	\brief This fills out the details div for the given meeting.                        *
    ****************************************************************************************/
    this.populateDetailsDiv = function (    in_meeting_object   ///< The object for the meeting to display
                                        )
        {
        if ( this.m_details_inner_div )
            {
            this.m_single_meeting_display_div = document.createElement ( 'div' );
            this.m_single_meeting_display_div.className = 'bmlt_nouveau_single_meeting_wrapper_div';
            this.m_single_meeting_display_div.id = 'bmlt_nouveau_single_meeting_wrapper_div_' + in_meeting_object.id_bigint;
        
            if ( !this.m_details_meeting_name_div )
                {
                this.m_details_meeting_name_div = document.createElement ( 'div' );
                this.m_single_meeting_display_div.appendChild ( this.m_details_meeting_name_div );
                }
            else
                {
                this.m_details_meeting_name_div.innerHTML = '';
                };
            
            this.m_details_meeting_name_div.className = 'bmlt_nouveau_single_meeting_name_div' + (in_meeting_object.meeting_name ? '' : ' bmlt_nouveau_empty_name');
            
            this.m_details_meeting_name_div.appendChild ( document.createTextNode(in_meeting_object.meeting_name ? in_meeting_object.meeting_name : g_Nouveau_default_meeting_name) );
            
            if ( !this.m_details_meeting_time_div )
                {
                this.m_details_meeting_time_div = document.createElement ( 'div' );
                this.m_details_meeting_time_div.className = 'bmlt_nouveau_single_meeting_time_div';
                this.m_single_meeting_display_div.appendChild ( this.m_details_meeting_time_div );
                }
            else
                {
                this.m_details_meeting_time_div.innerHTML = '';
                };
            var time_text = this.constructTimeString ( in_meeting_object );

            this.m_details_meeting_time_div.appendChild ( document.createTextNode( time_text ) );

            if ( !this.m_details_meeting_location_div )
                {
                this.m_details_meeting_location_div = document.createElement ( 'div' );
                this.m_details_meeting_location_div.className = 'bmlt_nouveau_single_meeting_location_div';
                this.m_single_meeting_display_div.appendChild ( this.m_details_meeting_location_div );
                }
            else
                {
                this.m_details_meeting_location_div.innerHTML = '';
                };
        
            var loc_text = this.constructAddressString ( in_meeting_object );

            this.m_details_meeting_location_div.innerHTML = loc_text;
            
            if ( !this.m_details_map_container_div )
                {
                this.m_details_map_container_div = document.createElement ( 'div' );
                this.m_details_map_container_div.className = 'bmlt_nouveau_details_map_container_div';
                this.m_single_meeting_display_div.appendChild ( this.m_details_map_container_div );
                };
            
            if ( !this.m_details_map_div )
                {
                this.m_details_map_div = document.createElement ( 'div' );
                this.m_details_map_div.className = 'bmlt_nouveau_details_map_div';
                this.m_details_map_container_div.appendChild ( this.m_details_map_div );
                };
                
            if ( this.m_details_observer_only_div )
                {
                this.m_details_observer_only_div.parentNode.removeChild ( this.m_details_observer_only_div );
                this.m_details_observer_only_div = null;
                };
            
            if ( this.m_details_extra_fields_div )
                {
                this.m_details_extra_fields_div.parentNode.removeChild ( this.m_details_extra_fields_div );
                this.m_details_extra_fields_div = null;
                };
            
            // If the user is logged in, we will display all fields.
            if ( g_Nouveau_user_logged_in && !this.m_details_observer_only_div || (this.m_details_observer_only_div.innerHTML == '') )
                {
                var has_hidden_fields = false;
                
                for ( var key in in_meeting_object )
                    {
                    if ( in_meeting_object.hasOwnProperty ( key ) )
                        {
                        var meeting_property = in_meeting_object[key].split ( '#@-@#' );
                        
                        // We only display the hidden ones.
                        if ( meeting_property.length == 3 )
                            {
                            has_hidden_fields = true;
                            if ( !this.m_details_observer_only_div )
                                {
                                this.m_details_observer_only_div = document.createElement ( 'div' );
                                this.m_details_observer_only_div.className = 'bmlt_nouveau_details_hidden_element_outer_container_div';
                                this.m_single_meeting_display_div.appendChild ( this.m_details_observer_only_div );
                                };
                            
                            var prompt = meeting_property[1];
                            var value = meeting_property[2];
                            var line_container = document.createElement ( 'div' );
                            line_container.className = 'bmlt_nouveau_details_hidden_element_line_div';
                            var hidden_prompt = document.createElement ( 'div' );
                            hidden_prompt.className = 'bmlt_nouveau_details_hidden_element_prompt_div';
                            hidden_prompt.appendChild ( document.createTextNode ( prompt ) );
                            var hidden_value = document.createElement ( 'div' );
                            hidden_value.className = 'bmlt_nouveau_details_hidden_element_value_div';
                            hidden_value.appendChild ( document.createTextNode ( value ) );
                            
                            line_container.appendChild ( hidden_prompt );
                            line_container.appendChild ( hidden_value );
                            var breaker_breaker = document.createElement ( 'div' );
                            breaker_breaker.className = 'clear_both';
                            line_container.appendChild ( breaker_breaker );
                            this.m_details_observer_only_div.appendChild ( line_container );
                            };
                        };
                    };
                };
            
            // Show the rest.
            for ( var key in in_meeting_object )
                {
                if ( in_meeting_object.hasOwnProperty ( key ) )
                    {
                    var meeting_property = in_meeting_object[key].split ( '#@-@#' );
                    
                    if ( !(meeting_property[0] == 'distance_in_km') && !(meeting_property[0] == 'distance_in_miles') )
                    {
                        // We only display the non-hidden ones.
                        if ( meeting_property[1] && (meeting_property.length == 2) )
                            {
                            if ( !this.m_details_extra_fields_div )
                                {
                                this.m_details_extra_fields_div = document.createElement ( 'div' );
                                this.m_details_extra_fields_div.className = 'bmlt_nouveau_details_extra_element_outer_container_div';
                                this.m_single_meeting_display_div.appendChild ( this.m_details_extra_fields_div );
                                };
                        
                            var prompt = meeting_property[0];
                            var value = meeting_property[1];
                            var line_container = document.createElement ( 'div' );
                            line_container.className = 'bmlt_nouveau_details_extra_element_line_div';
                            var data_prompt = document.createElement ( 'div' );
                            data_prompt.className = 'bmlt_nouveau_details_extra_element_prompt_div';
                            data_prompt.appendChild ( document.createTextNode ( prompt ) );
                            var data_value = document.createElement ( 'div' );
                            data_value.className = 'bmlt_nouveau_details_extra_element_value_div';
                            data_value.appendChild ( document.createTextNode ( value ) );
                        
                            line_container.appendChild ( data_prompt );
                            line_container.appendChild ( data_value );
                            var breaker_breaker = document.createElement ( 'div' );
                            breaker_breaker.className = 'clear_both';
                            line_container.appendChild ( breaker_breaker );
                            this.m_details_extra_fields_div.appendChild ( line_container );
                            };
                        };
                    };
                };
                
            if ( !this.m_details_service_body_div )
                {
                this.m_details_service_body_div = document.createElement ( 'div' );
                this.m_details_service_body_div.className = 'bmlt_nouveau_details_service_body_div';
                
                var label_span = document.createElement ( 'span' );
                label_span.className = 'bmlt_nouveau_details_service_body_label_span';
                label_span.appendChild ( document.createTextNode ( g_Nouveau_single_service_body_label ) );
                this.m_details_service_body_div.appendChild ( label_span );
                this.m_single_meeting_display_div.appendChild ( this.m_details_service_body_div );
                
                if ( !this.m_details_service_body_span )
                    {
                    this.m_details_service_body_span = document.createElement ( 'span' );
                    this.m_details_service_body_span.className = 'bmlt_nouveau_details_service_body_contents_span';
                    this.m_details_service_body_div.appendChild ( this.m_details_service_body_span );
                    };
                
                this.m_single_meeting_display_div.appendChild ( this.m_details_service_body_div );
                };
            
            this.m_details_service_body_span.innerHTML = '';
            var name = this.getServiceBodyName ( in_meeting_object.service_body_bigint );
            var url = this.getServiceBodyURL ( in_meeting_object.service_body_bigint );
            
            var content_node = null;
            
            if ( url )
                {
                content_node = document.createElement ( 'a' );
                content_node.className = 'bmlt_nouveau_details_service_body_uri_a';
                content_node.setAttribute ( 'href', url );
                content_node.setAttribute ( 'rel', 'external' );
                content_node.appendChild ( document.createTextNode ( name ) );
                }
            else
                {
                content_node = document.createTextNode ( name );
                };
            
            this.m_details_service_body_span.appendChild ( content_node );

            if ( !this.m_details_comments_div )
                {
                this.m_details_comments_div = document.createElement ( 'div' );
                this.m_details_comments_div.className = 'bmlt_nouveau_details_comments_div';
                this.m_single_meeting_display_div.appendChild ( this.m_details_comments_div );
                };
            
            this.m_details_comments_div.innerHTML = '';
            if ( in_meeting_object.comments )
                {
                this.m_details_comments_div.appendChild ( document.createTextNode ( in_meeting_object.comments ) );
                };
            
            if ( !this.m_details_formats_div )
                {
                this.m_details_formats_div = document.createElement ( 'div' );
                this.m_details_formats_div.className = 'bmlt_nouveau_details_format_div';
                
                var formats_label_span = document.createElement ( 'span' );
                formats_label_span.className = 'bmlt_nouveau_details_format_label_span';
                formats_label_span.appendChild ( document.createTextNode ( g_Nouveau_single_formats_label ) );

                this.m_details_formats_div.appendChild ( formats_label_span );
                
                this.m_single_meeting_display_div.appendChild ( this.m_details_formats_div );
                };
            
            if ( !this.m_details_formats_contents_div )
                {
                this.m_details_formats_contents_div = document.createElement ( 'div' );
                this.m_details_formats_contents_div.className = 'bmlt_nouveau_details_format_contents_div';
                this.m_details_formats_div.appendChild ( this.m_details_formats_contents_div );
                };
            
            this.m_details_formats_contents_div.innerHTML = '';
            
            var loc_array = in_meeting_object.formats.split ( ',');
        
            if ( loc_array && loc_array.length )
                {
                var formats_dl = document.createElement ( 'dl' );
                formats_dl.className = 'bmlt_nouveau_details_format_contents_dl';
                
                for ( var c = 0; c < loc_array.length; c++ )
                    {
                    var loc_text = loc_array[c];
        
                    var format_header = document.createElement ( 'dt' );
                    format_header.className = 'bmlt_nouveau_details_formats_contents_dt bmlt_nouveau_details_formats_contents_dt_' + loc_text;
                    
                    var format_code = document.createElement ( 'span' );
                    format_code.className = 'bmlt_nouveau_details_formats_code_span bmlt_nouveau_details_formats_code_span_' + loc_text;
                    
                    format_code.appendChild ( document.createTextNode( loc_text ) );
                    format_header.appendChild ( format_code );
                    
                    var format_name = document.createElement ( 'span' );
                    format_name.className = 'bmlt_nouveau_details_formats_name_span bmlt_nouveau_details_formats_name_span_' + loc_text;
                    
                    format_name.appendChild ( document.createTextNode( this.getFormatName ( loc_text ) ) );
                    format_header.appendChild ( format_name );
                    
                    var format_description = document.createElement ( 'dd' );
                    format_description.className = 'bmlt_nouveau_details_formats_description_dd bmlt_nouveau_details_formats_description_dd_' + loc_text;
                    
                    format_description.appendChild ( document.createTextNode( this.getFormatDescription ( loc_text ) ) );
                    
                    formats_dl.appendChild ( format_header );
                    formats_dl.appendChild ( format_description );
                    };
                
                this.m_details_formats_contents_div.appendChild ( formats_dl );
                this.m_single_meeting_display_div.appendChild ( this.m_details_formats_contents_div );
                };
            
            this.m_details_inner_div.appendChild ( this.m_single_meeting_display_div );
            };
        };
        
    /************************************************************************************//**
    *	\brief Constructs the address string for a single details page.                     *
    *   \returns a string with the complete address.                                        *
    ****************************************************************************************/
    this.loadDetailsMap = function( in_meeting_object  ///< The object for the meeting to display
                                    )
        {
        var center = new google.maps.LatLng ( in_meeting_object.latitude, in_meeting_object.longitude );
        var zoom = g_Nouveau_default_details_map_zoom;
        
        if ( !this.m_details_map )
            {
            var myOptions = {
                            'center': center,
                            'zoom': zoom,
                            'mapTypeId': google.maps.MapTypeId.ROADMAP,
                            'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                            'zoomControl': true,
                            'mapTypeControl': true,
                            'disableDoubleClickZoom' : true,
                            'scaleControl' : true
                            };

            myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };

            this.m_details_map = new google.maps.Map ( this.m_details_map_div, myOptions );
            this.m_details_map.main_marker = new google.maps.Marker (
                                                                    {
                                                                    'position':     center,
                                                                    'map':		    this.m_details_map,
                                                                    'shadow':		this.m_icon_shadow,
                                                                    'icon':			this.m_icon_image_selected,
                                                                    'clickable':	false,
                                                                    'cursor':		'default',
                                                                    'draggable':    false
                                                                    } );
            this.m_details_map.setOptions({'scrollwheel': false});   // For some reason, it ignores setting this in the options.
            }
        else
            {
            this.m_details_map.setCenter ( center );
            this.m_details_map.setZoom ( zoom );
            this.m_details_map.setMapTypeId ( google.maps.MapTypeId.ROADMAP );
            this.m_details_map.getStreetView().setVisible ( false );
            this.m_details_map.main_marker.setPosition ( center );
            };
        };
        
    /************************************************************************************//**
    *	\brief Constructs the address string for a single details page.                     *
    *          Yes, it's a big, long, awkward function. I'll fix it in my copious free time *
    *          If it bothers you so damn much, feel free to pitch in, as opposed to just    *
    *          just bitching in. mmmm...kay?                                                *
    *   \returns a string with the complete address.                                        *
    ****************************************************************************************/
    this.constructAddressString = function( in_meeting_object   ///< The object for the meeting to display
                                            )
        {
        var ret = '';
        
        if ( in_meeting_object['location_municipality'] && in_meeting_object['location_province'] && in_meeting_object['location_postal_code_1'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_town_province_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_town_province_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_town_province_zip, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_town_province_zip, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_town_province_zip, in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_town_province_zip, in_meeting_object['location_text'], in_meeting_object['location_municipality'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else if ( in_meeting_object['location_municipality'] && in_meeting_object['location_province'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_town_province, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_town_province, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_town_province, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_town_province, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_town_province, in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_town_province, in_meeting_object['location_text'], in_meeting_object['location_municipality'], in_meeting_object['location_province'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else if ( in_meeting_object['location_municipality'] && in_meeting_object['location_postal_code_1'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_town_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_town_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_town_zip, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_town_zip, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_town_zip, in_meeting_object['location_street'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_town_zip, in_meeting_object['location_text'], in_meeting_object['location_municipality'], in_meeting_object['location_postal_code_1'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else if ( in_meeting_object['location_province'] && in_meeting_object['location_postal_code_1'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_province_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_province_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_province_zip, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_province_zip, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_province_zip, in_meeting_object['location_street'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_province_zip, in_meeting_object['location_text'], in_meeting_object['location_province'], in_meeting_object['location_postal_code_1'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else if ( in_meeting_object['location_province'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_province, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_province, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_province, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_province, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_province, in_meeting_object['location_street'], in_meeting_object['location_province'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_province, in_meeting_object['location_text'], in_meeting_object['location_province'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else if ( in_meeting_object['location_postal_code_1'] )
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_zip, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info_zip, in_meeting_object['location_street'], in_meeting_object['location_info'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info_zip, in_meeting_object['location_text'], in_meeting_object['location_info'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_zip, in_meeting_object['location_street'], in_meeting_object['location_postal_code_1'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_zip, in_meeting_object['location_text'], in_meeting_object['location_postal_code_1'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            }
        else
            {
            if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street_info, in_meeting_object['location_text'], in_meeting_object['location_street'], in_meeting_object['location_info'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_street, in_meeting_object['location_text'], in_meeting_object['location_street'] );
                }
            else if ( in_meeting_object['location_street'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street_info, in_meeting_object['location_street'], in_meeting_object['location_info'] );
                }
            else if ( in_meeting_object['location_text'] && in_meeting_object['location_info'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc_info, in_meeting_object['location_text'], in_meeting_object['location_info'] );
                }
            else if ( in_meeting_object['location_street'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_street, in_meeting_object['location_street'] );
                }
            else if ( in_meeting_object['location_text'] )
                {
                ret = sprintf ( g_Nouveau_location_sprintf_format_single_loc, in_meeting_object['location_text'] );
                }
            else
                {
                ret = g_Nouveau_location_sprintf_format_wtf;
                };
            };
        
        var longitude = in_meeting_object['longitude'];
        var latitude = in_meeting_object['latitude'];
        
        if ( (ret != g_Nouveau_location_sprintf_format_wtf) && latitude && longitude )
            {
            ret += ' (<a class="bmlt_satellite_meeting_map_link" href="' + sprintf ( g_Nouveau_meeting_details_map_link_uri_format, parseFloat ( latitude ), parseFloat ( longitude ) ) + '">' + g_Nouveau_meeting_details_map_link_text + '</a>)';
            };
        
        return ret;
        };
        
    /************************************************************************************//**
    *	\brief Constructs the time and duration string for a single details page.           *
    *   \returns a string with the complete display string..                                *
    ****************************************************************************************/
    this.constructTimeString = function ( in_meeting_object ///< The meeting that needs the string.
                                        )
        {
        var ret = '';
        
        var weekday_string = g_Nouveau_weekday_long_array[in_meeting_object['weekday_tinyint'] - 1];
        
        var time = (in_meeting_object['start_time'].toString()).split(':');

        time[0] = parseInt ( time[0], 10 );
        time[1] = parseInt ( time[1], 10 );
        
        var time_string = null;
        
        if ( (time[0] == 12) && (time[1] == 0) )
            {
            time_string = g_Nouveau_noon;
            }
        else if ( ((time[0] == 23) && (time[1] >= 55)) || ((time[0] == 0) && (time[1] == 0)) )
            {
            time_string = g_Nouveau_midnight;
            }
        else
            {
            var hours = (time[0] > 12) ? time[0] - 12 : time[0];
            var minutes = time[1];
            var a = ((time[0] > 12) || ((time[0] == 12) && (time[1] > 0))) ? g_Nouveau_pm : g_Nouveau_am;
            
            if ( g_Nouveau_military_time )
                {
                time_string = sprintf ( "%d:%02d", parseInt ( time[0], 10 ), parseInt ( time[1], 10 ) );
                }
            else
                {
                time_string = sprintf ( g_Nouveau_time_sprintf_format, hours, time[1], a );
                };
            };
        
        var duration_array = (in_meeting_object['duration_time'].toString()).split(':');

        duration_array[0] = parseInt ( duration_array, 10 );
        duration_array[1] = parseInt ( duration_array[1], 10 );
        
        var duration_string = '';
        
        if ( (duration_array[0] > 1) && (duration_array[1] > 1) )
            {
            duration_string = sprintf ( g_Nouveau_single_duration_sprintf_format_hrs_mins, duration_array[0], duration_array[1] );
            }
        else if ( (duration_array[0] > 1) && (duration_array[1] == 0) )
            {
            duration_string = sprintf ( g_Nouveau_single_duration_sprintf_format_hrs, duration_array[0] );
            }
        else if ( (duration_array[0] == 1) && (duration_array[1] == 0) )
            {
            duration_string = g_Nouveau_single_duration_sprintf_format_1_hr;
            }
        else if ( (duration_array[0] == 1) && (duration_array[1] > 0) )
            {
            duration_string = sprintf ( g_Nouveau_single_duration_sprintf_format_hr_mins, duration_array[1] );
            }
        else if ( (duration_array[0] == 0) && (duration_array[1] > 0) )
            {
            duration_string = sprintf ( g_Nouveau_single_duration_sprintf_format_mins, duration_array[1] );
            };

        ret = sprintf ( g_Nouveau_single_time_sprintf_format, weekday_string, time_string, duration_string );
        
        return ret;
        };

    /***************************************************************************************
    *############################ INSTANCE CALLBACK FUNCTIONS #############################*
    *                                                                                      *
    * These functions are called for an instance, and have object context.                 *
    ****************************************************************************************/
    /************************************************************************************//**
    *	\brief Responds to the Specify A New Search link being hit.                         *
    ****************************************************************************************/
    this.searchSpecButtonHit = function()
        {
        this.m_search_results_shown = false;
        this.m_current_zoom = this.m_initial_zoom;
        
        this.setDisplayedSearchResults();
        
        if ( ((this.m_current_view == 'map') || (this.m_current_view == 'advanced_map')) && !this.m_search_results_shown )
            {
            this.loadSpecMap();
            };
        };
    
    /************************************************************************************//**
    *	\brief Responds to the Show Search Results link being hit.                          *
    ****************************************************************************************/
    this.searchResultsButtonHit = function()
        {
        this.m_mapResultsDisplayed = true;
        this.m_listResultsDisplayed = true;
        this.m_search_results_shown = true;
        this.setMapResultsDisclosure();
        this.setListResultsDisclosure();
        this.clearMarkerHighlight();    // Get rid of selected meetings.
        this.setDisplayedSearchResults();
        };

    /************************************************************************************//**
    *	\brief Responds to either of the GO buttons being hit.                              *
    ****************************************************************************************/
    this.goButtonHit = function()
        {
        if ( this.m_location_checkbox.checked && this.m_text_input && this.m_text_input.value && (this.m_text_input.value != this.m_text_input.defaultValue) )
            {
            this.lookupLocation();
            }
        else
            {
            this.beginSearch();
            };
        };

    /************************************************************************************//**
    *	\brief Toggles the state of the Basic/Advanced search spec display.                 *
    ****************************************************************************************/
    this.toggleAdvanced = function()
        {
        switch ( this.m_current_view )   // Vet the class state.
            {
            case 'map':
                this.m_current_view = 'advanced_map';
                this.validateGoButtons();
            break;
        
            case 'advanced_map':
                this.m_current_view = 'map';
            break;
        
            case 'text':
                this.m_current_view = 'advanced_text';
                this.validateGoButtons();
            break;
        
            case 'advanced_text':
                this.m_current_view = 'text';
                this.validateGoButtons();
            break;
            };

        this.setBasicAdvancedSwitch();
        
        if ( ((this.m_current_view == 'map') || (this.m_current_view == 'advanced_map')) && !this.m_search_results_shown )
            {
            this.loadSpecMap();
            };
        };
        
    /************************************************************************************//**
    *	\brief Responds to the Search By Map link being hit.                                *
    ****************************************************************************************/
    this.mapButtonHit = function()
        {
        switch ( this.m_current_view )   // Vet the class state.
            {
            case 'text':
                this.m_current_view = 'map';
            break;
        
            case 'advanced_text':
                this.m_current_view = 'advanced_map';
                this.validateGoButtons();
            break;
            };
            
        this.setMapTextSwitch();

        this.setBasicAdvancedSwitch();
        
        this.loadSpecMap();
        };
        
    /************************************************************************************//**
    *	\brief Responds to the Search By Text button being hit.                             *
    ****************************************************************************************/
    this.textButtonHit = function()
        {
        switch ( this.m_current_view )   // Vet the class state.
            {
            case 'map':
                this.m_current_view = 'text';
                this.validateGoButtons();
            break;
        
            case 'advanced_map':
                this.m_current_view = 'advanced_text';
                this.validateGoButtons();
            break;
            };
        
        this.setMapTextSwitch();
        };
        
    /************************************************************************************//**
    *	\brief Toggles the display state of the Advanced Weekdays section.                  *
    ****************************************************************************************/
    this.toggleWeekdaysDisclosure = function()
        {
        this.m_advanced_weekdays_shown = !this.m_advanced_weekdays_shown;
    
        this.setAdvancedWeekdaysDisclosure();
        };
        
    /************************************************************************************//**
    *	\brief Toggles the display state of the Advanced Formats section.                   *
    ****************************************************************************************/
    this.toggleFormatsDisclosure = function()
        {
        this.m_advanced_formats_shown = !this.m_advanced_formats_shown;
    
        this.setAdvancedFormatsDisclosure();
        };
        
    /************************************************************************************//**
    *	\brief Toggles the display state of the Advanced Service Bodies section.            *
    ****************************************************************************************/
    this.toggleServiceBodiesDisclosure = function()
        {
        this.m_advanced_service_bodies_shown = !this.m_advanced_service_bodies_shown;
    
        this.setAdvancedServiceBodiesDisclosure();
        };
        
    /************************************************************************************//**
    *	\brief This is called when the Display Map Results Disclosure link is hit.          *
    ****************************************************************************************/
    this.displayMapResultsDiscolsureHit = function()
        {
        this.m_mapResultsDisplayed = !this.m_mapResultsDisplayed;
        
        this.setMapResultsDisclosure();
        };
        
    /************************************************************************************//**
    *	\brief This is called when the Display List Results Disclosure link is hit.         *
    ****************************************************************************************/
    this.displayListResultsDiscolsureHit = function()
        {
        this.m_listResultsDisplayed = !this.m_listResultsDisplayed;
        
        this.setListResultsDisclosure();
        };
        
    /************************************************************************************//**
    *	\brief Sets the state of the two GO buttons, as necessary.                          *
    ****************************************************************************************/
    this.validateGoButtons = function()
        {
        var valid = !this.m_geocoder && !((this.m_current_view == 'text') && this.m_location_checkbox.checked && (!this.m_text_input.value || (this.m_text_input.value == this.m_text_input.defaultValue)));
        
        if ( valid )
            {
            this.m_advanced_go_a.className = 'bmlt_nouveau_advanced_go_button_a';
            this.m_advanced_go_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.goButtonHit()' );
            this.m_text_go_a.className = 'bmlt_nouveau_text_go_button_a';
            this.m_text_go_a.setAttribute ( 'href', 'javascript:g_instance_' + this.m_uid + '_js_handler.goButtonHit()' );
            }
        else
            {
            this.m_advanced_go_a.className = 'bmlt_nouveau_advanced_go_button_a bmlt_nouveau_button_disabled';
            this.m_advanced_go_a.removeAttribute ( 'href' );
            this.m_text_go_a.className = 'bmlt_nouveau_text_go_button_a bmlt_nouveau_button_disabled';
            this.m_text_go_a.removeAttribute ( 'href' );
            };
        };
      
    /************************************************************************************//**
    *	\brief This handles the "MORE DETAILS" link for each table row.                     *
    ****************************************************************************************/
    this.detailsButtonHit = function ( in_meeting_id    ///< The ID of the meeting that needs details displayed
                                    )
        {
        var meeting = this.getMeetingObjectFromId ( in_meeting_id );
        this.populateDetailsDiv ( meeting );
        this.displayDetails();
        this.loadDetailsMap ( meeting );
        };
      
    /************************************************************************************//**
    *	\brief This is called to handle a row being rolled over.                            *
    ****************************************************************************************/
    this.handleRowClick = function( in_meeting_id
                                    )
        {
        var marker = this.getMarkerForMeetingId ( in_meeting_id );
        this.m_selected_search_results = 0;
        
        if ( marker )
            {
            this.m_selected_search_results = marker.meeting_obj_array.length;
            if ( marker.meeting_obj_array.length > 1 )
                {
                this.respondToMultiClick ( marker, true );
                }
            else
                {
                this.respondToSingleClick ( marker, true );
                };
            };
        };
  
    /****************************************************************************************
    *##################################### CONSTRUCTOR #####################################*
    ****************************************************************************************/
    
    this.m_uid = in_unique_id;
    this.m_current_view = in_initial_view;
    this.m_current_long = in_initial_long;
    this.m_current_lat = in_initial_lat;
    this.m_initial_zoom = in_initial_zoom;
    this.m_current_zoom = this.m_initial_zoom;
    this.m_distance_units = in_distance_units;
    this.m_theme_dir = in_theme_dir;
    this.m_root_server_uri = in_root_server_uri.replace(/&amp;/g,'&');
    this.m_initial_text = in_initial_text;
    this.m_checked_location = in_checked_location ? true : false;
    this.m_show_location = in_show_location ? true : false;
    this.m_single_meeting_id = in_single_meeting_id;
    this.m_grace_period = in_grace_period;
    
    this.m_advanced_weekdays_shown = false;
    this.m_advanced_formats_shown = false;
    this.m_advanced_service_bodies_shown = false;
    this.m_mapResultsDisplayed = false;
    this.m_listResultsDisplayed = false;
    this.m_results_map_loaded = false;
    this.m_search_results_shown = false;         ///< If this is true, then the results div is displayed.
    this.m_search_radius = g_Nouveau_default_geo_width;
    this.m_default_duration = g_Nouveau_default_duration;

	/// These describe the regular NA meeting icon
	this.m_icon_image_single = new google.maps.MarkerImage ( this.m_theme_dir + "/images/google_map_images/NAMarker.png", new google.maps.Size(22, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	this.m_icon_image_multi = new google.maps.MarkerImage ( this.m_theme_dir + "/images/google_map_images/NAMarkerG.png", new google.maps.Size(22, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	this.m_icon_image_selected = new google.maps.MarkerImage ( this.m_theme_dir + "/images/google_map_images/NAMarkerSel.png", new google.maps.Size(34, 38), new google.maps.Point(0,0), new google.maps.Point(18, 38) );
	this.m_icon_shadow = new google.maps.MarkerImage( this.m_theme_dir + "/images/google_map_images/NAMarkerS.png", new google.maps.Size(43, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	
	/// These describe the "You are here" icon.
	this.m_center_icon_image = new google.maps.MarkerImage ( this.m_theme_dir + "/images/google_map_images/NACenterMarker.png", new google.maps.Size(21, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
	this.m_center_icon_shadow = new google.maps.MarkerImage( this.m_theme_dir + "/images/google_map_images/NACenterMarkerS.png", new google.maps.Size(43, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
    
    this.m_search_sort_key = 'time';             ///< This can be 'time', 'town', 'name', or 'distance'.

    this.m_container_div = document.getElementById ( this.m_uid + '_container' );   ///< This is the main outer container.
    
    switch ( this.m_current_view )   // Vet the class state.
        {
        case 'text':            // These are OK.
        case 'advanced_text':
        break;
        
        case 'advanced':        // These are the same for this implementation
        case 'advanced_map':
            this.m_current_view = 'advanced_map';
        break;
        
        default:    // The default is map. That includes a "server select."
            this.m_current_view = 'map';
        break;
        };
    
    this.buildDOMTree();
    this.displayThrobber();
    this.getFormats();
};

/********************************************************************************************
*								  PUBLIC CLASS FUNCTIONS									*
********************************************************************************************/

/********************************************************************************************
*######################## CONTEXT-ESTABLISHING CALLBACK FUNCTIONS ##########################*
*                                                                                           *
* These functions are called statically, but establish context from an ID passed in.        *
********************************************************************************************/
/****************************************************************************************//**
*	\brief This catches the AJAX response, and fills in the response form.				    *
********************************************************************************************/
	
NouveauMapSearch.prototype.sGeoCallback = function ( in_geocode_response,	///< The JSON object.
                                                     in_uid                 ///< The id (to establish context).
							                        )
	{
    eval ('var context = g_instance_' + in_uid + '_js_handler');
	context.lookupCompleteHandler ( in_geocode_response );
	
	var old_geocoder = context.m_geocoder;

    context.m_geocoder = null;
    
    google.maps.event.removeListener ( old_geocoder );
	};

/****************************************************************************************//**
*	\brief This reacts to a Service body container checkbox being hit.                      *
*          It will either completely select, or completely unselect its members.            *
********************************************************************************************/
NouveauMapSearch.prototype.sServiceBodyContainerCheckHit = function (   in_checkbox_object,
                                                                        in_uid  ///< The unique ID of the object (establishes context).
                                                                    )
    {
    eval ('var context = g_instance_' + in_uid + '_js_handler');
    for ( var c = 0; c < context.m_advanced_service_bodies_checkboxes_array.length; c++ )
        {
        if ( in_checkbox_object.value == context.m_advanced_service_bodies_checkboxes_array[c].parent_service_body_id )
            {
            context.m_advanced_service_bodies_checkboxes_array[c].checked = in_checkbox_object.checked;
            };
        };
    };

/****************************************************************************************//**
*	\brief This reacts to the location checkbox changing. It validates the go buttons.      *
********************************************************************************************/
NouveauMapSearch.prototype.sLocationCheckboxHit = function (    in_uid  ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_uid + '_js_handler');
    context.validateGoButtons();
    context.m_text_input.focus();
    };

/****************************************************************************************//**
*	\brief Will check a text element upon blur, and will fill it with the default string.   *
********************************************************************************************/
NouveauMapSearch.prototype.sCheckTextInputBlur = function ( in_text_element  ///< The text element being evaluated.
                                                            )
    {
    // This funky line creates an object context from the ID passed in.
    // Each object is represented by a dynamically-created global variable, defined by ID, so we access that.
    // 'context' becomes a placeholder for 'this'.
    eval ('var context = g_instance_' + in_text_element.uid + '_js_handler');
        
    if ( in_text_element && in_text_element.value && (in_text_element.value != in_text_element.defaultValue) )
        {
        in_text_element.className = 'bmlt_nouveau_text_input';
        }
    else
        {
        in_text_element.className = 'bmlt_nouveau_text_input_empty';
        in_text_element.value = in_text_element.defaultValue;
        };

    context.validateGoButtons();
    };
	
/****************************************************************************************//**
*	\brief Will test a text element upon keyUp, and may change its appearance.              *
********************************************************************************************/
NouveauMapSearch.prototype.sCheckTextInputKeyUp = function ( in_text_element ///< The text element being evaluated.
                                                            )
    {
    eval ('var context = g_instance_' + in_text_element.uid + '_js_handler');

    if ( in_text_element && in_text_element.value && (in_text_element.value != in_text_element.defaultValue) )
        {
        in_text_element.className = 'bmlt_nouveau_text_input';
        }
    else
        {
        in_text_element.className = 'bmlt_nouveau_text_input_empty';
        };

    context.validateGoButtons();
    };

/****************************************************************************************//**
*	\brief Will test a text element upon focus, and remove any default string.              *
********************************************************************************************/
NouveauMapSearch.prototype.sCheckTextInputFocus = function ( in_text_element ///< The text element being evaluated.
                                                            )
    {
    eval ('var context = g_instance_' + in_text_element.uid + '_js_handler');

    if ( in_text_element.value && (in_text_element.value == in_text_element.defaultValue) )
        {
        in_text_element.value = '';
        };

    context.validateGoButtons();
    };

/****************************************************************************************//**
*	\brief Responds to a click in the map.                                                  *
********************************************************************************************/
NouveauMapSearch.prototype.sMapClicked = function ( in_event,   ///< The map event
                                                    in_id       ///< The unique ID of the object (establishes context).
                                                    )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
	
	// We set the long/lat from the event.
	context.m_current_long = in_event.latLng.lng().toString();
	context.m_current_lat = in_event.latLng.lat().toString();

    if ( context.m_current_view == 'map' ) // If it is a simple map, we go straight to a search.
        {
        context.basicMapClicked();
        }
    else    // Otherwise, we simply move the marker.
        {
        context.advancedMapClicked();
        };
    };

/****************************************************************************************//**
*	\brief Responds to the end of the marker drag in the results map.                       *
********************************************************************************************/
NouveauMapSearch.prototype.sResultsMapDragend = function (  in_event,   ///< The map event
                                                            in_id       ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
	
	// We set the long/lat from the event.
	context.m_current_long = in_event.latLng.lng().toString();
	context.m_current_lat = in_event.latLng.lat().toString();
	
	if ( context.m_main_map )
	    {
        context.m_main_map.setCenter ( in_event.latLng );
        context.m_main_map.setZoom ( context.m_current_zoom );

        if ( context.m_current_view == 'advanced_map' ) // If it is a simple map, we go straight to a search.
            {
            context.advancedMapClicked();
            };
        };
    
    context.m_semaphore_lookup_location_services = true;
    context.basicMapClicked();
    };

/****************************************************************************************//**
*	\brief Responds to the end of the marker drag in the results map.                       *
********************************************************************************************/
NouveauMapSearch.prototype.sAdvancedMapDragEnd = function ( in_event,   ///< The map event
                                                            in_id       ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
	
	// We set the long/lat from the event.
	context.m_current_long = in_event.latLng.lng().toString();
	context.m_current_lat = in_event.latLng.lat().toString();
    context.advancedMapClicked();
    };

/****************************************************************************************//**
*	\brief Responds to a click in the result map (Clears any selected markers).             *
********************************************************************************************/
NouveauMapSearch.prototype.sResultMapClicked = function (   in_event,   ///< The map event
                                                            in_id       ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    context.clearMarkerHighlight();
    };

/****************************************************************************************//**
*	\brief Responds to a click on a blue marker.                                             *
********************************************************************************************/
NouveauMapSearch.prototype.sResultsMarkerClicked = function (   in_marker,          ///< The map marker object
                                                                in_id               ///< The unique ID of the object (establishes context).
                                                                )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    if ( in_marker.meeting_obj_array.length > 1 )
        {
        context.respondToMultiClick ( in_marker, false );
        }
    else
        {
        context.respondToSingleClick ( in_marker, false );
        };
    };

/****************************************************************************************//**
*	\brief Responds to the map's zoom changing.                                             *
********************************************************************************************/
NouveauMapSearch.prototype.sMapZoomChanged = function ( in_event,   ///< The map event
                                                        in_id       ///< The unique ID of the object (establishes context).
                                                        )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    context.redrawResultMapMarkers();
    };

/****************************************************************************************//**
*	\brief Responds to the map's tiles being loaded.                                        *
********************************************************************************************/
NouveauMapSearch.prototype.sMapTilesLoaded = function ( in_id       ///< The unique ID of the object (establishes context).
                                                        )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    context.m_results_map_loaded = true;
    context.redrawResultMapMarkers();
    };


/****************************************************************************************//**
*	\brief This is the AJAX callback from a search request.                                 *
********************************************************************************************/
NouveauMapSearch.prototype.sFormatCallback = function ( in_response_object, ///< The HTTPRequest response object.
                                                        in_id               ///< The unique ID of the object (establishes context).
                                                        )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    if ( context )
        {
        if ( in_response_object.responseText )
            {
            var new_object = null;
            var json_builder = "var new_object = " + in_response_object.responseText + ";";
        
            // This is how you create JSON objects.
            eval ( json_builder );
            context.m_format_descriptions = new_object;
            context.buildDOMTree_Advanced_Formats_Content();
            context.setAdvancedFormatsDisclosure();
            context.getServiceBodies();
            };
        }
    else
        {
        if ( context.m_pre_search_lat && context.m_pre_search_long )
            {
            context.m_current_lat = context.m_pre_search_lat;
            context.m_current_long = context.m_pre_search_long;
            };
        
        alert ( g_Nouveau_no_search_results_text );
        this.hideThrobber();
        };
    
    context.m_pre_search_lat = null;
    context.m_pre_search_long = null;
    };
	
/****************************************************************************************//**
*	\brief This is the AJAX callback from a search request.                                 *
********************************************************************************************/
NouveauMapSearch.prototype.sServiceBodiesCallback = function (  in_response_object, ///< The HTTPRequest response object.
                                                                in_id               ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    if ( context )
        {
        if ( in_response_object.responseText )
            {
            var new_object = null;
            var json_builder = "var new_object = " + in_response_object.responseText + ";";
        
            // This is how you create JSON objects.
            eval ( json_builder );
            context.m_service_bodies = new_object;
            context.buildDOMTree_Advanced_Service_Bodies_Content();
            context.setAdvancedServiceBodiesDisclosure();
            if ( context.m_single_meeting_id )
                {
                context.beginSearch();
                }
            else
                {
                context.hideThrobber();
                if ( context.m_current_view == 'text' || context.m_current_view == 'advanced_text' )
                    {
                    context.m_text_input.focus();  // This just starts them off with the correct focus.
                    };
                };
            };
        }
    else
        {
        if ( context.m_pre_search_lat && context.m_pre_search_long )
            {
            context.m_current_lat = context.m_pre_search_lat;
            context.m_current_long = context.m_pre_search_long;
            };
        
        alert ( g_Nouveau_no_search_results_text );
        this.hideThrobber();
        };
    
    context.m_pre_search_lat = null;
    context.m_pre_search_long = null;
    };
	
/****************************************************************************************//**
*	\brief This is the AJAX callback from a search request.                                 *
********************************************************************************************/
NouveauMapSearch.prototype.sMeetingsCallback = function (   in_response_object, ///< The HTTPRequest response object.
                                                            in_id               ///< The unique ID of the object (establishes context).
                                                            )
    {
    eval ('var context = g_instance_' + in_id + '_js_handler');
    
    if ( context )
        {
        context.m_ajax_request = null;
        context.m_search_results = null;
        context.m_selected_search_results = 0;
        context.hideThrobber();
        
        var text_reply = in_response_object.responseText;
    
        if ( text_reply )
            {
            var json_builder = 'var response_object = ' + text_reply + ';';
        
            // This is how you create JSON objects.
            eval ( json_builder );
        
            if ( response_object.length )
                {
                context.processSearchResults ( response_object );
                }
            else
                {
                if ( context.m_pre_search_lat && context.m_pre_search_long )
                    {
                    context.m_current_lat = context.m_pre_search_lat;
                    context.m_current_long = context.m_pre_search_long;
                    };
                    
                alert ( g_Nouveau_no_search_results_text );
                };
            }
        else
            {
            if ( context.m_pre_search_lat && context.m_pre_search_long )
                {
                context.m_current_lat = context.m_pre_search_lat;
                context.m_current_long = context.m_pre_search_long;
                };
                    
            alert ( g_Nouveau_no_search_results_text );
            };
        }
    else
        {
        if ( context.m_pre_search_lat && context.m_pre_search_long )
            {
            context.m_current_lat = context.m_pre_search_lat;
            context.m_current_long = context.m_pre_search_long;
            };
        
        alert ( g_Nouveau_no_search_results_text );
        };
    
    context.m_pre_search_lat = null;
    context.m_pre_search_long = null;
    };
    
/********************************************************************************************
*	\brief This responds to a row of the table results being clicked.                       *
********************************************************************************************/
NouveauMapSearch.prototype.sRowClick = function (   in_meeting_id,  ///< The meeting ID for the table row.
                                                    in_uid          ///< The UID of the object calling this (establishes context).
                                                    )
    {
    eval ('var context = g_instance_' + in_uid + '_js_handler;' );
    context.handleRowClick ( in_meeting_id );
    };
    
/********************************************************************************************
*	\brief This responds to a row of the table results being clicked.                       *
********************************************************************************************/
NouveauMapSearch.prototype.sRadiusChanged = function (  in_uid          ///< The UID of the object calling this (establishes context).
                                                    )
    {
    eval ('var context = g_instance_' + in_uid + '_js_handler;' );
    context.handleRadiusChange();
    };
  
/****************************************************************************************//**
*	\brief This just traps the enter key for the text entry.                                *
********************************************************************************************/
NouveauMapSearch.prototype.sKeyDown = function (    in_id       ///< The unique ID of the object (establishes context).
                                                )
    {
    if ( event.keyCode == 13 )
        {
        eval ('var context = g_instance_' + in_id + '_js_handler');
        
        if ( context.m_text_go_a.className == 'bmlt_nouveau_text_go_button_a' )
            {
            context.goButtonHit();
            };
        };
    };
  
/********************************************************************************************
*	\brief This responds to a details button in a row of the table results being clicked.   *
********************************************************************************************/
NouveauMapSearch.prototype.sDetailsButtonHit = function (   in_uid,         ///< The UID of the object calling this (establishes context).
                                                            in_meeting_id   ///< The meeting ID for the details.
                                                            )
    {
    eval ('var context = g_instance_' + in_uid + '_js_handler;' );
    context.detailsButtonHit(in_meeting_id);
    };

/********************************************************************************************
*	\brief 
********************************************************************************************/
NouveauMapSearch.prototype.sWhereAmI_CallBack = function (  in_position,
                                                            in_uid
                                                        )
{
    eval ('var context = g_instance_' + in_uid + '_js_handler;' );
    context.handleWhereAmI_CallBack(in_position);
};

/********************************************************************************************
*	\brief 
********************************************************************************************/
NouveauMapSearch.prototype.sWhereAmI_Fail_Final = function (    in_error,   ///< The error that caused the failure.
                                                                in_uid      ///< A unique ID, to establish context.
                                                            )
{
    eval ('var context = g_instance_' + in_uid + '_js_handler;' );
    context.handleWhereAmI_Fail_Final(in_error);
};

/********************************************************************************************
*	\brief Used to sort the search results. Context is established by fetching the          *
*          'context' data member of either of the passed in objects.                        *
*   \returns -1 if a<b, 0 if a==b, and 1 if a>b                                             *
********************************************************************************************/
NouveauMapSearch.prototype.sSortCallback = function( in_obj_a,
                                                     in_obj_b
                                                    )
    {
    eval ('var context = g_instance_' + in_obj_a.uid.toString() + '_js_handler;' );
    
    var ret = 0;
    
    switch ( context.m_search_sort_key )
        {
        case 'distance':
            if ( in_obj_a.distance_in_km < in_obj_b.distance_in_km )
                {
                ret = -1;
                }
            else if ( in_obj_a.distance_in_km > in_obj_b.distance_in_km )
                {
                ret = 1;
                };
        
        // We try the town, next (Very doubtful this will ever happen).
        
        case 'town':
            if ( ret == 0 )
                {
                var a_nation = in_obj_a.location_province.replace(/\s/g, "").toLowerCase();
                var a_state = in_obj_a.location_province.replace(/\s/g, "").toLowerCase();
                var a_county = in_obj_a.location_sub_province.replace(/\s/g, "").toLowerCase();
                var a_town = in_obj_a.location_municipality.replace(/\s/g, "").toLowerCase();
                var a_borough = in_obj_a.location_city_subsection.replace(/\s/g, "").toLowerCase();
            
                var b_nation = in_obj_b.location_province.replace(/\s/g, "").toLowerCase();
                var b_state = in_obj_b.location_province.replace(/\s/g, "").toLowerCase();
                var b_county = in_obj_b.location_sub_province.replace(/\s/g, "").toLowerCase();
                var b_town = in_obj_b.location_municipality.replace(/\s/g, "").toLowerCase();
                var b_borough = in_obj_b.location_city_subsection.replace(/\s/g, "").toLowerCase();
            
                // We bubble down through the various levels of location.
                // One of the participants being missing prevents comparison.
                if ( a_nation && b_nation )
                    {
                    if ( a_nation < b_nation )
                        {
                        ret = -1;
                        }
                    else if ( a_nation > b_nation )
                        {
                        ret = 1;
                        };
                    };
                
                if ( ret == 0 && a_state && b_state )
                    {
                    if ( a_state < b_state )
                        {
                        ret = -1;
                        }
                    else if ( a_state > b_state )
                        {
                        ret = 1;
                        };
                    };
                
                if ( ret == 0 && a_county && b_county )
                    {
                    if ( a_county < b_county )
                        {
                        ret = -1;
                        }
                    else if ( a_county > b_county )
                        {
                        ret = 1;
                        };
                    };
                
                if ( ret == 0 && a_town && b_town )
                    {
                    if ( a_town < b_town )
                        {
                        ret = -1;
                        }
                    else if ( a_town > b_town )
                        {
                        ret = 1;
                        };
                    };
                
                if ( ret == 0 && a_borough && b_borough )
                    {
                    if ( a_borough < b_borough )
                        {
                        ret = -1;
                        }
                    else if ( a_borough > b_borough )
                        {
                        ret = 1;
                        };
                    };
                };
            
        // We sort by time for the same town.
        
        default:    // 'time' is default
            if ( ret == 0 )
                {
                var weekday_score_a = parseInt ( in_obj_a.weekday_tinyint, 10 );
                var weekday_score_b = parseInt ( in_obj_b.weekday_tinyint, 10 );
                
                if ( weekday_score_a < g_Nouveau_start_week )
                    {
                    weekday_score_a += 7;
                    }
                
                if ( weekday_score_b < g_Nouveau_start_week )
                    {
                    weekday_score_a += 7;
                    }
                    
                if ( weekday_score_a < weekday_score_b )
                    {
                    ret = -1;
                    }
                else if ( weekday_score_a > weekday_score_b )
                    {
                    ret = 1;
                    }
                else
                    {
                    var time_a = parseInt ( (in_obj_a.start_time.toString().replace(/[\s:]/g, "")), 10);
                    var time_b = parseInt ( (in_obj_b.start_time.toString().replace(/[\s:]/g, "")), 10);
                
                    if ( time_a < time_b )
                        {
                        ret = -1;
                        }
                    else if ( time_a > time_b )
                        {
                        ret = 1;
                        };
                    };
                };
        
        // And finally, by meeting name.
                
        case 'name':
            if ( ret == 0 )
                {
                var a_name = in_obj_a.meeting_name.replace(/\s/g, "").toLowerCase();
                var b_name = in_obj_b.meeting_name.replace(/\s/g, "").toLowerCase();
            
                if ( a_name < b_name )
                    {
                    ret = -1;
                    }
                else if ( a_name > b_name )
                    {
                    ret = 1;
                    };
                };
        break;
        };
        
    return ret;
    };

/********************************************************************************************
*############################ SIMPLE STATIC UTILITY FUNCTIONS ##############################*
*                                                                                           *
* These functions are called statically, and have no need for object context.               *
********************************************************************************************/
	
/****************************************************************************************//**
*	\brief	This returns an array, mapping out markers that overlap.					    *
*																						    *
*	\returns An array of arrays. Each array element is an array with n >= 1 elements, each  *
*	of which is a meeting object. Each of the array elements corresponds to a single        *
*	marker, and all the objects in that element's array will be covered by that one marker. *
*	The returned sub-arrays will be sorted in order of ascending weekday.	                *
********************************************************************************************/
	
NouveauMapSearch.prototype.sMapOverlappingMarkers = function (  in_meeting_array,	///< Used to draw the markers when done.
	                                                            in_map_object       ///< The map instance to use.
									                        )
    {
    var tolerance = g_Nouveau_default_marker_aggregation_threshold_in_pixels;	/* This is how many pixels we allow. */
    var tmp = new Array;
    
    for ( var c = 0; c < in_meeting_array.length; c++ )
        {
        tmp[c] = new Object;
        tmp[c].matched = false;
        tmp[c].matches = null;
        tmp[c].object = in_meeting_array[c];
        tmp[c].coords = NouveauMapSearch.prototype.sFromLatLngToPixel ( new google.maps.LatLng ( tmp[c].object.latitude, tmp[c].object.longitude ), in_map_object );
        };
    
    for ( var c = 0; c < in_meeting_array.length; c++ )
        {
        if ( false == tmp[c].matched )
            {
            tmp[c].matched = true;
            tmp[c].matches = new Array;
            tmp[c].matches[0] = tmp[c].object;

            for ( var c2 = 0; c2 < in_meeting_array.length; c2++ )
                {
                if ( false == tmp[c2].matched && tmp[c] && tmp[c2] )
                    {
                    var outer_coords = tmp[c].coords;
                    var inner_coords = tmp[c2].coords;
                    
                    if ( outer_coords && inner_coords )
                        {
                        var xmin = outer_coords.x - tolerance;
                        var xmax = outer_coords.x + tolerance;
                        var ymin = outer_coords.y - tolerance;
                        var ymax = outer_coords.y + tolerance;
                    
                        /* We have an overlap. */
                        if ( (inner_coords.x >= xmin) && (inner_coords.x <= xmax) && (inner_coords.y >= ymin) && (inner_coords.y <= ymax) )
                            {
                            tmp[c].matches[tmp[c].matches.length] = tmp[c2].object;
                            tmp[c2].matched = true;
                            };
                        };
                    };
                };
            };
        };

    var ret = new Array;
    
    for ( var c = 0; c < tmp.length; c++ )
        {
        if ( tmp[c].matches )
            {
            tmp[c].matches.sort ( function(a,b){return a.weekday_tinyint-b.weekday_tinyint});
            ret[ret.length] = tmp[c].matches;
            };
        };
    
    return ret;
    };
    
/****************************************************************************************//**
*	\brief This takes a latitude/longitude location, and returns an x/y pixel location for  *
*	it.																				        *
*																						    *
*	\returns a Google Maps API V3 Point, with the pixel coordinates (top, left origin).	    *
********************************************************************************************/
    
NouveauMapSearch.prototype.sFromLatLngToPixel = function (  in_Latng,
                                                            in_map_object
                                                        )
    {
    var	ret = null;
    
    if ( in_map_object )
        {
        // We measure the container div element.
        var	div = in_map_object.getDiv();
    
        if ( div )
            {
            var	pixel_width = div.offsetWidth;
            var	pixel_height = div.offsetHeight;
            var	lat_lng_bounds = in_map_object.getBounds();
            if ( lat_lng_bounds )
                {
                var north_west_corner = new google.maps.LatLng ( lat_lng_bounds.getNorthEast().lat(), lat_lng_bounds.getSouthWest().lng() );
                var lng_width = lat_lng_bounds.getNorthEast().lng()-lat_lng_bounds.getSouthWest().lng();
                var	lat_height = lat_lng_bounds.getNorthEast().lat()-lat_lng_bounds.getSouthWest().lat();
        
                // We do this, so we have the largest values possible, to get the most accuracy.
                var	pixels_per_degree = (( pixel_width > pixel_height ) ? (pixel_width / lng_width) : (pixel_height / lat_height));
        
                // Figure out the offsets, in long/lat degrees.
                var	offset_vert = north_west_corner.lat() - in_Latng.lat();
                var	offset_horiz = in_Latng.lng() - north_west_corner.lng();
        
                ret = new google.maps.Point ( Math.round(offset_horiz * pixels_per_degree),  Math.round(offset_vert * pixels_per_degree) );
                };
            };
        };

    return ret;
    };
