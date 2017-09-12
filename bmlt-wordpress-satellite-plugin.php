<?php
/****************************************************************************************//**
*   \file   bmlt-wordpress-satellite-plugin.php                                             *
*                                                                                           *
*   \brief  This is a WordPress plugin of a BMLT satellite client.                          *
*                                                                                           *
*   These need to be without the asterisks, as WP parses them.                              *
Plugin Name: BMLT WordPress Satellite
Plugin URI: http://bmlt.magshare.net
Author: MAGSHARE
Description: This is a WordPress plugin satellite of the Basic Meeting List Toolbox.
Version: 3.5.2
Install: Drop this directory into the "wp-content/plugins/" directory and activate it.
********************************************************************************************/

define ( '_DEBUG_MODE_', 1 ); //Uncomment for easier JavaScript debugging.

global $bmlt_localization;  ///< Use this to control the localization.

if ( isset ( $_COOKIE ) && isset ( $_COOKIE['bmlt_lang_selector'] ) && $_COOKIE['bmlt_lang_selector'] )
    {
    $bmlt_localization = $_COOKIE['bmlt_lang_selector'];
    }
else
    {
    if ( !isset ( $bmlt_localization ) || !$bmlt_localization )
        {
        $language = get_locale();
    
        if ( $language )
            {
            $bmlt_localization = substr ( $language, 0, 2 );
            }
        }
    elseif ( function_exists ( 'get_option' ) )
        {
        $bmlt_localization = get_option ( 'BMLT_lang_base' );
        }
    }
    
if ( !isset ( $bmlt_localization ) || !$bmlt_localization )
    {
    $bmlt_localization = 'en';  ///< Last-ditch default value.
    }

if ( function_exists ( 'update_option' ) )
    {
    update_option ( 'BMLT_lang_base', $bmlt_localization );
    }

// Include the satellite driver class.
require_once ( dirname ( __FILE__ ).'/BMLT-Satellite-Base-Class/bmlt-cms-satellite-plugin.php' );

/****************************************************************************************//**
*   \class BMLTWPPlugin                                                                     *
*                                                                                           *
*   \brief This is the class that implements and encapsulates the plugin functionality.     *
*   A single instance of this is created, and manages the plugin.                           *
*                                                                                           *
*   This plugin registers errors by echoing HTML comments, so look at the source code of    *
*   the page if things aren't working right.                                                *
********************************************************************************************/

class BMLTWPPlugin extends BMLTPlugin
{
    var $plugin_read_me_loc = 'http://plugins.trac.wordpress.org/browser/bmlt-wordpress-satellite-plugin/trunk/readme.txt?format=txt';
    var $plugin_file_name = 'bmlt-wordpress-satellite-plugin/bmlt-wordpress-satellite-plugin.php';
    var $plugin_update_message_1 = 'The WordPress BMLT Plugin has been updated. Here is a change list, so you can see what\'s been changed or fixed:';
    var $plugin_update_message_2 = '= Latest Version =';
    var $plugin_update_message_3 = 'Release Date:';
    var $plugin_settings_name = 'Settings';
    var $plugin_is_main = true;

    /************************************************************************************//**
    *   \brief Constructor.                                                                 *
    ****************************************************************************************/
    function __construct ()
        {
        parent::__construct ();
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the AJAX callback target.                             *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_admin_ajax_base_uri()
        {
        return $this->get_admin_form_uri();
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the basic admin form submit (action) URI              *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_admin_form_uri()
        {
        return $_SERVER['PHP_SELF'].'?page=bmlt-wordpress-satellite-plugin.php';
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
        
        if ( function_exists ( 'get_post_meta' ) )
            {
            $ret = get_post_meta ( $in_page_id, $in_settings_id, true );
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (cms_get_post_meta)! No get_post_meta()! -->";
            }
        
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
    protected function cms_get_page_settings_id ($in_content,               ///< Required (for the base version) content to check.
                                                 $in_check_mobile = false   ///< True if this includes a check for mobile. Default is false.
                                                )
        {
        $my_option_id = null;
        $page_id = null;
        $page = get_page($page_id);
        
        if ( !$in_check_mobile && isset ( $this->my_http_vars['bmlt_settings_id'] ) && is_array ($this->getBMLTOptions ( $this->my_http_vars['bmlt_settings_id'] )) )
            {
            $my_option_id = $this->my_http_vars['bmlt_settings_id'];
            }
        else
            {
            $support_mobile = preg_replace ( '/\D/', '', trim ( $this->cms_get_post_meta ( $page->ID, 'bmlt_mobile' ) ) );
            
            if ( !$support_mobile && $in_check_mobile )
                {
                $support_mobile = self::get_shortcode ( $in_content, 'bmlt_mobile');
                
                if ( $support_mobile === true )
                    {
                    $options = $this->getBMLTOptions ( 1 );
                    $support_mobile = strval ( $options['id'] );
                    }
                }

            if ( $in_check_mobile && $support_mobile && !isset ( $this->my_http_vars['BMLTPlugin_mobile'] ) && (self::mobile_sniff_ua ($this->my_http_vars) != 'xhtml') )
                {
                $my_option_id = $support_mobile;
                }
            elseif ( !$in_check_mobile )
                {
                $my_option_id = intval ( preg_replace ( '/\D/', '', trim ( $this->cms_get_post_meta ( $page->ID, 'bmlt_settings_id' ) ) ) );
                if ( isset ( $this->my_http_vars['bmlt_settings_id'] ) && intval ( $this->my_http_vars['bmlt_settings_id'] ) )
                    {
                    $my_option_id = intval ( $this->my_http_vars['bmlt_settings_id'] );
                    }
                elseif ( $in_content = $in_content ? $in_content : $page->post_content )
                    {
                    $my_option_id_content = parent::cms_get_page_settings_id ( $in_content, $in_check_mobile );
                    
                    $my_option_id = $my_option_id_content ? $my_option_id_content : $my_option_id;
                    }
                
                if ( !$my_option_id )   // If nothing else gives, we go for the default (first) settings.
                    {
                    $options = $this->getBMLTOptions ( 1 );
                    $my_option_id = $options['id'];
                    }
                }
            }
        
        return $my_option_id;
        }
        
    /************************************************************************************//**
    *                               THE WORDPRESS CALLBACKS                                 *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief Sets the flag if this is a singular post or page. Otherwise, we don't show.  *
    ****************************************************************************************/
    function stop_filter_if_not_main ()
        {
        $this->plugin_is_main = is_singular();
        }
    
    /************************************************************************************//**
    *   \brief Presents the admin page.                                                     *
    ****************************************************************************************/
    function admin_page ( )
        {
        echo $this->return_admin_page ( );
        }
       
    /************************************************************************************//**
    *   \brief Presents the admin menu options.                                             *
    *                                                                                       *
    * NOTE: This function requires WP. Most of the rest can probably be more easily         *
    * converted for other CMSes.                                                            *
    ****************************************************************************************/
    function option_menu ( )
        {
        global $bmlt_localization;
        
        $this->adapt_to_lang($bmlt_localization);
        if ( function_exists ( 'add_options_page' ) && (self::get_plugin_object() instanceof BMLTPlugin) )
            {
            $options_title = $this->my_current_language->local_options_title;
            $menu_string = $this->my_current_language->local_menu_string;
            
            add_options_page ( $options_title, $menu_string, 9, basename ( __FILE__ ), array ( self::get_plugin_object(), 'admin_page' ) );
            }
        elseif ( !function_exists ( 'add_options_page' ) )
            {
            echo "<!-- BMLTPlugin ERROR (option_menu)! No add_options_page()! -->";
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (option_menu)! No BMLTPlugin Object! -->";
            }
        }
    
    /************************************************************************************//**
    *   \brief Massages the page content.                                                   *
    *                                                                                       *
    *   \returns a string, containing the "massaged" content.                               *
    ****************************************************************************************/
    function content_filter ( $in_the_content   ///< The content in need of filtering.
                            )
        {
        if ( $this->plugin_is_main )
            {
            // Simple searches can be mixed in with other content.
            $in_the_content = parent::content_filter ( $in_the_content );

            $count = 0;

            $in_the_content = $this->display_popup_search ( $in_the_content, $this->cms_get_post_meta ( get_the_ID(), 'bmlt_simple_searches' ), $count );
            }
        else    // If we are not in a page in the main display, we remove the shortcodes.
            {
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt_map', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'simple_search_list', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt_mobile', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt_simple', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt_changes', '' );
            $in_the_content = self::replace_shortcode ( $in_the_content, 'bmlt_table', '' );
            }
        
        return $in_the_content;
        }

    /************************************************************************************//**
    *   \brief This was cribbed from the W3TC (TotalCache) plugin.                          *
    *                                                                                       *
    *   This function adds a settings link to the plugin listing.                           *
    ****************************************************************************************/
    function filter_plugin_actions ( $links,
                                    $file
                                    )
        {
        static $this_plugin;
        
        if ( !$this_plugin && function_exists ( 'plugin_basename' ) )
            {
            if ( $file == plugin_basename ( __FILE__ ) )
                {
                $settings_link = '<a href="options-general.php?page=' . basename ( __FILE__ ) . '">' . $this->process_text ( $this->plugin_settings_name ) . '</a>';
                array_unshift ( $links, $settings_link );
                }
            }

        return $links;
        }

    /************************************************************************************//**
    *   \brief This is called before anything else.                                         *
    ****************************************************************************************/
    function filter_init ()
        {
        ob_start(); // This prevents themes and plugins from screwing us over.
        $this->ajax_router();
        }
        
    /************************************************************************************//**
    *                                BASE CLASS OVERRIDES                                   *
    ****************************************************************************************/
        
    /************************************************************************************//**
    *   \brief Echoes any necessary head content.                                           *
    ****************************************************************************************/
    function standard_head ( )
        {
        $load_head = false;   // This is a throwback. It prevents the GM JS from being loaded if there is no directly specified settings ID.
        $head_content = "<!-- Added by the BMLT plugin 3.X. -->\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=EmulateIE7\" />\n<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />\n";
        $load_head = true;
        $additional_css = '';
        
        // If you specify the bmlt_mobile custom field in this page (not post), then it can force the browser to redirect to a mobile handler.
        // The value of bmlt_mobile must be the settings ID of the server you want to handle the mobile content.
        // Post redirectors are also handled, but at this point, only the page will be checked.
        $page_id = null;
        $page = get_page($page_id);
        
        $support_mobile = $this->cms_get_page_settings_id ( $page->post_content, true );
        
        if ( $support_mobile )
            {
            $mobile_options = $this->getBMLTOptions_by_id ( $support_mobile );
            }
        else
            {
            $support_mobile = null;
            }
        
        $options = $this->getBMLTOptions_by_id ( $this->cms_get_page_settings_id( $page->post_content ) );

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
        
        if ( !$this->get_shortcode ( $page->post_content, 'bmlt' ) )
            {
            $load_head = false;
            }
        
        $this->my_http_vars['start_view'] = $options['bmlt_initial_view'];
        
        $this->load_params ( );
        
        $root_server_root = $options['root_server'];
        
        $head_content .= '<meta name="BMLT-Root-URI" content="'.htmlspecialchars ( $root_server_root ).'" />';
        
        $url = $this->get_plugin_path();
        
        if ( file_exists ( dirname ( __FILE__ ).'/BMLT-Satellite-Base-Class/themes/'.$options['theme'].'/styles.css' ) )
            {
            $head_content .= '<link rel="stylesheet" type="text/css" href="';
        
            $head_content .= htmlspecialchars ( $url.'themes/'.$options['theme'].'/' );
        
            $head_content .= 'styles.css" />';
            }
        
        if ( file_exists ( dirname ( __FILE__ ).'/BMLT-Satellite-Base-Class/themes/'.$options['theme'].'/nouveau_map_styles.css' ) )
            {
            $head_content .= '<link rel="stylesheet" type="text/css" href="';
        
            $head_content .= htmlspecialchars ( $url.'themes/'.$options['theme'].'/' );
        
            $head_content .= 'nouveau_map_styles.css" />';
            }

        $head_content .= "\n".'<style type="text/css">'."\n";
    
        $head_content .= "\t".$this->loadTable_StyleFile ( "" ) . "\n";
        
        $dirname = dirname ( __FILE__ ) . '/BMLT-Satellite-Base-Class/themes';
        $dir = new DirectoryIterator ( $dirname );

        foreach ( $dir as $fileinfo )
            {
            if ( !$fileinfo->isDot () )
                {
                $fName = $fileinfo->getFilename ();
                $temp = $this->loadTable_StyleFile ( "themes/$fName/" );
                if ( $temp )
                    {
                    $head_content .= "\t$temp\n";
                    }
                }
            }
        
        $head_content .= "</style>\n";
        
        $additional_css = '#content div.bmlt_table_display_div ul.bmlt_table_data_ul { width:100%; }';
        $additional_css .= ' #content div.bmlt_table_display_div ul.bmlt_table_header_weekday_list,#content div.bmlt_table_display_div ul.bmlt_table_header_weekday_list li,#content div.bmlt_table_display_div div.bmlt_table_div ul,#content div.bmlt_table_display_div div.bmlt_table_div ul li { list-style-type:none;list-style-position:outside; }';
        $additional_css .= ' #content div.bmlt_table_display_div div.bmlt_table_div ul.bmlt_table_data_ul,#content div.bmlt_table_display_div div.bmlt_table_div ul.bmlt_table_data_ul li.bmlt_table_data_ul_li { margin:0;padding:0; } ';
        $additional_css .= ' #content div.bmlt_table_display_div ul.bmlt_table_data_ul li.bmlt_table_data_ul_li ul { margin:0;padding:0; } ';

        if ( $root_server_root )
            {
            $additional_css .= '.bmlt_container * {margin:0;padding:0;text-align:center }';
            
            if ( $options['additional_css'] )
                {
                $additional_css .= $options['additional_css'];
                }
            
            if ( $additional_css )
                {
                $head_content .= '<style type="text/css">'.preg_replace ( "|\s+|", " ", $additional_css ).'</style>';
                }
            }
        
        $head_content .= '<script type="text/javascript" src="';
        
        $head_content .= htmlspecialchars ( $url );
        
        $head_content .= 'javascript.js"></script>';

        echo $head_content;
        }
        
    /************************************************************************************//**
    *   This will strip the cruft out of the CSS files.                                     *
    *   \returns a string, with the stripped CSS.                                           *
    ****************************************************************************************/
    function loadTable_StyleFile ( $in_theme_dirname    ///< The dirname of the theme to use. It should include the "themes" prefix dir.
                                    )
        {
        $pathname = dirname ( __FILE__ )."/BMLT-Satellite-Base-Class/$in_theme_dirname"."table_styles.css";
        if ( file_exists ( $pathname ) )
            {
            $opt = file_get_contents ( $pathname );
            $opt = preg_replace( "|\/\*.*?\*\/|s", "", $opt );
            $opt = preg_replace( "|\s+|s", " ", $opt );
            return $opt;
            }
            
        return "";
        }
        
    /************************************************************************************//**
    *   \brief Echoes any necessary head content for the admin.                             *
    ****************************************************************************************/
    function admin_head ( )
        {
        $this->standard_head ( );   // We start with the standard stuff.
        
        $options = $this->getBMLTOptions(1);    // All options contain the admin key.
        $key = $options['google_api_key'];
        
        // Include the Google Maps API V3 files.
        $head_content = '<script type="text/javascript" src="https://maps.google.com/maps/api/js?key='.$key.'"></script>';  // Load the Google Maps stuff for our map.
        
        if ( function_exists ( 'plugins_url' ) )
            {
            $head_content .= '<link rel="stylesheet" type="text/css" href="';
            
            $url = $this->get_plugin_path();
            
            $head_content .= htmlspecialchars ( $url );
            
            $head_content .= 'admin_styles.css" />';
            
            $head_content .= '<script type="text/javascript" src="';
            
            $head_content .= htmlspecialchars ( $url );
            
            $head_content .= 'admin_javascript.js"></script>';
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (head)! No plugins_url()! -->";
            }
            
        echo $head_content;
        }
    
    /************************************************************************************//**
    *   \brief This gets the admin options from the database (allows CMS abstraction).      *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    protected function cms_get_option ( $in_option_key    ///< The name of the option
                                    )
        {
        $ret = $this->geDefaultBMLTOptions();
        
        if ( function_exists ( 'get_option' ) )
            {
            $ret = get_option ( $in_option_key );
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (cms_get_option)! No get_option()! -->";
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
        // Added a test, to prevent the creation of multiple empty settings by low-rank users trying SQL injections.
        if ( function_exists ( 'update_option' ) && is_array ( $in_option_value ) && count ( $in_option_value ) )
            {
            $ret = update_option ( $in_option_key, $in_option_value );
            }
        elseif ( is_array ( $in_option_value ) && count ( $in_option_value ) ) 
            {
            echo "<!-- BMLTPlugin ERROR (cms_set_option)! No update_option()! -->";
            }
        }
    
    /************************************************************************************//**
    *   \brief Deletes a stored option (allows CMS abstraction).                            *
    ****************************************************************************************/
    protected function cms_delete_option ( $in_option_key   ///< The name of the option
                                        )
        {
        if ( function_exists ( 'delete_option' ) )
            {
            $ret = delete_option ( $in_option_key );
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (cms_delete_option)! No delete_option()! -->";
            }
        }
    
    /************************************************************************************//**
    *   \brief Return an HTTP path to the plugin directory.                                 *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    protected function get_plugin_path()
        {
        $url = '';
        if ( function_exists ( 'plugins_url' ) )
            {
            if ( plugins_url() )
                {
                $url = plugins_url()."/bmlt-wordpress-satellite-plugin/BMLT-Satellite-Base-Class/";
                }
            elseif ( defined ('WP_PLUGIN_URL' ) )
                {
                $url = WP_PLUGIN_URL."/bmlt-wordpress-satellite-plugin/BMLT-Satellite-Base-Class/";
                }
            else
                {
                echo "<!-- BMLTPlugin ERROR (get_plugin_path)! Empty plugins_url() and no WP_PLUGIN_URL! -->";
                }
            }
        elseif ( !function_exists ( 'plugins_url' ) && defined ('WP_PLUGIN_URL' ) )
            {
            $url = WP_PLUGIN_URL."/bmlt-wordpress-satellite-plugin/BMLT-Satellite-Base-Class/";
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (get_plugin_path)! No plugins_url() and no WP_PLUGIN_URL! -->";
            }
        
        return $url;
        }
        
    /************************************************************************************//**
    *   \brief Sets up the admin and handler callbacks.                                     *
    ****************************************************************************************/
    protected function set_callbacks ( )
        {
        if ( function_exists ( 'add_filter' ) )
            {
            add_filter ( 'the_content', array ( self::get_plugin_object(), 'content_filter')  );
            add_filter ( 'wp_head', array ( self::get_plugin_object(), 'standard_head' ) );
            add_filter ( 'admin_head', array ( self::get_plugin_object(), 'admin_head' ) );
            add_filter ( 'plugin_action_links', array ( self::get_plugin_object(), 'filter_plugin_actions' ), 10, 2 );
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (set_callbacks)! No add_filter()! -->";
            }
        
        if ( function_exists ( 'add_action' ) )
            {
            add_action ( 'pre_get_posts', array ( self::get_plugin_object(), 'stop_filter_if_not_main' ) );
            add_action ( 'admin_init', array ( self::get_plugin_object(), 'admin_ajax_handler' ) );
            add_action ( 'admin_menu', array ( self::get_plugin_object(), 'option_menu' ) );
            add_action ( 'init', array ( self::get_plugin_object(), 'filter_init' ) );
            }
        else
            {
            echo "<!-- BMLTPlugin ERROR (set_callbacks)! No add_action()! -->";
            }
        }

    /************************************************************************************//**
    *   \brief This uses the WordPress text processor (__) to process the given string.     *
    *                                                                                       *
    *   This allows easier translation of displayed strings. All strings displayed by the   *
    *   plugin should go through this function.                                             *
    *                                                                                       *
    *   \returns a string, processed by WP.                                                 *
    ****************************************************************************************/
    protected function process_text (  $in_string  ///< The string to be processed.
                                    )
        {
        if ( function_exists ( '__' ) )
            {
            $in_string = htmlspecialchars ( __( $in_string, 'BMLTPlugin' ) );
            }
        else
            {
            echo "<!-- BMLTPlugin Warning (process_text): __() does not exist! -->";
            }
            
        return $in_string;
        }
};

/****************************************************************************************//**
*                                   MAIN CODE CONTEXT                                       *
********************************************************************************************/
global $BMLTPluginOp;

if ( !isset ( $BMLTPluginOp ) && class_exists ( "BMLTWPPlugin" ) )
    {
    $BMLTPluginOp = new BMLTWPPlugin();
    }
?>