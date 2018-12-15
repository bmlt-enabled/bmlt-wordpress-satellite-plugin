/****************************************************************************************//**
* \file fast_mobile_lookup.js																*
* \brief Javascript functions for the fast mobile lookup map interface.						*
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
*	\brief The basic function that asks the browser for location information, and is the	*
*	start of the whole party.																*
*																							*
*	There are two ways to get the location from the user: the W3C way (iPhone) and Google	*
*	Gears (Android devices). This function will try to use whichever one is available, and	*
*	respond in a generic manner.															*
********************************************************************************************/

function WhereAmI ( in_qualifier_day,	/**< A string. This determines whether to focus the search on a particular day.
												Values can be:
													- 'today' - Searches for meetings later today
													- 'tomorrow' - Searches for meetings tomorrow
										*/
					in_address,			///< An optional string, with an address. If supplied, an address lookup is done, instead of a browser GPS lookup.
					in_coords			///< An optional coords object, with accuracy, longitude and latitude. If this is supplied, we go straight to the callback, and will ignore the other stuff.
					)
{
	/****************************************************************************************
	*										CLASS VARIABLES									*
	****************************************************************************************/
	
	var	g_location_coords = null;		///< Will hold the longitude and latitude result.
	var	g_main_map = null;				///< This will hold the Google Map object.
	var	g_qualifier_day = null;			///< This holds any "qualifier" for the day of the search.
	var	g_last_response = null;			///< Caches the last AJAX response.
	var	g_allMarkers = [];				///< Holds all the markers.
	var	g_callback_count = 0;			///< To prevent us from trying too hard.
	var	g_default_style_sheet = null;	///< A reference to the standard stylesheet object.
	var	g_custom_style_sheet = null;	///< A reference to the custom stylesheet object (if any).

	/// These describe the regular NA meeting icon

	var g_icon_image_single = new google.maps.MarkerImage ( c_g_BMLTPlugin_images + "/NAMarker.png", new google.maps.Size(23, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_image_multi = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NAMarkerG.png", new google.maps.Size(23, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_shadow = new google.maps.MarkerImage( c_g_BMLTPlugin_images+"/NAMarkerS.png", new google.maps.Size(43, 32), new google.maps.Point(0,0), new google.maps.Point(12, 32) );
	var g_icon_shape = { coord: [16,0,18,1,19,2,20,3,21,4,21,5,22,6,22,7,22,8,22,9,22,10,22,11,22,12,22,13,22,14,22,15,22,16,21,17,21,18,22,19,20,20,19,21,20,22,18,23,17,24,18,25,17,26,15,27,
	                            14,28,15,29,12,30,12,31,10,31,10,30,9,29,8,28,8,27,7,26,6,25,5,24,5,23,4,22,3,21,3,20,2,19,1,18,1,17,1,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,1,6,1,5,2,
	                            4,2,3,3,2,5,1,6,0,16,0], type: 'poly' };
	
	/// These describe the "You are here" icon.
	var g_center_icon_image = new google.maps.MarkerImage ( c_g_BMLTPlugin_images+"/NACenterMarker.png", new google.maps.Size(21, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
	var g_center_icon_shadow = new google.maps.MarkerImage( c_g_BMLTPlugin_images+"/NACenterMarkerS.png", new google.maps.Size(43, 36), new google.maps.Point(0,0), new google.maps.Point(11, 36) );
	var g_center_icon_shape = { coord: [16,0,18,1,19,2,19,3,20,4,20,5,20,6,20,7,20,8,20,9,20,10,20,11,19,12,17,13,16,14,16,15,15,16,15,17,14,18,14,19,13,20,13,21,13,22,13,23,12,24,12,25,12,26,12,
	                                    27,11,28,11,29,11,30,11,31,11,32,11,33,11,34,11,35,10,35,10,34,9,33,9,32,9,31,9,30,9,29,9,28,8,27,8,26,8,25,8,24,8,23,7,22,7,21,7,20,6,19,6,18,5,17,5,16,4,
	                                    15,4,14,3,13,1,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,0,4,1,3,1,2,3,1,4,0,16,0], type: 'poly' };

	/****************************************************************************************
	*									GOOGLE MAPS STUFF									*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief Load the map and set it up.													*
	****************************************************************************************/
	
	function load_map ( )
	{
		var myOptions = null;
		var window_size = WhereAmI.GetWindowSize ();		
		if ( (window_size.width < 640) || (window_size.height < 640) )
			{
			myOptions = { 'zoom': 7, 'center': g_location_coords, 'mapTypeId': google.maps.MapTypeId.ROADMAP,
				'mapTypeControl': true,
				'mapTypeControlOptions': { 'style': google.maps.MapTypeControlStyle.DROPDOWN_MENU },
				'zoomControl': true,
				'zoomControlOptions': { 'style': google.maps.ZoomControlStyle.SMALL }
				};
			}
		else
			{
			myOptions = { 'zoom': 5, 'center': g_location_coords, 'mapTypeId': google.maps.MapTypeId.ROADMAP };
			};
	
		g_main_map = new google.maps.Map(document.getElementById("location_finder"), myOptions);
	
		if ( g_main_map )
			{
			// We initialize the map by calculating the "fit" during the initial "bounds_changed" event.
			g_main_map.init_listener = google.maps.event.addListener ( g_main_map, "bounds_changed", bounds_changed_handler );
			};
	};
	
	/************************************************************************************//**
	*	\brief This is only called once, at the start, to make sure that the fit is correct	*
	*	We can't calculate the fit until the map has had a chance to initialize. If we wait	*
	*	until this event is fired, then the bounds will be ready.							*
	*	Once the fit has been calculated, we stop listening for this event.					*
	****************************************************************************************/
	
	function bounds_changed_handler ( )
	{
		google.maps.event.removeListener ( g_main_map.init_listener );
		g_main_map.init_listener = google.maps.event.addListener	( g_main_map, "tilesloaded", tiles_loaded_handler );
		fit_markers();
		// The markers get drawn during the "zoom_changed" event.
		google.maps.event.addListener ( g_main_map, "zoom_changed", zoom_changed_handler );
	};
	
	/************************************************************************************//**
	*	\brief When the zoom changes, we will need to redraw the markers. However, since we	*
	*	are in a tile_loaded handler, we need to make sure we don't keep overdrawing them.	*
	*	We only draw new markers if they were previously cleared.							*
	****************************************************************************************/
	
	function tiles_loaded_handler ( )
	{
		if ( !g_allMarkers.length )
			{
			draw_markers();
			};
	};
	
	/************************************************************************************//**
	*	\brief Respond to zoom changes by recalculating and redrawing the markers.			*
	****************************************************************************************/
	
	function zoom_changed_handler ( )
	{
		clearAllMarkers();
		g_main_map.init_listener = google.maps.event.addListener ( g_main_map, "tilesloaded", tiles_loaded_handler );
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
	
		bounds.extend ( g_location_coords );
		
		// We now have the full rectangle of our meeting search results. Scale the map to fit them.
		g_main_map.fitBounds ( bounds );
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
			for ( var c = 0; c < g_allMarkers.length; c++ )
				{
				g_allMarkers[c].setMap( null );
				g_allMarkers[c] = null;
				};
			
			g_allMarkers.length = 0;
			};
	};
	
	/************************************************************************************//**
	*	\brief Calculate and draw the markers.												*
	****************************************************************************************/
	
	function draw_markers()
	{
		// This calculates which markers are the red "multi" markers.
		var overlap_map = MapOverlappingMarkers ( g_main_map.response_object );
		
		// Draw the meeting markers.
		for ( var c = 0; c < overlap_map.length; c++ )
			{
			CreateMapMarker ( overlap_map[c] );
			};
		
		// Finish with the main (You are here) marker.
		CreateMarker ( g_location_coords, g_center_icon_shadow, g_center_icon_image, g_center_icon_shape );
	};
	
	/************************************************************************************//**
	*	\brief	This returns an array, mapping out markers that overlap.					*
	*																						*
	*	\returns An array of arrays. Each array element is an array with n >= 1 elements,	*
	*	each of which is a meeting object. Each of the array elements corresponds to a		*
	*	single marker, and all the objects in that element's array will be covered by that	*
	*	one marker. The returned sub-arrays will be sorted in order of ascending weekday.	*
	****************************************************************************************/
	
	function MapOverlappingMarkers (in_meeting_array	///< Used to draw the markers when done.
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
			tmp[c].coords = WhereAmI.fromLatLngToPixel ( new google.maps.LatLng ( tmp[c].object.latitude, tmp[c].object.longitude ), g_main_map );
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
				tmp[c].matches.sort ( function(a,b){return a.weekday_tinyint-b.weekday_tinyint});
				ret[ret.length] = tmp[c].matches;
				};
			};
		
		return ret;
	};
	
	/************************************************************************************//**
	*	 \brief	This creates a single meeting's marker on the map.							*
	****************************************************************************************/
	
	function CreateMapMarker (	in_mtg_obj_array	/**< A meeting object array. */
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
			
			marker_html += '<div class="multi_day_info_div"><fieldset class="marker_fieldset">';
			marker_html += '<legend>';
			
			if ( included_weekdays.length > 1 )
				{
				marker_html += '<select id="sel_'+in_mtg_obj_array[0].id_bigint.toString()+'" onchange="WhereAmI.marker_change_day('+in_mtg_obj_array[0].id_bigint.toString()+')">';
				
				for ( var wd = 1; wd < 8; wd++ )
					{
					for ( var c = 0; c < included_weekdays.length; c++ )
						{
						if ( included_weekdays[c] == wd )
							{
							marker_html += '<option value="'+included_weekdays[c]+'">'+c_g_weekdays[included_weekdays[c]]+'</option>';
							}
						}
					};
				marker_html += '</select>';
				}
			else
				{
				marker_html += '<strong>'+c_g_weekdays[included_weekdays[0]]+'</strong>';
				};
			
			marker_html += '</legend>';
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
					marker_internal_html += '<div class="marker_div_weekday marker_div_weekday_'+wd.toString()+'" style="display:';
					if ( first )
						{
						marker_internal_html += 'block'; 
						first = false;
						}
					else
						{
						marker_internal_html += 'none'; 
						};
						
					marker_internal_html += '" id="marker_'+in_mtg_obj_array[0].id_bigint.toString()+'_'+wd.toString()+'_id">';
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
			marker_html += '</fieldset></div>';
			}
		else
			{
			marker_html += '">';
			marker_html += marker_make_meeting ( in_mtg_obj_array[0], c_g_weekdays[in_mtg_obj_array[0].weekday_tinyint] );
			};
		
		marker_html += '</div>';
		var marker = CreateMarker ( main_point, g_icon_shadow, ((in_mtg_obj_array.length>1) ? g_icon_image_multi : g_icon_image_single), g_icon_shape, marker_html );
	};
	
	/************************************************************************************//**
	*	\brief Return the HTML for a meeting marker info window.							*
	*																						*
	*	\returns the XHTML for the meeting marker.											*
	****************************************************************************************/
	
	function marker_make_meeting ( in_meeting_obj,
									in_weekday )
	{
		var ret = '';
		
		ret = '<div class="marker_div_meeting" id="meeting_display_'+in_meeting_obj.id_bigint.toString()+'">';
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
			if ( (parseInt ( time[0] ) == 23) && (parseInt ( time[1] ) >= 50) )
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
		
		url = c_g_googleURI + '&q='+encodeURIComponent(in_meeting_obj.latitude.toString())+','+encodeURIComponent(in_meeting_obj.longitude.toString()) + url + '&amp;ll='+encodeURIComponent(in_meeting_obj.latitude.toString())+','+encodeURIComponent(in_meeting_obj.longitude.toString());

		ret += url + '" rel="external">'+c_g_map_link_text+'</a>';
		ret += '</div>';
		 
		if ( in_meeting_obj.distance_in_km )
			{
			ret += '<div class="marker_div_distance"><span class="distance_span">'+c_g_distance_prompt+':</span> '+(Math.round((c_g_distance_units_are_km ? in_meeting_obj.distance_in_km : in_meeting_obj.distance_in_miles) * 10)/10).toString()+' '+c_g_distance_units;
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
	
	function CreateMarker (	in_coords,		///< The long/lat for the marker.
							in_shadow_icon,	///< The URI for the icon shadow
							in_main_icon,	///< The URI for the main icon
							in_shape,		///< The shape for the marker
							in_html			///< The info window HTML
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
													'cursor':		'default'
													} );
			if ( marker )
				{
				marker.all_markers_ = g_allMarkers;
				if ( in_html )
					{
					google.maps.event.addListener ( marker, "click", function () {
																				for(var c=0; c < this.all_markers_.length; c++)
																					{
																					if ( this.all_markers_[c] != this )
																						{
																						if ( this.all_markers_[c].info_win_ )
																							{
																							this.all_markers_[c].info_win_.close();
																							this.all_markers_[c].info_win_ = null;
																							};
																						};
																					};
																				this.info_win_ = new SmartInfoWindow ({'position': marker.getPosition(), 'map': marker.getMap(), 'content': in_html});
																				}
												);
					};
				g_allMarkers[g_allMarkers.length] = marker;
				};
			};
		
		return marker;
	};
	
	/****************************************************************************************
	*									AJAX HANDLERS										*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief A simple, generic AJAX request function.										*
	****************************************************************************************/
		
	function AjaxRequest ( 	url,		///< The URI to be called
							callback,	///< The success callback
							method		///< The method ('get' or 'post')
							)
	{
		/************************************************************************************//**
		*	\brief Create a generic XMLHTTPObject.												*
		*																						*
		*	This will account for the various flavors imposed by different browsers.			*
		*																						*
		*	\returns a new XMLHTTPRequest object.												*
		****************************************************************************************/
		
		function createXMLHTTPObject()
		{
			var XMLHttpArray = [
				function() {return new XMLHttpRequest()},
				function() {return new ActiveXObject("Msxml2.XMLHTTP")},
				function() {return new ActiveXObject("Msxml2.XMLHTTP")},
				function() {return new ActiveXObject("Microsoft.XMLHTTP")}
				];
				
			var xmlhttp = false;
			
			for ( var i=0; i < XMLHttpArray.length; i++ )
				{
				try
					{
					xmlhttp = XMLHttpArray[i]();
					}
				catch(e)
					{
					continue;
					};
				break;
				};
			
			return xmlhttp;
		};
		
		var req = createXMLHTTPObject();
		req.finalCallback = callback;
		req.onreadystatechange = function ( )
			{
			if ( req.readyState != 4 ) return;
			if( req.status != 200 ) return;
			callback ( req );
			};
		req.open ( method,url, true );
		req.send ( null );
	};
	
	/****************************************************************************************
	*									MAIN AJAX CALLBACKS									*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief This catches the AJAX response, and fills in the response form.				*
	****************************************************************************************/
	
	function GeoCallback ( in_geocode_response	///< The JSON object.
							)
	{
		if ( in_geocode_response && in_geocode_response[0] && in_geocode_response[0].geometry && in_geocode_response[0].geometry.location )
			{
			WhereAmI_CallBack ( {'coords':{'latitude':in_geocode_response[0].geometry.location.lat(),'longitude':in_geocode_response[0].geometry.location.lng()}} );
			}
		else
			{
			alert ( c_g_address_lookup_fail );
			window.history.back();
			};
	};
	
	/************************************************************************************//**
	*	\brief If an address lookup fails, it comes here.									*
	****************************************************************************************/
	
	function WhereAmI_Fail_Final ( in_error    ///< The error that caused the failure.
                                )
    {
        switch ( in_error.code )
            {
            case in_error.TIMEOUT:
			    navigator.geolocation.getCurrentPosition ( WhereAmI_CallBack, WhereAmI_Fail_Final, {enableHighAccuracy:true, maximumAge:600000, timeout:100} );
            break;
            
            default:
		        alert ( c_g_cannot_determine_location );
		        window.history.back();
            break;
            };
	};
	
	/************************************************************************************//**
	*	\brief This handles the drawing of the map after a successful lookup.				*
	****************************************************************************************/
	
	function WhereAmI_Meeting_Search_CallBack ( in_response_object )
	{
		var text_reply = in_response_object.responseText;
		if ( text_reply )
			{
			g_last_response = in_response_object;
			
			eval ( 'var json_obj = '+text_reply );
			
			if ( json_obj )
				{
				if ( json_obj.length )
					{
					load_map ( );
					g_main_map.response_object = json_obj;
					}
				else
					{
					alert ( c_g_no_meetings_found );
					window.history.back();
					};
				}
			else
				{
				alert ( c_g_server_error );
				window.history.back();
				};
			}
		else
			{
			alert ( c_g_no_meetings_found );
			window.history.back();
			};
	};
	
	/************************************************************************************//**
	*	\brief This is the callback for a successful location lookup. It sets up a request	*
	*	to the server to get the meeting data, which is then drawn in the above function.	*
	****************************************************************************************/
	
	function WhereAmI_CallBack ( in_position	///< The position object.
								)
	{
		if ( in_position.coords )
			{
			g_callback_count = 0;
			g_location_coords = new google.maps.LatLng ( in_position.coords.latitude, in_position.coords.longitude );
				
			// This is the basic URI.
			var uri = c_BMLTPlugin_files_uri+'BMLTPlugin_mobile_ajax_router=1&bmlt_settings_id='+c_bmlt_settings_id+'&request=';
			
			// This is the actual search filter.
			var request = '/client_interface/json/index.php?switcher=GetSearchResults&compress_output=1&bmlt_settings_id='+c_bmlt_settings_id;
			
			// This is the location part.
			request += '&geo_width=-10&lat_val=' + g_location_coords.lat() + '&long_val=' + g_location_coords.lng();
		
			// If we have a day "qualifier," then we add an extra filter to the search.
			if ( g_qualifier_day )
				{
				var todays_date = new Date ();
				
				var today_weekday = parseInt(todays_date.getDay()) + 1;
				
				// If today, then we look for this weekday, and a time after now.
				if ( g_qualifier_day == 'today' )
					{
					request += '&weekdays[]='+today_weekday.toString();
					request += '&StartsAfterH='+c_g_hour.toString();
					request += '&StartsAfterM='+c_g_min.toString();
					}
				else
					{
					if ( g_qualifier_day == 'tomorrow' )
						{
						var tomorrow_weeday = (today_weekday < 7) ? today_weekday + 1 : 1;
						request += '&weekdays[]='+tomorrow_weeday.toString();
						};
					};
				};
			
			uri += encodeURIComponent ( request );
			AjaxRequest ( uri, WhereAmI_Meeting_Search_CallBack, 'GET' );
			};
	};
	
	/****************************************************************************************
	*									GEOCODE BY ADDRESS									*
	****************************************************************************************/
	
	/************************************************************************************//**
	*	\brief Perform a geocode lookup on an address. It will start the process from an	*
	*	address, as opposed to a browser GPS lookup.										*
	****************************************************************************************/
	
	function GetGeoAddress ( address	///< This is either a string, or a GLatLng object. If the latter, this will be a reverse lookup.
							)
	{
		var	geocoder = new google.maps.Geocoder;
		
		if ( geocoder )
			{
			var	status = geocoder.geocode ( { 'address' : address }, GeoCallback );
			
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
	};
	
	/****************************************************************************************
	*								MAIN FUNCTIONAL INTERFACE								*
	****************************************************************************************/
	
	g_qualifier_day = in_qualifier_day;
	if ( in_coords )
		{
		WhereAmI_CallBack ( { 'coords' : in_coords } );
		}
	else
		{
		for ( var c = 0; c < document.styleSheets.length; c++ )
			{
			var rex_default = /.*?\/fast_mobile_lookup.css/;
			var rex_custom = /.*?\/custom.css/;
			
			if ( document.styleSheets[c] && document.styleSheets[c].href.toString().match(rex_default) )
				{
				g_default_style_sheet = document.styleSheets[c];
				continue;
				};
			
			if ( document.styleSheets[c] && document.styleSheets[c].href.toString().match(rex_custom) )
				{
				g_custom_style_sheet = document.styleSheets[c];
				};
			};
		
		// If given an address, we do a geolocation lookup.
		if ( in_address )
			{
			GetGeoAddress ( in_address );
			}
		else
			{
			navigator.geolocation.getCurrentPosition ( WhereAmI_CallBack, WhereAmI_Fail_Final, {enableHighAccuracy:true, maximumAge:600000, timeout:0} );
			};
		};
};

/****************************************************************************************//**
*	\brief Static function to Reveal and/hide day <div> elements in the marker info window.	*
********************************************************************************************/

WhereAmI.marker_change_day = function ( in_id	///< The base ID of the element.
										)
{
	var sel = document.getElementById ( 'sel_'+in_id );
	
	if ( sel && sel.value )
		{
		for ( var wd = 1; wd < 8; wd++ )
			{
			var elem = document.getElementById ( 'marker_'+in_id.toString()+'_'+wd.toString()+'_id' );
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

/********************************************************************************************
*									STATIC UTILITY FUNCTIONS								*
********************************************************************************************/

/****************************************************************************************//**
*	\brief This takes a latitude/longitude location, and returns an x/y pixel location		*
*	for it.																					*
*																							*
*	\returns a Google Maps API V3 Point, with the pixel coordinates (top, left origin).		*
********************************************************************************************/

WhereAmI.fromLatLngToPixel = function (	in_Latng,
										in_map
										)
{
	var	ret = null;
	
	// We measure the container div element.
	var	div = in_map.getDiv();
	
	if ( div )
		{
		var	pixel_width = div.offsetWidth;
		var	pixel_height = div.offsetHeight;
		var	lat_lng_bounds = in_map.getBounds();
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

	return ret;
};

/****************************************************************************************//**
*	\brief Get the size of the browser display window.										*
*																							*
*	\returns An object with the height and width.											*
********************************************************************************************/

WhereAmI.GetWindowSize = function ()
{
	var myWidth = 0;
	var myHeight = 0;
	
	if( typeof( window.innerWidth ) == 'number' )
		{
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
		}
	else if( document.documentElement && document.documentElement.clientWidth )
		{
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
		}
	else if( document.body && document.body.clientWidth )
		{
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
		};
	
	var mySize = { 'height' : myHeight, 'width' : myWidth };
	
	return mySize;
};

/********************************************************************************************
*										INFO WINDOW CLASS									*
*																							*
* Because this is aimed at mobile phones, and we're using a flexibly-sized info window, we	*
* actually eschew most of the "smartwindow" functionality. We always want the info window	*
* to be displayed ate the top, center of the map, regardless of where the marker is. This	*
* will seem awkward on a computer, but very natural for phones.								*
********************************************************************************************/
/****************************************************************************************//**
* \brief This is from the sample galleries from Google Maps API V3. It is a class that will	*
* display a more useful type of info window.												*
*																							*
* \license: Public domain, but not owned by MAGSHARE. It comes from here:					*
*	http://gmaps-samples-v3.googlecode.com/svn/trunk/smartinfowindow/						*
********************************************************************************************/
function SmartInfoWindow(opts
						)
{
	google.maps.OverlayView.call(this);
	this.content_ = opts.content;
	this.map_ = opts.map;
	this.setMap(this.map_);
	
	// We need to listen to bounds_changed event so that we can redraw
	// absolute position every time the map moves.
	// This is only needed because we append to body instead of map panes.
	var me = this;
	google.maps.event.addListener(this.map_, 'bounds_changed', function() {me.draw();});
};

/**
 * \brief SmartInfoWindow extends GOverlay class from the Google Maps API
 */
SmartInfoWindow.prototype = new google.maps.OverlayView();

/**
 * \brief Creates the DIV representing this SmartInfoWindow
 */
SmartInfoWindow.prototype.onRemove = function()
{
	if (this.div_)
		{
		this.div_.parentNode.removeChild(this.div_);
		this.div_ = null;
		};
};

/**
 * \brief Called when the overlay is first added to the map.
 */
SmartInfoWindow.prototype.onAdd = function()
{
	// Creates the element if it doesn't exist already.
	this.createElement();
};

/**
 * \brief Redraw based on the current projection and zoom level.
 */
SmartInfoWindow.prototype.draw = function()
{
	this.div_.style.display = 'block';
	
	this.wrapperDiv_.className = 'infoWindow_Wrapper_div';
};

/**
 * \brief Creates the DIV representing this SmartInfoWindow in the floatPane.  If the panes
 * object, retrieved by calling getPanes, is null, remove the element from the
 * DOM.  If the div exists, but its parent is not the floatPane, move the div
 * to the new pane.
 * Called from within draw.  Alternatively, this can be called specifically on
 * a panes_changed event.
 */
SmartInfoWindow.prototype.createElement = function()
{
	var panes = this.getPanes();
	var div = this.div_;
	if (!div)
		{
		// This does not handle changing panes.  You can set the map to be null and
		// then reset the map to move the div.
		div = this.div_ = document.createElement('div');
		div.className = 'info_window_div';
		var wrapperDiv = this.wrapperDiv_ = document.createElement('div');
		var contentDiv = document.createElement('div');
		if (typeof this.content_ == 'string')
			{
			contentDiv.innerHTML = this.content_;
			}
		else
			{
			contentDiv.appendChild(this.content_);
			};
		
		var topDiv = document.createElement('div');
		topDiv.style.textAlign = 'right';
		var closeImg = document.createElement('img');
		closeImg.src = c_g_BMLTPlugin_images+'/closebigger.gif';
		closeImg.style.width = '18px';
		closeImg.style.height = '18px';
		closeImg.style.cursor = 'pointer';
		topDiv.appendChild(closeImg);
		
		function removeSmartInfoWindow(ib)
			{
			return function() { ib.setMap(null); };
			};
		
		google.maps.event.addDomListener(closeImg, 'click', removeSmartInfoWindow(this));
		
		wrapperDiv.appendChild(topDiv);
		wrapperDiv.appendChild(contentDiv);
		div.appendChild(wrapperDiv);
		div.style.display = 'none';
		// Append to body, to avoid bug with Webkit browsers
		// attempting CSS transforms on IFRAME or SWF objects
		// and rendering badly.
		document.body.appendChild(div);
		}
	else
		{
		if (div.parentNode != panes.floatPane)
			{
			// The panes have changed.  Move the div.
			div.parentNode.removeChild(div);
			panes.floatPane.appendChild(div);
			}
		else
			{
			// The panes have not changed, so no need to create or move the div.
			};
		};
};

/**
 * \brief Closes infowindow
 */
SmartInfoWindow.prototype.close = function()
{
	this.setMap(null);
};
