<?php
/****************************************************************************************//**
*   \file   bmlt-unit-test-satellite-plugin.php                                             *
*                                                                                           *
*   \brief  This is a standalone unit test plugin of a BMLT satellite client.               *
*             
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

// Include the satellite driver class.
require_once ( dirname ( __FILE__ ).'/bmlt-cms-satellite-plugin.php' );

/****************************************************************************************//**
*   \class BMLTUTestPlugin                                                                  *
*                                                                                           *
*   \brief This is the class that implements and encapsulates the plugin functionality.     *
*   A single instance of this is created, and manages the plugin.                           *
*                                                                                           *
*   This plugin registers errors by echoing HTML comments, so look at the source code of    *
*   the page if things aren't working right.                                                *
********************************************************************************************/

class BMLTUTestPlugin extends BMLTPlugin
{
    /************************************************************************************//**
    *   \brief Constructor.                                                                 *
    ****************************************************************************************/
    function __construct ()
        {
        // This line is customized for the developer's test environment. If you are debugging on a local machine, you may want to change the first choice.
        self::$default_rootserver = 'https://bmlt.newyorkna.org/main_server';
        self::$default_map_center_latitude = 40.780281;
        self::$default_map_center_longitude = -73.965497;
        self::$default_map_zoom = 12;
        parent::__construct ();
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the AJAX callback target.                             *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_admin_ajax_base_uri()
        {
        return $this->get_ajax_base_uri().'?utest_string=admin';
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the basic admin form submit (action) URI              *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_admin_form_uri()
        {
        return $this->get_admin_ajax_base_uri();
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the AJAX callback target.                             *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_ajax_base_uri()
        {
        $port = $_SERVER['SERVER_PORT'] ;
        // IIS puts "off" in the HTTPS field, so we need to test for that.
        $https = (!empty ( $_SERVER['HTTPS'] ) && (($_SERVER['HTTPS'] !== 'off') || ($port == 443))); 
        $server_path = $_SERVER['SERVER_NAME'];
        $my_path = $_SERVER['PHP_SELF'];
        $server_path .= trim ( (($https && ($port != 443)) || (!$https && ($port != 80))) ? ':'.$port : '', '/' );
        $server_path = 'http'.($https ? 's' : '').'://'.$server_path.$my_path;
        return $server_path;
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the plugin directory.                                 *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_plugin_path()
        {
        $ret = isset ( $this->my_http_vars['base_url'] ) ? $this->my_http_vars['base_url'] : dirname( $this->get_ajax_base_uri() ).'/';
    
        return $ret;
        }
    
    /************************************************************************************//**
    *   \brief This uses the CMS text processor (t) to process the given string.            *
    *                                                                                       *
    *   This allows easier translation of displayed strings. All strings displayed by the   *
    *   plugin should go through this function.                                             *
    *                                                                                       *
    *   \returns a string, processed by WP.                                                 *
    ****************************************************************************************/
    function process_text (  $in_string  ///< The string to be processed.
                                    )
        {
        $in_string = htmlspecialchars ( $in_string );
            
        return $in_string;
        }

    /************************************************************************************//**
    *   \brief This gets the admin options from the database (allows CMS abstraction).      *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    protected function cms_get_option ( $in_option_key   ///< The name of the option
                                        )
        {        
        $ret = null;
        
        session_start ();
        
        if ( isset ( $_SESSION ) && isset ( $_SESSION ['bmlt_settings'] ) )
            {
            $row = unserialize ( $_SESSION ['bmlt_settings'] );
            }
        else
            {
            $row = array ( $this->geDefaultBMLTOptions() );
            }
        
        if ( $in_option_key != self::$admin2OptionsName )
            {
            $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));
            
            $ret = isset ( $row[$index - 1] ) ? $row[$index - 1] : $defaults[$index - 1];
            }
        else
            {
            $ret = array ( 'num_servers' => count ( $row ) );
            }

        return $ret;
        }
    
    /************************************************************************************//**
    *   \brief This gets the admin options from the database (allows CMS abstraction).      *
    ****************************************************************************************/
    protected function cms_set_option ( $in_option_key,   ///< The name of the option
                                        $in_option_value  ///< the values to be set (associative array)
                                        )
        {
        $ret = false;
        
        $index = 0;
        
        if ( $in_option_key != self::$admin2OptionsName )
            {
            $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));

            session_start ();
    
            if ( isset ( $_SESSION ) && isset ( $_SESSION ['bmlt_settings'] ) )
                {
                $row_data = unserialize ( $_SESSION ['bmlt_settings'] );
                }
            else
                {
                $row_data = array ( $this->geDefaultBMLTOptions() );
                }
            
            if ( isset ( $row_data ) && is_array ( $row_data ) && count ( $row_data ) )
                {
                $row_data[$index - 1] = $in_option_value;
                unset ( $_SESSION ['bmlt_settings'] );
                $_SESSION ['bmlt_settings'] = serialize ( $row_data );
    
                $ret = true;
                }
            }
        else
            {
            $ret = true; // Fake it, till you make it.
            }
        
        return $ret;
        }
    
    /************************************************************************************//**
    *   \brief Deletes a stored option (allows CMS abstraction).                            *
    ****************************************************************************************/
    protected function cms_delete_option ( $in_option_key   ///< The name of the option
                                        )
        {
        $ret = false;
        
        session_start ();
        
        if ( isset ( $_SESSION ['bmlt_settings'] ) )
            {
            $row = unserialize (  $_SESSION ['bmlt_settings'] );
            
            if ( $in_option_key != self::$admin2OptionsName )
                {
                $index = max ( 1, intval(str_replace ( self::$adminOptionsName.'_', '', $in_option_key ) ));
                
                unset ( $row[$index - 1] );
                
                $_SESSION ['bmlt_settings'] = serialize ( $row );
    
                $ret = true;
                }
            }
        
        return $ret;
        }

    /************************************************************************************//**
    *   \brief This gets the page meta for the given page. (allows CMS abstraction).        *
    *                                                                                       *
    *   \returns a mixed type, with the meta data                                           *
    ****************************************************************************************/
    protected function cms_get_post_meta (  $in_page_id,    ///< The ID of the page/post
                                            $in_settings_id ///< The ID of the meta tag to fetch
                                            )
        {
        $ret = null;
        
        return $ret;
        }

    /************************************************************************************//**
    *   \brief This function fetches the settings ID for a page (if there is one).          *
    *                                                                                       *
    *   If $in_check_mobile is set to true, then ONLY a check for mobile support will be    *
    *   made, and no other shortcodes will be checked.                                      *
    *                                                                                       *
    *   \returns a mixed type, with the settings ID.                                        *
    ****************************************************************************************/
    protected function cms_get_page_settings_id ($in_text,                  ///< Required (for the base version) content to check.
                                                 $in_check_mobile = false   ///< True if this includes a check for mobile. Default is false.
                                                )
        {
        $my_option_id = NULL;
        
        if ( !$in_check_mobile && isset ( $this->my_http_vars['bmlt_settings_id'] ) && is_array ($this->getBMLTOptions ( $this->my_http_vars['bmlt_settings_id'] )) )
            {
            $my_option_id = $this->my_http_vars['bmlt_settings_id'];
            }
        else
            {
            $support_mobile = self::get_shortcode ( $in_text, 'bmlt_mobile');
            
            if ( $support_mobile === true )
                {
                $options = $this->getBMLTOptions ( 1 );
                $support_mobile = strval ( $options['id'] );
                }

            if ( $in_check_mobile && $support_mobile && !isset ( $this->my_http_vars['BMLTPlugin_mobile'] ) && (self::mobile_sniff_ua ($this->my_http_vars) != 'xhtml') )
                {
                $my_option_id = $support_mobile;
                }
            elseif ( !$in_check_mobile )
                {
                if ( isset ( $this->my_http_vars['bmlt_settings_id'] ) && intval ( $this->my_http_vars['bmlt_settings_id'] ) )
                    {
                    $my_option_id = intval ( $this->my_http_vars['bmlt_settings_id'] );
                    }
                elseif ( $in_content = (isset ( $in_content ) && $in_content) ? $in_content : $in_text )
                    {
                    $my_option_id_content = parent::cms_get_page_settings_id ( $in_content, $in_check_mobile );
                    
                    $my_option_id = $my_option_id_content ? $my_option_id_content : isset ( $my_option_id ) ? $my_option_id : null;
                    }
                
                if ( !isset ( $my_option_id ) || !$my_option_id )   // If nothing else gives, we go for the default (first) settings.
                    {
                    $options = $this->getBMLTOptions ( 1 );
                    $my_option_id = $options['id'];
                    }
                }
            }
        
        return $my_option_id ;
        }
        
    /************************************************************************************//**
    *                                   THE CMS CALLBACKS                                   *
    ****************************************************************************************/
        
    /************************************************************************************//**
    *   \brief Presents the admin page.                                                     *
    ****************************************************************************************/
    function admin_page ( )
        {
        echo $this->return_admin_page ( );
        }
        
    /************************************************************************************//**
    *   \brief returns any necessary head content.                                          *
    ****************************************************************************************/
    function standard_head ( $in_text = null   ///< This is the page content text.
                            )
        {
        $this->ajax_router ( );
        $load_head = false;   // This is a throwback. It prevents the GM JS from being loaded if there is no directly specified settings ID.
        $head_content = "<!-- Added by the BMLT plugin 3.0. -->\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7\" />\n<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
        
        $support_mobile = $this->cms_get_page_settings_id ( $in_text, true );
        
        if ( $support_mobile )
            {
            $mobile_options = $this->getBMLTOptions_by_id ( $support_mobile );
            }
        else
            {
            $support_mobile = null;
            }
        
        $options = $this->getBMLTOptions_by_id ( $this->cms_get_page_settings_id($in_text) );

        if ( $support_mobile && is_array ( $mobile_options ) && count ( $mobile_options ) )
            {
            $mobile_url = $_SERVER['PHP_SELF'].'?BMLTPlugin_mobile&bmlt_settings_id='.$support_mobile;

            if ( isset ( $this->my_http_vars['WML'] ) )
                {
                $mobile_url .= '&WML='.intval ( $this->my_http_vars['WML'] );
                }
            if ( isset ( $this->my_http_vars['simulate_smartphone'] ) )
                {
                $mobile_url .= '&simulate_smartphone';
                }
            ob_end_clean();
            header ( "location: $mobile_url" );
            die ( );
            }
        
        $this->my_http_vars['start_view'] = $options['bmlt_initial_view'];
        
        $this->load_params ( );
        
        $root_server_root = $options['root_server'];
        
        $url = $this->get_plugin_path();
        
        $head_content .= "\n".'<style type="text/css">'."\n";
        $temp = self::stripFile ( 'styles.css', $options['theme'] );
        if ( $temp )
            {
            $image_dir_path = $this->get_plugin_path() . '/themes/' . $options['theme'] . '/images/';
            $temp = str_replace ( '##-IMAGEDIR-##', $image_dir_path, $temp );
            $head_content .= "\t$temp\n";
            }
        $temp = self::stripFile ( 'nouveau_map_styles.css', $options['theme'] );
        if ( $temp )
            {
            $image_dir_path = $this->get_plugin_path() . '/themes/' . $options['theme'] . '/images/';
            $temp = str_replace ( '##-IMAGEDIR-##', $image_dir_path, $temp );
            $head_content .= "\t$temp\n";
            }
        
        $head_content .= self::stripFile ( 'table_styles.css' ) . "\n";
        $head_content .= self::stripFile ( 'quicksearch.css' ) . "\n";
    
        $dirname = dirname ( __FILE__ ) . '/themes';
        $dir = new DirectoryIterator ( $dirname );

        foreach ( $dir as $fileinfo )
            {
            if ( !$fileinfo->isDot () )
                {
                $fName = $fileinfo->getFilename ();

                $temp = self::stripFile ( "table_styles.css", $fName );
                if ( $temp )
                    {
                    $image_dir_path = $this->get_plugin_path() . '/themes/' . $fName . '/images/';
                    $temp = str_replace ( '##-IMAGEDIR-##', $image_dir_path, $temp );
                    $head_content .= "\t$temp\n";
                    }
                
                $temp = self::stripFile ( "quicksearch.css", $fName );
                if ( $temp )
                    {
                    $head_content .= "\t$temp\n";
                    }
                }
            }
        
        $head_content .= '</style>';
        $head_content .= "\n".'<style type="text/css">'."\n";
            
        $head_content .= "\n/* Responsiveness */\n";
        
        $head_content .= self::stripFile ( 'responsiveness.css' );
        
        $head_content .= '</style>'."\n";

        if ( $root_server_root )
            {
            $root_server = $root_server_root."/client_interface/xhtml/index.php";
            
            $additional_css = '.bmlt_container * {margin:0;padding:0;text-align:center }';
            
            if ( $options['additional_css'] )
                {
                $additional_css .= $options['additional_css'];
                }
            
            if ( $additional_css )
                {
                $head_content .= '<style type="text/css">'.preg_replace ( "|\s+|", " ", $additional_css ).'</style>';
                }
            }
            
        $head_content .= '<script type="text/javascript">';
        
        $head_content .= self::stripFile ( 'javascript.js' );

        if ( $this->get_shortcode ( $in_text, 'bmlt_quicksearch' ) )
            {
            $head_content .= self::stripFile ( 'quicksearch.js' ) . (defined ( '_DEBUG_MODE_' ) ? "\n" : '');
            }
        
        if ( $this->get_shortcode ( $in_text, 'bmlt_map' ) )
            {
            $head_content .= self::stripFile ( 'map_search.js' );
            }
        
        if ( $this->get_shortcode ( $in_text, 'bmlt_mobile' ) )
            {
            $head_content .= self::stripFile ( 'fast_mobile_lookup.js' );
            }
    
        $head_content .= '</script>';
        
        return $head_content;
        }
        
    /************************************************************************************//**
    *   \brief Returns any necessary head content for the admin.                            *
    ****************************************************************************************/
    function admin_head ( )
        {
        $this->admin_ajax_handler ( );
        
        $head_content = $this->standard_head ( );   // We start with the standard stuff.
        
        $head_content .= '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>';  // Load the Google Maps stuff for our map.
        
        $head_content .= '<link rel="stylesheet" type="text/css" href="';
        
        $url = $this->get_plugin_path();
        
        $head_content .= htmlspecialchars ( $url );
        
        $head_content .= 'admin_styles.css" />';
        
        $head_content .= '<script type="text/javascript" src="';
        
        $head_content .= htmlspecialchars ( $url );
        
        if ( !defined ('_DEBUG_MODE_' ) )
            {
            $head_content .= 'js_stripper.php?filename=';
            }
        
        $head_content .= 'admin_javascript.js"></script>';
            
        return $head_content;
        }
};

/****************************************************************************************//**
*                                   MAIN CODE CONTEXT                                       *
********************************************************************************************/
global $BMLTPluginOp;

if ( !isset ( $BMLTPluginOp ) && class_exists ( "BMLTUTestPlugin" ) )
    {
    $BMLTPluginOp = new BMLTUTestPlugin();
    }
?>