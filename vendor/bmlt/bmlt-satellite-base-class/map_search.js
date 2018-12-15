/****************************************************************************************//**
* \file map_search.js																        *
* \brief Javascript functions for the new map search implementation.                        *
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

/****************************************************************************************//**
*	\brief  This class governs the display of one APIV3 map search instance. It plays games *
*           with dynamic DOM construction and complex IDs, because it is designed to allow  *
*           multiple instances on a page.                                                   *
********************************************************************************************/

function MapSearch (
                    in_unique_id,
                    in_settings_id,
                    in_div,
                    in_coords
                    )
{
	/****************************************************************************************
	*										CLASS VARIABLES									*
	****************************************************************************************/
	
	var	g_main_map = null;				///< This will hold the Google Map object.
	var	g_allMarkers = [];				///< Holds all the markers.
	var g_main_id = in_unique_id;
    var g_search_radius = null;
    var g_AJAX_Request = null;
    var g_initial_call = false;         ///< Set to true, once we have zoomed in.
    var g_initial_coords = in_coords;
    var g_initial_div = in_div;
    var g_info_id = null;
    
    /// These allow us to customize the content a bit.
    /// These are "hooks," and will only work on a page where there is only one instance of the map search.
    var g_searchbox_a = null;   ///< The ID of the disclosure link for the search spec
    var g_searchbox_f = null;   ///< The ID of the search spec fieldset
    var g_options_a = null;    ///< The ID of the disclosure link for the weekdays and formats
    var g_options_d = null;    ///< The ID of the div for the weekdays and formats
    var g_weekdays_f = null;    ///< The ID of the fieldset for the weekdays.
    var g_formats_f = null;     ///< The ID of the fieldset for the formats.

	/// These describe the regular NA meeting icon
	var g_icon_image_single = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NAMarker.png", new google.maps.Size(23, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_image_multi = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NAMarkerG.png", new google.maps.Size(23, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_image_selected = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NAMarkerSel.png", new google.maps.Size(23, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_shadow = new google.maps.MarkerImage( c_g_BMLTPlugin_images+"/NAMarkerS.png", new google.maps.Size(43, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_shape = { coord: [16,0,18,1,19,2,20,3,21,4,21,5,22,6,22,7,22,8,22,9,22,10,22,11,22,12,22,13,22,14,22,15,22,16,21,17,21,18,22,19,20,20,19,21,20,22,18,23,17,24,18,25,17,26,15,27,14,28,15,29,12,30,12,31,10,31,10,30,9,29,8,28,8,27,7,26,6,25,5,24,5,23,4,22,3,21,3,20,2,19,1,18,1,17,1,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,1,6,1,5,2,4,2,3,3,2,5,1,6,0,16,0], type: 'poly' };
	
	/// These describe the "You are here" icon.
	var g_center_icon_image = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NACenterMarker.png", new google.maps.Size(21, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
	var g_center_icon_shadow = new google.maps.MarkerImage( c_g_BMLTPlugin_images+"/NACenterMarkerS.png", new google.maps.Size(43, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
	var g_center_icon_shape = { coord: [16,0,18,1,19,2,19,3,20,4,20,5,20,6,20,7,20,8,20,9,20,10,20,11,19,12,17,13,16,14,16,15,15,16,15,17,14,18,14,19,13,20,13,21,13,22,13,23,12,24,12,25,12,26,12,27,11,28,11,29,11,30,11,31,11,32,11,33,11,34,11,35,10,35,10,34,9,33,9,32,9,31,9,30,9,29,9,28,8,27,8,26,8,25,8,24,8,23,7,22,7,21,7,20,6,19,6,18,5,17,5,16,4,15,4,14,3,13,1,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,0,4,1,3,1,2,3,1,4,0,16,0], type: 'poly' };

    /// These comprise a search state that will be used to filter searches.
    var g_basic_options_open = false;                   ///< This is set to true if the basic options box is visible. If it is, then its options will be considered. If not, then they will be ignored.
        
	/****************************************************************************************
	*									GOOGLE MAPS STUFF									*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief Load the map and set it up.													*
	****************************************************************************************/
	
	function load_map ( in_div, in_location_coords )
	{
        var g_main_div = in_div;
        
        if ( g_main_div )
            {
            g_main_div.myThrobber = null;
            
            if ( in_location_coords )
                {
                var myOptions = {
                                    'center': new google.maps.LatLng ( in_location_coords.latitude, in_location_coords.longitude ),
                                    'zoom': in_location_coords.zoom,
                                    'mapTypeId': google.maps.MapTypeId.ROADMAP,
                                    'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
                                    'zoomControl': true,
                                    'mapTypeControl': true,
                                    'disableDoubleClickZoom' : true,
                                    'draggableCursor': "pointer",
                                    'scaleControl' : true
                                };
    
                var	pixel_width = in_div.offsetWidth;
                var	pixel_height = in_div.offsetHeight;
                
                if ( (pixel_width < 640) || (pixel_height < 640) )
                    {
                    myOptions.scrollwheel = true;
                    myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.SMALL };
                    }
                else
                    {
                    myOptions.zoomControlOptions = { 'style': google.maps.ZoomControlStyle.LARGE };
                    };
                    
                g_main_map = new google.maps.Map ( g_main_div, myOptions );
                };
            
            if ( g_main_map )
                {
                g_main_map.response_object = null;
                g_main_map.center_marker = null;
                g_main_map.geo_width = null;
                g_main_map.uid = g_main_div.id+'-MAP';
                google.maps.event.addListener ( g_main_map, 'click', map_clicked );
                create_throbber ( g_main_div );
                    
                // Options for circle overlay object

                var circle_options =   {
                                'center': g_main_map.getCenter(),
                                'fillColor': "#999",
                                'radius':1000,
                                'fillOpacity': 0.25,
                                'strokeOpacity': 0.0,
                                'map': null,
                                'clickable': false
                                };

                g_main_map._circle_overlay = new google.maps.Circle(circle_options);
                };
            };
	};
	
	/************************************************************************************//**
	*	\brief 
	****************************************************************************************/
	
	function create_throbber ( in_div    ///< The container div for the throbber.
	                        )
	{
	    if ( !g_main_map.myThrobber )
	        {
            g_main_map.myThrobber = document.createElement("div");
            if ( g_main_map.myThrobber )
                {
                g_main_map.myThrobber.id = in_div.id+'_throbber_div';
                g_main_map.myThrobber.className = 'bmlt_map_throbber_div';
                g_main_map.myThrobber.style.display = 'none';
                in_div.appendChild ( g_main_map.myThrobber );
                var img = document.createElement("img");
                
                if ( img )
                    {
	                // We construct a variable name that uses our unique ID.
                    eval ( 'var srcval = c_g_BMLTPlugin_throbber_img_src_'+g_main_id+';' );
                    img.src = srcval;
                    img.className = 'bmlt_map_throbber_img';
                    img.id = in_div.id+'_throbber_img';
                    img.alt = 'AJAX Throbber';
                    g_main_map.myThrobber.appendChild ( img );
                    }
                else
                    {
                    in_div.myThrobber = null;
                    };
                };
            };
        };
        
	/************************************************************************************//**
	*	\brief 
	****************************************************************************************/
	
	function show_throbber()
	{
	    if ( g_main_map.myThrobber )
	        {
	        g_main_map.myThrobber.style.display = 'block';
	        };
    };
        
	/************************************************************************************//**
	*	\brief 
	****************************************************************************************/
	
	function hide_throbber()
	{
	    if ( g_main_map.myThrobber )
	        {
	        g_main_map.myThrobber.style.display = 'none';
	        };
    };
    
	/************************************************************************************//**
	*	\brief Respond to initial map click.                                                *
	****************************************************************************************/
	
	function map_clicked ( in_event ///< The mouse event that caused the click.
	                        )
	{
	    show_throbber();
	    clearAllMarkers();
	    g_main_map.response_object = null;
	    
	    // We construct a variable name that uses our unique ID. This will determine what units we use.
        eval ( 'var dist_r_km = c_g_distance_units_are_km_'+g_main_id+';' );
        
        var geo_width = (null == g_main_map.geo_width) ? -10 : (g_main_map.geo_width / (( dist_r_km ) ? 1.0 : 1.609344 ));
        
        if ( document.getElementById ( g_main_id+'_radius_select' ).selectedIndex == 0 ) // We do another auto select if that item is selected.
            {
            geo_width = -10;
            g_search_radius = null;
            g_main_map.geo_width = null;
            if ( g_main_map.zoom_handler )
                {
                google.maps.event.removeListener ( g_main_map.zoom_handler );
                g_main_map.zoom_handler = null;
                };
            }
    
	    var args = 'geo_width='+geo_width+'&long_val='+in_event.latLng.lng().toString()+'&lat_val='+in_event.latLng.lat().toString();
        if ( g_basic_options_open )
            {
            args += readWeekdayCheckBoxes();
            args += readFormatCheckBoxes();
            };

	    g_main_map.g_location_coords = in_event.latLng;
	    if ( in_event.panTo )
	        {
	        g_main_map.panTo ( in_event.latLng );
	        };

	    call_root_server ( args );
	};
	
	/************************************************************************************//**
	*	\brief  Does an AJAX call for a JSON response, based on the given criteria and      *
	*           callback function.                                                          *
	*           The callback will be a function in the following format:                    *
	*               function ajax_callback ( in_json_obj )                                  *
	*           where "in_json_obj" is the response, converted to a JSON object.            *
	*           it will be null if the function failed.                                     *
	****************************************************************************************/
	
	function call_root_server ( in_args
	                            )
	{
	    if ( g_AJAX_Request )   // This prevents the requests from piling up. We are single-threaded.
	        {
	        g_AJAX_Request.abort();
	        g_AJAX_Request = null;
	        };
	    
	    // We construct a variable name that uses our unique ID.
	    eval ( 'var url = c_g_BMLTRoot_URI_JSON_SearchResults_'+g_main_id+'+\'&\'+in_args;' );
	    
        g_AJAX_Request = BMLTPlugin_AjaxRequest ( url, bmlt_ajax_router, 'get' );
	};
	
	/************************************************************************************//**
	*	\brief  This routes the AJAX response to the correct function, and will display any *
	*           error alerts.                                                               *
	****************************************************************************************/
	
	function bmlt_ajax_router ( in_response_object,
	                            in_extra
	                            )
	{
	    g_AJAX_Request = null;  // Make sure we're done, here.
	    
		var text_reply = in_response_object.responseText;
		
		if ( text_reply )
			{
	        var json_builder = 'var response_object = '+text_reply+';';
	        
	        // This is how you create JSON objects.
            eval ( json_builder );
            
            if ( response_object )
                {
                if ( !g_main_map.response_object )
                    {
                    g_main_map.response_object = response_object;
                    search_response_callback();
	                hide_throbber();
                   }
                else
                    {
	                hide_throbber();
	                alert ( c_g_server_error );
                    };
                }
            else
                {
	            hide_throbber();
                alert ( c_g_server_error );
                };
	        }
	    else
	        {
            g_main_map.geo_width = null;
            g_search_radius = null;
            g_main_map.response_object = null;
            if ( g_main_map.zoom_handler )
                {
                google.maps.event.removeListener ( g_main_map.zoom_handler );
                g_main_map.zoom_handler = null;
                };
	        hide_throbber();
	        };
	};
	
	/************************************************************************************//**
	*	\brief 
	****************************************************************************************/
	
	function search_response_callback()
	{
	    if ( !g_main_map.response_object.length )
	        {
	        alert ( g_no_meetings_found );
	        return;
	        };
	        
		if ( !g_allMarkers.length )
			{
			if ( g_main_map.response_object.length && !g_main_map.zoom_handler )
			    {
	            fit_markers();
	            g_initial_call = true;
                g_main_map.zoom_handler = google.maps.event.addListener ( g_main_map, 'zoom_changed', search_response_callback );
	            };
			};
		
		if ( fit_circle() )
		    {
            draw_markers();
            
            if ( g_main_map.center_marker )
                {
                g_main_map._circle_overlay.bindTo('center', g_main_map.center_marker, 'position');
                g_main_map._circle_overlay.setMap ( g_main_map );
                showNewSearch();
                };
            };
	};
	
	/************************************************************************************//**
	*	\brief Determine the zoom level necessary to show all the markers in the viewport.	*
	****************************************************************************************/
	
	function fit_markers()
	{
		var bounds = new google.maps.LatLngBounds();
		
		// We go through all the results, and get the "spread" from them.
		for ( var c = 0; c < g_main_map.response_object.length; c++ )
			{
			var	lat = g_main_map.response_object[c].latitude;
			var	lng = g_main_map.response_object[c].longitude;
			// We will set our minimum and maximum bounds.
			bounds.extend ( new google.maps.LatLng ( lat, lng ) );
			};
	
		bounds.extend ( g_main_map.g_location_coords );
		
		// We now have the full rectangle of our meeting search results. Scale the map to fit them.
		g_main_map.fitBounds ( bounds );
	};
	
	/************************************************************************************//**
	*	\brief This calculates a circle that will fit all of the meetings found.            *
	*                                                                                       *
	*   \returns    A boolean. True if the circle was successfully calculated.              *
	****************************************************************************************/
	function fit_circle()
	{
	    var ret = false;
	    if ( g_main_map._circle_overlay )
	        {
	        var search_radius = g_search_radius;
	        
            if ( !search_radius )
                {
                search_radius = 0.0;
                // What we do here, is look at each meeting, and determine a circle that just fits them all.
                for ( var c = 0; c < g_main_map.response_object.length; c++ )
                    {
                    var meeting_object = g_main_map.response_object[c];
                    var distance = google.maps.geometry.spherical.computeDistanceBetween ( g_main_map.g_location_coords, new google.maps.LatLng(meeting_object.latitude, meeting_object.longitude) );
                    search_radius = Math.max ( distance, search_radius );
                    };
                    
                eval ( 'var dist_r_km = c_g_distance_units_are_km_'+g_main_id+';' );
                // We only allow a maximum of 150% the highest value, so we don't have ginormous circles.
                if ( search_radius > (c_g_diameter_choices[c_g_diameter_choices.length-1] * (dist_r_km ? 2000 : 2414.016)) )
                    {
                    search_radius = null;
                    };
                };
	    
	        if ( search_radius )
	            {
                g_search_radius = search_radius;
                g_main_map._circle_overlay.setRadius ( g_search_radius );
                g_main_map._circle_overlay.setCenter ( g_main_map.g_location_coords );
                g_main_map.geo_width = g_search_radius / 1000.0;
                ret = true;
                };
            };
        
        return ret;
    };
	
	/************************************************************************************//**
	*	\brief Start dragging the center marker (clears all the markers).                   *
	****************************************************************************************/
	function center_dragStart ( in_event )
	{
	    clearAllMarkers();
	    
	    return true;
	};
	
	/************************************************************************************//**
	*	\brief The drag of the center marker has stopped. Recalculate the search.           *
	****************************************************************************************/
	function center_dragEnd ( in_event )
	{
	    map_clicked (in_event);
	    
	    return true;
	};
    	
	/****************************************************************************************
	*									CREATING MARKERS									*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief Remove all the markers.														*
	****************************************************************************************/
	function clearAllMarkers ( )
	{
		if ( g_allMarkers )
			{
			if ( g_main_map.center_marker && g_main_map.center_marker.info_win_ )
			    {
			    g_main_map.center_marker.info_win_.close();
			    g_main_map.center_marker.info_win_ = null;
			    };
			
			for ( var c = 0; c < g_allMarkers.length; c++ )
				{
				if ( g_allMarkers[c].info_win_ )
				    {
				    g_allMarkers[c].info_win_.close();
				    g_allMarkers[c].info_win_ = null;
				    };
				
				g_allMarkers[c].setMap( null );
				g_allMarkers[c] = null;
				};
			
			g_allMarkers.length = 0;
			};
		
		g_info_id = null;
	};
	
	/************************************************************************************//**
	*	\brief Calculate and draw the markers.												*
	****************************************************************************************/
	
	function draw_markers()
	{
	    clearAllMarkers();
	    
		// This calculates which markers are the red "multi" markers.
		var overlap_map = mapOverlappingMarkers ( g_main_map.response_object );
		
		// Draw the meeting markers.
		for ( var c = 0; c < overlap_map.length; c++ )
			{
			createMapMarker ( overlap_map[c] );
			};
		
		if ( g_initial_call )
		    {
            // Finish with the main (You are here) marker.
            createMarker ( g_main_map.g_location_coords, g_center_icon_shadow, g_center_icon_image, g_center_icon_shape, marker_make_centerHTML(), true, 0 );
            
            google.maps.event.addListener ( g_main_map.center_marker, 'dragstart', center_dragStart );
            google.maps.event.addListener ( g_main_map.center_marker, 'dragend', center_dragEnd );
            };
	};
	
	/************************************************************************************//**
	*	\brief	This returns an array, mapping out markers that overlap.					*
	*																						*
	*	\returns An array of arrays. Each array element is an array with n >= 1 elements,	*
	*	each of which is a meeting object. Each of the array elements corresponds to a		*
	*	single marker, and all the objects in that element's array will be covered by that	*
	*	one marker. The returned sub-arrays will be sorted in order of ascending weekday.	*
	****************************************************************************************/
	
	function mapOverlappingMarkers (in_meeting_array	///< Used to draw the markers when done.
									)
	{
		var tolerance = 8;	/* This is how many pixels we allow. */
		var tmp = new Array;
		
		for ( var c = 0; c < in_meeting_array.length; c++ )
			{
			tmp[c] = new Object;
			tmp[c].matched = false;
			tmp[c].matches = null;
			tmp[c].object = in_meeting_array[c];
			tmp[c].coords = fromLatLngToPixel ( new google.maps.LatLng ( tmp[c].object.latitude, tmp[c].object.longitude ), g_main_map );
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
					if ( false == tmp[c2].matched )
						{
						var outer_coords = tmp[c].coords;
						var inner_coords = tmp[c2].coords;
						
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
	
		var ret = new Array;
		
		for ( var c = 0; c < tmp.length; c++ )
			{
			if ( tmp[c].matches )
				{
				tmp[c].matches.sort ( sortMeetingSearchResponseCallback );
				ret[ret.length] = tmp[c].matches;
				};
			};
		
		return ret;
	};
	
	/************************************************************************************//**
	*	 \brief	Callback used to sort the meeting response by weekday.                      *
	*    \returns 1 if a>b, -1 if a<b or 0 if they are equal.                               *
	****************************************************************************************/
	function sortMeetingSearchResponseCallback (    in_mtg_obj_a,   ///< Meeting object A
	                                                in_mtg_obj_b    ///< Meeting Object B
	                                            )
	{
	    var ret = 0;
        var weekday_score_a = parseInt ( in_mtg_obj_a.weekday_tinyint, 10 );
        var weekday_score_b = parseInt ( in_mtg_obj_b.weekday_tinyint, 10 );
        
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
            };
        
        return ret;
	};
	
	/************************************************************************************//**
	*	 \brief	This creates a single meeting's marker on the map.							*
	****************************************************************************************/
	
	function createMapMarker (	in_mtg_obj_array	/**< A meeting object array. */
								)
	{
		var main_point = new google.maps.LatLng ( in_mtg_obj_array[0].latitude, in_mtg_obj_array[0].longitude );
		
		var	marker_html = '<div class="meeting_info_window_contents_div';
		
		if ( in_mtg_obj_array.length > 1 )
			{
			marker_html += '_multi">';
			var included_weekdays = [];
			
			for ( var c = 0; c < in_mtg_obj_array.length; c++ )
				{
				var already_there = false;
				for ( var c2 = 0; c2 < included_weekdays.length; c2++ )
					{
					if ( included_weekdays[c2] == in_mtg_obj_array[c].weekday_tinyint )
						{
						already_there = true;
						break;
						};
					};
				
				if ( !already_there )
					{
					included_weekdays[included_weekdays.length] = in_mtg_obj_array[c].weekday_tinyint;
					};
				};
			
			marker_html += '<div class="multi_day_info_div">';
			
			var meeting_id = in_mtg_obj_array[0].id_bigint.toString();
			
			if ( included_weekdays.length > 1 )
				{
				marker_html += '<div id="wd_'+g_main_map.uid+'_'+meeting_id+'_div" class="bmlt_day_tabs"><ul class="wd_info_win_ul">';
				
				for ( var wd = 1; wd < 8; wd++ )
					{
					var weekday_int = (parseInt ( wd ) - 1) + g_Nouveau_start_week;
					if ( weekday_int > 7 )
					    {
					    weekday_int -= 7;
					    };
					
					for ( var c = 0; c < included_weekdays.length; c++ )
						{
						if ( included_weekdays[c] == weekday_int )
							{
							marker_html += '<li id="'+g_main_map.uid+'_'+meeting_id+'_'+included_weekdays[c].toString()+'_li" class="'+((c == 0) ? 'bmlt_selected_weekday_info' : 'bmlt_unselected_weekday_info')+'">';
							marker_html += '<a class="bmlt_info_win_day_a" href="javascript:expose_weekday(document.getElementById(\'wd_'+g_main_map.uid+'_'+meeting_id+'_div\'),'+included_weekdays[c].toString()+',\''+meeting_id+'\',\''+g_main_map.uid+'\')">'+c_g_weekdays_short[included_weekdays[c]]+'</a></li>';
							};
						};
					};
				marker_html += '<li style="float:none;clear:both"></li></ul></div>';
				}
			else
				{
				marker_html += '<strong>'+c_g_weekdays[included_weekdays[0]]+'</strong>';
				};
			
			var	first = true;
			for ( var wd = 1; wd < 8; wd++ )
				{
				marker_internal_html = '';
				var	meetings_html = [];
				for ( var c = 0; c < in_mtg_obj_array.length; c++ )
					{
					if ( in_mtg_obj_array[c].weekday_tinyint == wd )
						{
						meetings_html[meetings_html.length] = marker_make_meeting ( in_mtg_obj_array[c] );
						};
					};
				
				if ( meetings_html.length )
					{
					marker_internal_html += '<div class="marker_div_weekday" id="marker_info_weekday_'+g_main_map.uid+'_'+meeting_id+'_'+wd.toString()+'_div" style="display:';
					if ( first )
						{
						marker_internal_html += 'block'; 
						first = false;
						}
					else
						{
						marker_internal_html += 'none'; 
						};
						
					marker_internal_html += '">';
					for ( var c2 = 0; c2 < meetings_html.length; c2++ )
						{
						if ( c2 > 0 )
							{
							marker_internal_html += '<hr class="meeting_divider_hr" />';
							};
						marker_internal_html += meetings_html[c2];
						};
					marker_internal_html += '</div>';
					marker_html += marker_internal_html;
					};
				};
			marker_html += '</div>';
			}
		else
			{
			marker_html += '">';
			marker_html += marker_make_meeting ( in_mtg_obj_array[0], c_g_weekdays[in_mtg_obj_array[0].weekday_tinyint] );
			};
		
		marker_html += '</div>';
		var marker = createMarker ( main_point, g_icon_shadow, ((in_mtg_obj_array.length>1) ? g_icon_image_multi : g_icon_image_single), g_icon_shape, marker_html, false, in_mtg_obj_array[0].id_bigint );
	};
	
	
	/************************************************************************************//**
	*	\brief Expose the weekday selected by the tab.
	****************************************************************************************/
	expose_weekday = function ( in_container,
	                            in_wd,
	                            in_id,
	                            in_main_id
	                            )
	{
        for ( var wd = 1; wd < 8; wd++ )
            {
            var li = document.getElementById(in_main_id+'_'+in_id+'_'+wd.toString()+'_li');
            var elem = document.getElementById('marker_info_weekday_'+in_main_id+'_'+in_id+'_'+wd.toString()+'_div');
            
            if ( elem && li )
                {
                elem.style.display = ( wd == in_wd ) ? 'block' : 'none';
                li.className = ( wd == in_wd ) ? 'bmlt_selected_weekday_info' : 'bmlt_unselected_weekday_info'
                };
            };
	};
	
	/************************************************************************************//**
	*	\brief Responds to the new circle radius popup being changed.                       *
	****************************************************************************************/
	
	change_circle_diameter = function(  in_from_ext ///< If true, then we will use the value of the location options popup instead of the map center one.
	                                    )
	{
	    // We construct a variable name that uses our unique ID.
        eval ( 'var dist_r_km = c_g_distance_units_are_km_'+g_main_id+';' );

        if ( in_from_ext )
            {
            if ( document.getElementById ( g_main_id+'_radius_select' ).value ) // It's possible that we have selected a null entry.
                {
                g_search_radius = document.getElementById ( g_main_id+'_radius_select' ).value * (dist_r_km ? 1000 : 1609.344);
                }
            else
                {
                g_search_radius = null;
                g_main_map.geo_width = null;
                return;
                };
            }
        else
            {
            g_search_radius = document.getElementById ( g_main_id+'_bmlt_center_marker_select' ).value * (dist_r_km ? 500 : 804.672);
            var elem = document.getElementById ( g_main_id+'_radius_select' );
            
            if ( elem )
                {
                var old_onChange = elem.onChange; // We do this to prevent the handler from being called as we change the value.
                elem.onChange = null;
                
                elem.selectedIndex = 1;

                for ( var c = 2; c < elem.options.length; c++ )
                    {
                    var comp = elem.options[c].value * (dist_r_km ? 1000 : 1609.344);

                    if ( comp == g_search_radius )
                        {
                        elem.selectedIndex = c;
                        break;
                        };
                    };
                
                elem.onChange = old_onChange;
                };
            };
        
        if ( fit_circle() )
            {
            clearAllMarkers();
                
            g_main_map._circle_overlay.bindTo('center', g_main_map.center_marker, 'position');
            g_main_map._circle_overlay.setMap ( g_main_map );
            
            map_clicked ( {'latLng':g_main_map.g_location_coords} );
            };
	};
	
	/************************************************************************************//**
	*	\brief Return the HTML for the center marker info window.							*
	*																						*
	*	\returns the XHTML for the center marker.											*
	****************************************************************************************/
	
	function marker_make_centerHTML ()
	{
	    // We construct a variable name that uses our unique ID.
        eval ( 'var dist_r_km = c_g_distance_units_are_km_'+g_main_id+';' );

		var ret = '<div class="marker_div_meeting marker_info_center">';
		    
		    ret += '<div class="center_marker_desc">'+c_g_distance_center_marker_desc+'</div>';
		    
		    // This strange formula, is because we want to round to a couple of decimal places, and we also want to multiply by 2 (turn a radius into a diameter).
		    // Folks are more able to identify with diameter, so that's how we present it.
            var about = Math.round ( (g_main_map.geo_width / (dist_r_km?1.0:1.609344)) * 2000 ) / 1000;
            
            ret += '<label for="'+g_main_id+'_bmlt_center_marker_select">';
                ret += c_g_center_marker_curent_radius_1;
            ret += '</label>';
            ret += '<select id="'+g_main_id+'_bmlt_center_marker_select" class="bmlt_center_marker_select" onclick="fix_popup_position(this)" onchange="change_circle_diameter(false)">';

                var count = c_g_diameter_choices.length;
                
                if ( about > c_g_diameter_choices[c_g_diameter_choices.length-1] )
                    {
                    count++;
                    };
                
                for ( c = 0; c < count; c++ )
                    {
                    var length = (c < c_g_diameter_choices.length) ? c_g_diameter_choices[c] : about;
                    
                    var about_slot = ((about > 0) && (about <= length) && (about > ((c > 0)?c_g_diameter_choices[c-1]:0)));
                    
                    if ( about_slot )
                        {
                        length = about;
                        };
                    
                    ret += '<option value="';
                        ret += length.toString();
                        
                        if ( length == about )
                            {
                            ret += '" selected="selected';
                            };
                                
                        ret += '">';
                        ret += length.toString();
                    ret += '</option>';
                    };
                
                if ( about_slot )
                    {
                    about = null;
                    };
            ret += '</select>';
            ret += '<label for="bmlt_center_marker_select">';
                ret += (dist_r_km?c_g_center_marker_curent_radius_2_km:c_g_center_marker_curent_radius_2_mi);
            ret += '</label>';
		ret += '</div>';
		return ret;
	}
	
	/************************************************************************************//**
	*	\brief Return the HTML for a meeting marker info window.							*
	*																						*
	*	\returns the XHTML for the meeting marker.											*
	****************************************************************************************/
	
	function marker_make_meeting ( in_meeting_obj,
									in_weekday )
	{
	    var id = in_meeting_obj.id_bigint.toString()+'_'+g_main_id;
		var ret = '<div class="marker_div_meeting marker_info_meeting" id="'+id+'">';
		ret += '<h4>'+in_meeting_obj.meeting_name.toString()+'</h4>';
		
		var	time = in_meeting_obj.start_time.toString().split(':');

		if ( time[0][0] == '0' )
			{
			time[0] = parseInt(time[0][1]);
			};
		
		var time_str = '';
		
		if ( in_weekday )
			{
			time_str = in_weekday.toString()+' ';
			};
			
		if ( (parseInt ( time[0] ) == 12) && (parseInt ( time[0] ) == 0) )
			{
			time_str += c_g_Noon;
			}
		else
			{
			if ( ((parseInt ( time[0] ) == 23) && (parseInt ( time[1] ) >= 55)) || ((parseInt ( time[0] ) == 0) && (parseInt ( time[1] ) == 0)) )
				{
				time_str += c_g_Midnight;
				}
			else
				{
				if ( parseInt ( time[0] ) > 12 )
					{
					time[0] = (parseInt ( time[0] ) - 12);
					time[2] = 'PM';
					}
				else
					{
					if ( parseInt ( time[0] ) == 12 )
						{
						time[2] = 'PM';
						}
					else
						{
						time[2] = 'AM';
						};
					};
				time_str += time[0]+':'+time[1]+' '+time[2];
				};
			};
		
		ret += '<h5>'+time_str+'</h5>';
		
		var location = '';
		
		if ( in_meeting_obj.location_text )
			{
			ret += '<div class="marker_div_location_text">'+in_meeting_obj.location_text.toString()+'</div>';
			};
		
		if ( in_meeting_obj.location_street )
			{
			ret += '<div class="marker_div_location_street">'+in_meeting_obj.location_street.toString()+'</div>';
			};
		
		if ( in_meeting_obj.location_municipality )
			{
			ret += '<div class="marker_div_location_municipality">'+in_meeting_obj.location_municipality.toString();
			if ( in_meeting_obj.location_province )
				{
				ret += '<span class="marker_div_location_province">, '+in_meeting_obj.location_province.toString()+'</span>';
				};
			ret += '</div>';
			};
		
		if ( in_meeting_obj.location_info )
			{
			ret += '<div class="marker_div_location_info">'+in_meeting_obj.location_info.toString()+'</div>';
			};
		
		if ( in_meeting_obj.comments )
			{
			ret += '<div class="marker_div_location_info">'+in_meeting_obj.comments.toString()+'</div>';
			};
		
		ret += '<div class="marker_div_location_maplink"><a href="';
		url = '';
		
		var comma = false;
		if ( in_meeting_obj.meeting_name )
			{
			url += encodeURIComponent(in_meeting_obj.meeting_name.toString());
			comma = true;
			};
			
		if ( in_meeting_obj.location_text )
			{
			url += (comma ? ',+' : '')+encodeURIComponent(in_meeting_obj.location_text.toString());
			comma = true;
			};
		
		if ( in_meeting_obj.location_street )
			{
			url += (comma ? ',+' : '')+encodeURIComponent(in_meeting_obj.location_street.toString());
			comma = true;
			};
		
		if ( in_meeting_obj.location_municipality )
			{
			url += (comma ? ',+' : '')+encodeURIComponent(in_meeting_obj.location_municipality.toString());
			comma = true;
			};
			
		if ( in_meeting_obj.location_province )
			{
			url += (comma ? ',+' : '')+encodeURIComponent(in_meeting_obj.location_province.toString());
			};
		
		url = url.toString().replace(/[\(\)]/gi,'-');
		
		url = '+(%22' + url + '%22)';
		
		url = 'http://maps.google.com/maps?q='+encodeURIComponent(in_meeting_obj.latitude.toString())+','+encodeURIComponent(in_meeting_obj.longitude.toString()) + url + '&amp;ll='+encodeURIComponent(in_meeting_obj.latitude.toString())+','+encodeURIComponent(in_meeting_obj.longitude.toString());

		ret += url + '" rel="external">'+c_g_map_link_text+'</a>';
		ret += '</div>';
		 
		if ( in_meeting_obj.distance_in_km )
			{
	        // We construct a variable name that uses our unique ID. This will determine what units we use.
            eval ( 'var dist_r_km = c_g_distance_units_are_km_'+g_main_id+';var dist_units = c_g_distance_units_'+g_main_id+';' );
			ret += '<div class="marker_div_distance"><span class="distance_span">'+c_g_distance_prompt+':</span> '+(Math.round((dist_r_km ? in_meeting_obj.distance_in_km : in_meeting_obj.distance_in_miles) * 100)/100).toString()+' '+dist_units+c_g_distance_prompt_suffix;
			ret += '</div>';
			};
		 
		if ( in_meeting_obj.formats )
			{
			ret += '<div class="marker_div_formats"><span class="formats_span">'+c_g_formats+':</span> '+in_meeting_obj.formats;
			ret += '</div>';
			};
	
		ret += '</div>';
		
		return ret;
	}
	
	/************************************************************************************//**
	*	\brief Create a generic marker.														*
	*																						*
	*	\returns a marker object.															*
	****************************************************************************************/
	
	function createMarker (	in_coords,		///< The long/lat for the marker.
							in_shadow_icon,	///< The URI for the icon shadow
							in_main_icon,	///< The URI for the main icon
							in_shape,		///< The shape for the marker
							in_html,		///< The info window HTML
							in_draggable,   ///< True if the marker is draggable
							in_meeting_id   ///< Used to give the info window a unique ID.
							)
	{
		var marker = null;
		
		if ( in_coords )
			{
			var	is_clickable = (in_html ? true : false);

			var marker = new google.maps.Marker ( { 'position':		in_coords,
													'map':			g_main_map,
													'shadow':		in_shadow_icon,
													'icon':			in_main_icon,
													'shape':		in_shape,
													'clickable':	is_clickable,
													'cursor':		'default',
													'draggable':    in_draggable == true
													} );
			if ( marker )
				{
				marker.all_markers_ = g_allMarkers;
                if (!in_draggable)  // We don't change the center marker. All the other ones turn green when selected.
                    {
                    marker.old_image = marker.getIcon();
                    };
				if ( in_html )
					{
					google.maps.event.addListener ( marker, "click", function () {
					                                                            g_info_id = null;
																				for(var c=0; c < this.all_markers_.length; c++)
																					{
                                                                                    if ( g_main_map.center_marker && g_main_map.center_marker.info_win_ && (g_main_map.center_marker.info_win_ != this) )
                                                                                        {
                                                                                        g_main_map.center_marker.info_win_.close();
                                                                                        g_main_map.center_marker.info_win_ = null;
                                                                                        };
                                                                                    
																					if ( this.all_markers_[c] != this )
																						{
																						if ( this.all_markers_[c].info_win_ )
																							{
																							if(this.all_markers_[c].old_image){this.all_markers_[c].setIcon(this.all_markers_[c].old_image);};
																							this.all_markers_[c].setZIndex(null);
																							this.all_markers_[c].info_win_.close();
																							this.all_markers_[c].info_win_ = null;
																							};
																						};
																					};
																				
																				if ( !marker.info_win_ )
																				    {
                                                                                    if(marker.old_image){marker.setIcon(g_icon_image_selected)};
                                                                                    marker.setZIndex(google.maps.Marker.MAX_ZINDEX+1);
																				    marker.info_win_ = new google.maps.InfoWindow ({'position': marker.getPosition(), 'map': marker.getMap(), 'content': '<div id="info_win_'+g_main_id+'_'+in_meeting_id+'">'+in_html+'</div>', 'pixelOffset': new google.maps.Size ( 0, -32 ) });
																				    g_info_id = 'info_win_'+g_main_id+'_'+in_meeting_id;
																				    google.maps.event.addListenerOnce(marker.info_win_, 'closeclick', function() {marker.info_win_ = null;if(marker.old_image){marker.setIcon(marker.old_image)};marker.setZIndex(null)});
																				    google.maps.event.addListenerOnce(marker.info_win_, 'domready', marker_info_window_loaded);
																				    };
																				}
												);
					};
				if ( !in_draggable )
				    {
				    g_allMarkers[g_allMarkers.length] = marker;
				    }
				else
				    {
                    if ( g_main_map.center_marker )
                        {
                        g_main_map.center_marker.setMap ( null );
                        g_main_map.center_marker = null;
                        };
				    g_main_map.center_marker = marker;
				    };
				};
			};
		
		return marker;
	};
	
	/************************************************************************************//**
	*	\brief  This is a slimy, dope-fiend move. Google makes it difficult to get at their *
	*           DOM elements, so we tag our info window, and back out to the surrounding    *
	*           div. We then give that div a known classname, so we can apply CSS to it.    *
	****************************************************************************************/
	
	function marker_info_window_loaded()
	{
	    var info_win = document.getElementById(g_info_id);
	    
	    if ( info_win )
	        {
	        if(info_win.parentNode)
	            {
	            if(info_win.parentNode.parentNode)
	                {
                    info_win.parentNode.parentNode.className = 'bmlt_info_win_container';
	                };
	            };
	        };
	        
	};
	
    /****************************************************************************************//**
    *	\brief Function to Reveal and/hide day <div> elements in the marker info window.	    *
    ********************************************************************************************/
    marker_change_day = function (  in_sel_id,
                                    in_id	///< The base ID of the element.
                                    )
    {
        var sel = document.getElementById ( in_sel_id );
        
        if ( sel && sel.value )
            {
            for ( var wd = 1; wd < 8; wd++ )
                {
                var elem = document.getElementById ( in_sel_id+'_marker_'+in_id.toString()+'_'+wd.toString()+'_id' );
                if ( elem )
                    {
                    if ( wd == sel.value )
                        {
                        elem.style.display = 'block';
                        }
                    else
                        {
                        elem.style.display = 'none';
                        };
                    };
                };
            };
    };
    
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function hideNewSearch()
    {
        var elem_id = g_main_id+'_bmlt_search_map_new_search_div';
        
        var element = document.getElementById ( elem_id );
        
        if ( element )
            {
            element.style.display = 'none';
            };
    };
    
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function showNewSearch()
    {
        var elem_id = g_main_id+'_bmlt_search_map_new_search_div';
        
        var element = document.getElementById ( elem_id );
        
        if ( element )
            {
            element.style.display = 'block';
            };
    };
    
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function setUpNewSearch()
    {
        clearAllMarkers();
        g_main_map = null;
        g_AJAX_Request = null;
        g_initial_call = false;
        g_search_radius = null;
        
        document.getElementById ( g_main_id+'_radius_select' ).options[0].disabled = false;
        var old_onChange = document.getElementById ( g_main_id+'_radius_select' ).onChange; // We do this to prevent the handler from being called as we change the value.
        document.getElementById ( g_main_id+'_radius_select' ).onChange = null;
        document.getElementById ( g_main_id+'_radius_select' ).selectedIndex = 0;
        document.getElementById ( g_main_id+'_radius_select' ).onChange = old_onChange;
        document.getElementById ( g_main_id+'_location_text' ).value = '';
        load_map ( g_initial_div, g_initial_coords );
        
        if ( document.getElementById ( g_main_id+'_options_loc' ).style.display != 'none' )
            {
            document.getElementById( g_main_id+'_location_text' ).select();
            }
        
        hideNewSearch();
    };
    
    /************************************************************************************//**
    *	\brief  This function reads the form elements, and redraws the map, based on the    *
    *           current search criteria.                                                    *
    ****************************************************************************************/
    function recalculateMap(in_cb   ///< Optional checkbox item. If supplied, then the "all reset" will be bypassed.
                            )
    {
        var element_id = g_main_id+'_options_1_a';
            
        g_basic_options_open = document.getElementById ( g_main_id+'_options_1' ).style.display != 'none';    // See if the basic options area is visible.
        
        // We process the checkboxes. These functions allow them to configure properly.
        var args = readWeekdayCheckBoxes(in_cb);
        args += readFormatCheckBoxes(in_cb);
        
        // We may not need to refresh the search. We only refresh if we have already done a search and either one of the filters is off of "All."
        if ( (in_cb || ((args != '') && !g_basic_options_open) || ((args != '') && g_basic_options_open)) && g_main_map.g_location_coords )
            {
            map_clicked ( {'latLng':g_main_map.g_location_coords} );
            };
    };
    
    /************************************************************************************//**
    *	\brief  This function reads the weekday checkboxes, and formats an args string from *
    *           their state.                                                                *
    *																						*
    *	\returns a string, with the arguments in it.                                        *
    ****************************************************************************************/
    function readWeekdayCheckBoxes(in_cb   ///< Optional checkbox item. If supplied, then the "all reset" will be bypassed.
                                    )
    {
        var args = '';
        
        var all_element_id = 'weekday_'+g_main_id+'_0'; // This is the special "all" tag. It is mutually exclusive to all the others.
        var all_element = document.getElementById(all_element_id);
        
        if ( all_element )
            {
            if ( all_element.checked && (in_cb == all_element) )
                {
                for ( var c = 1; c < 8; c++ )
                    {
                    var element_id = 'weekday_'+g_main_id+'_'+c;
                    
                    var weekday_checkbox = document.getElementById(element_id);
                    
                    if ( weekday_checkbox )
                        {
                        var old_onChange = weekday_checkbox.onChange;   // We do this to keep this function from being called like crazy.
                        weekday_checkbox.onChange = null;
                        weekday_checkbox.checked = false;
                        weekday_checkbox.onChange = old_onChange;
                        };
                    };
                }
            else
                {
                var weekday_checked = false;
                for ( var c = 1; c < 8; c++ )
                    {
                    var element_id = 'weekday_'+g_main_id+'_'+c;
                    
                    var weekday_checkbox = document.getElementById(element_id);
                    
                    if ( weekday_checkbox )
                        {
                        if ( weekday_checkbox.checked )
                            {
                            weekday_checked = true;
                            args += '&weekdays[]='+c;
                            };
                        };
                    };
                
                // If there are no checked weekdays, the "all" checkbox is checked. Otherwise, it is not.
                var old_onChange = all_element.onChange;   // We do this to keep this function from being called like crazy.
                all_element.onChange = null;
                all_element.checked = !weekday_checked;
                all_element.onChange = old_onChange;
                };
            };
            
        return args;
    };
    
    /************************************************************************************//**
    *	\brief  This function reads the format checkboxes, and formats an args string from  *
    *           their state.                                                                *
    *																						*
    *	\returns a string, with the arguments in it.                                        *
    ****************************************************************************************/
    function readFormatCheckBoxes(in_cb   ///< Optional checkbox item. If supplied, then the "all reset" will be bypassed.
                                    )
    {
        var args = '';
        
        var all_element_id = 'formats_'+g_main_id+'_0'; // This is the special "all" tag. It is mutually exclusive to all the others.
        var all_element = document.getElementById(all_element_id);
        
        // This is how we count the format checkboxes. A bit crude, but works like a charm.
        var formats_divs = document.getElementsByClassName('bmlt_map_container_div_search_options_formats_checkbox_div');
        
        if ( all_element )
            {
            if ( all_element.checked && (in_cb == all_element) )
                {
                for ( var c = 1; c < formats_divs.length; c++ )
                    {
                    var element_id = 'formats_'+g_main_id+'_'+c;
                    
                    var format_checkbox = document.getElementById(element_id);
                    
                    if ( format_checkbox )
                        {
                        var old_onChange = format_checkbox.onChange;   // We do this to keep this function from being called like crazy.
                        format_checkbox.onChange = null;
                        format_checkbox.checked = false;
                        format_checkbox.onChange = old_onChange;
                        };
                    };
                }
            else
                {
                var format_checked = false;
                for ( var c = 1; c < formats_divs.length; c++ )
                    {
                    var element_id = 'formats_'+g_main_id+'_'+c;
                    
                    var format_checkbox = document.getElementById(element_id);
                    
                    if ( format_checkbox )
                        {
                        if ( format_checkbox.checked )
                            {
                            format_checked = true;
                            args += '&formats[]='+format_checkbox.value;
                            };
                        };
                    };
                
                // If there are no checked weekdays, the "all" checkbox is checked. Otherwise, it is not.
                var old_onChange = all_element.onChange;   // We do this to keep this function from being called like crazy.
                all_element.onChange = null;
                all_element.checked = !format_checked;
                all_element.onChange = old_onChange;
                };
            };
            
        return args;
    };
    
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function openLocationSection (  in_location_text_item,  ///< This is the location text item object.
                                    in_submit_button        ///< The submit button for this text item
                                    )
    {
        in_location_text_item.select();
    };
        
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function selectLocationTextItem (   in_location_text_item,  ///< This is the location text item object.
                                        in_submit_button,       ///< The submit button for this text item
                                        in_blur                 ///< True, if this is a blur (false if it is a focus)
                                    )
    {
        if ( !in_blur && (in_location_text_item.value == c_g_BMLTPlugin_default_location_text) )
            {
            in_location_text_item.value = '';
            in_location_text_item.className = 'location_text_input_item_focused';
            }
        else if ( in_blur )
            {
            if ( !in_location_text_item.value )
                {
                in_location_text_item.value = c_g_BMLTPlugin_default_location_text;
                in_location_text_item.className = 'location_text_input_item_blurred';
                in_submit_button.disabled = true;
                };
            };
    };
        
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function enterTextIntoLocationText (in_location_text_item,  ///< This is the location text item object.
                                        in_submit_button        ///< The submit button for this text item
                                        )
    {
        if ( !in_location_text_item.value )
            {
            in_submit_button.disabled = true;
            }
        else
            {
            in_submit_button.disabled = false;
            };
    };
    
    /************************************************************************************//**
    *	\brief  
    ****************************************************************************************/
    function lookupLocation (   in_location_text_item,  ///< This is the location text item object.
                                in_submit_button        ///< The submit button for this text item
                            )
    {
        if ( in_location_text_item.value && (in_location_text_item.value != c_g_BMLTPlugin_default_location_text) )
            {
            var	geocoder = new google.maps.Geocoder;
            
            if ( geocoder )
                {
                var	status = geocoder.geocode ( { 'address' : in_location_text_item.value }, geoCallback );
                
                in_location_text_item.select();
                
                if ( google.maps.OK != status )
                    {
                    if ( google.maps.INVALID_REQUEST != status )
                        {
                        alert ( c_g_address_lookup_fail );
                        }
                    else
                        {
                        if ( google.maps.ZERO_RESULTS != status )
                            {
                            alert ( c_g_address_lookup_fail );
                            }
                        else
                            {
                            alert ( c_g_server_error );
                            };
                        };
                    };
                }
            else	// None of that stuff is defined if we couldn't create the geocoder.
                {
                alert ( c_g_server_error );
                };
            }
        else
            {
			alert ( c_g_address_lookup_fail );
            };
    };
	
	/************************************************************************************//**
	*	\brief This catches the AJAX response, and fills in the response form.				*
	****************************************************************************************/
	
	function geoCallback ( in_geocode_response	///< The JSON object.
							)
	{
		if ( in_geocode_response && in_geocode_response[0] && in_geocode_response[0].geometry && in_geocode_response[0].geometry.location )
			{
            var elem = document.getElementById ( g_main_id+'_radius_select' );
            
            if ( elem && (elem.selectedIndex == 0) )    // They are expecting an autofit.
                {
                if ( g_main_map.zoom_handler )
                    {
                    google.maps.event.removeListener ( g_main_map.zoom_handler );
                    g_main_map.zoom_handler = null;
                    g_search_radius = null;
                    g_main_map.geo_width = null;
                    };
                };
            
            map_clicked ( {'latLng':in_geocode_response[0].geometry.location, 'panTo':true} );
			}
		else    // FAIL
			{
			alert ( c_g_address_lookup_fail );
			};
	};
        
    /****************************************************************************************
    *									  UTILITY FUNCTIONS                                 *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *	\brief This takes a latitude/longitude location, and returns an x/y pixel location	*
    *	for it.																				*
    *																						*
    *	\returns a Google Maps API V3 Point, with the pixel coordinates (top, left origin).	*
    ****************************************************************************************/
    
    function fromLatLngToPixel ( in_Latng )
    {
        var	ret = null;
        
        if ( g_main_map )
            {
            // We measure the container div element.
            var	div = g_main_map.getDiv();
            
            if ( div )
                {
                var	pixel_width = div.offsetWidth;
                var	pixel_height = div.offsetHeight;
                var	lat_lng_bounds = g_main_map.getBounds();
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
    
        return ret;
    };

	/****************************************************************************************
	*								MAIN FUNCTIONAL INTERFACE								*
	****************************************************************************************/
	
	if ( in_div && in_coords )
		{
		load_map ( in_div, in_coords );
		this.recalculateMapExt = recalculateMap;
		this.newSearchExt = setUpNewSearch;
		this.changeRadiusExt = change_circle_diameter;
		this.openLocationSectionExt = openLocationSection;
		this.focusLocationTextExt = selectLocationTextItem;
		this.enterTextIntoLocationTextExt = enterTextIntoLocationText;
		this.lookupLocationExt = lookupLocation;
		};
};

MapSearch.prototype.recalculateMapExt = null;       ///< These are the only exported functions. We use this to recalculate the map when the user changes options.
MapSearch.prototype.newSearchExt = null;            ///< This will be used to reset the search.
MapSearch.prototype.changeRadiusExt = null;         ///< This will be used to reset the search.
MapSearch.prototype.openLocationSectionExt = null;  ///< This is called when the user opens the location section.
MapSearch.prototype.focusLocationTextExt = null;    ///< This is called when the user selects the location text item.
MapSearch.prototype.enterTextIntoLocationTextExt = null;    ///< This is called to validate entered text.
MapSearch.prototype.lookupLocationExt = null;       ///< This is called to look up a location selected in the text item.