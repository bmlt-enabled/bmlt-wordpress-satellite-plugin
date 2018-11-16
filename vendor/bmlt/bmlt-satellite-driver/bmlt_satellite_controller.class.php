<?php
/****************************************************************************************/
/**
    \brief Provides low-level communication to the BMLT Root Server.
    
    \version 1.0.19
    
    This file is part of the Basic Meeting List Toolbox (BMLT).
    
    Find out more at: https://bmlt.app
    
    BMLT is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    BMLT is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this code.  If not, see <http://www.gnu.org/licenses/>.
*/

/****************************************************************************************//**
*   \brief This is the main class for the Satellite Controller. It establishes a liaison    *
*   with the root server.                                                                   *
*                                                                                           *
*   This class will perform the REST communication with the server, and will also aid in    *
*   interpreting and organizing the communications. It does not assemble any HTML,          *
*   JavaScript or CSS. That will be left to View Layer implementations that use this class. *
********************************************************************************************/
class bmlt_satellite_controller
{   
    /****************************************************************************************
    *                                   STATIC DATA MEMBERS                                 *
    ****************************************************************************************/
    /// This is a static data member, because the list will apply to all instances. Slot 0 is the default protocol.
    private static $m_supported_protocols = array (         ///< An array of strings. The supported protocols
                                                    'http', ///< Standard HTTP (Default protocol)
                                                    'https' ///< SSL
                                                    );
    
    /****************************************************************************************
    *                                   DYNAMIC DATA MEMBERS                                *
    ****************************************************************************************/
    private $m_root_uri_string = null;  ///< This is a string, containing the URI to the root server.
    private $m_error_message = null;    ///< This is a string that will contain any error messages.
    
    /************************************************************************************//**
    *   The way that the outgoing associative array will work, is that it will be filled    *
    *   with the keys that are available to the implementor to be used in a query to the    *
    *   root server. Some of these keys will have arrays of still more keys, and some of    *
    *   these "contained arrays" of values will be filled at runtime, after some            *
    *   transactions have been executed with the server.                                    *
    ****************************************************************************************/
    private $m_outgoing_parameters = null;  /**< An associative array of mixed values.
                                                 The array keys will be the parameter keys
                                                 for outgoing transaction stimuli.
                                                 This array is preset with keys for the available parameters.
                                            */
    private $m_server_version = null;       ///< The server version. Null if the server has not been queried.
    private $m_current_transaction = null;  ///< This will hold an array of transaction parameter values for an outgoing transaction.
    
    /****************************************************************************************
    *                                       CONSTRUCTOR                                     *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief Constructor -Set the value of the Root URI.                                  *
    *   If a URI is passed in, then the object establishes and tests a connection, and      *
    *   loads up the standard outgoing parameters.                                          *
    *   This object requires that the server be of version 1.8.6 or greater.                *
    ****************************************************************************************/
    function __construct (  $in_root_uri_string = null  ///< The URI to the root server, can be null
                        )
    {
        if ( $in_root_uri_string )
            {
            // Don't need to flush the params, as we'll be doing that next.
            $this->set_m_root_uri ( $in_root_uri_string, true );
            }
        
        // Initialize the parameters.
        $this->flush_parameters();
        
        // OK, now we talk to the server, and fill up on the various server-specific things.
        if ( $in_root_uri_string )
            {
            // The first thing we do, is get the server version. We must have version 1.8.6 or greater.
            $version = $this->get_server_version();
            if ( !$this->get_m_error_message() )
                {
                $version_int = intval ( str_replace ( '.', '', $version ) );
                if ( $version_int > 185 )
                    {
                    if ( !extension_loaded ( 'curl' ) ) // Must have cURL. This puppy won't work without cURL.
                        {
                        $this->set_m_error_message ( '__construct: The cURL extension is not available! This code will not work on this server!' );
                        }
                    else
                        {
                        $this->load_standard_outgoing_parameters();
                        }
                    }
                else
                    {
                    $this->set_m_error_message ( '__construct: The root server at ('.$in_root_uri_string.') is too old (it is version '.$version.')! It needs to be at least Version 1.8.6!' );
                    }
                }
            }
    }
    
    /****************************************************************************************
    *                                   ACCESSOR FUNCTIONS                                  *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief Accessor -Set the value of the Root URI.                                     *
    *                                                                                       *
    *   NOTE: If the server URI changes, the parameters are flushed.                        *
    ****************************************************************************************/
    function set_m_root_uri ( $in_root_uri_string,  /**< A string. The URI to set to the data member.
                                                         This is set verbatim.
                                                         Cleaning is performed at recall time.
                                                    */
                              $in_skip_flush = false    ///< Optional. If true, the parameters won't be flushed, even if they need to be.
                            )
    {
        // If we are selecting a new server, or changing servers, we flush all stored parameters.
        if ( !$in_skip_flush && strcmp ( $in_root_uri_string, $this->m_root_uri_string ) )
            {
            $this->flush_parameters();
            }
        
        $this->m_root_uri_string = $in_root_uri_string;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Return the value of the Root URI. Perform "cleaning" if necessary. *
    *                                                                                       *   
    *   \returns A string. The root URI, with the protocol preamble. If none is given,      *
    *   "http" is used. Also, there is no trailing slash.                                   *
    ****************************************************************************************/
    function get_m_root_uri ()
    {
        $ret_string = $this->m_root_uri_string;
        $protocols = self::get_m_supported_protocols();
        $protocol = $protocols[0];  // Element zero has the default protocol.
        
        // We check for a supported protocol, here. It must be HTTP or HTTPS.
        $matches = array();
        $uri = '';  // This will be the base URI to the main_server directory.
        // See if we have a protocol preamble. Separate the URI into components.
        if ( preg_match ( '|^(.*?):\/\/(.*?)/?$|', $ret_string, $matches ) )
            {
            $protocol = strtolower ( $matches[1] );
            // See if we are a supported protocol.
            if ( !in_array ( $protocol, $protocols ) )
                {
                $protocol = $protocols[0];  // The default protocol is in element zero.
                }
                
            $uri = $matches[2]; // This strips off any trailing slash.
            }
        else
            {
            // Strip off any trailing slash.
            preg_match ( '|^(.*?)\/?$|', $ret_string, $matches );
            $uri = $matches[1];
            }
        
        // At this point, we have a protocol, and a URI that has had its trailing slash removed.
        // Reassemble them into a "cleaned" return string.
        
        $ret_string = $protocol.'://'.$uri;
        
        return $ret_string;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Set the server version.                                            *
    ****************************************************************************************/
    private function set_m_server_version ( $in_version ///< A string. The version information.
                                            )
    {
        $this->m_server_version = $in_version;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Return the value of the server version (if any).                   *
    *                                                                                       *
    *   \returns A string.                                                                  *
    ****************************************************************************************/
    private function get_m_server_version ()
    {
        return $this->m_server_version;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Set the class error message.                                       *
    ****************************************************************************************/
    private function set_m_error_message ( $in_error_message    ///< A string. The error message.
                                    )
    {
        $this->m_error_message = $in_error_message;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Return the value of the class error message (if any).              *
    *                                                                                       *
    *   \returns A string.                                                                  *
    ****************************************************************************************/
    function get_m_error_message ()
    {
        return $this->m_error_message;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Set the class transaction "bucket"                                 *
    ****************************************************************************************/
    private function set_m_current_transaction ( $in_current_transaction    ///< An array of mixed.
                                                )
    {
        $this->m_current_transaction = $in_current_transaction;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Return a reference to the class transaction "bucket."              *
    *                                                                                       *
    *   \returns A reference to an array of mixed.                                          *
    ****************************************************************************************/
    function &get_m_current_transaction ()
    {
        return $this->m_current_transaction;
    }
    
    /************************************************************************************//**
    *   \brief Accessor -Return the transaction stimulus array.                             *
    *                                                                                       *
    *   \returns A reference to an array of mixed.                                          *
    ****************************************************************************************/
    function &get_m_outgoing_parameters ()
    {
        return $this->m_outgoing_parameters;
    }
        
    /****************************************************************************************
    *                                   CLASS FUNCTIONS                                     *
    ****************************************************************************************/
    /************************************************************************************//**
    *   \brief Test the stored URI to see if it points to a valid root server, and return   *
    *   the server version.                                                                 *
    *                                                                                       *
    *   This will cache the response in the incoming parameter ('m_server_version'), and    *
    *   will return the cached value, if possible.                                          *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns A string, containing the server version. Null if the test fails.           *
    ****************************************************************************************/
    function get_server_version ( $in_force_refresh = false ///< If this is true, then the server will be queried, even if there is a cache.
                                )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
        
        if ( $in_force_refresh || !$this->get_m_server_version() )
            {
            $uri = $this->get_m_root_uri(); // Get the cleaned URI.
            
            $uri .= '/client_interface/serverInfo.xml'; // We will load the XML file.
        
            // Get the XML data from the remote server. We will use GET.
            $data = self::call_curl ( $uri, false, $error_message );
            
            // Save any internal error message from the transaction.
            $this->set_m_error_message ( $error_message );
            
            // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
            if ( !$this->get_m_error_message() && $data )
                {
                $info_file = new DOMDocument;
                if ( $info_file instanceof DOMDocument )
                    {
                    if ( @$info_file->loadXML ( $data ) )
                        {
                        $has_info = $info_file->getElementsByTagName ( "bmltInfo" );
                        
                        if ( ($has_info instanceof domnodelist) && $has_info->length )
                            {
                            $ret = $has_info->item(0)->nodeValue;
                            $this->set_m_server_version( $ret );
                            }
                        }
                    }
                }
            
            if ( !$ret && !$this->get_m_error_message() )
                {
                $this->set_m_error_message ( 'get_server_version: Invalid URI ('.$uri.')' );
                }
            }
        else
            {
            $ret = $this->get_m_server_version();
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return the server supported languages.                                       *
    *                                                                                       *
    *   This will cache the response in the outgoing parameters ('langs'), and will return  *
    *   the cached value, if possible.                                                      *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns An associative array, containing the server languages (the key will        *
    *   indicate the language key, and the value will be an array with the readable         *
    *   name of the language, and a "default" if this is the server's "native" language).   *
    ****************************************************************************************/
    function get_server_langs ( $in_force_refresh = false   ///< If this is true, then the server will be queried, even if there is a cache.
                                )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
        
        if ( $in_force_refresh || !is_array ( $this->get_m_outgoing_parameter('langs') ) || !count ( $this->get_m_outgoing_parameter('langs') ) )
            {
            $uri = $this->get_m_root_uri(); // Get the cleaned URI.
            
            $uri .= '/client_interface/xml/GetLangs.php';   // We will load the XML file.
        
            // Get the XML data from the remote server. We will use GET.
            $data = self::call_curl ( $uri, false, $error_message );
            
            // Save any internal error message from the transaction.
            $this->set_m_error_message ( $error_message );
            
            // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
            if ( !$this->get_m_error_message() && $data )
                {
                $info_file = new DOMDocument;
                if ( $info_file instanceof DOMDocument )
                    {
                    if ( @$info_file->loadXML ( $data ) )
                        {
                        $has_info = $info_file->getElementsByTagName ( "language" );
                        
                        if ( ($has_info instanceof domnodelist) && $has_info->length )
                            {
                            $ret = array();
                            
                            foreach ( $has_info as $node )
                                {
                                $value = $node->nodeValue;
                                $key = $node->getAttribute('key');
                                $ret[$key]['name'] = $value;
                                $ret[$key]['default'] = $node->getAttribute('default') ? true : false;
                                }
                            $this->set_m_outgoing_parameter( 'langs', $ret );
                            }
                        }
                    }
                }
            
            if ( !$ret && !$this->get_m_error_message() )
                {
                $this->set_m_error_message ( 'get_server_langs: Invalid URI ('.$uri.')' );
                }
            }
        else
            {
            $ret = $this->get_m_outgoing_parameter('langs');
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return meeting changes between two dates.                                    *
    *                                                                                       *
    *   This requires that the server be version 1.8.13 or greater.                         *
    *   This queries the server for meeting change records between (and including) the two  *
    *   dates given. The dates are optional. However, not supplying them means that the     *
    *   entire server change record is returned, which is quite a mouthful. You can specify *
    *   just one of the parameters (all the changes after a date to now, or all of the      *
    *   changes since the server started until a certain date).                             *
    *                                                                                       *
    *   There is no caching of this call. It is always real-time.                           *
    *                                                                                       *
    *   The dates are given as PHP UNIX times (integer epoch times).                        *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns An indexed array containing the change records as associative arrays.      *
    ****************************************************************************************/
    function get_meeting_changes (  $in_start_date = null,      ///< Optional. If given (a PHP time() format UNIX Epoch time), the changes will be loaded from midnight (00:00:00) of the date of the time.
                                    $in_end_date = null,        ///< Optional. If given (a PHP time() format UNIX Epoch time), the changes will be loaded until midnight (23:59:59) of the date of the time.
                                    $in_meeting_id = null,      ///< If supplied, an ID for a particular meeting. Only changes for that meeting will be returned.
                                    $in_service_body_id = null  ///< If supplied, an ID for a particular Service body. Only changes for meetings within that Service body will be returned.
                                    )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
    
        $uri = $this->get_m_root_uri(); // Get the cleaned URI.
        
        $uri .= '/client_interface/xml/index.php?switcher=GetChanges';  // We will load the XML file.
        
        if ( intval ( $in_start_date ) )
            {
            $uri .= '&start_date='.date ( 'Y-m-d', intval ( $in_start_date ) );
            }
        
        if ( intval ( $in_end_date ) )
            {
            $uri .= '&end_date='.date ( 'Y-m-d', intval ( $in_end_date ) );
            }
        
        if ( intval ( $in_meeting_id ) )
            {
            $uri .= '&meeting_id='.intval ( $in_meeting_id );
            }
        
        if ( intval ( $in_service_body_id ) )
            {
            $uri .= '&service_body_id='.intval ( $in_service_body_id );
            }
    
        // Get the XML data from the remote server. We will use GET.
        $data = self::call_curl ( $uri, false, $error_message );
        
        // Save any internal error message from the transaction.
        $this->set_m_error_message ( $error_message );
        
        // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
        if ( !$this->get_m_error_message() && $data )
            {
            $info_file = new DOMDocument;
            if ( $info_file instanceof DOMDocument )
                {
                if ( @$info_file->loadXML ( $data ) )
                    {
                    $has_info = $info_file->getElementsByTagName ( "row" );
                    
                    if ( ($has_info instanceof domnodelist) && $has_info->length )
                        {
                        $ret = array();
                        
                        foreach ( $has_info as $change )
                            {
                            if ( $change->hasChildNodes() )
                                {
                                $change_ar = array();
                                foreach ( $change->childNodes as $change_record_elem )
                                    {
                                    $key = $change_record_elem->nodeName;
                                    $value = $change_record_elem->nodeValue;
                                    $change_ar[$key] = $value;
                                    }
                                
                                $ret[] = $change_ar;
                                }
                            }
                        }
                    else
                        {
                        $this->set_m_error_message ( 'get_meeting_changes: Invalid XML Format ('.$uri.')' );
                        }
                    }
                else
                    {
                    $this->set_m_error_message ( 'get_meeting_changes: Invalid XML Format ('.$uri.')' );
                    }
                }
            else
                {
                $this->set_m_error_message ( 'get_meeting_changes: Invalid XML Format ('.$uri.')' );
                }
            }
        elseif ( !$this->get_m_error_message() )
            {
            $this->set_m_error_message ( 'get_meeting_changes: Invalid URI ('.$uri.')' );
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return the server supported formats.                                         *
    *                                                                                       *
    *   This will cache the response in the outgoing parameters ('formats'), and will       *
    *   return the cached value, if possible.                                               *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns An associative array containing the formats as arrays. The array index is  *
    *   that format's shared ID, for quick lookup.                                          *
    ****************************************************************************************/
    function get_server_formats ( $in_force_refresh = false ///< If this is true, then the server will be queried, even if there is a cache.
                                )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
        
        if ( $in_force_refresh || !is_array ( $this->get_m_outgoing_parameter('formats') ) || !count ( $this->get_m_outgoing_parameter('formats') ) )
            {
            $uri = $this->get_m_root_uri(); // Get the cleaned URI.
            
            $uri .= '/client_interface/xml/index.php?switcher=GetFormats';  // We will load the XML XML.
        
            // Get the XML data from the remote server. We will use GET.
            $data = self::call_curl ( $uri, false, $error_message );
            
            // Save any internal error message from the transaction.
            $this->set_m_error_message ( $error_message );
            
            // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
            if ( !$this->get_m_error_message() && $data )
                {
                $info_file = new DOMDocument;
                if ( $info_file instanceof DOMDocument )
                    {
                    if ( @$info_file->loadXML ( $data ) )
                        {
                        $has_info = $info_file->getElementsByTagName ( "row" );
                        
                        if ( ($has_info instanceof domnodelist) && $has_info->length )
                            {
                            $ret = array();
                            
                            foreach ( $has_info as $format )
                                {
                                if ( $format->hasChildNodes() )
                                    {
                                    $format_ar = array();
                                    foreach ( $format->childNodes as $format_elem )
                                        {
                                        $key = $format_elem->nodeName;
                                        $value = $format_elem->nodeValue;
                                        $format_ar[$key] = $value;
                                        }
                                    $ret[$format_ar['id']] = $format_ar;
                                    }
                                }
                            $this->set_m_outgoing_parameter( 'formats', $ret );
                            }
                        else
                            {
                            $this->set_m_error_message ( 'get_server_formats: Invalid XML Format ('.$uri.')' );
                            }
                        }
                    else
                        {
                        $this->set_m_error_message ( 'get_server_formats: Invalid XML Format ('.$uri.')' );
                        }
                    }
                else
                    {
                    $this->set_m_error_message ( 'get_server_formats: Invalid XML Format ('.$uri.')' );
                    }
                }
            elseif ( !$this->get_m_error_message() )
                {
                $this->set_m_error_message ( 'get_server_formats: Invalid URI ('.$uri.')' );
                }
            }
        else
            {
            $ret = $this->get_m_outgoing_parameter('formats');
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return the server's Service bodies, in hierarchical fashion.                 *
    *                                                                                       *
    *   This will cache the response in the outgoing parameters ('services'), and will      *
    *   return the cached value, if possible.                                               *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns An associative array, containing the server Service bodies
    ****************************************************************************************/
    function get_server_service_bodies ( $in_force_refresh = false  ///< If this is true, then the server will be queried, even if there is a cache.
                                        )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
        
        if ( $in_force_refresh || !is_array ( $this->get_m_outgoing_parameter('services') ) || !count ( $this->get_m_outgoing_parameter('services') ) )
            {
            $uri = $this->get_m_root_uri(); // Get the cleaned URI.
            
            $uri .= '/client_interface/xml/GetServiceBodies.php';   // We will load the XML file.
        
            // Get the XML data from the remote server. We will use GET.
            $data = self::call_curl ( $uri, false, $error_message );
            
            // Save any internal error message from the transaction.
            $this->set_m_error_message ( $error_message );
            
            // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
            if ( !$this->get_m_error_message() && $data )
                {
                $info_file = new DOMDocument;
                if ( $info_file instanceof DOMDocument )
                    {
                    if ( @$info_file->loadXML ( $data ) )
                        {
                        $has_info = $info_file->getElementsByTagName ( "serviceBodies" );
                        
                        if ( ($has_info instanceof DOMNodeList) && $has_info->length )
                            {
                            $sb_node = $has_info->item(0);
                            if ( $sb_node instanceof DOMElement )
                                {
                                foreach ( $sb_node->childNodes as $node )
                                    {
                                    if ( method_exists ( $node, 'getAttribute' ) )
                                        {
                                        $ret[$node->getAttribute('id')] = self::extract_service_body_info ( $node );
                                        }
                                    }
                                }
                            $this->set_m_outgoing_parameter( 'services', $ret );
                            }
                        else
                            {
                            $this->set_m_error_message ( 'get_server_service_bodies: Invalid XML Format ('.$uri.')' );
                            }
                        }
                    else
                        {
                        $this->set_m_error_message ( 'get_server_service_bodies: Failed to Load File ('.$uri.')' );
                        }
                    }
                }
            elseif ( !$this->get_m_error_message() )
                {
                $this->set_m_error_message ( 'get_server_service_bodies: Invalid URI ('.$uri.')' );
                }
            }
        else
            {
            $ret = $this->get_m_outgoing_parameter('services');
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return a list of the supported meeting_key values..                          *
    *                                                                                       *
    *   Each root server can define its own meeting data item keys, so we need to fetch the *
    *   ones defined by this server. We do this by parsing the dynamically-generated        *
    *   schema document from the server.                                                    *
    *                                                                                       *
    *   This will cache the response in the outgoing parameters ('meeting_keys'), and will  *
    *   return the cached value, if possible.                                               *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    *                                                                                       *   
    *   \returns An array of strings, containing the server meeting_key values.             *
    ****************************************************************************************/
    function get_server_meeting_keys ( $in_force_refresh = false    ///< If this is true, then the server will be queried, even if there is a cache.
                                    )
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );
        
        if ( $in_force_refresh || !is_array ( $this->get_m_outgoing_parameter('meeting_key') ) || !count ( $this->get_m_outgoing_parameter('meeting_key') ) )
            {
            $uri = $this->get_m_root_uri(); // Get the cleaned URI.
            
            $uri .= '/client_interface/xsd/GetSearchResults.php';   // We will load the XML file.
        
            // Get the XML data from the remote server. We will use GET.
            $data = self::call_curl ( $uri, false, $error_message );
            // Save any internal error message from the transaction.
            $this->set_m_error_message ( $error_message );
            
            // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
            if ( !$this->get_m_error_message() && $data )
                {
                $info_file = new DOMDocument;
                if ( $info_file instanceof DOMDocument )
                    {
                    if ( @$info_file->loadXML ( $data ) )
                        {
                        $sequence_elements = $info_file->getElementsByTagName ( "sequence" );
                        $sequence_element = $sequence_elements->item(1);
                        // We now have the XSD sequence element that is the immediate container for the meeting_key items
                        if ( $sequence_element instanceof DOMElement )
                            {
                            foreach ( $sequence_element->childNodes as $sb_node )
                                {
                                if ( method_exists ( $sb_node, 'getAttribute' ) && $sb_node->getAttribute ( 'name' ) )
                                    {
                                    $ret[] = $sb_node->getAttribute ( 'name' );
                                    }
                                }
                            
                            $this->set_m_outgoing_parameter('meeting_key', $ret);
                            }
                        else
                            {
                            $this->set_m_error_message ( 'get_server_meeting_keys: Invalid XML Format ('.$uri.')' );
                            }
                        }
                    else
                        {
                        $this->set_m_error_message ( 'get_server_service_bodies: Failed to Load File ('.$uri.')' );
                        }
                    }
                }
            elseif ( !$this->get_m_error_message() )
                {
                $this->set_m_error_message ( 'get_server_meeting_keys: Invalid URI ('.$uri.')' );
                }
            }
        else
            {
            $ret = $this->get_m_outgoing_parameter('meeting_key');
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief See if a given parameter key is valid for an outgoing parameter.             *
    *                                                                                       *
    *   This will set or clear the error message.                                           *
    *                                                                                       *
    *   \returns An array, or null. If an array, it will be an array of possible values.    *
    *   Null is not an error. It simply means that this transaction key does not have a set *
    *   of preset values.                                                                   *
    ****************************************************************************************/
    function get_transaction_key_values (   $in_parameter_key   ///< A string. The key for this parameter..
                                        )
    {
        $ret = null;
        
        $this->set_m_error_message ( null );    // Clear the error message.
        
        if ( $this->is_legal_transaction_key ( $in_parameter_key ) )
            {
            // We start by getting a reference to the outgoing parameters array.
            $outgoing_parameters =& $this->get_m_outgoing_parameters();
            
            // We only respond with keys if the parameter value is a non-empty array.
            if ( is_array ( $outgoing_parameters[$in_parameter_key] ) && count ( $outgoing_parameters[$in_parameter_key] ) )
                {
                $ret = $outgoing_parameters[$in_parameter_key];
                }
            }
        else
            {
            $this->set_m_error_message ( 'get_transaction_key_values: Invalid Parameter Key: "'.$in_parameter_key.'"' );
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief See if a given parameter key is valid for an outgoing parameter.             *
    *                                                                                       *
    *   \returns A Boolean. True if it is legal, false, otherwise.                          *
    ****************************************************************************************/
    function is_legal_transaction_key ( $in_parameter_key,  ///< A string. The key for this parameter.
                                        $in_sub_key = null  ///< Optional. If this is a meeting_key value, see if it is legal. Ignored, otherwise.
                                        )
    {
        // We start by getting a reference to the outgoing parameters array.
        $legal_entities =& $this->get_m_outgoing_parameters();
        
        $ret = array_key_exists ( $in_parameter_key, $legal_entities );
        
        if ( ($in_parameter_key == 'meeting_key') && isset ( $in_sub_key ) )
            {
            $ret = $ret && array_key_exists ( $in_sub_key, $legal_entities[$in_parameter_key] );
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Add a transaction parameter to a transaction being built.                    *
    *                                                                                       *
    *   This will set or clear the error message.                                           *
    *                                                                                       *
    *   \returns A Boolean. True if it is OK, false, otherwise.                             *
    ****************************************************************************************/
    function set_current_transaction_parameter (    $in_parameter_key,          ///< A string. The key for this parameter. If there is one already set, this will overwrite that.
                                                    $in_parameter_value = null  ///< Mixed. It can be any value. If an array, then the value will be presented as multiple values.
                                                    )
    {
        $ret = false;
        
        $this->set_m_error_message ( null );    // Clear the error message.
        
        if ( $this->is_legal_transaction_key ( $in_parameter_key, $in_parameter_value ) )
            {
            // We start by getting a reference to our transaction array.
            $transaction_array =& $this->get_m_current_transaction();
            
            $transaction_array[$in_parameter_key] = $in_parameter_value;
            $ret = true;
            }
        else
            {
            $this->set_m_error_message ( 'set_current_transaction_parameter: Invalid Parameter Key: "'.$in_parameter_key.'"' );
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Clear the Error Message.                                                     *
    ****************************************************************************************/
    function clear_m_error_message ()
    {
        $this->m_error_message = null;
    }
    
    /************************************************************************************//**
    *   \brief Return a value from the transaction stimuli array.                           *
    *                                                                                       *
    *   \returns A reference to a mixed. This is the value in the array.                    *
    ****************************************************************************************/
    function &get_m_outgoing_parameter ($in_parameter_key_string,                   ///< A string. The parameter key
                                        $in_parameter_secondary_key_string = null   ///< If the parameter has an embedded array, a key for that (optional)
                                        )
    {
        $ret = null;
        
        if ( !isset ( $this->m_outgoing_parameters[$in_parameter_key_string] ) )
            {
            $this->set_m_error_message ( 'get_m_outgoing_parameter: Invalid Key: "'.$in_parameter_key_string.'"' );
            }
        else
            {
            if ( isset ( $in_parameter_secondary_key_string ) )
                {
                if ( !isset ( $this->m_outgoing_parameters[$in_parameter_key_string][$in_parameter_secondary_key_string] ) )
                    {
                    $this->set_m_error_message ( 'get_m_outgoing_parameter: Invalid Secondary Key: "'.$in_parameter_secondary_key_string.'"' );
                    }
                else
                    {
                    $ret =& $this->m_outgoing_parameters[$in_parameter_key_string][$in_parameter_secondary_key_string];
                    }
                }
            else
                {
                $ret =& $this->m_outgoing_parameters[$in_parameter_key_string];
                }
            }
            
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Set a parameter value to the transaction stimulus array.                     *
    *                                                                                       *
    *   The outgoing array is "pre-keyed" with the possible parameters. You cannot change   *
    *   the keys or access the values by reference.                                         *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    ****************************************************************************************/
    function set_m_outgoing_parameter ( $in_parameter_key_string,   ///< A string. The parameter key
                                                $in_parameter_value_mixed   ///< A mixed value
                                                )
    {
        // We start by clearing any internal error message.
        $this->set_m_error_message ( null );

        if ( isset ( $this->m_outgoing_parameters[$in_parameter_key_string] ) )
            {   // Null is not allowed.
            if ( $in_parameter_value_mixed === null )
                {
                $in_parameter_value_mixed = '';
                }
            $this->m_outgoing_parameters[$in_parameter_key_string] = $in_parameter_value_mixed;
            }
        else
            {
            $this->set_m_error_message ( 'set_m_outgoing_parameter: Invalid Key: "'.$in_parameter_key_string.'"' );
            }
    }
    
    /************************************************************************************//**
    *   \brief Sets the outgoing parameter array to its default values.                     *
    ****************************************************************************************/
    private function set_default_outgoing ()
    {
        $this->m_outgoing_parameters = array    (
                                    /// Weekdays the meeting gathers.
                                    'weekdays'                  => array (),
                                    
                                    /// Meeting start time values
                                    'StartsAfterH'  => 0,
                                    'StartsAfterM'  => 0,
                                    'StartsBeforeH' => 0,
                                    'StartsBeforeM' => 0,
                                    
                                    /// Meeting duration values
                                    'MinDurationH'  => 0,
                                    'MinDurationM'  => 0,
                                    'MaxDurationH'  => 0,
                                    'MaxDurationM'  => 0,
                                    
                                    /// Search string values
                                    'SearchString'              => '',
                                    'SearchStringAll'           => false,
                                    'SearchStringExact'         => false,
                                    
                                    /// String address values
                                    'StringSearchIsAnAddress'   => false,
                                    'SearchStringRadius'        => 0,
                                    
                                    /// Location radius values
                                    'geo_width'                 => 0,
                                    'geo_width_km'              => 0,
                                    'long_val'                  => 0,
                                    'lat_val'                   => 0,
                                    
                                    /// Meeting data items (Array of keys completed at runtime)
                                    'meeting_key'               => array (),
                                    'meeting_key_value'         => '',
                                    'meeting_key_match_case'    => false,
                                    'meeting_key_contains'      => false,
                                    
                                    /// Sorting
                                    'sort_key'                  => array (  'weekday'   => true,
                                                                            'time'      => false,
                                                                            'town'      => false
                                                                        ),
                                    'sort_dir'                  => array (  'asc'       => true,
                                                                            'desc'      => false
                                                                        ),
                                    'sort_results_by_distance'  => false,   ///< This allows a sort of the results by distance.
                                    
                                    /// Service body IDs (Array of keys completed at runtime)
                                    'services'                  => array (),
                                    
                                    /// Meeting IDs -Array of values filled by implementor
                                    'meeting_ids'               => array (),
                                    
                                    /// Formats (Array of keys completed at runtime)
                                    'formats'                   => array (),
                                    
                                    /// Languages (Array of keys completed at runtime)
                                    'langs'                     => array (),
                                    
                                    /// This allows filtered responses.
                                    'data_field_key'            => null
                                    );
    }
    
    /************************************************************************************//**
    *   \brief Flush all the parameters, and the dynamically-filled outgoing ones.          *
    ****************************************************************************************/
    function flush_parameters ()
    {
        $this->set_m_server_version ( null );
        $this->set_m_current_transaction(null);
        $this->set_default_outgoing();
        $this->clear_m_error_message();
    }
    
    /************************************************************************************//**
    *   \brief Read all the standard parameters from the server                             *
    *                                                                                       *   
    *   This will set or clear the internal $m_error_message data member.                   *
    ****************************************************************************************/
    function load_standard_outgoing_parameters ()
    {
        // We start off with a clean slate.
        $this->clear_m_error_message();
        $this->set_m_outgoing_parameter('meeting_key', array());
        $this->set_m_outgoing_parameter('services', array());
        $this->set_m_outgoing_parameter('formats', array());
        $this->set_m_outgoing_parameter('langs', array());
        // Now, we get the values from the server.
        $this->get_server_formats();
        if ( !$this->get_m_error_message() )
            {
            $this->get_server_langs();
            if ( !$this->get_m_error_message() )
                {
                $this->get_server_service_bodies();
                if ( !$this->get_m_error_message() )
                    {
                    $this->get_server_meeting_keys();
                    }
                }
            }
    }
    
    /************************************************************************************//**
    *   \brief Execute a meeting search transaction                                         *
    *                                                                                       *   
    *   \returns An array of meeting data (mixed). Each element of the array will, itself,  *
    *   be an array, and will contain the meeting data. Null if no meetings were found.     *
    ****************************************************************************************/
    function meeting_search()
    {
        $ret = null;
        
        $error_message = null;  // We will collect any error messages.
        
        // We start by clearing any internal error message.
        $this->set_m_error_message ( $error_message );

        $uri = $this->get_m_root_uri(); // Get the cleaned URI.
        
        // For meeting searches, we ask for the response to be compressed, as it can be verbose.
        $uri .= '/client_interface/xml/index.php?switcher=GetSearchResults&compress_output=1';  // We will load the XML file.
        
        $serialized_list = null;
        
        if ( $transaction_params = $this->build_transaction_parameter_list($serialized_list) )
            {
            $uri .= $transaction_params;
            }
        // Get the XML data from the remote server. We will use GET.
        $data = self::call_curl ( $uri, false, $error_message );

        $ret['uri'] = $uri;
        $ret['serialized'] = $serialized_list;
        
        // Save any internal error message from the transaction.
        $this->set_m_error_message ( $error_message );
        
        // If we get a valid response, we then parse the XML using the PHP DOMDocument class.
        if ( !$this->get_m_error_message() && $data )
            {
            // We now have a whole bunch of meetings. Time to process the response, and turn it into usable data.
            $info_file = new DOMDocument;
            if ( $info_file instanceof DOMDocument )
                {
                if ( @$info_file->loadXML ( $data ) )
                    {
                    // OK. We have our meeting data in a DOMDocument. Time to start rockin' and' rollin'...
                    
                    // Get each of the meeting elements. This will create a DOMNodeList
                    $meeting_elements = $info_file->getElementsByTagName ( "row" );
                    
                    if ( $meeting_elements instanceof DOMNodeList )
                        {
                        foreach ( $meeting_elements as $meeting )
                            {
                            if ( $meeting instanceof DOMNode )
                                {
                                // Turn the DOMNode into an associative array.
                                $node = self::extract_meeting_data ( $meeting );
                                // Needs to be a valid meeting.
                                if ( $node )
                                    {
                                    // We save each meeting in an element with its ID as the key.
                                    $ret['meetings'][] = $node;
                                    }
                                }
                            }
                        }
                    }
                else
                    {
                    $this->set_m_error_message ( 'meeting_search: Failed to Load File ('.$uri.')' );
                    }
                }
            }
        elseif ( !$this->get_m_error_message() )
            {
            $this->set_m_error_message ( 'meeting_search: Invalid URI ('.$uri.')' );
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Unserialize a serialized transaction.                                        *
    *                                                                                       *
    *   This allows you to save a transaction, and re-use it. The transaction is not        *
    *   executed. You still need to call meeting_search(). However, this replaces the setup *
    *   steps (set_current_transaction_parameter). It clears the transaction parameters     *
    *   before it starts, so you cannot rely on any previous data being in the transaction  *
    *   array. You can add transaction data afterward.                                      *
    *                                                                                       *   
    *   \returns An array of string. If any of the given parameters cannot be set, their    *
    *   key is given here. It is not an error. Null if everything fit.                      *
    ****************************************************************************************/
    function apply_serialized_transaction( $in_serialized_list  ///< A string that holds the serialized transaction list.
                                            )
    {
        $ret = null;
        
        $new_array = unserialize ( $in_serialized_list );
        if ( isset ( $new_array ) && is_array ( $new_array ) && count ( $new_array ) )
            {
            $ret = array();
            $this->set_m_current_transaction(null); // Clear current transactions.
            foreach ( $new_array as $param_key => $param_value )
                {
                if ( $this->is_legal_transaction_key ( $param_key, $param_value ) )
                    {
                    $this->set_current_transaction_parameter ( $param_key, $param_value );
                    }
                else
                    {
                    $ret[] = $param_key;
                    }
                }
            }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Return the query parameter list for the next transaction in a serialized     *
    *   string.                                                                             *
    *                                                                                       *   
    *   \returns A string. The transaction parameter list in a serialized form.             *
    ****************************************************************************************/
    function get_serialized_transaction ()
    {
        return serialize ( $this->get_m_current_transaction() );
    }
    
    /************************************************************************************//**
    *   \brief Return the query parameter list for the next transaction.                    *
    *                                                                                       *   
    *   \returns A string. The transaction parameter list.                                  *
    ****************************************************************************************/
    private function build_transaction_parameter_list(  &$in_out_serialized_list    ///< A reference to a string that will hold the serialized transaction list.
                                                        )
    {
        $ret = null;
        
        $transaction_array =& $this->get_m_current_transaction();
        
        if ( is_array ( $transaction_array ) && count ( $transaction_array ) )
            {
            foreach ( $transaction_array as $param_key => &$param_value )
                {
                if ( $this->is_legal_transaction_key ( $param_key, $param_value ) )
                    {
                    if ( is_array ( $transaction_array[$param_key] ) && (count ( $transaction_array[$param_key] ) > 1) )
                        {
                        foreach ( $transaction_array[$param_key] as $param )
                            {
                            $ret .= '&';
                            if ( $param === true )  // Boolean is converted to a "1"
                                {
                                $param = 1;
                                }
                            $ret .= $param_key.'[]='.urlencode ( trim ( strval ( $param ) ) );
                            }
                        }
                    elseif ( (is_array ( $transaction_array[$param_key] ) && (count ( $transaction_array[$param_key] ) == 1)) || (!is_array ( $transaction_array[$param_key] ) && isset ( $transaction_array[$param_key] )) )
                        {
                        $ret .= '&';
                        $param = $transaction_array[$param_key];
                        if ( is_array ( $param ) )
                            {
                            $param = $param[0];
                            }
                        if ( $param === true )  // Boolean is converted to a "1"
                            {
                            $param = 1;
                            }
                        $ret .= $param_key.'='.urlencode ( trim ( strval ( $param ) ) );
                        }
                    else
                        {
                        $this->set_m_error_message ( 'build_transaction_parameter_list: Invalid Parameter Value: "'.$param_value.'" ('.$param_key.')' );
                        break;
                        }
                    }
                else
                    {
                    $this->set_m_error_message ( 'build_transaction_parameter_list: Invalid Parameter Key: "'.$param_key.'"' );
                    break;
                    }
                }
            }
        
        // This will be used to allow persistent state.
        $in_out_serialized_list = $this->get_serialized_transaction();
        return $ret;
    }

    /****************************************************************************************
    *                                   STATIC FUNCTIONS                                    *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief Accessor -Return the array of supported protocols.                           *
    *                                                                                       *
    *   \returns An array of strings.                                                       *
    ****************************************************************************************/
    static function get_m_supported_protocols ()
    {
        return self::$m_supported_protocols;
    }

    /************************************************************************************//**
    *   \brief Extract Service Body Information from A DOMDocument Node. Recursive function *
    *                                                                                       *
    *   \returns An array of arrays. The resulting array can be hierarchical, with elements *
    *   containing other elements. The array is associative, with a numeric key. This key   *
    *   is the Service body ID. The 'name' element is the readable name of the Service      *
    *   body, and the 'children' element (if it has one), contains "contained" Service body *
    *   elements.                                                                           *
    ****************************************************************************************/
    private static function extract_service_body_info ( $in_dom_node    ///< The DOMNode for one Service Body, extracted from XML.
                                                        )
    {
        $ret = array();
        $id = $in_dom_node->getAttribute('id');
        $name = $in_dom_node->getAttribute('sb_name');
        $description = $in_dom_node->getAttribute('sb_desc');
        $type = $in_dom_node->getAttribute('sb_type');
        $uri = $in_dom_node->getAttribute('sb_uri');
        $kmluri = $in_dom_node->getAttribute('sb_kmluri');
        $ret['name'] = $name;
        $ret['description'] = $description;
        $ret['type'] = $type;
        $ret['uri'] = $uri;
        $ret['kmluri'] = $kmluri;
        if ( $in_dom_node->hasChildNodes() )
            {
            foreach ( $in_dom_node->childNodes as $sb_node )
                {
                if ( method_exists ( $sb_node, 'getAttribute' ) && $sb_node->getAttribute('id') )
                    {
                    $ret['children'][$sb_node->getAttribute('id')] = self::extract_service_body_info ( $sb_node );
                    }
                }
            }
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Extracts the data from one meeting.                                          *
    *                                                                                       *   
    *   \returns An associative array, with all the meeting data.                           *
    ****************************************************************************************/
    static private function extract_meeting_data( $in_meeting_node  ///< The DOMNode for one meeting, extracted from XML.
                                        )
    {
        $ret = null;
        
        if ( $in_meeting_node->hasChildNodes() )
            {
            foreach ( $in_meeting_node->childNodes as $node )
                {
                $key = $node->nodeName;
                $value = $node->nodeValue;
                $ret[$key] = $value;
                }
            }
        
        return $ret;
    }

    /************************************************************************************//**
    *   \brief This is a function that returns the results of an HTTP call to a URI.        *
    *   It is a lot more secure than file_get_contents, but does the same thing.            *
    *                                                                                       *   
    *   \returns a string, containing the response. Null if the call fails to get any data. *
    ****************************************************************************************/
    static function call_curl ( $in_uri,                ///< A string. The URI to call.
                                $in_post = false,       ///< If false, the transaction is a GET, not a POST. Default is true.
                                &$error_message = null, ///< A string. If provided, any error message will be placed here.
                                &$http_status = null    ///< Optional reference to a string. Returns the HTTP call status.
                                )
    {
        $ret = null;
        
        // Make sure we don't give any false positives.
        if ( $error_message )
            {
            $error_message = null;
            }
        
        if ( !extension_loaded ( 'curl' ) ) // Must have cURL.
            {
            // If there is no error message variable passed, we die quietly.
            if ( isset ( $error_message ) )
                {
                $error_message = 'call_curl: The cURL extension is not available! This code will not work on this server!';
                }
            }
        else
            {
            // This gets the session as a cookie.
            if (isset ( $_COOKIE['PHPSESSID'] ) && $_COOKIE['PHPSESSID'] )
                {
                $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';

                session_write_close();
                }

            // Create a new cURL resource.
            $resource = curl_init();
        
            if ( isset ( $strCookie ) && $strCookie )
                {
                curl_setopt ( $resource, CURLOPT_COOKIE, $strCookie );
                }
        
            // If we will be POSTing this transaction, we split up the URI.
            if ( $in_post )
                {
                curl_setopt ( $resource, CURLOPT_POST, true );
                
                $spli = explode ( "?", $in_uri, 2 );
                
                if ( is_array ( $spli ) &&  (1 < count ( $spli )) )
                    {
                    $in_uri = $spli[0];
                    $in_params = $spli[1];
                    // Convert query string into an array using parse_str(). parse_str() will decode values along the way.
                    parse_str($in_params, $temp);
                    
                    // Now rebuild the query string using http_build_query(). It will re-encode values along the way.
                    // It will also take original query string params that have no value and appends a "=" to them
                    // thus giving them and empty value.
                    $in_params = http_build_query($temp);
                
                    curl_setopt ( $resource, CURLOPT_POSTFIELDS, $in_params );
                    }
                }
            
            if ( isset ( $strCookie ) && $strCookie )
                {
                curl_setopt ( $resource, CURLOPT_COOKIE, $strCookie );
                }

            // Set url to call.
            curl_setopt ( $resource, CURLOPT_URL, $in_uri );
            
            // Make curl_exec() function (see below) return requested content as a string (unless call fails).
            curl_setopt ( $resource, CURLOPT_RETURNTRANSFER, true );
            
            // By default, cURL prepends response headers to string returned from call to curl_exec().
            // You can control this with the below setting.
            // Setting it to false will remove headers from beginning of string.
            // If you WANT the headers, see the Yahoo documentation on how to parse with them from the string.
            curl_setopt ( $resource, CURLOPT_HEADER, false );
            
            // Allow  cURL to follow any 'location:' headers (redirection) sent by server (if needed set to true, else false- defaults to false anyway).
// Disabled, because some servers disable this for security reasons.
//          curl_setopt ( $resource, CURLOPT_FOLLOWLOCATION, true );
            
            // Set maximum times to allow redirection (use only if needed as per above setting. 3 is sort of arbitrary here).
            curl_setopt ( $resource, CURLOPT_MAXREDIRS, 3 );
            
            // Set connection timeout in seconds (very good idea).
            curl_setopt ( $resource, CURLOPT_CONNECTTIMEOUT, 10 );
            
            // Direct cURL to send request header to server allowing compressed content to be returned and decompressed automatically (use only if needed).
            curl_setopt ( $resource, CURLOPT_ENCODING, 'gzip,deflate' );
            
            // Pretend we're a browser, so that anti-cURL settings don't pooch us.
            curl_setopt ( $resource, CURLOPT_USERAGENT, "cURL Mozilla/5.0 (Windows NT 5.1; rv:21.0) Gecko/20130401 Firefox/21.0" ); 

            // Trust meeeee...
            curl_setopt ( $resource, CURLOPT_SSL_VERIFYPEER, FALSE);

            // Execute cURL call and return results in $content variable.
            $content = curl_exec ( $resource );
            
            // Check if curl_exec() call failed (returns false on failure) and handle failure.
            if ( $content === false )
                {
                // If there is no error message variable passed, we die quietly.
                if ( isset ( $error_message ) )
                    {
                    // Cram as much info into the error message as possible.
                    $error_message = "call_curl: curl failure calling $in_uri, ".curl_error ( $resource )."\n".curl_errno ( $resource );
                    }
                }
            else
                {
                // Do what you want with returned content (e.g. HTML, XML, etc) here or AFTER curl_close() call below as it is stored in the $content variable.
            
                // You MIGHT want to get the HTTP status code returned by server (e.g. 200, 400, 500).
                // If that is the case then this is how to do it.
                $http_status = curl_getinfo ($resource, CURLINFO_HTTP_CODE );
                }
            
            // Close cURL and free resource.
            curl_close ( $resource );
            
            // Maybe echo $contents of $content variable here.
            if ( $content !== false )
                {
                $ret = $content;
                }
            }
        
        return $ret;
    }
};
?>