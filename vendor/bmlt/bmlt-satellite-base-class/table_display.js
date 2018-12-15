/****************************************************************************************//**
  \file table_display.js                                                                    *
  \brief Javascript functions for the basic table display.                                  *
                                                                                            *\
                                                                                            *
    This file contains a function/object that implements the Fast Table Shortcode. Upon     *
    instantiation, it uses the page DOM to create a tabular display of meetings that are    *
    returned by a focused semantic search. The meetings are segregated by weekday, and only *
    one weekday is populated at a time. Clicking on another weekday will start an AJAX      *
    query to the server, fetching the meetings for that weekday (and only that weekday).    *
    Search results are cached.                                                              *
                                                                                            *
    The displayed meetings can be sorted by clicking on the column headers (with the sort   *
    direction being changed by repeated clicks). Changing the sort flushes all cached data. *
                                                                                            *
    This class was designed to be highly self-contained. It can be instantiated multiple    *
    times per page, with each instance reflecting a different set of settings from the      *
    BMLT Administration setup (Theme, Week Start, Time Format and Root Server). The basic   *
    search parameters are passed in upon instantiation. These define the dataset to be      *
    fetched and displayed. The dataset is retrieved through a standard JSON semantic query  *
    to the instance's Root Server.                                                          *
                                                                                            *
    The weekday tabs tend to be the "hubs" of the functionality. JSON search results are    *
    attached to the weekday tabs, and this data is used to create the weekday search        *
    display. The formats JSON is held by the main class, as that is used throughout the     *
    class. Otherwise, we try to encapsulate data and functionality as much as possible.     *
                                                                                            *
    This should only be used on Root Server version 2.7.8 or above, because the town sort   *
    will not work properly on older versions (but this will generally work, anyway).        *
                                                                                            *
    SOME NOTES ON CONVENTIONS:                                                              *
    You will notice an abundance of semicolons. This is because this file is passed through *
    an optimizer that crunches the entire file into one line. In this case, semicolons are  *
    absolutely vital.                                                                       *
                                                                                            *
    We use a lot of completion block functions, which is fairly standard for JavaScript.    *
    These are for things like mouseovers and click handlers.                                *
                                                                                            *
    We use ul elements, as opposed to table elements, in order to maximize the potential    *
    for CSS modification of the displayed results (such as for responsive design).          *
                                                                                            *
    This file is part of the BMLT Common Satellite Base Class Project. The project GitHub   *
    page is available here: https://github.com/MAGSHARE/BMLT-Common-CMS-Plugin-Class        *
                                                                                            *
    This file is part of the Basic Meeting List Toolbox (BMLT).                             *
                                                                                            *
    Find out more at: https://bmlt.app                                              *
                                                                                            *
    BMLT is free software: you can redistribute it and/or modify                            *
    it under the terms of the GNU General Public License as published by                    *
    the Free Software Foundation, either version 3 of the License, or                       *
    (at your option) any later version.                                                     *
                                                                                            *
    BMLT is distributed in the hope that it will be useful,                                 *
    but WITHOUT ANY WARRANTY; without even the implied warranty of                          *
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                           *
    GNU General Public License for more details.                                            *
                                                                                            *
    You should have received a copy of the GNU General Public License                       *
    along with this code.  If not, see <http://www.gnu.org/licenses/>.                      *
********************************************************************************************/

/****************************************************************************************//**
    \brief  This class function implements the [[bmlt_table]] shortcode. Most of the shortcode
            functionality is JavaScript. This function builds a new DOM tree that contains
            a <div> element that will, in turn, contain a couple of lists that are warped
            using CSS into a semblance of a table.
    \param in_display_id The element DOM ID of the container div.
    \param in_settings_id The ID of the settings used for this table.
    \param in_theme_name The name of the styling theme.
    \param in_ajax_base_uri The base URI for AJAX callbacks.
    \param in_start_weekday The weekday that starts the week. 0 is Sunday, 6 is Saturday.
    \param in_is_military_time True, if the time is to be displayed as military.
    \param in_search_params The query parameters for the base search.
********************************************************************************************/
function TableSearchDisplay ( in_display_id, in_settings_id, in_theme_name, in_ajax_base_uri, in_start_weekday, in_is_military_time, in_search_params )
{
    /****************************************************************************************
    *                                   INSTANCE DATA MEMBERS                               *
    ****************************************************************************************/

    // These are the parameters passed in.
    var my_settings_id;             ///\var my_settings_id This is the unique ID of the BMLT settings that we use to reference default values.
    var my_ajax_base_uri;           ///\var my_ajax_base_uri This is the base URI for our AJAX calls.
    var my_theme_name;              ///\var my_theme_name This is the name of our styling theme.
    var my_search_query_params;     ///\var my_search_query_params These are the search parameters that we use to populate the search.
    var my_start_weekday;           ///\var my_start_weekday The day of week that starts the header (0 is Sunday, 6 is Saturday).
    var my_military_time;           ///\var my_military_time True, if the start time is military.
    
    // These are DOM elements that we track.
    var my_container_object;        ///\var my_container_object This is the div block that surrounds this table.
    var my_header_container;        ///\var my_header_container The div element that will contain the weekday tabs.
    var my_body_container;          ///\var my_body_container The table element that will contain the search results.
    var my_body_container_header;   ///\var my_body_container_header The table thead element that will contain the search results.
    var my_body_container_body;     ///\var my_body_container_body The table tbody element that will contain the search results.
    var my_weekday_links;           ///\var my_weekday_links An array of li elements, containing the weekday tabs
    var my_selected_tab;            ///\var my_selected_tab This will be the selected tab object.
    
    var my_selected_sort_key;       ///\var my_selected_sort_key The currently selected sort key.
    var my_sort_dir;                ///\var my_sort_dir The currently selected sort direction.
    
    var my_format_data;             ///\var my_format_data This is the JSON object that contains the format data for the search. It is loaded at startup, and not changed afterwards.
    
    // These are basically constants. They are the sort keys.
    var my_sort_key_time;           ///\var my_sort_key_time The sort keys for sorting by start time.
    var my_sort_key_meeting_name;   ///\var my_sort_key_meeting_name The sort keys for sorting by meeting name.
    var my_sort_key_town;           ///\var my_sort_key_town The sort keys for sorting by borough/town.
    var my_sort_key_address;        ///\var my_sort_key_address The sort keys for sorting by street address/location name.

    this.my_sort_key_time = 'start_time,location_nation,location_province,location_sub_province,location_city_subsection,location_municipality,location_neighborhood,meeting_name';
    this.my_sort_key_meeting_name = 'meeting_name,start_time,location_city_subsection,location_municipality';
    this.my_sort_key_town = 'location_city_subsection,location_municipality,start_time,meeting_name';
    this.my_sort_key_address = 'location_text,location_street,location_city_subsection,location_municipality,start_time,meeting_name';
    
    /****************************************************************************************
    *################################### MAIN FUNCTION CODE ################################*
    ****************************************************************************************/
    
    // Start off by getting our container.
    this.my_container_object = document.getElementById ( in_display_id );
    this.my_container_object.handlerObject = this;  // Attach a context object (this function instance).
    
    // Set up the parameters passed in.
    this.my_settings_id = in_settings_id;
    this.my_ajax_base_uri = in_ajax_base_uri;
    this.my_theme_name = in_theme_name;
    this.my_start_weekday = in_start_weekday;
    this.my_search_query_params = in_search_params;
    this.my_military_time = in_is_military_time;
    
    // Initial values.
    this.my_selected_sort_key = this.my_sort_key_time;
    this.my_sort_dir = 'asc';
    
    // This makes the elements visible (This means that non-JS browsers see nothing but the <noscript> element).
    this.my_container_object.style.display = 'block';
    
    // Fire off the first shot -get the formats, which will cascade to the first search.
    this.domBuilder_CreateHeader();
};

/****************************************************************************************
*############################### CLASS PROTOTYPE FUNCTIONS #############################*
****************************************************************************************/

/****************************************************************************************
*############################### COMMUNICATIONS ROUTINES ###############################*
****************************************************************************************/
/************************************************************************************//**
                                      AJAX HANDLER                                      *
****************************************************************************************/

/************************************************************************************//**
    \brief A simple, generic AJAX request function.
    \param url The URI to be called. This should include all parameters in GET form (even if this is POST).
    \param callback The success callback
    \param method The method ('get', 'GET', 'post' or 'POST')
    \param extra_data If supplied, extra data to be delivered to the callback.
    \returns a new XMLHTTPRequest object.
****************************************************************************************/
TableSearchDisplay.prototype.comms_AjaxRequest = function ( url, callback, method, extra_data )
{
    /********************************************************************************//**
    *   \brief Create a generic XMLHTTPObject.
    *   This will account for the various flavors imposed by different browsers.
    *   \returns a new XMLHTTPRequest object.
    ************************************************************************************/

    function createXMLHTTPObject()
    {
        // Various systems will produce one or more instances of these classes.
        var XMLHttpArray = [
            function() {return new XMLHttpRequest()},                   // Your average browser.
            function() {return new ActiveXObject("Msxml2.XMLHTTP")},    // These are for Microsoft browsers.
            function() {return new ActiveXObject("Msxml2.XMLHTTP")},
            function() {return new ActiveXObject("Microsoft.XMLHTTP")}
            ];
        
        var xmlhttp = false;
        
        // Go through the array until you hit a dispatcher that was properly instantiated.
        // Once you find it, make that our dispatcher.
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
    
    // Set up a dispatcher.
    var req = createXMLHTTPObject();
    req.finalCallback = callback;   // Point to the given callback.
    var sVars = null;
    method = method.toString().toUpperCase();   // Clean the input method to all uppercase.

    // Split the URL up, if this is a POST.
    if ( method == "POST" )
        {
        var rmatch = /^([^\?]*)\?(.*)$/.exec ( url );
        url = rmatch[1];
        sVars = rmatch[2];
        // This horrible, horrible kludge, is because Drupal insists on having its q parameter in the GET list only.
        var rmatch_kludge = /(q=admin\/settings\/bmlt)&?(.*)/.exec ( rmatch[2] );
        if ( rmatch_kludge && rmatch_kludge[1] )
            {
            url += '?'+rmatch_kludge[1];
            sVars = rmatch_kludge[2];
            };
        };
    
    // If we are passing some extra data to the callback, we do so here.
    if ( extra_data )
        {
        req.extra_data = extra_data;
        };
    
    // Create the HTTPRequest
    req.open ( method, url, true );
    
    // In the case of POST, our data is separate from the URI.
    if ( method == "POST" )
        {
        req.setRequestHeader ( "Method", "POST "+url+" HTTP/1.1" );
        req.setRequestHeader ( "Content-Type", "application/x-www-form-urlencoded" );
        };
    
    // Set the function to be called as the request does its thing.
    req.onreadystatechange = function ( )
        {
        if ( req.readyState != 4 ) return;  // We only care about the end.
        if( req.status != 200 ) return;     // If we failed, we drop out.
        callback ( req, req.extra_data );   // If we made it this far, we call our callback.
        req = null;                         // Kill the request.
        };
    
    req.send ( sVars ); // Throw the switch, Igor!

    return req;
};

/************************************************************************************//**
    \brief  Called to load all the format objects for the search.
            This will give us the "used formats" for the given filtered search. This will
            request a JSON response from the server. It does not use switcher=GetFormats.
            Instead, it requests the formats only rom switcher=GetSearchResults. This will
            allow the number of formats to be limited to only those used for the search results.
            This uses the filter established by the query parameters passed in on instantiation.
            This function initiates an AJAX call that will return and call the class function
            comms_callback_loadloadFormatData.
****************************************************************************************/
TableSearchDisplay.prototype.comms_loadFormatData = function()
{
    // Since loading the formats happens, regardless of the weekday, we set all the spinners going.
    for ( var i = 0; i < 7; i++ )
        {
        var tab_object = this.my_weekday_links[i];
        tab_object.className = 'bmlt_table_header_weekday_list_element is_loading';
        };
    
    // Ask for the formats used by the search set (not every format in the database).
    // Tell the formats to come alone, bearing small, unmarked bills.
    var uri = encodeURI ( this.my_search_query_params + '&bmlt_settings_id=' + this.my_settings_id + '&get_formats_only=1' ).toString();
    uri =  this.my_ajax_base_uri + encodeURI ( 'switcher=GetSearchResults&' ) + uri;
    this.m_ajax_request = this.comms_AjaxRequest ( uri, this.comms_callback_loadloadFormatData, 'get', this );
};

/************************************************************************************//**
    \brief  Called to load a weekday.
            This is called when the tab doesn't yet have a cache. It requests a JSON response
            from the Root server for switcher=GetSearchResults.
            This uses the filter established by the query parameters passed in on instantiation.
            This function initiates an AJAX call that will return and call the class function
            comms_callback_loadloadWeekdayData.
    \param in_tab_object The tab DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.comms_loadWeekdayData = function ( in_tab_object )
{
    // We append the weekday, sort and sort order parameters to the URI.
    var uri = encodeURI ( this.my_search_query_params + '&sort_keys=' + this.my_selected_sort_key  + ((this.my_sort_dir == 'desc') ? ',desc' : '') +'&bmlt_settings_id=' + this.my_settings_id + '&weekdays[]=' + (parseInt (in_tab_object.index) + 1).toString() ).toString();
    uri =  this.my_ajax_base_uri + encodeURI ( 'switcher=GetSearchResults&' ) + uri;
    this.m_ajax_request = this.comms_AjaxRequest ( uri, this.comms_callback_loadloadWeekdayData, 'get', in_tab_object );
};

/****************************************************************************************
*##################################### AJAX CALLBACKS ##################################*
****************************************************************************************/

/************************************************************************************//**
    \brief  Called when the format data is loaded.
            This loads the function object with a cache of the retrieved JSON data. This
            data is loaded once, then used to construct the format objects in the table.
            This is called once, at the start. After the format data has been loaded, the
            callback then initiates a new search for the current weekday.
    \param in_response_object The HTTPRequest object for this AJAX call.
    \param in_context_object The TableSearchDisplay object that initiated the call.
****************************************************************************************/
TableSearchDisplay.prototype.comms_callback_loadloadFormatData = function ( in_response_object, in_context_object )
{
    for ( var i = 0; i < 7; i++ )
        {
        var tab_object = in_context_object.my_weekday_links[i];
        tab_object.className = 'bmlt_table_header_weekday_list_element';
        };

    eval ( "var format_data = " + in_response_object.responseText + ";" );    // Extract the JSON object with the returned data.
    in_context_object.my_format_data = format_data.formats;

    // Now that we have the formats, get the meeting list for today.
    var d = new Date();
    in_context_object.my_header_container.selectTab ( d.getDay() );
};

/************************************************************************************//**
    \brief  Called when the weekday data is loaded.
            This loads the tab object with a cache of the retrieved JSON data, and has
            the tab object select itself.
    \param in_response_object The HTTPRequest object for this AJAX call.
    \param in_tab_object The Tab object that denotes the chosen weekday.
****************************************************************************************/
TableSearchDisplay.prototype.comms_callback_loadloadWeekdayData = function ( in_response_object, in_tab_object )
{
    eval ( "var json_response = " + in_response_object.responseText + ";" );    // Extract the JSON object with the returned data.
    in_tab_object.weekday_json_data = json_response;
    in_tab_object.select();
};

/****************************************************************************************
*#################################### UTILITY ROUTINES #################################*
****************************************************************************************/

/************************************************************************************//**
    \brief  This turns military time to ante meridian format or cleaned military time.
            If the time is midnight or noon, the localized string for that is returned.
    \param in_time_string A string with the time in military format.
    \returns A string, with the properly formatted time ("Noon", "Midnight", "HH:MM" or "HH:MM AM|PM").
****************************************************************************************/
TableSearchDisplay.prototype.utility_convertTime = function ( in_time_string )
{
    var ret = in_time_string;
    var time_array = in_time_string.split ( ":" );
    var hours = parseInt ( time_array[0] );
    var minutes = parseInt ( time_array[1] );
    
    // We use "Noon" and "Midnight" strings for those times.
    // Kludge, to make "Midnight" late, as the computer wants it to be early.
    // Anything over 23:54:59 is "Midnight."
    if ( ((0 == hours) && (0 == minutes)) || ((hours == 23) && (minutes >= 55)) )
        {
        ret = g_table_ampm_array[3];    // Midnight
        }
    else if ( (12 == hours) && (0 == minutes) )
        {
        ret = g_table_ampm_array[2];    // Noon
        }
    else if ( !this.my_military_time )
        {
        var ampm = ( hours >= 12 ) ? g_table_ampm_array[1] : g_table_ampm_array[0];
        
        // Subtract 12 from afternoon.
        if ( hours > 12 )
            {
            hours -= 12;
            };
        
        // Padding with a leading 0.
        if ( minutes < 10 )
            {
            minutes = "0" + minutes.toString();
            };
        
        ret = hours.toString() + ':' + minutes + ' ' + ampm;
        }
    else
        {
        // Padding with a leading 0.
        if ( minutes < 10 )
            {
            minutes = "0" + minutes.toString();
            };
        
        
        ret = hours.toString() + ':' + minutes.toString();
        };
    
    return ret;
};

/************************************************************************************//**
    \brief  This builds a readable aggregated street address from the components in the JSON.
            Very simply, we just do the location name and street address.
    \param in_json_data The JSON data for one meeting.
    \returns a string, with the address HTML.
****************************************************************************************/
TableSearchDisplay.prototype.utility_createStreetAddress = function ( in_json_data )
{
    var ret = '';

    if ( in_json_data.location_text && in_json_data.location_text.toString() )
        {
        ret = '<span class="bmlt_table_location_text_span">';  // This allows folks to style the location name.
        ret += in_json_data.location_text.toString();
        if ( (ret != '') && in_json_data.location_street && in_json_data.location_street.toString() )
            {
            ret += '<span class="bmlt_table_location_text_comma_span">, </span>';
            };
        ret += '</span>';
        };
    
    if ( in_json_data.location_street && in_json_data.location_street.toString() )
        {
        ret += '<span class="bmlt_table_location_street_span">';  // This allows folks to style the street.
        ret += in_json_data.location_street.toString();
        ret += '</span>';
        };
        
    return ret;
};

/************************************************************************************//**
    \brief  This builds an aggregated town from the components in the JSON.
    \param in_json_data The JSON data for one meeting.
    \returns a string, with the town HTML.
****************************************************************************************/
TableSearchDisplay.prototype.utility_createAddressTown = function ( in_json_data )
{
    var hasTown = false;
    var ret = '<span class="bmlt_table_town_span">';  // This allows folks to style the town.
    
    // We prefer boroughs, as folks think of them as "towns."
    if ( in_json_data.location_city_subsection && in_json_data.location_city_subsection.toString() )
        {
        ret += in_json_data.location_city_subsection.toString();
        hasTown = true;
        }
    else
        {
        if ( in_json_data.location_municipality && in_json_data.location_municipality.toString() )
            {
            ret += in_json_data.location_municipality.toString();
            hasTown = true;
            };
        };
    
    if ( hasTown && in_json_data.location_province && in_json_data.location_province.toString() )
        {
        ret += '<span class="bmlt_table_town_comma_span">, </span>';
        };
    
    ret += '</span>';
    
    // Add the state.
    if ( in_json_data.location_province && in_json_data.location_province.toString() )
        {
        ret += '<span class="bmlt_table_state_span">';  // This allows folks to style the state.            
        ret += in_json_data.location_province.toString();
        ret += '</span>';
        };
    
    return ret;
};

/****************************************************************************************
*#################################### THIRD-PARTY CODE #################################*
****************************************************************************************/
/**
    \param in_pattern   The printf pattern to use
    \param ... any variables needed to satisfy the pattern.
sprintf() for JavaScript 0.6

Copyright (c) Alexandru Marasteanu <alexaholic [at) gmail (dot] com>
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * Neither the name of sprintf() for JavaScript nor the
      names of its contributors may be used to endorse or promote products
      derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Alexandru Marasteanu BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


Changelog:
2007.04.03 - 0.1:
 - initial release
2007.09.11 - 0.2:
 - feature: added argument swapping
2007.09.17 - 0.3:
 - bug fix: no longer throws exception on empty paramenters (Hans Pufal)
2007.10.21 - 0.4:
 - unit test and patch (David Baird)
2010.05.09 - 0.5:
 - bug fix: 0 is now preceeded with a + sign
 - bug fix: the sign was not at the right position on padded results (Kamal Abdali)
 - switched from GPL to BSD license
2010.05.22 - 0.6:
 - reverted to 0.4 and fixed the bug regarding the sign of the number 0
 Note:
 Thanks to Raphael Pigulla <raph (at] n3rd [dot) org> (http://www.n3rd.org/)
 who warned me about a bug in 0.5, I discovered that the last update was
 a regress. I appologize for that.
*/
TableSearchDisplay.prototype.utility_sprintf = function ()
{
    function str_repeat (   i,
                            m
                        )
    {
        for (var o = []; m > 0; o[--m] = i);
        return o.join('');
    };
    
    var i = 0, a, f = arguments[i++], o = [], m, p, c, x, s = '';
    
    while (f)
        {
        if (m = /^[^\x25]+/.exec(f))
            {
            o.push(m[0]);
            }
        else if (m = /^\x25{2}/.exec(f))
            {
            o.push('%');
            }
        else if (m = /^\x25(?:(\d+)\$)?(\+)?(0|'[^$])?(-)?(\d+)?(?:\.(\d+))?([b-fosuxX])/.exec(f))
            {
            if (((a = arguments[m[1] || i++]) == null) || (a == undefined))
                {
                throw('Too few arguments.');
                };
            
            if (/[^s]/.test(m[7]) && (typeof(a) != 'number'))
                {
                throw('Expecting number but found ' + typeof(a));
                };
            
            switch (m[7])
                {
                case 'b': a = a.toString(2); break;
                case 'c': a = String.fromCharCode(a); break;
                case 'd': a = parseInt(a,10); break;
                case 'e': a = m[6] ? a.toExponential(m[6]) : a.toExponential(); break;
                case 'f': a = m[6] ? parseFloat(a).toFixed(m[6]) : parseFloat(a); break;
                case 'o': a = a.toString(8); break;
                case 's': a = ((a = String(a)) && m[6] ? a.substring(0, m[6]) : a); break;
                case 'u': a = Math.abs(a); break;
                case 'x': a = a.toString(16); break;
                case 'X': a = a.toString(16).toUpperCase(); break;
                };
            
            a = (/[def]/.test(m[7]) && m[2] && a >= 0 ? '+' + a : a);
            c = m[3] ? m[3] == '0' ? '0' : m[3].charAt(1) : ' ';
            x = m[5] - String(a).length - s.length;
            p = m[5] ? str_repeat(c, x) : '';
            o.push(s + (m[4] ? a + p : p + a));
            }
        else
            {
            throw('Huh ?!');
            };
        
        f = f.substring(m[0].length);
        };
    
    return o.join('');
};

/****************************************************************************************
*################################## DOM BUILDER ROUTINES ###############################*
*****************************************************************************************
    These routines all construct the DOM tree for the shortcode. The weekday header is
    displayed all the time (with the selected tab changing), but the table data (and the
    header) is reconstructed from scratch each time.
*************************************************************************************//**
    \brief Creates the weekday selection header.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_CreateHeader = function ()
{
    this.my_weekday_links = new Array();
    this.my_header_container = document.createElement ( 'ul' );   // Create the header container.
    this.my_header_container.handler = this;
    this.my_header_container.className = 'bmlt_table_header_weekday_list';
    
    this.my_container_object.appendChild ( this.my_header_container );

    var startingIndex = this.my_start_weekday;  // Allows us to start from different weekdays.
    
    // Build the list of weekday tab objects.
    for ( var i = 0; i < 7; i++ )
        {
        this.my_weekday_links[i] = this.domBuilder_CreateOneWeekday ( startingIndex++ );
        if ( startingIndex == 7 )
            {
            startingIndex = 0;
            };
    
        this.my_header_container.appendChild ( this.my_weekday_links[i] );
        };
    
    // This inline function tells the header to select the indexed (0-based) tab.
    this.my_header_container.selectTab = function ( in_weekday  ///< The day of the week (0 is Sunday, 6 is Saturday)
                                                    )
        {
        for ( var i = 0; i < 7; i++ )
            {
            if ( this.handler && this.handler.my_weekday_links && this.handler.my_weekday_links[i] )
                {
                // If this is the day we are looking at, we select it.
                if ( this.handler.my_weekday_links[i].index == in_weekday )
                    {
                    this.handler.my_weekday_links[i].select();
                    };
                };
            };
        };

    this.comms_loadFormatData();
};

/************************************************************************************//**
    \brief Creates one weekday button for the header.
    \param in_weekday_index The index of the weekday to be created. 0 is Sunday, 6 is Saturday.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_CreateOneWeekday = function ( in_weekday_index )
{
    // First set up the weekday tab element. It will anchor the context for the data.
    var weekdayElement = document.createElement ( 'li' );        // Create the basic list element.
    weekdayElement.handler = this;  // We will do this a lot. It allows anyone to grab the object context from the element.
    weekdayElement.index = in_weekday_index;    // This is the 0-based weekday index.
    weekdayElement.rest_className = 'bmlt_table_header_weekday_list_element';
    weekdayElement.className = weekdayElement.rest_className;
    
    // Today is a great day!
    var d = new Date();
    if ( d.getDay() == in_weekday_index )
        {
        weekdayElement.rest_className += ' is_today';
        };
    
    // If we click, we select.
    weekdayElement.onclick = function () {this.select()};
    
    // This inline function will force a reload of the search for the given weekday.
    weekdayElement.reload = function()
        {
        if ( this.className != (this.rest_className + ' is_loading') )
            {
            if ( this.handler.my_body_container )   // Clear any preexisting condition.
                {
                this.handler.my_body_container.parentNode.removeChild ( this.handler.my_body_container );
                this.handler.my_body_container.innerHTML = '';
                this.handler.my_body_container = null;
                };
        
            this.weekday_json_data = null;
            this.className = this.rest_className + ' is_loading';
            
            // Set a title, saying what is happening.
            this.setAttribute ( 'title', this.handler.utility_sprintf ( g_table_header_tab_loading_format, g_table_weekday_long_name_array[this.index] ) );
            
            this.handler.comms_loadWeekdayData ( this );
            };
        };
    
    // This inline function will read the data from the server if it is not already cached.
    // If the tab has cached data, then it deselects all the other tabs, selects itself, and populates the main area with the data in its cache.
    weekdayElement.select = function()
        {
        if ( !this.weekday_json_data )
            {
            this.reload();
            }
        else
            {
            if ( this.handler.my_body_container )   // Clear any preexisting condition.
                {
                this.handler.my_body_container.parentNode.removeChild ( this.handler.my_body_container );
                this.handler.my_body_container.innerHTML = '';
                this.handler.my_body_container = null;
                };

            for ( var i = 0; i < 7; i++ )
                {
                if ( this.handler && this.handler.my_weekday_links && this.handler.my_weekday_links[i] )
                    {
                    var tabObject = this.handler.my_weekday_links[i];
                    // If the indexed object is not ours, then we deselect it.
                    if ( tabObject.index != this.index )
                        {
                        // This is not selected or loading.
                        tabObject.className = tabObject.rest_className;
                        // Set a title, saying what will happen when this is clicked.
                        tabObject.setAttribute ( 'title', this.handler.utility_sprintf ( g_table_header_tab_format, g_table_weekday_long_name_array[tabObject.index] ) );
                        };
                    };
                };
        
            this.handler.my_selected_tab = this;
            this.sort_key = this.handler.my_selected_sort_key;
            this.sort_dir = this.handler.my_sort_dir;
            this.handler.domBuilder_PopulateWeekday ( this.weekday_json_data, this.index, this.sort_key, this.sort_dir, this );
            };
        };
    
    // This will be the span element that holds the weekday name.
    var textNode = document.createElement ( 'span' );
    textNode.appendChild ( document.createTextNode ( g_table_weekday_name_array[in_weekday_index] ) );
    textNode.rest_className = 'bmlt_table_weekday_span';
    textNode.className = textNode.rest_className;
    
    // We add these functions to handle changing the class when the cursor moves over the tab.
    textNode.onmouseover = function ()
        {
        if ( this.parentNode.className != (this.parentNode.rest_className + ' is_loading') )
            {
            if ( this.parentNode.handler.my_selected_tab != this.parentNode )
                {
                this.parentNode.className = this.parentNode.rest_className + ' bmlt_table_mouseover';
                };
            };
        };
    
    // We restore the class when we mouse out. However, we ignore it if we are loading.
    textNode.onmouseout = function ()
        {
        if ( !this.parentNode.className.match (/is_loading/) )
            {
            this.parentNode.className = this.parentNode.rest_className;
            if ( this.parentNode.handler.my_selected_tab == this.parentNode )
                {
                this.parentNode.className += ' is_selected';
                };
            };
        };
    
    weekdayElement.appendChild ( textNode );
    
    // This is the throbber image. It will be hidden unless the weekday is loading.
    var throbberImage = document.createElement ( 'img' );
    // We replace the "default" with our theme.
    var regex = /themes\x2Fdefault\x2F/;
    throbberImage.src = g_table_throbber_img_src.replace ( regex, 'themes/' + this.my_theme_name.toString() + '/' );
    throbberImage.className = 'bmlt_table_throbber_image';
    weekdayElement.appendChild ( throbberImage );
    
    return weekdayElement;
};

/************************************************************************************//**
    \brief Populates the meeting result table for the given data.
    \param in_search_results_json A JSON object with the meeting search results.
    \param in_index The 0-based weekday index.
    \param in_sort_key The sort key, for the data sort.
    \param in_sort_dir The sort direction, for the data sort. It will be 'asc' or 'desc'.
    \param in_tabObject The tab object, which will be set to selected when the weekday is done.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekday = function ( in_search_results_json, in_index, in_sort_key, in_sort_dir, in_tabObject )
{
    if ( null != this.my_body_container )   // Clear any preexisting condition.
        {
        this.my_body_container.parentNode.removeChild ( this.my_body_container );
        this.my_body_container.innerHTML = '';
        this.my_body_container = null;
        };
    
    this.my_body_container = document.createElement ( 'div' );        // Create the main display container element.
    this.my_body_container.rest_className = 'bmlt_table_div';
    this.my_body_container.rest_className += (' bmlt_table_' + ((in_sort_dir == 'asc') ? 'sort_asc' : 'sort_desc') + '_div');
    this.my_body_container.rest_className += (' bmlt_table_sort_' + in_sort_key.replace ( /,/g, '_' ) + '_div');
    this.my_body_container.className = this.my_body_container.rest_className;
    this.my_body_container_header = null;
    this.my_body_container_body = null;
    
    // Attach all the various DOM elements.
    this.my_container_object.appendChild ( this.my_body_container );        
    this.my_body_container.appendChild ( this.domBuilder_PopulateWeekdayHeader ( in_index, in_sort_key, in_sort_dir ) );
    this.my_body_container.appendChild ( this.domBuilder_PopulateWeekdayBody ( in_search_results_json ) );
    
    var div = document.createElement ( 'div' );
    div.className = 'bmlt_breaker_div';
    this.my_body_container.appendChild ( div );

    in_tabObject.className = in_tabObject.rest_className + ' is_selected';
};

/************************************************************************************//**
    \brief Populates the header for the meetings table.
    \param in_index The 0-based weekday index.
    \param in_sort_key The sort key, for the data sort.
    \param in_sort_dir The sort direction, for the data sort. It will be 'asc' or 'desc'.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekdayHeader = function ( in_index, in_sort_key, in_sort_dir )
{
    if ( null != this.my_body_container_header )   // Clear any preexisting condition.
        {
        this.my_body_container_header.parentNode.removeChild ( this.my_body_container_header );
        this.my_body_container_header.innerHTML = '';
        this.my_body_container_header = null;
        };
    
    this.my_body_container_header = document.createElement ( 'ul' );        // Create the header element.
    this.my_body_container_header.className = 'bmlt_table_header_ul';
    
    var theElement = this.domBuilder_PopulateWeekdayHeaderColumn ( g_table_time_header_text, this.my_sort_key_time, ((in_sort_key == this.my_sort_key_time) ? in_sort_dir : 'asc'), in_sort_key == this.my_sort_key_time );
    theElement.className = 'bmlt_table_header_li bmlt_table_header_li_time';
    this.my_body_container_header.appendChild ( theElement );
    
    theElement = this.domBuilder_PopulateWeekdayHeaderColumn ( g_table_town_header_text, this.my_sort_key_town, ((in_sort_key == this.my_sort_key_town) ? in_sort_dir : 'asc'), in_sort_key == this.my_sort_key_town );
    theElement.className = 'bmlt_table_header_li bmlt_table_header_li_town';
    this.my_body_container_header.appendChild ( theElement );
    
    theElement = this.domBuilder_PopulateWeekdayHeaderColumn ( g_table_name_header_text, this.my_sort_key_meeting_name, ((in_sort_key == this.my_sort_key_meeting_name) ? in_sort_dir : 'asc'), in_sort_key == this.my_sort_key_meeting_name );
    theElement.className = 'bmlt_table_header_li bmlt_table_header_li_name';
    this.my_body_container_header.appendChild ( theElement );
    
    theElement = this.domBuilder_PopulateWeekdayHeaderColumn ( g_table_address_header_text, this.my_sort_key_address, ((in_sort_key == this.my_sort_key_address) ? in_sort_dir : 'asc'), in_sort_key == this.my_sort_key_address );
    theElement.className = 'bmlt_table_header_li bmlt_table_header_li_address';
    this.my_body_container_header.appendChild ( theElement );
    
    theElement = this.domBuilder_PopulateWeekdayHeaderColumn ( g_table_format_header_text, null, null, false );
    theElement.className = 'bmlt_table_header_li bmlt_table_header_li_format';
    this.my_body_container_header.appendChild ( theElement );
    
    return this.my_body_container_header;
};

/************************************************************************************//**
    \brief Populates the header for the meetings table.
    \param in_name The text name for the column.
    \param in_sort_key The sort key, for the data sort.
    \param in_sort_dir The sort direction, for the data sort. It will be 'asc' or 'desc'.
    \param in_is_selected A boolean value. True, if this is the sort key.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekdayHeaderColumn = function ( in_name, in_sort_key, in_sort_dir, in_is_selected )
{
    var theElement =  document.createElement ( 'li' );       // Create the column element
    theElement.key = in_sort_key;                            // This will be the sort key for this column.
    var textNode = document.createElement ( 'span' );
    textNode.appendChild ( document.createTextNode ( in_name ) );
    textNode.is_selected = false;
    textNode.rest_className = 'bmlt_table_header_span';
    textNode.className = textNode.rest_className;
    textNode.sort_dir = in_sort_dir;
    textNode.sort_key = in_sort_key;
    textNode.handler = this;
    
    if ( in_sort_key )
        {
        if ( in_is_selected )
            {
            textNode.rest_className = 'bmlt_table_header bmlt_table_header_selected' + ' bmlt_table_header_selected_sort_direction_' + ((in_sort_dir == 'asc') ? 'asc' : 'desc');
            textNode.className = textNode.rest_className;
            textNode.is_selected = true;
            };
    
        textNode.onmouseover = function ()
            {
            this.className = this.rest_className + ' bmlt_table_mouseover';
            };
    
        textNode.onmouseout = function ()
            {
            this.className = this.rest_className;
            };

        textNode.onclick = function()
            {
            if ( this.handler.my_selected_sort_key == this.sort_key )
                {
                this.sort_dir = ((this.sort_dir == 'asc') ? 'desc' : 'asc');
                };
        
            this.handler.my_selected_sort_key = this.sort_key;
            this.handler.my_sort_dir = this.sort_dir;
            for ( var i = 0; i < 7; i++ )
                {
                this.handler.my_weekday_links[i].weekday_json_data = null;
                };
            
            this.handler.my_selected_tab.reload();
            };
        }
    else
        {
        textNode.rest_className = 'bmlt_header_formats_span';
        textNode.className = textNode.rest_className;
        };
    
    theElement.appendChild ( textNode );

    return theElement;
};

/************************************************************************************//**
    \brief Populates the main table for the meetings table.
    \param in_json_data The JSON data for all the meetings.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekdayBody = function ( in_json_data )
{
    if ( null != this.my_body_container_body )   // Clear any preexisting condition.
        {
        this.my_body_container_body.parentNode.removeChild ( this.my_body_container_body );
        this.my_body_container_body.innerHTML = '';
        this.my_body_container_body = null;
        };
    
    this.my_body_container_body = document.createElement ( 'ul' );
    this.my_body_container_body.className = 'bmlt_table_data_ul';
    
    if ( in_json_data.length )
        {
        for ( var i = 0; i < in_json_data.length; i++ )
            {
            var listElement = document.createElement ( 'li' );
            listElement.className = 'bmlt_table_data_ul_li';
            listElement.className += (' bmlt_row' + ((i % 2) ? '_odd' : '_even'));
            var meeting_json = in_json_data[i];
            var meeting_object = this.domBuilder_PopulateWeekdayBody_one_meeting ( meeting_json );
            listElement.appendChild ( meeting_object );
            this.my_body_container_body.appendChild ( listElement );
            };
        }
    else
        {
        var listElement = document.createElement ( 'li' );
        listElement.className = 'bmlt_table_data_ul_no_meetings_li';
        listElement.innerHTML = '<span class="bmlt_table_no_meetings_span">' + this.utility_sprintf ( g_table_no_meetings_format, g_table_weekday_long_name_array[this.my_selected_tab.index] ) + '</span>';
        this.my_body_container_body.appendChild ( listElement );
        };
    
    return this.my_body_container_body;
};

/************************************************************************************//**
    \brief  Populates one row (meeting)
    \param in_json_data The JSON data for the meeting.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekdayBody_one_meeting = function ( in_json_data )
{
    var rowElement = document.createElement ( 'ul' );
    rowElement.className = 'bmlt_table_data_ul_li_ul';
    var comments = null;
    
    if ( in_json_data.comments )
        {
        comments = in_json_data.comments;
        };
    
    var latitude = in_json_data.latitude;
    var longitude = in_json_data.longitude;

    var time_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'time', this.utility_convertTime ( in_json_data.start_time.toString() ), 0, 0 );
    var town_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'town', this.utility_createAddressTown ( in_json_data ), 0, 0 );
    var name_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'name', in_json_data.meeting_name.toString(), 0, 0 );
    var address_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'address', this.utility_createStreetAddress ( in_json_data ), latitude, longitude );
    var format_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'formats', in_json_data.formats.toString(), 0, 0 );
    
    rowElement.appendChild ( time_column );
    rowElement.appendChild ( town_column );
    rowElement.appendChild ( name_column );
    rowElement.appendChild ( address_column );
    rowElement.appendChild ( format_column );
    
    if ( in_json_data.comments )
        {
        var comments_column = this.domBuilder_PopulateWeekdayBody_one_column ( 'comments', in_json_data.comments.toString(), 0, 0 );
        rowElement.appendChild ( comments_column );
        };
    
    var div = document.createElement ( 'div' );
    div.className = 'bmlt_breaker_div';
    rowElement.appendChild ( div );
    
    return rowElement;
};

/************************************************************************************//**
    \brief Populates one column of a meeting row.
    \param in_tag The tag used as a key for this column
    \param in_string The name of the column (displayed as a string)
    \param in_latitude if supplied, the longitude of a meeting (is usually null)
    \param in_longitude if supplied, the latitude of a meeting (is usually null)
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_PopulateWeekdayBody_one_column = function ( in_tag, in_string, in_latitude, in_longitude )
{
    var columnElement = document.createElement ( 'li' );
    columnElement.className = 'bmlt_table_data_ul_li_ul_li bmlt_table_data_ul_li_ul_li_' + in_tag;

    if ( in_tag == 'formats' )
        {
        var formats_div = this.domBuilder_createFormats ( in_string );
        if ( formats_div )
            {
            columnElement.appendChild ( formats_div );
            };
        }
    else if ( in_latitude && in_longitude )
        {
        var textNode = document.createElement ( 'span' );
        textNode.className = 'bmlt_table_data_ul_li_ul_li_span bmlt_table_data_ul_li_ul_li_span_' + in_tag;
        var anchorNode = document.createElement ( 'a' );
        textNode.className = 'bmlt_table_data_ul_li_ul_li_span_a bmlt_table_data_ul_li_ul_li_span_a_' + in_tag;
        anchorNode.href= this.utility_sprintf ( g_table_map_link_uri_format, parseFloat ( in_latitude ), parseFloat ( in_longitude ) );
        anchorNode.innerHTML = in_string;
        textNode.appendChild ( anchorNode );
        columnElement.appendChild ( textNode );
        }
    else
        {
        var textNode = document.createElement ( 'span' );
        textNode.className = 'bmlt_table_data_ul_li_ul_li_span bmlt_table_data_ul_li_ul_li_span_' + in_tag;
        textNode.innerHTML = in_string ? in_string : '&nbsp;';
        columnElement.appendChild ( textNode );
        };
        
    return columnElement;
};

/************************************************************************************//**
    \brief  This builds a div element for the given formats. The div is filled with spans; one for each format.
    \param in_formats_string The JSON data for one meeting.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_createFormats = function ( in_formats_string )
{
    var ret = document.createElement ( 'div' );
    ret.className = 'bmlt_formats_div';
    var formatsArray = in_formats_string.split ( ',' );
    
    for ( var i = 0; i < formatsArray.length; i++ )
        {
        var format_string = formatsArray[i].toString();
        var span = this.domBuilder_createOneFormatSpan ( format_string );
        if ( span )
            {
            ret.appendChild ( span );
            };
        };
    
    if ( ret.childNodes.length == 0 )
        {
        ret = null;
        }
    else
        {
        var div = document.createElement ( 'div' );
        div.className = 'bmlt_breaker_div';
        ret.appendChild ( div );
        };
    
    return ret;
};

/************************************************************************************//**
    \brief  This builds a span element for the given format.
    \param in_format_string format key.
    \returns the instantiated DOM object.
****************************************************************************************/
TableSearchDisplay.prototype.domBuilder_createOneFormatSpan = function ( in_format_string )
{
    if ( in_format_string )
        {
        var ret = document.createElement ( 'span' );
        ret.rest_className = 'bmlt_format_span bmlt_format_span_' + in_format_string;
        ret.className = ret.rest_className;
        ret.appendChild ( document.createTextNode ( in_format_string ) );
        for ( var i = 0; i < this.my_format_data.length; i++ )
            {
            var format_object = this.my_format_data[i];
            
            if ( format_object.key_string == in_format_string )
                {
                var title_string = format_object.name_string;
                ret.setAttribute ( 'title', title_string );
                var description_string = format_object.description_string;
                ret.onclick = function()
                    {
                    alert ( description_string );
                    };
            
                ret.onmouseover = function ()
                    {
                    this.className = this.rest_className + ' bmlt_table_mouseover';
                    };
    
                ret.onmouseout = function ()
                    {
                    this.className = this.rest_className;
                    };
                };
            };
        return ret;
        };
    
    return null;
};
