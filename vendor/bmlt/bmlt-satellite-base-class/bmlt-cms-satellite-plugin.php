<?php
/****************************************************************************************//**
*   \file   bmlt-cms-satellite-plugin.php                                                   *
*                                                                                           *
*   \brief  This is a generic CMS plugin class for a BMLT satellite client.                 *
*   \version 3.9.12                                                                          *
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

// define ( '_DEBUG_MODE_', 1 ); //Uncomment for easier JavaScript debugging.

// Include the satellite driver class.
if (!defined('ROOTPATH')) {
    require_once(__DIR__.'/../bmlt-satellite-driver/bmlt_satellite_controller.class.php');
} else {
    require_once(ROOTPATH .'/vendor/bmlt/bmlt-satellite-driver/bmlt_satellite_controller.class.php');
}

global $g_lang_keys;
global $g_my_languages;

$g_my_languages = array();
$g_lang_keys = array();

$dirname = dirname(__FILE__) . "/lang";
$dir = new DirectoryIterator($dirname);

foreach ($dir as $fileinfo) {
    if (!$fileinfo->isDot()) {
        $fName = $fileinfo->getFilename();
        if (($fName != "index.php") && preg_match("|^lang_|", $fName)) {
            $fPath = $dirname . "/" . $fName;
            if ($file = fopen($fPath, "r")) {
                $line0 = fgets($file);
                $line1 = fgets($file);
                $lang_name = trim(substr($line1, 3));
                $lang_key = trim(substr($fName, 5, -4));
                if ($lang_name && $lang_key) {
                    include_once($fPath);

                    $eval_string = '$lang_instance = new BMLT_Localized_BaseClass_' . $lang_key . '();';
                    eval($eval_string);
                    $g_my_languages[$lang_key] = $lang_instance;
                }
            }
        }
    }
}

// This is the cookie (and parameter) name used to explicitly set the language used by the client.
if (!defined('_LANG_COOKIE_NAME')) {
    define('_LANG_COOKIE_NAME', 'bmlt_admin_lang_pref');
}

global $bmlt_localization;  ///< Use this to control the localization.
$tmp_local = false;         ///< This will hold the selected language as we test for an explicit one.

// We can use a cookie to store the language pref. The name is historical, and comes from an existing cookie for the Root Server.
if (isset($_COOKIE) && isset($_COOKIE[_LANG_COOKIE_NAME]) && $_COOKIE[_LANG_COOKIE_NAME]) {
    $tmp_local = $_COOKIE[_LANG_COOKIE_NAME];
}

// GET overpowers cookie.
if (isset($_GET['lang_enum']) && $_GET['lang_enum']) {
    $tmp_local = $_GET['lang_enum'];
}

// POST overpowers GET.
if (isset($_POST['lang_enum']) && $_POST['lang_enum']) {
    $tmp_local = $_POST['lang_enum'];
}

// This allows us a "superparameter" to override the standard 'lang_enum'.
// GET overpowers cookie.
if (isset($_GET[_LANG_COOKIE_NAME]) && $_GET[_LANG_COOKIE_NAME]) {
    $tmp_local = $_GET[_LANG_COOKIE_NAME];
}

// POST overpowers GET.
if (isset($_POST[_LANG_COOKIE_NAME]) && $_POST[_LANG_COOKIE_NAME]) {
    $tmp_local = $_POST[_LANG_COOKIE_NAME];
}

// If the language is not valid, we fall back on the existing global.
if ((!$tmp_local || !file_exists(dirname(__FILE__)."/lang/lang_".$tmp_local.".php")) && isset($bmlt_localization) && $bmlt_localization) {   // Fall back on a previously set global.
    $tmp_local = $bmlt_localization;
}

// If the language is not valid, we fall back on the existing global.
if (!$tmp_local) {
    $tmp_local = 'en';
}

$bmlt_localization = $tmp_local;

/****************************************************************************************//**
*   \class BMLTPlugin                                                                       *
*                                                                                           *
*   \brief This is the class that implements and encapsulates the plugin functionality.     *
*   A single instance of this is created, and manages the plugin.                           *
*                                                                                           *
*   This plugin registers errors by echoing HTML comments, so look at the source code of    *
*   the page if things aren't working right.                                                *
********************************************************************************************/
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
abstract class BMLTPlugin
// phpcs:enable PSR1.Classes.ClassDeclaration.MissingNamespace
{
    /************************************************************************************//**
    *                           STATIC DATA MEMBERS (SINGLETON)                             *
    ****************************************************************************************/
    
    /// This is a SINGLETON pattern. There can only be one...
    public static $g_s_there_can_only_be_one = null;                              ///< This is a static variable that holds the single instance.
    
    /************************************************************************************//**
    *                           STATIC DATA MEMBERS (DEFAULTS)                              *
    *   In Version 2, these are all ignored:                                                *
    *       $default_bmlt_fullscreen                                                        *
    *       $default_support_old_browsers                                                   *
    *       $default_sb_array                                                               *
    ****************************************************************************************/

    public static $adminOptionsName = "BMLTAdminOptions";                         ///< The name, in the database, for the version 1 options for this plugin.
    public static $admin2OptionsName = "BMLT2AdminOptions";                       ///< These options are for version 2.
    
    // These are the old settings that we still care about.
    public static $default_rootserver = '';                                       ///< This is the default root BMLT server URI.
    public static $default_map_center_latitude = 29.764377375163125;              ///< This is the default basic search map center latitude
    public static $default_map_center_longitude = -95.4931640625;                 ///< This is the default basic search map center longitude
    public static $default_map_zoom = 8;                                          ///< This is the default basic search map zoom level
    public static $default_details_map_zoom = 11;                                 ///< This is the default basic search map zoom level
    public static $default_location_checked = 0;                                  ///< If nonzero, then the "This is a location" checkbox will be preselected.
    public static $default_location_services = 0;                                 ///< This tells the new default implementation whether or not location services should be available only for mobile devices.
    public static $default_gkey = '';                                             ///< This is only necessary for older versions.
    public static $default_additional_css = '';                                   ///< This is additional CSS that is inserted inline into the <head> section.
    public static $default_initial_view = '';                                     ///< The initial view for old-style BMLT. It can be 'map', 'text', 'advanced', 'advanced map', 'advanced text' or ''.
    public static $default_theme = 'default';                                     ///< This is the default for the "style theme" for the plugin. Different settings can have different themes.
    public static $default_language = 'en';                                       ///< The default language is English, but the root server can override.
    public static $default_language_string = 'English';                           ///< The default language is English, and the name is spelled out, here.
    public static $default_distance_units = 'mi';                                 ///< The default distance units are miles.
    public static $default_grace_period = 15;                                     ///< The default grace period for the mobile search (in minutes).
    public static $default_time_offset = 0;                                       ///< The default time offset from the main server (in hours).
    public static $default_military_time = false;                                 ///< If this is true, then time displays will be in military time.
    public static $default_startWeekday = 1;                                      ///< The default starting weekday (Sunday)
    public static $default_duration = '1:30';                                     ///< The default duration of meetings.
    public static $default_geo_width = '-10';                                     ///< The default geo width for searches.
    
    /************************************************************************************//**
    *                               STATIC DATA MEMBERS (MISC)                              *
    ****************************************************************************************/
    
    public static $local_options_success_time = 2000;                             ///< The number of milliseconds a success message is displayed.
    public static $local_options_failure_time = 5000;                             ///< The number of milliseconds a failure message is displayed.

    /************************************************************************************//**
    *                                  DYNAMIC DATA MEMBERS                                 *
    ****************************************************************************************/

    public $my_driver = null;              ///< This will contain an instance of the BMLT satellite driver class.
    public $my_params = null;              ///< This will contain the $this->my_http_vars and $_POST query variables.
    public $my_http_vars = null;           ///< This will hold all of the query arguments.
    public $my_table_next_id = 0;          ///< The next ID to use for the table.
    public $my_current_language;           ///< This contains whatever the current localization object is.
    
    /************************************************************************************//**
    *                                    FUNCTIONS/METHODS                                  *
    ****************************************************************************************/
        
    /************************************************************************************//**
    *   \brief Get the instance                                                             *
    *                                                                                       *
    *   \return An instance  of BMLTPlugin                                                  *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function get_plugin_object()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        return self::$g_s_there_can_only_be_one;
    }
        
    /************************************************************************************//**
    *                           ACCESSORS AND INTERNAL FUNCTIONS                            *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief Adapts all the static data members to the selected language.                 *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function adapt_to_lang( $in_lang = "en" ///< The language code. Default is English.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        global $g_my_languages;

        $this->my_current_language = $g_my_languages[$in_lang];
    }
    
    /************************************************************************************//**
    *   \brief Accessor: This gets the driver object.                                       *
    *                                                                                       *
    *   \returns a reference to the bmlt_satellite_controller driver object                 *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function &get_my_driver()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        return $this->my_driver;
    }

    /************************************************************************************//**
    *   \brief Loads the parameter list.                                                    *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function load_params()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $this->my_params = self::get_params($this->my_http_vars);
    }
    
    /************************************************************************************//**
    *   \brief Loads a parameter list.                                                      *
    *                                                                                       *
    *   \returns a string, containing the joined parameters.                                *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function get_params($in_array)
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $my_params = '';

        foreach ($in_array as $key => $value) {
            if (($key != 'lang_enum') && isset($in_array['direct_simple']) || (!isset($in_array['direct_simple']) && $key != 'switcher') && ($key != 'redirect_ajax_json')) {    // We don't propagate switcher or the language.
                if (isset($value) && is_array($value) && count($value)) {
                    foreach ($value as $val) {
                        if (isset($val) &&  is_array($val) && count($val)) {
                            // This stupid, stupid, kludgy dance, is because Drupal 7
                            // Doesn't seem to acknowledge the existence of the join() or
                            // implode() functions, and puts out a notice.
                            $val_ar = '';
                            
                            foreach ($val as $v) {
                                if (!is_array($v)) { // This makes sure that we ignore any nested arrays, which can happen for some CMS implementations.
                                    if ($val_ar) {
                                        $val_ar .= ',';
                                    }
                                
                                    $val_ar .= $v;
                                }
                            }
                                
                            $val = strval($val_ar);
                        } elseif (!isset($val)) {
                            $val = '';
                        }

                        $my_params .= '&'.urlencode($key) ."[]=". urlencode($val);
                    }
                    $key = null;
                }
                
                if ($key) {
                    $my_params .= '&'.urlencode($key);
                    
                    if ($value) {
                        $my_params .= "=". urlencode($value);
                    }
                }
            }
        }
        return $my_params;
    }
        
    /************************************************************************************//**
    *   This will strip the cruft out of CSS and JS files.                                  *
    *   \returns a string, with the stripped CSS.                                           *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function stripFile(
        $in_filename,               ///< The filename to open and optimize. If in a subdirectory (other than themes), then that should be part of the string.
        $in_theme_dirname = null    ///< The dirname of the theme to use. Default is NULL (Looks in the same directory as this file).
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $pathname = dirname(__FILE__) . ($in_theme_dirname ? '/themes/' . $in_theme_dirname : '') . '/' . $in_filename;
        if (file_exists($pathname)) {
            $opt = file_get_contents($pathname);
            $opt = preg_replace("|\/\*.*?\*\/|s", "", $opt);
            $opt = preg_replace("|[^:]\/\/.*?\n|s", "", $opt);
            if (!defined('_DEBUG_MODE_')) {
                $opt = preg_replace("|\s+|s", " ", $opt);
            }
            return $opt;
        }
            
        return "";
    }
    
    /****************************************************************************************//**
    *   \brief Checks the UA of the caller, to see if it should return XHTML Strict or WML.     *
    *                                                                                           *
    *   NOTE: This is very, very basic. It is not meant to be a studly check, like WURFL.       *
    *                                                                                           *
    *   \returns A string. The supported type ('xhtml', 'xhtml_mp' or 'wml')                    *
    ********************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function mobile_sniff_ua(   $in_http_vars   ///< The query variables.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        if (isset($in_http_vars['WML']) && (intval($in_http_vars['WML']) == 1)) {
            $language = 'wml';
        } elseif (isset($in_http_vars['WML']) && (intval($in_http_vars['WML']) == 2)) {
            $language = 'xhtml_mp';
        } else {
            if (!isset($_SERVER['HTTP_ACCEPT'])) {
                return false;
            }
        
            $http_accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        
            $accept = array();
        
            foreach ($http_accept as $type) {
                $type = strtolower(trim(preg_replace('/\;.*$/', '', preg_replace('/\s+/', '', $type))));
        
                $accept[$type] = true;
            }
        
            $language = 'xhtml';
        
            if (isset($accept['text/vnd.wap.wml'])) {
                $language = 'wml';
        
                if (isset($accept['application/xhtml+xml']) || isset($accept['application/vnd.wap.xhtml+xml'])) {
                    $language = 'xhtml_mp';
                }
            } else {
                if (preg_match('/ipod/i', $_SERVER['HTTP_USER_AGENT'])
                    ||  preg_match('/ipad/i', $_SERVER['HTTP_USER_AGENT'])
                    ||  preg_match('/iphone/i', $_SERVER['HTTP_USER_AGENT'])
                    ||  preg_match('/android/i', $_SERVER['HTTP_USER_AGENT'])
                    ||  preg_match('/blackberry/i', $_SERVER['HTTP_USER_AGENT'])
                    ||  preg_match("/opera\s+mini/i", $_SERVER['HTTP_USER_AGENT'])
                    ||  isset($in_http_vars['simulate_smartphone'])
                    ) {
                    $language = 'smartphone';
                }
            }
        }
        return $language;
    }

    /************************************************************************************//**
    *   \brief This will parse the given text, to see if it contains the submitted code.    *
    *                                                                                       *
    *   The code can be contained in EITHER an HTML comment (<!--CODE-->), OR a double-[[]] *
    *   notation.                                                                           *
    *                                                                                       *
    *   \returns Boolean true if the code is found (1 or more instances), OR an associative *
    *   array of data that is associated with the code (anything within parentheses). Null  *
    *   is returned if there is no shortcode detected.                                      *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function get_shortcode(
        $in_text_to_parse,  ///< The text to search for shortcodes
        $in_code            ///< The code that w're looking for.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = null;
        
        $code_regex_html = "\<\!\-\-\s?".preg_quote(strtolower(trim($in_code)))."\s?(\(.*?\))?\s?\-\-\>";
        $code_regex_brackets = "\[\[\s?".preg_quote(strtolower(trim($in_code)))."\s?(\(.*?\))?\s?\]\]";
        
        $matches = array();
      
        if (preg_match('|'.$code_regex_html.'|i', $in_text_to_parse, $matches) || preg_match('|'.$code_regex_brackets.'|i', $in_text_to_parse, $matches)) {
            if (!isset($matches[1]) || !($ret = trim($matches[1], '()'))) { // See if we have any parameters.
                $ret = true;
            }
        }
        
        return $ret;
    }

    /************************************************************************************//**
    *   \brief This will parse the given text, to see if it contains the submitted code.    *
    *                                                                                       *
    *   The code can be contained in EITHER an HTML comment (<!--CODE-->), OR a double-[[]] *
    *   notation.                                                                           *
    *                                                                                       *
    *   \returns A string, consisting of the new text.                                      *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function replace_shortcode(
        $in_text_to_parse,      ///< The text to search for shortcodes
        $in_code,               ///< The code that w're looking for.
        $in_replacement_text    ///< The text we'll be replacing the shortcode with.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $code_regex_html = "#(\<p[^\>]*?\>)?\<\!\-\-\s?".preg_quote(strtolower(trim($in_code)))."\s?(\(.*?\))?\s?\-\-\>(\<\/p>)?#i";
        $code_regex_brackets = "#(\<p[^\>]*?\>)?\[\[\s?".preg_quote(strtolower(trim($in_code)))."\s?(\(.*?\))?\s?\]\](\<\/p>)?#i";

        $ret = preg_replace($code_regex_html, $in_replacement_text, $in_text_to_parse, 1);
        $ret = preg_replace($code_regex_brackets, $in_replacement_text, $ret, 1);
        
        return $ret;
    }
    
    /************************************************************************************//**
    *                               OPTIONS MANAGEMENT                                      *
    *****************************************************************************************
    *   This takes some 'splainin'.                                                         *
    *                                                                                       *
    *   The admin2 options track how many servers we're tracking, and allow the admin to    *
    *   increment by 1. The first options don't have a number. "Numbered" options begin at  *
    *   2. You are allowed to save new options at 1 past the current number of options. You *
    *   delete options by decrementing the number in the admin2 options (the index). If you *
    *   re-increment the options, you will see the old values. It is possible to reset to   *
    *   default, and you do that by specifying an option number less than 0 (-1).           *
    *                                                                                       *
    *   The reason for this funky, complex game, is so we can have multiple options, and we *
    *   don't ignore old options from previous versions.                                    *
    *                                                                                       *
    *   I considered setting up an abstracted, object-based system for managing these, but  *
    *   it's complex enough without the added overhead, and, besides, that would give a lot *
    *   more room for bugs. It's kinda hairy already, and the complexity is not great       *
    *   enough to justify designing a whole object subsystem for it.                        *
    ****************************************************************************************/
        
    /************************************************************************************//**
    *   \brief This gets the default admin options from the object (not the DB).            *
    *                                                                                       *
    *   \returns an associative array, with the default option settings.                    *
    ****************************************************************************************/
    protected function geDefaultBMLTOptions()
    {
        global $bmlt_localization;
        // These are the defaults. If the saved option has a different value, it replaces the ones in here.
        $ret = array (  'root_server' => self::$default_rootserver,
                        'map_center_latitude' => self::$default_map_center_latitude,
                        'map_center_longitude' => self::$default_map_center_longitude,
                        'map_zoom' => self::$default_map_zoom,
                        'bmlt_initial_view' => self::$default_initial_view,
                        'additional_css' => self::$default_additional_css,
                        'id' => strval(time() + intval(rand(0, 999))),   // This gives the option a unique slug
                        'setting_name' => '',
                        'bmlt_location_checked'=> self::$default_location_checked,
                        'bmlt_location_services' => self::$default_location_services,
                        'theme' => self::$default_theme,
                        'distance_units' => self::$default_distance_units,
                        'grace_period' => self::$default_grace_period,
                        'time_offset' => self::$default_time_offset,
                        'military_time' => self::$default_military_time,
                        'startWeekday' => self::$default_startWeekday,
                        'google_api_key' => 'INVALID',
                        'region_bias' => 'us',
                        'lang' => $bmlt_localization,
                        'default_geo_width' => self::$default_geo_width
                        );
            
            return $ret;
    }
    
    /************************************************************************************//**
    *   \brief This gets the admin options from the database.                               *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    public function getBMLTOptions( $in_option_number = null  /**<    It is possible to store multiple options.
                                                                If there is a number here (>=1), that will be used.
                                                                If <0, a new option will be returned (not saved).
                                                        */
                            )
    {
        $BMLTOptions = $this->geDefaultBMLTOptions(); // Start off with the defaults.
        
        // Make sure we aren't resetting to default.
        if (($in_option_number == null) || (intval($in_option_number) > 0)) {
            $option_number = null;
            // If they want a certain option number, then it needs to be greater than 1, and within the number we have assigned.
            if ((intval($in_option_number) > 1) && (intval($in_option_number) <= $this->get_num_options() )) {
                $option_number = '_'.intval($in_option_number);
            }
        
            // These are the standard options.
            $old_BMLTOptions = $this->cms_get_option(self::$adminOptionsName.$option_number);
            
            if (is_array($old_BMLTOptions) && count($old_BMLTOptions)) {
                foreach ($old_BMLTOptions as $key => $value) {
                    if (isset($BMLTOptions[$key])) { // We deliberately ignore old settings that no longer apply.
                        $BMLTOptions[$key] = $value;
                    }
                }
            }
        
            // Strip off the trailing slash.
            $BMLTOptions['root_server'] = preg_replace("#\/$#", "", trim($BMLTOptions['root_server']), 1);
        }
            
        if (isset($BMLTOptions['lang']) || !$BMLTOptions['lang']) {
            global $bmlt_localization;
        
            $BMLTOptions['lang'] = $bmlt_localization;
        }
        
        if (!$BMLTOptions['lang']) {
            $BMLTOptions['lang'] = self::$default_language;
        }
            
        return $BMLTOptions;
    }
    
    /************************************************************************************//**
    *   \brief This gets the admin options from the database, but by using the option id.   *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getBMLTOptions_by_id(
        $in_option_id,              ///< The option ID. It cannot be optional.
        &$out_option_number = null  ///< This can be optional. A reference to an integer that will be given the option number.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $BMLTOptions = null;
        
        if (isset($out_option_number)) {
            $out_option_number = 0;
        }
        
        if (!$in_option_id) {
            $BMLTOptions = $this->getBMLTOptions(1);
            
            if (isset($out_option_number)) {
                $out_option_number = 1;
            }
        } else {
            $count = $this->get_num_options();
            
            // We sort through the available options, looking for the ID.
            for ($i = 1; $i <= $count; $i++) {
                $option_number = '';
                
                if ($i > 1) {   // We do this, for compatibility with older options.
                    $option_number = "_$i";
                }
                
                $name = self::$adminOptionsName.$option_number;
                $temp_BMLTOptions = $this->cms_get_option($name);
                
                if (is_array($temp_BMLTOptions) && count($temp_BMLTOptions)) {
                    if ($temp_BMLTOptions['id'] == $in_option_id) {
                        $BMLTOptions = $temp_BMLTOptions;
                        // If they want to know the ID, we supply it here.
                        if (isset($out_option_number)) {
                            $out_option_number = $i;
                        }
                        break;
                    }
                } else {
                    echo "<!-- BMLTPlugin ERROR (getBMLTOptions_by_id)! No options found for $name! -->";
                }
            }
        }
            
        if (!isset($BMLTOptions['lang']) || !$BMLTOptions['lang']) {
            global $bmlt_localization;
        
            $BMLTOptions['lang'] = $bmlt_localization;
        }
        
        if (!isset($BMLTOptions['lang']) || !$BMLTOptions['lang']) {
            $BMLTOptions['lang'] = self::$default_language;
        }
        
        if (!isset($BMLTOptions['default_geo_width']) || !$BMLTOptions['default_geo_width']) {
            $BMLTOptions['default_geo_width'] = self::$default_geo_width;
        }
        
        return $BMLTOptions;
    }
    
    /************************************************************************************//**
    *   \brief This updates the database with the given options.                            *
    *                                                                                       *
    *   \returns a boolean. true if success.                                                *
    ****************************************************************************************/
    public function setBMLTOptions(
        $in_options,            ///< An array. The options to be stored. If no number is supplied in the next parameter, the ID is used.
        $in_option_number = 1   ///< It is possible to store multiple options. If there is a number here, that will be used.
    ) {
        $ret = false;
        
        if (($in_option_number == null) || (intval($in_option_number) < 1) || (intval($in_option_number) > ($this->get_num_options() + 1))) {
            $in_option_number = 0;
            $this->getBMLTOptions_by_id($in_options['id'], $in_option_number);
        }
        
        if (intval($in_option_number) > 0) {
            $option_number = null;
            // If they want a certain option number, then it needs to be greater than 1, and within the number we have assigned (We can also increase by 1).
            if ((intval($in_option_number) > 1) && (intval($in_option_number) <= ($this->get_num_options() + 1))) {
                $option_number = '_'.intval($in_option_number);
            }
            $in_option_number = (intval($in_option_number) > 1) ? intval($in_option_number) : 1;

            $name = self::$adminOptionsName.$option_number;
            
            // If this is a new option, then we also update the admin 2 options, incrementing the number of servers.
            
            if (intval($in_option_number) == ($this->get_num_options() + 1)) {
                $in_options['id'] = strval(time() + intval(rand(0, 999)));   // This gives the option a unique slug
                $admin2Options = array ('num_servers' => intval($in_option_number));
                $gKey = '';

                if (isset($in_options['google_api_key']) && ('' != $in_options['google_api_key']) && ('INVALID' != $in_options['google_api_key'])) {
                    $gKey = $in_options['google_api_key'];
                }

                for ($c = 0; $c < count($in_options); $c++) {
                    if ($num != $in_option_number) {
                        $option = $this->getBMLTOptions($c);
                        $option['google_api_key'] = $gKey;
                    }
                }

                $admin2Options = array ('num_servers' => intval($in_option_number), 'google_api_key' => $gKey );
                $this->setAdmin2Options($admin2Options);
            }
            
            $this->cms_set_option($name, $in_options);
            
            $ret = true;
        } else {
            echo "<!-- BMLTPlugin ERROR (setBMLTOptions)! The option number ($in_option_number) is out of range! -->";
        }
            
        return $ret;
    }
        
    /************************************************************************************//**

    *   \brief This gets the admin 2 options from the database.                             *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    public function getAdmin2Options()
    {
        $bmlt2_BMLTOptions = null;
        
        // We have a special set of options for version 2.
        $old_BMLTOptions = $this->cms_get_option(self::$admin2OptionsName);
        
        if (is_array($old_BMLTOptions) && count($old_BMLTOptions)) {
            foreach ($old_BMLTOptions as $key => $value) {
                $bmlt2_BMLTOptions[$key] = $value;
            }
        } else {
            $bmlt2_BMLTOptions = array ('num_servers' => 1, 'google_api_key' => '' );
            $this->setAdmin2Options($old_BMLTOptions);
        }
        
        return $bmlt2_BMLTOptions;
    }
    
    /************************************************************************************//**
    *   \brief This updates the database with the given options (Admin2 options).           *
    *                                                                                       *
    *   \returns a boolean. true if success.                                                *
    ****************************************************************************************/
    public function setAdmin2Options( $in_options ///< An array. The options to be stored.
                                )
    {
        $ret = false;
        
        if ($this->cms_set_option(self::$admin2OptionsName, $in_options)) {
            $ret = true;
        }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Gets the number of active options.                                           *
    *                                                                                       *
    *   \returns an integer. The number of options.                                         *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function get_num_options()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = 1;
        $opts = $this->getAdmin2Options();
        if (isset($opts['num_servers'])) {
            $ret = intval($opts['num_servers']);
        } else // If the options weren't already set, we create them now.
            {
            $opts = array ( 'num_servers' => 1, 'google_api_key' => '' );
            $this->setAdmin2Options($opts);
        }
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Makes a new set of options, set as default.                                  *
    *                                                                                       *
    *   \returns An integer. The index of the options (It will always be the number of      *
    *   initial options, plus 1. Null if failed.                                            *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function make_new_options()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $opt = $this->getBMLTOptions(-1);
        $ret = null;
        
        // If we successfully get the options, we save them, in order to put them in place
        if (is_array($opt) && count($opt)) {
            $this->setBMLTOptions($opt, $this->get_num_options() + 1);
            $ret = $this->get_num_options();
        } else {
            echo "<!-- BMLTPlugin ERROR (make_new_options)! Failed to create new options! -->";
        }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Deletes the options by ID.                                                   *
    *                                                                                       *
    *   \returns a boolean. true if success.                                                *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function delete_options_by_id( $in_option_id   ///< The ID of the option to delete.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = false;
        
        $option_num = 0;
        $this->getBMLTOptions_by_id($in_option_id, $option_num); // We just want the option number.
        
        if ($option_num > 0) {  // If it's 1, we'll let the next function register the error.
            $ret = $this->delete_options($option_num);
        }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Deletes the indexed options.                                                 *
    *                                                                                       *
    *   This is a bit of a delicate operation, because we need to re-index all of the other *
    *   options, beyond the one being deleted.                                              *
    *                                                                                       *
    *   You cannot delete the first options (1), if they are the only ones.                 *
    *                                                                                       *
    *   \returns a boolean. true if success.                                                *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function delete_options( $in_option_number /**<    The index of the option to delete.
                                                        It can be 1 -> the number of available options.
                                                        For safety's sake, this cannot be optional.
                                                        We cannot delete the first (primary) option if there are no others.
                                                */
                            )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $first_num = intval($in_option_number);

        $ret = false;
        
        if ($first_num) {
            $last_num = $this->get_num_options();
            
            if ((($first_num > 1) && ($first_num <= $last_num )) || (($first_num == 1) && ($last_num > 1))) {
                /*
                    OK. At this point, we know which option we'll be deleting. The way we "delete"
                    the option is to cascade all the ones after it down, and then we delete the last one.
                    If this is the last one, then there's no need for a cascade, and we simply delete it.
                */
                
                for ($i = $first_num; $i < $last_num; $i++) {
                    $opt = $this->getBMLTOptions($i + 1);
                    $this->setBMLTOptions($opt, $i);
                }
                
                $option_number = "_$last_num";
                
                // Delete the selected option
                $option_name = self::$adminOptionsName.$option_number;
                
                $this->cms_delete_option($option_name);
                
                $admin2Options = $this->getAdmin2Options();
                
                // This actually decrements the number of available options.
                $admin2Options['num_servers'] = $last_num - 1;

                $this->setAdmin2Options($admin2Options);
                $ret = true;
            } else {
                if ($first_num > 1) {
                    echo "<!-- BMLTPlugin ERROR (delete_options)! Option request number out of range! It must be between 1 and $last_num -->";
                } elseif ($first_num == 1) {
                    echo "<!-- BMLTPlugin ERROR (delete_options)! You can't delete the last option! -->";
                } else {
                    echo "<!-- BMLTPlugin ERROR (delete_options)! -->";
                }
            }
        } else {
            echo "<!-- BMLTPlugin ERROR (delete_options)! Option request number ($first_num) out of range! -->";
        }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *                      ADMIN PAGE DISPLAY AND PROCESSING FUNCTIONS                      *
    ****************************************************************************************/

    /************************************************************************************//**
    *   \brief This does any admin actions necessary.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function process_admin_page( &$out_option_number   ///< If an option number needs to be selected, it is set here.
                                )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $out_option_number = 1;
        global $bmlt_localization;
        $this->adapt_to_lang($bmlt_localization);
        $timing = self::$local_options_success_time;    // Success is a shorter fade, but failure is longer.
        $ret = '<div id="BMLTPlugin_Message_bar_div" class="BMLTPlugin_Message_bar_div">';
        if (isset($this->my_http_vars['BMLTPlugin_create_option'])) {
            $out_option_number = $this->make_new_options();
            if ($out_option_number) {
                $new_options = $this->getBMLTOptions($out_option_number);
                $def_options = $this->getBMLTOptions(1);
                    
                $new_options = $def_options;
                unset($new_options['setting_name']);
                unset($new_options['id']);
                unset($new_options['theme']);
                $this->setBMLTOptions($new_options, $out_option_number);
                    
                $ret .= '<h2 id="BMLTPlugin_Fader" class="BMLTPlugin_Message_bar_success">';
                    $ret .= $this->process_text($this->my_current_language->local_options_create_success);
                $ret .= '</h2>';
            } else {
                $timing = self::$local_options_failure_time;
                $ret .= '<h2 id="BMLTPlugin_Fader" class="BMLTPlugin_Message_bar_fail">';
                $ret .= $this->process_text($this->my_current_language->local_options_create_failure);
                $ret .= '</h2>';
            }
        } elseif (isset($this->my_http_vars['BMLTPlugin_delete_option'])) {
            $option_index = intval($this->my_http_vars['BMLTPlugin_delete_option']);
        
            if ($this->delete_options($option_index)) {
                $ret .= '<h2 id="BMLTPlugin_Fader" class="BMLTPlugin_Message_bar_success">';
                    $ret .= $this->process_text($this->my_current_language->local_options_delete_success);
                $ret .= '</h2>';
            } else {
                $timing = self::$local_options_failure_time;
                $ret .= '<h2 id="BMLTPlugin_Fader" class="BMLTPlugin_Message_bar_fail">';
                $ret .= $this->process_text($this->my_current_language->local_options_delete_failure);
                $ret .= '</h2>';
            }
        } else {
            $ret .= '<h2 id="BMLTPlugin_Fader" class="BMLTPlugin_Message_bar_fail">&nbsp;</h2>';
        }
            $ret .= '<script type="text/javascript">g_BMLTPlugin_TimeToFade = '.$timing.';BMLTPlugin_StartFader()</script>';
        $ret .= '</div>';
        return $ret;
    }
        
    /************************************************************************************//**
    *   \brief Returns the HTML for the admin page.                                         *
    *                                                                                       *
    *   \returns a string. The XHTML for the page.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function return_admin_page()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $selected_option = 1;
        $process_html = $this->process_admin_page($selected_option);
        $options_coords = array();

        $html = '<div class="BMLTPlugin_option_page" id="BMLTPlugin_option_page_div">';
            $html .= '<noscript class="no_js">'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';
            $html .= '<div id="BMLTPlugin_options_container" style="display:none">';    // This is displayed using JavaScript.
                $html .= '<h1 class="BMLTPlugin_Admin_h1">'.$this->process_text($this->my_current_language->local_options_title).'</h1>';
                $html .= $process_html;
                $html .= '<form class="BMLTPlugin_sheet_form" id="BMLTPlugin_sheet_form" action ="'.$this->get_admin_form_uri().'" method="get" onsubmit="function(){return false}">';
                    $html .= '<fieldset class="BMLTPlugin_option_fieldset" id="BMLTPlugin_option_fieldset">';
                        $html .= '<legend id="BMLTPlugin_legend" class="BMLTPlugin_legend">';
                            $count = $this->get_num_options();
                                
        if ($count > 1) {
            $html .= '<select id="BMLTPlugin_legend_select" onchange="BMLTPlugin_SelectOptionSheet(this.value,'.$count.')">';
            for ($i = 1; $i <= $count; $i++) {
                                $options = $this->getBMLTOptions($i);
                                        
                if (is_array($options) && count($options) && isset($options['id'])) {
                    $options_coords[$i] = array ( 'lat' => $options['map_center_latitude'], 'lng' => $options['map_center_longitude'], 'zoom' => $options['map_zoom'] );
                                            
                    $html .= '<option id="BMLTPlugin_option_sel_'.$i.'" value="'.$i.'"';
                                            
                    if ($i == $selected_option) {
                                            $html .= ' selected="selected"';
                    }
                                            
                    $html .= '>';
                    if (isset($options['setting_name']) && $options['setting_name']) {
                                    $html .= htmlspecialchars($options['setting_name']);
                    } else {
                                                $html .= $this->process_text($this->my_current_language->local_options_prefix).$i;
                    }
                            $html .= '</option>';
                } else {
                    echo "<!-- BMLTPlugin ERROR (admin_page)! Options not found for $i! -->";
                }
            }
                                $html .= '</select>';
        } elseif ($count == 1) {
            $options = $this->getBMLTOptions(1);
            $options_coords[1] = array ( 'lat' => $options['map_center_latitude'], 'lng' => $options['map_center_longitude'], 'zoom' => $options['map_zoom'] );
            if (isset($options['setting_name']) && $options['setting_name']) {
                $html .= htmlspecialchars($options['setting_name']);
            } else {
                $html .= $this->process_text($this->my_current_language->local_options_prefix).'1';
            }
        } else {
            echo "<!-- BMLTPlugin ERROR (admin_page)! No options! -->";
        }
                        $html .= '</legend>';
        for ($i = 1; $i <= $count; $i++) {
            $html .= $this->display_options_sheet($i, (($i == $selected_option) ? 'block' : 'none'));
        }
                    $html .= '</fieldset>';
                $html .= '</form>';
                $html .= '<div class="BMLTPlugin_toolbar_line_bottom">';
                    $html .= '<form action ="'.$this->get_admin_form_uri().'" method="post">';
                        $html .= '<div id="BMLTPlugin_bottom_button_div" class="BMLTPlugin_bottom_button_div">';
        if ($count > 1) {
            $html .= '<div class="BMLTPlugin_toolbar_button_line_left">';
                $html .= '<script type="text/javascript">';
                    $html .= "var c_g_delete_confirm_message='".$this->process_text($this->my_current_language->local_options_delete_option_confirm).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $html .= '</script>';
                $html .= '<input type="button" id="BMLTPlugin_toolbar_button_del" class="BMLTPlugin_delete_button" value="'.$this->process_text($this->my_current_language->local_options_delete_option).'" onclick="BMLTPlugin_DeleteOptionSheet()" />';
            $html .= '</div>';
        }
                            
                            $html .= '<input type="submit" id="BMLTPlugin_toolbar_button_new" class="BMLTPlugin_create_button" name="BMLTPlugin_create_option" value="'.$this->process_text($this->my_current_language->local_options_add_new).'" />';
                            
                            $html .= '<div class="BMLTPlugin_toolbar_button_line_right">';
                                $html .= '<input id="BMLTPlugin_toolbar_button_save" type="button" value="'.$this->process_text($this->my_current_language->local_options_save).'" onclick="BMLTPlugin_SaveOptions()" />';
                            $html .= '</div>';
                        $html .= '</div>';
                    $html .= '</form>';
                $html .= '</div>';
                $html .= '<div class="BMLTPlugin_toolbar_line_map">';
                    $html .= '<h2 class="BMLTPlugin_map_label_h2">'.$this->process_text($this->my_current_language->local_options_map_label).'</h2>';
                    $html .= '<div class="BMLTPlugin_Map_Div" id="BMLTPlugin_Map_Div"></div>';
                    $html .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "BMLTPlugin_DirtifyOptionSheet(true);" . (defined('_DEBUG_MODE_') ? "\n" : '');    // This sets up the "Save Changes" button as disabled.
                        // This is a trick I use to hide irrelevant content from non-JS browsers. The element is drawn, hidden, then uses JS to show. No JS, no element.
                        $html .= "document.getElementById('BMLTPlugin_options_container').style.display='block';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_no_name = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_no_name_string))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_no_root = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_no_root_server_string))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_root_canal = '".$this->my_current_language->local_options_url_bad.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                        $html .= "var c_g_BMLTPlugin_success_message = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_save_success))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_failure_message = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_save_failure))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_success_time = ".intval(self::$local_options_success_time).";" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_failure_time = ".intval(self::$local_options_failure_time).";" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_unsaved_prompt = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_unsaved_message))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_test_server_success = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_test_server_success))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_test_server_failure = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_test_server_failure))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var c_g_BMLTPlugin_coords = new Array();" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var g_BMLTPlugin_TimeToFade = ".intval(self::$local_options_success_time).";" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= "var g_BMLTPlugin_no_gkey_string = '".$this->process_text(str_replace("'", "\\'", $this->my_current_language->local_options_no_gkey_string))."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
        if (is_array($options_coords) && count($options_coords)) {
            foreach ($options_coords as $value) {
                $html .= 'c_g_BMLTPlugin_coords[c_g_BMLTPlugin_coords.length] = {';
                $f = true;
                foreach ($value as $key2 => $value2) {
                    if ($f) {
                        $f = false;
                    } else {
                        $html .= ',';
                    }
                    $html .= "'".htmlspecialchars($key2)."':";
                    $html .= "'".htmlspecialchars($value2)."'";
                }
                $html .= '};';
            }
        }
                        $url = $this->get_plugin_path();
                        $url = htmlspecialchars($url.'google_map_images');
                        $html .= "var c_g_BMLTPlugin_admin_google_map_images = '$url';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                        $html .= 'BMLTPlugin_admin_load_map();' . (defined('_DEBUG_MODE_') ? "\n" : '');
                    $html .= '</script>';
                $html .= '</div>';
            $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
            
    /************************************************************************************//**
    *   \brief This will return the HTML for one sheet of options in the admin page.        *
    *                                                                                       *
    *   \returns The XHTML to be displayed.                                                 *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_options_sheet(
        $in_options_index = 1,  ///< The options index. If not given, the first (main) ones are used.
        $display_mode = 'none'  ///< If this page is to be displayed, make it 'block'.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '';

        $in_options_index = intval($in_options_index);
        
        if (($in_options_index < 1) || ($in_options_index > $this->get_num_options())) {
            echo "<!-- BMLTPlugin Warning (display_options_sheet)! $in_options_index is out of range! Using the first options. -->";
            $in_options_index = 1;
        }
        
        $options = $this->getBMLTOptions($in_options_index);
        
        if (is_array($options) && count($options) && isset($options['id'])) {
            $ret .= '<div class="BMLTPlugin_option_sheet" id="BMLTPlugin_option_sheet_'.$in_options_index.'_div" style="display:'.htmlspecialchars($display_mode).'">';
                $ret .= '<h2 class="BMLTPlugin_option_id_h2">'.$this->process_text($this->my_current_language->local_options_settings_id_prompt).htmlspecialchars($options['id']).'</h2>';
                $ret .= '<input type="hidden" name="actual_options_id" id="BMLTPlugin_option_sheet_'.$in_options_index.'_actual_options_id" value="'.htmlspecialchars($options['id']).'" />';
                $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                    $id = 'BMLTPlugin_option_sheet_name_'.$in_options_index;
                    $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_name_label).'</label>';
                        $string = (isset($options['setting_name']) && $options['setting_name'] ? $options['setting_name'] : $this->process_text($this->my_current_language->local_options_no_name_string) );
                    $ret .= '<input class="BMLTPlugin_option_sheet_line_name_text" id="'.htmlspecialchars($id).'" type="text" value="'.htmlspecialchars($string).'"';
                    $ret .= ' onfocus="BMLTPlugin_ClickInText(this.id,\''.$this->process_text($this->my_current_language->local_options_no_name_string).'\',false)"';
                    $ret .= ' onblur="BMLTPlugin_ClickInText(this.id,\''.$this->process_text($this->my_current_language->local_options_no_name_string).'\',true)"';
                    $ret .= ' onchange="BMLTPlugin_DirtifyOptionSheet()" onkeyup="BMLTPlugin_DirtifyOptionSheet()" />';
                $ret .= '</div>';
                $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                    $id = 'BMLTPlugin_option_sheet_root_server_'.$in_options_index;
                    $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_rootserver_label).'</label>';
                        $string = (isset($options['root_server']) && $options['root_server'] ? $options['root_server'] : $this->process_text($this->my_current_language->local_options_no_root_server_string) );
                    $ret .= '<input class="BMLTPlugin_option_sheet_line_root_server_text" id="'.htmlspecialchars($id).'" type="text" value="'.htmlspecialchars($string).'"';
                    $ret .= ' onfocus="BMLTPlugin_ClickInText(this.id,\''.$this->process_text($this->my_current_language->local_options_no_root_server_string).'\',false)"';
                    $ret .= ' onblur="BMLTPlugin_ClickInText(this.id,\''.$this->process_text($this->my_current_language->local_options_no_root_server_string).'\',true)"';
                    $ret .= ' onchange="BMLTPlugin_DirtifyOptionSheet()" onkeyup="BMLTPlugin_DirtifyOptionSheet()" />';
                    $ret .= '<div class="BMLTPlugin_option_sheet_Test_Button_div">';
                        $ret .= '<input type="button" value="'.$this->process_text($this->my_current_language->local_options_test_server).'" onclick="BMLTPlugin_TestRootUri_call()" title="'.$this->process_text($this->my_current_language->local_options_test_server_tooltip).'" />';
                        $ret .= '<div class="BMLTPlugin_option_sheet_NEUT" id="BMLTPlugin_option_sheet_indicator_'.$in_options_index.'"></div>';
                        $ret .= '<div class="BMLTPlugin_option_sheet_Version" id="BMLTPlugin_option_sheet_version_indicator_'.$in_options_index.'"></div>';
                    $ret .= '</div>';
                $ret .= '</div>';
                $dir_res = opendir(dirname(__FILE__).'/themes');
            if ($dir_res) {
                $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                    $id = 'BMLTPlugin_option_sheet_theme_'.$in_options_index;
                    $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_theme_prompt).'</label>';
                    $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
                while (false !== ( $file_name = readdir($dir_res) )) {
                    if (!preg_match('/^\./', $file_name) && is_dir(dirname(__FILE__).'/themes/'.$file_name)) {
                                $ret .= '<option value="'.htmlspecialchars($file_name).'"';
                        if ($file_name == $options['theme']) {
                            $ret .= ' selected="selected"';
                        }
                                $ret .= '>'.htmlspecialchars($file_name).'</option>';
                    }
                }
                    $ret .= '</select>';
                    $ret .= '</div>';
            }
                $ret .= '<div class="BMLTPlugin_option_sheet_line_div BMLTPlugin_additional_css_line">';
                    $id = 'BMLTPlugin_option_sheet_additional_css_'.$in_options_index;
                    $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_more_styles_label).'</label>';
                    $ret .= '<textarea class="BMLTPlugin_option_sheet_additional_css_textarea" id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()" onkeyup="BMLTPlugin_DirtifyOptionSheet()">';
                    $ret .= htmlspecialchars($options['additional_css']);
                    $ret .= '</textarea>';
                $ret .= '</div>';
                $ret .= '<fieldset class="BMLTPlugin_option_sheet_mobile_settings_fieldset">';
                    $ret .= '<legend class="BMLTPlugin_gmap_caveat_legend">'.$this->process_text($this->my_current_language->local_options_mobile_legend).'</legend>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div BMLTPlugin_google_api_line">';
                        $id = 'BMLTPlugin_google_api_label_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_google_api_label).'</label>';
                        $id = 'BMLTPlugin_google_api_text_'.$in_options_index;
                        $gKey = '';

            if (isset($options['google_api_key']) && ('' != $options['google_api_key']) && ('INVALID' != $options['google_api_key'])) {
                $gKey = $options['google_api_key'];
            }

                        $ret .= '<input class="BMLTPlugin_google_api_text" id="'.htmlspecialchars($id).'" type="text" value="'.htmlspecialchars($gKey).'" onchange="BMLTPlugin_DirtifyOptionSheet(); BMLTPlugin_PropagateAPIKey( this.value )" onkeyup="BMLTPlugin_DirtifyOptionSheet(); BMLTPlugin_PropagateAPIKey( this.value )">';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_distance_units_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_distance_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
                            $ret .= '<option value="mi"';
            if ('mi' == $options['distance_units']) {
                $ret .= ' selected="selected"';
            }
                            $ret .= '>'.$this->process_text($this->my_current_language->local_options_miles).'</option>';
                            $ret .= '<option value="km"';
            if ('km' == $options['distance_units']) {
                $ret .= ' selected="selected"';
            }
                            $ret .= '>'.$this->process_text($this->my_current_language->local_options_kilometers).'</option>';
                        $ret .= '</select>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_lang_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_lang_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
                            $dirname = dirname(__FILE__) . "/lang";
                            $dir = new DirectoryIterator($dirname);
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    $fName = $fileinfo->getFilename();
                    if ($fName != "index.php") {
                        $fPath = $dirname . "/" . $fName;
                        if ($file = fopen($fPath, "r")) {
                            $line0 = fgets($file);
                            $line1 = fgets($file);
                            $lang_name = trim(substr($line1, 3));
                            $lang_key = trim(substr($fName, 5, -4));
                            if ($lang_name && $lang_key) {
                                $ret .= '<option value="' . $lang_key .'"';
                                if ($options['lang'] == $lang_key) {
                                    $ret .= ' selected="selected"';
                                }
                                $ret .= '>' . $this->process_text($lang_name) .'</option>';
                            }
                        }
                    }
                }
            }
                        $ret .= '</select>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_region_bias_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_mobile_region_bias_label).'</label>';
                        $ret .= $this->bmlt_create_region_bias_select($id, $options);
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_week_begins_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_week_begins_on_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
                            $sel_weekday = intval((isset($options['startWeekday']) && $options['startWeekday']) ? $options['startWeekday'] : self::$default_startWeekday);
                            
                            $counter = 1;
                            $weekdays = $this->my_current_language->local_nouveau_weekday_long_array;
                            
            foreach ($weekdays as $weekday_text) {
                $ret .= '<option';
                    $ret .= ' value="'.$counter.'"';
                if ($options['startWeekday'] == $counter) {
                                $ret .= ' selected="selected"';
                }
                    $counter++;
                                $ret .= '>'.$this->process_text($weekday_text).'</option>';
            }
                        $ret .= '</select>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_time_format_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_time_format_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
                            $ret .= '<option';
            if (!$options['military_time']) {
                $ret .= ' selected="selected"';
            }
                            $ret .= '>'.$this->process_text($this->my_current_language->local_options_time_format_ampm).'</option>';
                            $ret .= '<option';
            if ($options['military_time']) {
                $ret .= ' selected="selected"';
            }
                            $ret .= '>'.$this->process_text($this->my_current_language->local_options_time_format_military).'</option>';
                        $ret .= '</select>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_initial_view_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_initial_view_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
            foreach ($this->my_current_language->local_options_initial_view as $value => $prompt) {
                $ret .= '<option value="'.htmlspecialchars($value).'"';
                if ($value == $options['bmlt_initial_view']) {
                    $ret .= ' selected="selected"';
                }
                $ret .= '>'.$this->process_text($prompt).'</option>';
            }
                        $ret .= '</select>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_auto_search_radius_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_auto_search_radius_prompt).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
            foreach ($this->my_current_language->local_options_auto_search_radius_display_names as $prompt => $value) {
                $ret .= '<option value="'.htmlspecialchars($value).'"';
                if ($value == $options['default_geo_width']) {
                    $ret .= ' selected="selected"';
                }
                $ret .= '>'.$this->process_text($prompt).'</option>';
            }
                        $ret .= '</select>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div BMLTPlugin_location_checkbox_line">';
                        $id = 'BMLTPlugin_location_selected_checkbox_'.$in_options_index;
                        $ret .= '<div class="BMLTPlugin_option_sheet_checkbox_div"><input class="BMLTPlugin_option_sheet_line_location_checkbox" onchange="BMLTPlugin_DirtifyOptionSheet()" id="'.htmlspecialchars($id).'" type="checkbox"'.($options['bmlt_location_checked'] == 1 ? ' checked="checked"' : '' ).'"></div>';
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_settings_location_checkbox_label).'</label>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div BMLTPlugin_location_checkbox_line">';
                        $id = 'BMLTPlugin_location_services_checkbox_'.$in_options_index;
                        $ret .= '<div class="BMLTPlugin_option_sheet_checkbox_div"><input class="BMLTPlugin_option_sheet_line_location_services_checkbox" onchange="BMLTPlugin_DirtifyOptionSheet()" id="'.htmlspecialchars($id).'" type="checkbox"'.($options['bmlt_location_services'] == 1 ? ' checked="checked"' : '' ).'"></div>';
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_selectLocation_checkbox_text).'</label>';
                    $ret .= '</div>';
                    $ret .= '</div>';
                    $ret .= '<div class="BMLTPlugin_option_sheet_line_div">';
                        $id = 'BMLTPlugin_option_sheet_grace_period_'.$in_options_index;
                        $ret .= '<label for="'.htmlspecialchars($id).'">'.$this->process_text($this->my_current_language->local_options_mobile_grace_period_label).'</label>';
                        $ret .= '<select id="'.htmlspecialchars($id).'" onchange="BMLTPlugin_DirtifyOptionSheet()">';
            for ($minute = 0; $minute < 60; $minute += 5) {
                $ret .= '<option value="'.$minute.'"';
                if ($minute == $options['grace_period']) {
                    $ret .= ' selected="selected"';
                }
                $ret .= '>'.$minute.'</option>';
            }
                        $ret .= '</select>';
                        $ret .= '<div class="BMLTPlugin_option_sheet_text_div">'.$this->process_text($this->my_current_language->local_options_grace_period_disclaimer).'</div>';
                    $ret .= '</div>';
                $ret .= '</fieldset>';
            $ret .= '</div>';
        } else {
            echo "<!-- BMLTPlugin ERROR (display_options_sheet)! Options not found for $in_options_index! -->";
        }
        
        return $ret;
    }

    /*******************************************************************/
    /** \brief Creates the select element for the Region bias.
    *   \returns a string, containing the select element HTML.
    */
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function bmlt_create_region_bias_select($in_id, $in_options)
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '';
    
        $file_path = dirname(__FILE__).'/country_names_and_code_elements.txt';
        $cc_array = explode("\n", file_get_contents($file_path));
            
        $ret .= '<select onchange="BMLTPlugin_DirtifyOptionSheet()" id="'.$in_id.'">';
        foreach ($cc_array as $cc) {
            $cc_elem = explode("\t", trim($cc));
            
            if (isset($cc_elem) && is_array($cc_elem) && (count($cc_elem) == 2)) {
                $name = ucwords(strtolower(trim($cc_elem[0])));
                $code = strtolower(trim($cc_elem[1]));
                $ret .= '<option value="'.htmlspecialchars($code).'"';
                if (strtolower($in_options['region_bias']) == $code) {
                    $ret .= ' selected="selected"';
                }
                    $ret .= '>'.htmlspecialchars($name).'</option>';
            }
        }
            $ret .= '</select>';
        
            return $ret;
    }
        
    /************************************************************************************//**
    *                                   GENERIC HANDLERS                                    *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief This does any admin actions necessary.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function admin_ajax_handler()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        // We only go here if we are in an AJAX call (This function dies out the session).
        if (isset($this->my_http_vars['BMLTPlugin_Save_Settings_AJAX_Call'])) {
            $ret = 0;
            
            if (isset($this->my_http_vars['BMLTPlugin_set_options'])) {
                $ret = 1;
                
                $num_options = $this->get_num_options();
                
                for ($i = 1; $i <= $num_options; $i++) {
                    $options = $this->getBMLTOptions($i);
                    
                    if (is_array($options) && count($options)) {
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_actual_id_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_actual_id_'.$i])) {
                                $options['id'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_actual_id_'.$i]);
                            } else {
                                $options['id'] = '';
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_name_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_name_'.$i])) {
                                $options['setting_name'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_name_'.$i]);
                            } else {
                                $options['setting_name'] = '';
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_google_api_text_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_google_api_text_'.$i])) {
                                $options['google_api_key'] = trim($this->my_http_vars['BMLTPlugin_google_api_text_'.$i]);
                            } else {
                                $options['google_api_key'] = '';
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_root_server_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_root_server_'.$i])) {
                                $options['root_server'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_root_server_'.$i]);
                            } else {
                                $options['root_server'] = self::$default_rootserver;
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_initial_view_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_initial_view_'.$i])) {
                                $options['bmlt_initial_view'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_initial_view_'.$i]);
                            } else {
                                $options['bmlt_initial_view'] = self::$default_initial_view;
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_theme_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_theme_'.$i])) {
                                $options['theme'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_theme_'.$i]);
                            } else {
                                $options['theme'] = self::$default_theme;
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_additional_css_'.$i])) {
                            if (trim($this->my_http_vars['BMLTPlugin_option_sheet_additional_css_'.$i])) {
                                $options['additional_css'] = trim($this->my_http_vars['BMLTPlugin_option_sheet_additional_css_'.$i]);
                            } else {
                                $options['additional_css'] = self::$default_additional_css;
                            }
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_latitude_'.$i]) && floatVal($this->my_http_vars['BMLTPlugin_option_latitude_'.$i])) {
                            $options['map_center_latitude'] = floatVal($this->my_http_vars['BMLTPlugin_option_latitude_'.$i]);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_longitude_'.$i]) && floatVal($this->my_http_vars['BMLTPlugin_option_longitude_'.$i])) {
                            $options['map_center_longitude'] = floatVal($this->my_http_vars['BMLTPlugin_option_longitude_'.$i]);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_zoom_'.$i]) && intval($this->my_http_vars['BMLTPlugin_option_zoom_'.$i])) {
                            $options['map_zoom'] = floatVal($this->my_http_vars['BMLTPlugin_option_zoom_'.$i]);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_distance_units_'.$i])) {
                            $options['distance_units'] = $this->my_http_vars['BMLTPlugin_option_sheet_distance_units_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_time_format_'.$i])) {
                            $options['military_time'] = (intval($this->my_http_vars['BMLTPlugin_option_sheet_time_format_'.$i]) != 0);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_week_begins_'.$i])) {
                            $options['startWeekday'] = intval($this->my_http_vars['BMLTPlugin_option_sheet_week_begins_'.$i]);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_grace_period_'.$i])) {
                            $options['grace_period'] = $this->my_http_vars['BMLTPlugin_option_sheet_grace_period_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_region_bias_'.$i])) {
                            $options['region_bias'] = $this->my_http_vars['BMLTPlugin_option_sheet_region_bias_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_lang_'.$i])) {
                            $options['lang'] = $this->my_http_vars['BMLTPlugin_option_sheet_lang_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_auto_search_radius_'.$i])) {
                            $options['default_geo_width'] = $this->my_http_vars['BMLTPlugin_option_sheet_auto_search_radius_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_option_sheet_time_offset_'.$i])) {
                            $options['time_offset'] = $this->my_http_vars['BMLTPlugin_option_sheet_time_offset_'.$i];
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_location_selected_checkbox_'.$i])) {
                            $options['bmlt_location_checked'] = ($this->my_http_vars['BMLTPlugin_location_selected_checkbox_'.$i] != 0 ? 1 : 0);
                        }
                        
                        if (isset($this->my_http_vars['BMLTPlugin_location_services_checkbox_'.$i])) {
                            $options['bmlt_location_services'] = ($this->my_http_vars['BMLTPlugin_location_services_checkbox_'.$i] != 0 ? 1 : 0);
                        }
                        
                        if (!$this->setBMLTOptions($options, $i)) {
                            $ret = 0;
                            break;
                        }
                    }
                }
            }
            
            if (ob_get_level()) {
                ob_end_clean(); // Just in case we are in an OB
            }
            die(strVal($ret));
        } elseif (isset($this->my_http_vars['BMLTPlugin_AJAX_Call']) || isset($this->my_http_vars['BMLTPlugin_Fetch_Langs_AJAX_Call'])) {
            $ret = '';
            if (isset($this->my_http_vars['BMLTPlugin_AJAX_Call_Check_Root_URI'])) {
                $uri = trim($this->my_http_vars['BMLTPlugin_AJAX_Call_Check_Root_URI']);
                
                $test = new bmlt_satellite_controller($uri);
                if ($uri && ($uri != $this->my_current_language->local_options_no_root_server_string ) && ($test instanceof bmlt_satellite_controller)) {
                    if (!$test->get_m_error_message()) {
                        if (isset($this->my_http_vars['BMLTPlugin_AJAX_Call'])) {
                            $ret = trim($test->get_server_version());
                            
                            $ret = explode(".", $ret);
                            
                            if ((intval($ret[0]) < 1) || ((intval($ret[0]) == 1) && (intval($ret[1]) < 10))  || ((intval($ret[0]) == 1) && (intval($ret[1]) == 10) && (intval($ret[2]) < 3))) {
                                $ret = '';
                            } else {
                                $ret = implode('.', $ret);
                            }
                        } else {
                            $slangs = $test->get_server_langs();
                            
                            if ($slangs) {
                                $langs = array();
                                foreach ($slangs as $key => $value) {
                                    $langs[] = array ( $key, $value['name'], $value['default'] );
                                }
                                
                                $ret = array2json($langs);
                            }
                        }
                    }
                }
            }
            
            if (ob_get_level()) {
                ob_end_clean(); // Just in case we are in an OB
            }
            header("Content-type: text/html; charset=ISO-8859-1");
            die($ret);
        }
    }
      
    /************************************************************************************//**
    *   \brief Handles some AJAX routes                                                     *
    *                                                                                       *
    *   This function is called after the page has loaded its custom fields, so we can      *
    *   figure out which settings we're using. If the settings support mobiles, and the UA  *
    *   indicates this is a mobile phone, we redirect the user to our fast mobile handler.  *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function ajax_router()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        // If this is a basic AJAX call, we drop out quickly (We're really just a router).
        if (isset($this->my_http_vars['BMLTPlugin_mobile_ajax_router'])) {
            $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']); // This is for security. We don't allow URIs to be directly specified. They must come from the settings.
            $uri = $options['root_server'].'/'.$this->my_http_vars['request'];
            if (ob_get_level()) {
                ob_end_clean(); // Just in case we are in an OB
            }
            die(bmlt_satellite_controller::call_curl($uri));
        } else // However, if it is a mobile call, we do the mobile thing, then drop out.
            {
            if (isset($this->my_http_vars['BMLTPlugin_mobile'])) {
                $ret = $this->BMLTPlugin_fast_mobile_lookup();
                
                if (ob_get_level()) {
                    ob_end_clean(); // Just in case we are in an OB
                }
                
                $handler = null;
                
                if (zlib_get_coding_type() === false) {
                    $handler = "ob_gzhandler";
                }
                
                ob_start($handler);
                    echo $ret;
                ob_end_flush();
                die();
            } else {
                if (!isset($this->my_http_vars['bmlt_settings_id'])) {
                    $this->my_http_vars['bmlt_settings_id'] = null; // Just to squash a warning.
                }
                    
                $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
                
                $this->load_params();
                
                if (isset($this->my_http_vars['redirect_ajax']) && $this->my_http_vars['redirect_ajax']) {
                    $url = $options['root_server']."/client_interface/xhtml/index.php?switcher=RedirectAJAX$this->my_params";
                    
                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    $ret = bmlt_satellite_controller::call_curl($url);
                    
                    $handler = null;
                    
                    if (zlib_get_coding_type() === false) {
                        $handler = "ob_gzhandler";
                    }
                    
                    header("Content-type: text/html; charset=UTF-8");
                    ob_start($handler);
                        echo $ret;
                    ob_end_flush();
                    die();
                } elseif (isset($this->my_http_vars['redirect_ajax_json'])) {
                    $url = $options['root_server']."/client_interface/json/index.php?".$this->my_http_vars['redirect_ajax_json'].$this->my_params;

                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    $ret = bmlt_satellite_controller::call_curl($url);
                    
                    $handler = null;
                    
                    if (zlib_get_coding_type() === false) {
                        $handler = "ob_gzhandler";
                    }
                    
                    header("Content-type: text/json; charset=ISO-8859-1");
                    ob_start($handler);
                        echo $ret;
                    ob_end_flush();
                    die();
                } elseif (isset($this->my_http_vars['direct_simple'])) {
                    $this->adapt_to_lang($options['lang']);
                    $root_server = $options['root_server']."/client_interface/simple/index.php";
                    $params = urldecode($this->my_http_vars['search_parameters']);
                    $url = "$root_server?switcher=GetSearchResults&".$params;
                    $result = bmlt_satellite_controller::call_curl($url);
                    $result = preg_replace('|\<a |', '<a rel="nofollow external" ', $result);
                    // What all this does, is pick out the single URI in the search parameters string, and replace the meeting details link with it.
                    if (preg_match('|&single_uri=|', $params)) {
                        $single_uri = '';
                        $sp = explode('&', $params);
                        foreach ($sp as $s) {
                            if (preg_match('|single_uri=|', $s)) {
                                list ( $key, $single_uri ) = explode('=', $s);
                                break;
                            }
                        }
                        if ($single_uri) {
                            $result = preg_replace('|\<a [^>]*href="'.preg_quote($options['root_server']).'.*?single_meeting_id=(\d+)[^>]*>|', "<a rel=\"nofollow\" title=\"".$this->process_text($this->my_current_language->local_single_meeting_tooltip)."\" href=\"".$single_uri."=$1&amp;supports_ajax=yes\">", $result);
                        }
                        $result = preg_replace('|\<a rel="external"|', '<a rel="nofollow external" title="'.$this->process_text($this->my_current_language->local_gm_link_tooltip).'"', $result);
                    }

                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    
                    $handler = null;
                    
                    if (zlib_get_coding_type() === false) {
                        $handler = "ob_gzhandler";
                    }
                    
                    header("Content-type: text/html; charset=UTF-8");
                    ob_start($handler);
                        echo $result;
                    ob_end_flush();
                    die();
                } elseif (isset($this->my_http_vars['result_type_advanced']) && ($this->my_http_vars['result_type_advanced'] == 'booklet')) {
                    $uri =  $options['root_server']."/local_server/pdf_generator/?list_type=booklet$this->my_params";
                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    header("Location: $uri");
                    die();
                } elseif (isset($this->my_http_vars['result_type_advanced']) && ($this->my_http_vars['result_type_advanced'] == 'listprint')) {
                    $uri =  $options['root_server']."/local_server/pdf_generator/?list_type=listprint$this->my_params";
                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    header("Location: $uri");
                    die();
                }
            }
        }
    }
    
    /************************************************************************************//**
    *   \brief Massages the page content.                                                   *
    *                                                                                       *
    *   \returns a string, containing the "massaged" content.                               *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function content_filter( $in_the_content   ///< The content in need of filtering.
                            )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $old_content = $in_the_content; // We check to see if we added anything.
        // Simple searches can be mixed in with other content.
        $in_the_content = $this->display_table_search($in_the_content);
        
        $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
        if (file_exists(dirname(__FILE__).'/themes/'.$options['theme'].'/styles.css') && file_exists(dirname(__FILE__).'/themes/'.$options['theme'].'/nouveau_map_styles.css')) {
            $in_the_content = $this->display_simple_search($in_the_content);

            $in_the_content = $this->display_changes($in_the_content);
        
            $in_the_content = $this->display_new_map_search($in_the_content);
        
            $in_the_content = $this->display_bmlt_nouveau($in_the_content);
        
            $in_the_content = $this->display_quicksearch($in_the_content);
        }
        
        // This simply ensures that we remove any unused mobile shortcodes.
        $in_the_content = self::replace_shortcode($in_the_content, 'bmlt_mobile', '');
        
        if ($in_the_content != $old_content) {  // If we made changes, we add a wrapper element, so we can have some strong specificity.
            $in_the_content = "<div id=\"bmlt_page_items\" class=\"bmlt_page_items\">$in_the_content</div>";
        }
            
        return $in_the_content;
    }
    
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   "quick" search.                                                                     *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_quicksearch( $in_content     ///< This is the content to be filtered.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $my_form_next_id = 0;
        
        while ($params = self::get_shortcode($in_content, 'bmlt_quicksearch')) {
            $options_id = $this->cms_get_page_settings_id($in_content);
            $display = '';
            
            if ($params !== true) {
                $param_array = explode('##-##', $params);    // You can specify a settings ID, by separating it from the URI parameters with a ##-##.
                $params = null;
            }
            
            if (($params === true) || is_array($param_array)) {
                if ($params === true) {
                    $params = null;
                } else {
                    $params = str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $param_array[count($param_array)-1]);
                }
                
                // See if there is an options ID in the parameter list.
                if (isset($param_array) && ((is_array($param_array) && (count($param_array) > 1)) || (intval($param_array[0]) && preg_match('/^\d+$/', $param_array[0])))) {
                    $options_id = intval($param_array[0]);
                    if (count($param_array) == 1) {
                        $params = null;
                    }
                }
                
                $options = $this->getBMLTOptions_by_id($options_id);
                $this->adapt_to_lang($options['lang']);
                $theme = $options['theme'];
                $display .= '<noscript class="no_js">'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';
                
                $url = $this->get_plugin_path();
                $ajax_url = $this->get_ajax_base_uri();
                $throbber_loc = htmlspecialchars($url.'themes/'.$theme.'/images/Throbber.gif');
                
                $display .= '<div id="quicksearch_div_'.$my_form_next_id.'" class="quicksearch_div quicksearch_theme_'.$theme.'" style="display:none">' . "\n";
                    $display .= '<div class="quicksearch_print_header" id="quicksearch_print_header_'.$my_form_next_id.'" style="display:none"></div>';
                    $display .= '<div class="quicksearch_throbber_div" id="quicksearch_throbber_div_'.$my_form_next_id.'"><img src="'.htmlspecialchars($throbber_loc).'" alt="AJAX Throbber" /></div>';
                    $display .= '<div class="quicksearch_form_container" id="quicksearch_form_container_'.$my_form_next_id.'" style="display:none">' . "\n";
                        $display .= '<div class="quicksearch_form_select_container" id="quicksearch_form_select_container_'.$my_form_next_id.'" style="display:none">' . "\n";
                            $display .= '<select id="quicksearch_form_town_select_'.$my_form_next_id.'" class="quicksearch_form_town_select">' . "\n";
                                $display .= '<option value="" selected="selected">'.$this->my_current_language->local_quicksearch_select_option_0.'</select>' . "\n";
                            $display .= "</select>\n";
                        $display .= "</div>\n";
                        $display .= '<div class="quicksearch_form_weekdays_container" id="quicksearch_form_weekdays_container_'.$my_form_next_id.'">';
                            $display .= '<div class="quicksearch_form_weekday_container quicksearch_form_weekday_container_0">'."\n";
                                $display .= '<input type="checkbox" checked="checked" id="quicksearch_form_weekday_checkbox_'.$my_form_next_id.'_0" value="0" onchange="bmlt_quicksearch_form_'.$my_form_next_id.'.reactToWeekdayCheckboxChange(this)" />'."\n";
                                $display .= '<label for="quicksearch_form_weekday_checkbox_'.$my_form_next_id.'_0">'.$this->process_text($this->my_current_language->local_new_map_all_weekdays)."</label>\n";
                            $display .= '</div>'."\n";
                            
                for ($index = 1; $index < 8; $index++) {
                    $weekday_index = $index + intval($options['startWeekday']) - 1;

                    if ($weekday_index > 7) {
                        $weekday_index = 1;
                    }
                                
                    $weekdayName = $this->my_current_language->local_weekdays_short[$weekday_index];
                                
                    $display .= '<div class="quicksearch_form_weekday_container quicksearch_form_weekday_container_'.$weekday_index.'">'."\n";
                        $display .= '<input type="checkbox" checked="checked" id="quicksearch_form_weekday_checkbox_'.$my_form_next_id.'_'.$weekday_index.'" value="'.$weekday_index.'" />'."\n";
                        $display .= '<label for="quicksearch_form_weekday_checkbox_'.$weekday_index.'">'.$this->process_text($weekdayName)."</label>\n";
                    $display .= '</div>'."\n";
                }
                        
                        $display .= '<div style="clear:both"></div></div>'."\n";
                        $display .= '<div class="quicksearch_form_text_container quicksearch_form_text_container_0">'."\n";
                            $display .= '<input type="text" class="quicksearch_form_search_text" id="quicksearch_form_search_text_'.$my_form_next_id.'" placeholder="'.$this->process_text($this->my_current_language->local_nouveau_text_item_default_text).'" />'."\n";
                        $display .= '</div>'."\n";
                        $display .= '<input type="button" class="quicksearch_form_submit_button" id="quicksearch_form_submit_button_'.$my_form_next_id.'" value="'.$this->process_text($this->my_current_language->local_nouveau_text_go_button).'" />'."\n";
                    $display .= '</div>' . "\n";
                    $display .= '<div class="quicksearch_results_container" id="quicksearch_results_container_'.$my_form_next_id.'" style="display:none">';
                        $display .= '<div class="quicksearch_too_large_div" id="quicksearch_too_large_div_'.$my_form_next_id.'" style="display:none">'.$this->process_text($this->my_current_language->local_quicksearch_display_too_large).'</div>';
                        $display .= '<div class="quicksearch_no_results_div" id="quicksearch_no_results_div_'.$my_form_next_id.'" style="display:none">'.$this->process_text($this->my_current_language->local_cant_find_meetings_display).'</div>';
                        $display .= '<div class="quicksearch_search_results_div" id="quicksearch_search_results_div_'.$my_form_next_id.'" style="display:none"></div>';
                    $display .= "</div>\n";
                    $display .= "<script type=\"text/javascript\">\n";
                        $field_key = '';
                if ($params) {
                    if (('location_province' == $params)
                        ||  ('location_postal_code_1' == $params)
                        ||  ('location_sub_province' == $params)
                        ||  ('location_municipality' == $params)
                        ||  ('location_city_subsection' == $params)
                        ||  ('location_nation' == $params)
                        ||  ('location_neighborhood' == $params) ) {
                        $field_key = $params;
                        $params = '';
                    } else {
                        $pArray = explode('=', $params);
                        if (1 < count($pArray)) {
                            $field_key = $pArray[0];
                            if (!(('location_province' == $field_key)
                                ||  ('location_postal_code_1' == $field_key)
                                ||  ('location_sub_province' == $field_key)
                                ||  ('location_municipality' == $field_key)
                                ||  ('location_city_subsection' == $field_key)
                                ||  ('location_nation' == $field_key)
                                ||  ('location_neighborhood' == $field_key)) ) {
                                $field_key = '';
                            }
                            $params = $pArray[1];
                        }
                    }
                }
                            
                        $display .= "var bmlt_quicksearch_form_$my_form_next_id = new BMLTQuickSearch ( $my_form_next_id, '$ajax_url', '$options_id'";
                        $display .= ", '$field_key'";
                if ($params) {
                    $pArray = explode(',', $params);
                    $pString = implode('","', array_map("strtolower", $pArray));
                    $display .= ', ["'.$pString.'"]';
                } else {
                    $display .= ', []';
                }
                        $display .= ', ['.$this->my_current_language->local_table_ante_meridian.']';
                        $display .= ", '".htmlspecialchars($this->my_current_language->local_nouveau_meeting_details_map_link_uri_format)."'";
                        $display .= ", ['".join("','", $this->my_current_language->local_nouveau_weekday_long_array)."']";
                        $display .= ', '.strval(intval($options['startWeekday']));
                        $display .= ', '.($options['military_time'] ? 'true' : 'false');
                        $display .= " );\n";
                    $display .= "</script>\n";
                $display .= "</div>\n";
                
                $my_form_next_id++;
            }

            // This simply ensures that we remove any unused mobile shortcodes.
            $in_content = self::replace_shortcode($in_content, 'bmlt_quicksearch', $display);
        }
        return $in_content;
    }
    
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   "popup" search, if provided by the 'bmlt_simple_searches' custom field.             *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_popup_search(
        $in_content,    ///< This is the content to be filtered.
        $in_text,       ///< The text that has the parameters in it.
        &$out_count     ///< This is set to 1, if a substitution was made.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options_id = $this->cms_get_page_settings_id($in_content);
        $params = self::get_shortcode($in_content, 'simple_search_list');
        if ($in_text && $params) {
            $temp_id = $this->cms_get_page_settings_id($in_content);
            
            if ($temp_id) {
                $options_id = $temp_id;
            }
                
            if ($params !== true && intval($params)) {
                $options_id = intval($params);
            }

            if ($options_id) {
                $options = $this->getBMLTOptions_by_id($options_id);
                $this->adapt_to_lang($options['lang']);
            }
                
            $display .= '';

            $text_ar = explode("\n", $in_text);
            
            if (is_array($text_ar) && count($text_ar)) {
                $display .= '<noscript class="no_js">'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';
                $display .= '<div id="interactive_form_div" class="interactive_form_div" style="display:none"><form action="#" onsubmit="return false"><div>';
                $display .= '<label class="meeting_search_select_label" for="meeting_search_select">Find Meetings:</label> ';
                $display .= '<select id="meeting_search_select"class="simple_search_list" onchange="BMLTPlugin_simple_div_filler (this.value,this.options[this.selectedIndex].text);this.options[this.options.length-1].disabled=(this.selectedIndex==0)">';
                $display .= '<option disabled="disabled" selected="selected">'.$this->process_text($this->my_current_language->local_select_search).'</option>';
                $lines_max = count($text_ar);
                $lines = 0;
                while ($lines < $lines_max) {
                    $line['parameters'] = trim($text_ar[$lines++]);
                    $line['prompt'] = trim($text_ar[$lines++]);
                    if ($line['parameters'] && $line['prompt']) {
                        $uri = $this->get_ajax_base_uri().'?bmlt_settings_id='.$options_id.'&amp;direct_simple&amp;search_parameters='.urlencode($line['parameters']);
                        $display .= '<option value="'.$uri.'">'.__($line['prompt']).'</option>';
                    }
                }
                $display .= '<option disabled="disabled"></option>';
                $display .= '<option disabled="disabled" value="">'.$this->process_text($this->my_current_language->local_clear_search).'</option>';
                $display .= '</select></div></form>';
                
                $display .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '');
                $display .= 'document.getElementById(\'interactive_form_div\').style.display=\'block\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
                $display .= 'document.getElementById(\'meeting_search_select\').selectedIndex=0;' . (defined('_DEBUG_MODE_') ? "\n" : '');
                $url = $this->get_plugin_path();
                $img_url .= htmlspecialchars($url.'themes/'.$options['theme'].'/images/');
                
                $display .= "var c_g_BMLTPlugin_images = '$img_url';" . (defined('_DEBUG_MODE_') ? "\n" : '');
                $display .= '</script>';
                $display .= '<div id="simple_search_container"></div></div>';
            }
            
            if ($display) {
                $in_content = self::replace_shortcode($in_content, 'simple_search_list', $display);
            
                $out_count = 1;
            }
        }
        
        return $in_content;
    }
        
    /************************************************************************************//**
    *   \brief This function implements the new, Maps API V. 3 version of the "classic"     *
    *          BMLT search screen.                                                          *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_bmlt_nouveau($in_content      ///< This is the content to be filtered.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $theshortcode = 'bmlt';
        
        $options_id = $this->cms_get_page_settings_id($in_content);

        $in_content = str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $in_content);   // This stupid kludge is because WordPress does an untoward substitution. Won't do anything unless WordPress has been naughty.
        
        $first = true;

        while ($params = self::get_shortcode($in_content, $theshortcode)) {
            if ($params !== true && intval($params)) {
                $options_id = intval($params);
            }
        
            $options = $this->getBMLTOptions_by_id($options_id);

            $this->adapt_to_lang($options['lang']);
            $uid = htmlspecialchars('bmlt_nouveau_'.uniqid());
        
            $the_new_content = '<noscript>'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';    // We let non-JS browsers know that this won't work for them.
        
            if ($first) {   // We only load this the first time.
                // These are the basic global JavaScript properties.
                $the_new_content .= $this->BMLTPlugin_nouveau_map_search_global_javascript_stuff($options_id);
                // Most of the display is built in DOM, but this is how we get our localized strings into JS. We put them in globals.
                $the_new_content .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '');
                $the_new_content .= "var g_NouveauMapSearch_advanced_name_string ='".$this->process_text($this->my_current_language->local_nouveau_advanced_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_NouveauMapSearch_map_name_string ='".$this->process_text($this->my_current_language->local_nouveau_map_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_NouveauMapSearch_text_name_string ='".$this->process_text($this->my_current_language->local_nouveau_text_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_text_go_button_string ='".$this->process_text($this->my_current_language->local_nouveau_text_go_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_text_location_label_text ='".$this->process_text($this->my_current_language->local_nouveau_text_location_label_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_text_item_default_text ='".$this->process_text($this->my_current_language->local_nouveau_text_item_default_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_weekdays_disclosure_text ='".$this->process_text($this->my_current_language->local_nouveau_advanced_weekdays_disclosure_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_formats_disclosure_text ='".$this->process_text($this->my_current_language->local_nouveau_advanced_formats_disclosure_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_service_bodies_disclosure_text ='".$this->process_text($this->my_current_language->local_nouveau_advanced_service_bodies_disclosure_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_no_search_results_text ='".$this->process_text($this->my_current_language->local_nouveau_cant_find_meetings_display).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_cant_lookup_display ='".$this->process_text($this->my_current_language->local_nouveau_cant_lookup_display).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_select_search_spec_text ='".$this->process_text($this->my_current_language->local_nouveau_select_search_spec_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_select_search_results_text ='".$this->process_text($this->my_current_language->local_nouveau_select_search_results_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_display_map_results_text ='".$this->process_text($this->my_current_language->local_nouveau_display_map_results_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_display_list_results_text ='".$this->process_text($this->my_current_language->local_nouveau_display_list_results_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
            
                $the_new_content .= "var g_Nouveau_location_services_set_my_location_advanced_button ='".$this->process_text($this->my_current_language->local_nouveau_location_services_set_my_location_advanced_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_services_find_all_meetings_nearby_button ='".$this->process_text($this->my_current_language->local_nouveau_location_services_find_all_meetings_nearby_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_services_find_all_meetings_nearby_later_today_button ='".$this->process_text($this->my_current_language->local_nouveau_location_services_find_all_meetings_nearby_later_today_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_services_find_all_meetings_nearby_tomorrow_button ='".$this->process_text($this->my_current_language->local_nouveau_location_services_find_all_meetings_nearby_tomorrow_button).(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_meeting_results_count_sprintf_format ='".$this->my_current_language->local_nouveau_meeting_results_count_sprintf_format.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_meeting_results_selection_count_sprintf_format ='".$this->my_current_language->local_nouveau_meeting_results_selection_count_sprintf_format.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_meeting_results_single_selection_count_sprintf_format ='".$this->my_current_language->local_nouveau_meeting_results_single_selection_count_sprintf_format.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_time_sprintf_format ='".$this->my_current_language->local_nouveau_single_time_sprintf_format.(defined('_DEBUG_MODE_') ? "';\n" : "';");
            
                $the_new_content .= "var g_Nouveau_location_sprintf_format_loc_street_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_loc_street_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_loc_street = '".$this->my_current_language->local_nouveau_location_sprintf_format_loc_street.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_street_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_street_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_loc_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_loc_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_street = '".$this->my_current_language->local_nouveau_location_sprintf_format_street.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_loc = '".$this->my_current_language->local_nouveau_location_sprintf_format_loc.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_town_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_town_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_town_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_town_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_town_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_town_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_province_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_province_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_province = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_province.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_zip = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_zip.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_street = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_street.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc_info = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc_info.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_street = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_street.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_single_loc = '".$this->my_current_language->local_nouveau_location_sprintf_format_single_loc.(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_location_sprintf_format_wtf ='".$this->process_text($this->my_current_language->local_nouveau_location_sprintf_format_wtf).(defined('_DEBUG_MODE_') ? "';\n" : "';");

                $the_new_content .= "var g_Nouveau_time_sprintf_format = '".$this->my_current_language->local_nouveau_time_sprintf_format.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_am ='".$this->process_text($this->my_current_language->local_nouveau_am).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_pm ='".$this->process_text($this->my_current_language->local_nouveau_pm).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_noon ='".$this->process_text($this->my_current_language->local_nouveau_noon).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_midnight ='".$this->process_text($this->my_current_language->local_nouveau_midnight).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_label_1 ='".$this->process_text($this->my_current_language->local_nouveau_advanced_map_radius_label_1).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_label_2 ='".$this->process_text($this->my_current_language->local_nouveau_advanced_map_radius_label_2).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_value_2_km ='".$this->process_text($this->my_current_language->local_nouveau_advanced_map_radius_value_km).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_value_2_mi ='".$this->process_text($this->my_current_language->local_nouveau_advanced_map_radius_value_mi).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_value_auto ='".$this->process_text($this->my_current_language->local_nouveau_advanced_map_radius_value_auto).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_advanced_map_radius_value_array = [ ".$this->my_current_language->local_nouveau_advanced_map_radius_value_array." ];";
                $the_new_content .= "var g_Nouveau_meeting_details_link_title = '".$this->process_text($this->my_current_language->local_nouveau_meeting_details_link_title).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_meeting_details_map_link_uri_format = '".htmlspecialchars($this->my_current_language->local_nouveau_meeting_details_map_link_uri_format).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_meeting_details_map_link_text = '".$this->process_text($this->my_current_language->local_nouveau_meeting_details_map_link_text).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_array_keys = {";
                    $first = true;
                foreach ($this->my_current_language->local_nouveau_prompt_array as $key => $value) {
                    if (!$first) {
                        $the_new_content .= ',';
                    }
                    $first = false;
                    $the_new_content .= '"'.$key.'":';
                    $the_new_content .= '"'.$this->process_text($value).'"';
                }
                $the_new_content .= "};";
                $the_new_content .= 'var g_Nouveau_military_time = '.((isset($options['military_time']) && $options['military_time']) ? 'true' : 'false' ).';';
                $the_new_content .= 'var g_Nouveau_start_week = '.((isset($options['startWeekday']) && $options['startWeekday']) ? $options['startWeekday'] : self::$default_startWeekday ).';';
                $the_new_content .= 'var g_Nouveau_array_header_text = new Array ( "'.join('","', $this->my_current_language->local_nouveau_table_header_array).'");';
                $the_new_content .= 'var g_Nouveau_weekday_long_array = new Array ( "'.join('","', $this->my_current_language->local_nouveau_weekday_long_array).'");';
                $the_new_content .= 'var g_Nouveau_weekday_short_array = new Array ( "'.join('","', $this->my_current_language->local_nouveau_weekday_short_array).'");';
                $the_new_content .= "var g_Nouveau_lookup_location_failed = '".$this->process_text($this->my_current_language->local_nouveau_lookup_location_failed).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_lookup_location_server_error = '".$this->process_text($this->my_current_language->local_nouveau_lookup_location_server_error).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_default_geo_width = ".$options['default_geo_width'].";";
                $the_new_content .= "var g_Nouveau_default_details_map_zoom = ".self::$default_details_map_zoom.';';
                $the_new_content .= "var g_Nouveau_default_marker_aggregation_threshold_in_pixels = 8;";

                $the_new_content .= "var g_Nouveau_single_formats_label = '".$this->process_text($this->my_current_language->local_nouveau_single_formats_label).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_service_body_label = '".$this->process_text($this->my_current_language->local_nouveau_single_service_body_label).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                
                $the_new_content .= "var g_Nouveau_user_logged_in = '".((isset($this->m_is_logged_in_user) && $this->m_is_logged_in_user) ? "true" : "false" ).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_default_duration = '".self::$default_duration."';";
                $the_new_content .= "var g_Nouveau_location_sprintf_format_duration_title = '".$this->my_current_language->local_nouveau_location_sprintf_format_duration_title.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_duration_hour_only_title = '".$this->my_current_language->local_nouveau_location_sprintf_format_duration_hour_only_title.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_duration_hour_only_and_minutes_title = '".$this->my_current_language->local_nouveau_location_sprintf_format_duration_hour_only_and_minutes_title.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_location_sprintf_format_duration_hours_only_title = '".$this->my_current_language->local_nouveau_location_sprintf_format_duration_hours_only_title.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_duration_sprintf_format_1_hr ='".$this->my_current_language->local_nouveau_single_duration_sprintf_format_1_hr.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_duration_sprintf_format_mins ='".$this->my_current_language->local_nouveau_single_duration_sprintf_format_mins.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_duration_sprintf_format_hrs ='".$this->my_current_language->local_nouveau_single_duration_sprintf_format_hrs.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_duration_sprintf_format_hr_mins ='".$this->my_current_language->local_nouveau_single_duration_sprintf_format_hr_mins.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                $the_new_content .= "var g_Nouveau_single_duration_sprintf_format_hrs_mins ='".$this->my_current_language->local_nouveau_single_duration_sprintf_format_hrs_mins.(defined('_DEBUG_MODE_') ? "';\n" : "';");
                
                $the_new_content .= '</script>';
                $first = false;
            }
        
            $in_options_id = $options['id'];
        
            if (defined('_DEBUG_MODE_')) {
                $the_new_content .= "\n"; // These just make the code easier to look at.
            }
            // This is the overall container div.
            $the_new_content .= '<div id="'.$uid.'_container" class="bmlt_nouveau_container">';
                $single_meeting_id = isset($this->my_http_vars['single_meeting_id']) ? intval($this->my_http_vars['single_meeting_id']) : 0;
                // What we do here, is tell the client to create a global variable (in JS DOM), with a unique handler for this instance of the Nouveau search.
                $the_new_content .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '').'var g_instance_'.$uid.'_js_handler = new NouveauMapSearch ( \''.$uid.'\', \''
                                                                                                                                                                            .$options['bmlt_initial_view'].'\','
                                                                                                                                                                            .$options['map_center_latitude'].","
                                                                                                                                                                            .$options['map_center_longitude'].","
                                                                                                                                                                            .$options['map_zoom'].",'"
                                                                                                                                                                            .$options['distance_units']."','"
                                                                                                                                                                            .$this->get_plugin_path()."themes/".$options['theme']."','"
                                                                                                                                                                            .htmlspecialchars($this->get_ajax_base_uri())."?bmlt_settings_id=$in_options_id&redirect_ajax_json=', '', ".($options['bmlt_location_checked'] ? 'true' : 'false').", "
                                                                                                                                                                            .($options['bmlt_location_services'] == 0 || ($options['bmlt_location_services'] == 1 && BMLTPlugin_weAreMobile($this->my_http_vars)) ? 'true' : 'false').", "
                                                                                                                                                                            .$single_meeting_id.", "
                                                                                                                                                                            .$options['grace_period'].");" . (defined('_DEBUG_MODE_') ? "\n" : '')."</script>";
            $the_new_content .= '</div>';

            $in_content = self::replace_shortcode($in_content, $theshortcode, $the_new_content);
        }
            
        return $in_content;
    }
        
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   "simple" search                                                                     *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_simple_search($in_content      ///< This is the content to be filtered.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options_id = $this->cms_get_page_settings_id($in_content);
        
        if ($options_id) {
            $options = $this->getBMLTOptions_by_id($options_id);
            $this->adapt_to_lang($options['lang']);
            $root_server_root = $options['root_server'];
        }
        
        if ($root_server_root) {
            while ($params = self::get_shortcode($in_content, 'bmlt_simple')) {
                $param_array = explode('##-##', $params);    // You can specify a settings ID, by separating it from the URI parameters with a ##-##.
        
                $params = null;
        
                if (is_array($param_array) && (count($param_array) > 1)) {
                    $options = $this->getBMLTOptions_by_id($param_array[0]);
                    $this->adapt_to_lang($options['lang']);
                    $root_server_root = $options['root_server'];
                }
        
                $params = (count($param_array) > 0) ? '?'.str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $param_array[count($param_array)-1]) : null;
        
                $uri = $root_server_root."/client_interface/simple/index.php".$params;

                $the_new_content = bmlt_satellite_controller::call_curl($uri);
                $in_content = self::replace_shortcode($in_content, 'bmlt_simple', $the_new_content);
            }
        }
        
        return $in_content;
    }
        
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   new "table" search                                                                  *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_table_search($in_content      ///< This is the content to be filtered.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $my_table_next_id = 0;
        
        while ($params = self::get_shortcode($in_content, 'bmlt_table')) {
            $options_id = $this->cms_get_page_settings_id($in_content);
            
            if ($params !== true) {
                $param_array = explode('##-##', $params);    // You can specify a settings ID, by separating it from the URI parameters with a ##-##.
                $params = null;
            }
            
            if (($params === true) || is_array($param_array)) {
                if ($params === true) {
                    $params = null;
                } else {
                    $params = str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $param_array[count($param_array)-1]);
                }
                
                // See if there is an options ID in the parameter list.
                if ((is_array($param_array) && (count($param_array) > 1)) || (intval($param_array[0]) && preg_match('/^\d+$/', $param_array[0]))) {
                    $options_id = intval($param_array[0]);
                    if (count($param_array) == 1) {
                        $params = null;
                    }
                }
                $options = $this->getBMLTOptions_by_id($options_id);
                $this->adapt_to_lang($options['lang']);
                // This strips weekday selectors out. We will be dealing with this ourselves.
                $params = preg_replace('|(\&){0,1}weekdays(\[\]){0,1}=[0-9]{0,1}|', '', $params);
                // We ignore the block_mode and sort key selectors as well. We'll be doing our own thing. It will be table-based, and sorted by time (to start).
                $params = preg_replace('|(\&){0,1}block_mode=[a-zA-Z0-9]{0,5}|', '', $params);
                $params = preg_replace('|(\&){0,1}sort_key=[a-zA-Z0-9]*?|', '', $params);
                $params = preg_replace('|(\&){0,1}sort_dir=[a-zA-Z0-9]{0,4}|', '', $params);
                $params = preg_replace('|^[\&\?]|', '', $params);
                $params = preg_replace('|[\&\?]$|', '', $params);
                
                $the_new_content = '<noscript>'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';    // We let non-JS browsers know that this won't work for them.
                
                // The first time through, we import our JS file. After that, we no longer need it.
                if (!$my_table_next_id) {
                    $the_new_content .= "<script type=\"text/javascript\">".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= self::stripFile('table_display.js');
                    $the_new_content .= 'var g_table_weekday_name_array = new Array ( "'.join('","', $this->my_current_language->local_nouveau_weekday_short_array).'" );'.(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= 'var g_table_weekday_long_name_array = new Array ( "'.join('","', $this->my_current_language->local_nouveau_weekday_long_array).'" );'.(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_throbber_img_src = '".htmlspecialchars($this->get_plugin_path().'themes/default/images/TableThrobber.gif').(defined('_DEBUG_MODE_') ? "';\n" : "';");
                    $the_new_content .= "var g_table_time_header_text = '".$this->process_text($this->my_current_language->local_table_header_time_label)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_name_header_text = '".$this->process_text($this->my_current_language->local_table_header_meeting_name_label)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_town_header_text = '".$this->process_text($this->my_current_language->local_table_header_town_label)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_address_header_text = '".$this->process_text($this->my_current_language->local_table_header_address_label)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_format_header_text = '".htmlspecialchars($this->my_current_language->local_table_header_format_label)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_header_tab_format = '".htmlspecialchars($this->my_current_language->local_table_header_tab_title_format)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_header_tab_loading_format = '".htmlspecialchars($this->my_current_language->local_table_tab_loading_title_format)."';".(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= 'var g_table_ampm_array = new Array ( '.$this->my_current_language->local_table_ante_meridian.' );'.(defined('_DEBUG_MODE_') ? "\n" : "");
                    $the_new_content .= "var g_table_map_link_uri_format = '".htmlspecialchars($this->my_current_language->local_nouveau_meeting_details_map_link_uri_format).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                    $the_new_content .= "var g_table_no_meetings_format = '".htmlspecialchars($this->my_current_language->local_table_no_meetings_format).(defined('_DEBUG_MODE_') ? "';\n" : "';");
                    $the_new_content .= "</script>";
                }
                
                $my_table_next_id++;    // We increment the ID, so we can have multiple tables on the same page. This also makes the IDs very predictable for fun CSS tricks.
                
                $the_new_content .= '<div style="display_none" class="bmlt_table_display_div bmlt_table_display_div_theme_'.htmlspecialchars($options['theme']).'" id="bmlt_table_display_div_'.strval($my_table_next_id).'"></div>'.(defined('_DEBUG_MODE_') ? "\n" : "");
                $theWeekday = strval(intval($options['startWeekday'] - 1));
                $the_new_content .= "<script type=\"text/javascript\">var bmlt_table_display_func_".strval($my_table_next_id)." = new TableSearchDisplay ( 'bmlt_table_display_div_".strval($my_table_next_id)."', '$options_id', '".htmlspecialchars($options['theme'])."','".htmlspecialchars($this->get_ajax_base_uri())."?redirect_ajax_json=', '$theWeekday', ".($options['military_time'] ? 'true' : 'false' ).", '$params' );</script>".(defined('_DEBUG_MODE_') ? "\n" : "");
                
                $in_content = self::replace_shortcode($in_content, 'bmlt_table', $the_new_content);
            }
        }
        
        return $in_content;
    }
        
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   "new map" search                                                                    *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_new_map_search($in_content      ///< This is the content to be filtered.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options_id = $this->cms_get_page_settings_id($in_content);

        $in_content = str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $in_content);   // This stupid kludge is because WordPress does an untoward substitution. Won't do anything unless WordPress has been naughty.
        
        $first = true;

        while ($params = self::get_shortcode($in_content, 'bmlt_map')) {
            if ($params !== true && intval($params)) {
                $options_id = intval($params);
            }
            
            $options = $this->getBMLTOptions_by_id($options_id);
            $this->adapt_to_lang($options['lang']);
            $uid = htmlspecialchars('BMLTuid_'.uniqid());
            
            $the_new_content = '<noscript>'.$this->process_text($this->my_current_language->local_noscript).'</noscript>';    // We let non-JS browsers know that this won't work for them.
            
            if ($first) {   // We only load this the first time.
                $the_new_content .= $this->BMLTPlugin_map_search_global_javascript_stuff($options_id);
                $first = false;
            }

            $the_new_content .= '<div class="bmlt_map_container_div bmlt_map_container_div_theme_'.htmlspecialchars($options['theme']).'" style="display:none" id="'.$uid.'">';  // This starts off hidden, and is revealed by JS.
                $the_new_content .= '<div class="bmlt_map_container_div_header">';  // This allows a CSS "hook."
                    $the_new_content .= $this->BMLTPlugin_map_search_location_options($options_id, $uid);   // This is the box of location search choices.
                    $the_new_content .= $this->BMLTPlugin_map_search_search_options($options_id, $uid);     // This is the box of basic search choices.
                    $the_new_content .= $this->BMLTPlugin_map_search_local_javascript_stuff($options_id, $uid);
                $the_new_content .= '</div>';
                $the_new_content .= '<div class="bmlt_search_map_div" id="'.$uid.'_bmlt_search_map_div"></div>';
                $the_new_content .= '<script type="text/javascript">var g_military_time = '.($options['military_time'] ? 'true' : 'false' ).';g_no_meetings_found="'.htmlspecialchars($this->my_current_language->local_cant_find_meetings_display).'";document.getElementById(\''.$uid.'\').style.display=\'block\';c_ms_'.$uid.' = new MapSearch ( \''.htmlspecialchars($uid).'\',\''.htmlspecialchars($options_id).'\', document.getElementById(\''.$uid.'_bmlt_search_map_div\'), {\'latitude\':'.$options['map_center_latitude'].',\'longitude\':'.$options['map_center_longitude'].',\'zoom\':'.$options['map_zoom'].'} );var g_Nouveau_start_week = '.((isset($options['startWeekday']) && $options['startWeekday']) ? $options['startWeekday'] : self::$default_startWeekday ).';</script>';
            $the_new_content .= '</div>';
            
            $in_content = self::replace_shortcode($in_content, 'bmlt_map', $the_new_content);
        }
            
        return $in_content;
    }

    /************************************************************************************//**
    *   \brief  This returns a div of location options to be applied to the map search.     *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_map_search_location_options(
        $in_options_id, ///< The ID for the options to use for this implementation.
        $in_uid         ///< This is the UID of the enclosing div.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '<div class="bmlt_map_container_div_location_options_div" id="'.$in_uid.'_location">';
            $ret .= '<div class="bmlt_map_options_loc">';
                $ret .= '<a class="bmlt_map_reveal_options" id="'.$in_uid.'_options_loc_a" href="javascript:var a=document.getElementById(\''.$in_uid.'_options_loc_a\');var b=document.getElementById(\''.$in_uid.'_options_loc\');if(b &amp;&amp; a){if(b.style.display==\'none\'){a.className=\'bmlt_map_hide_options\';b.style.display=\'block\';c_ms_'.$in_uid.'.openLocationSectionExt(document.getElementById(\''.$in_uid.'_location_text\'), document.getElementById(\''.$in_uid.'_location_submit\'));}else{a.className=\'bmlt_map_reveal_options\';b.style.display=\'none\';};};c_ms_'.$in_uid.'.recalculateMapExt()"><span>'.$this->process_text($this->my_current_language->local_new_map_option_loc_label).'</span></a>';
                $ret .= '<div class="bmlt_map_container_div_search_options_div" id="'.$in_uid.'_options_loc" style="display:none">';
                    $ret .= '<form action="#" method="get" onsubmit="c_ms_'.$in_uid.'.lookupLocationExt(document.getElementById(\''.$in_uid.'_location_text\'), document.getElementById(\''.$in_uid.'_location_submit\'));return false">';
                        $ret .= '<fieldset class="bmlt_map_container_div_search_options_div_location_fieldset">';
                            $ret .= '<div class="location_radius_popup_div">';
                                $ret .= '<label for="">'.$this->process_text($this->my_current_language->local_new_map_option_loc_popup_label_1).'</label>';
                                $ret .= '<select class="bmlt_map_location_radius_popup" id="'.$in_uid.'_radius_select" onchange="c_ms_'.$in_uid.'.changeRadiusExt(true)">';
                                    $ret .= '<option value="" selected="selected">'.$this->process_text($this->my_current_language->local_new_map_option_loc_popup_auto).'</option>';
                                    $ret .= '<option value="" disabled="disabled"></option>';
                                    $options = $this->getBMLTOptions_by_id($in_options_id);
                                    $this->adapt_to_lang($options['lang']);
        foreach ($this->my_current_language->local_new_map_js_diameter_choices as $radius) {
            $ret .= '<option value="'.($radius / 2).'">'.($radius / 2).' '.$this->process_text((strtolower($options['distance_units']) == 'km') ? $this->my_current_language->local_new_map_option_loc_popup_km : $this->my_current_language->local_new_map_option_loc_popup_mi).'</option>';
        }
                                $ret .= '</select>';
                                $ret .= '<label for="">'.$this->process_text($this->my_current_language->local_new_map_option_loc_popup_label_2).'</label>';
                            $ret .= '</div>';
                            $ret .= '<fieldset class="location_text_entry_fieldset">';
                                $ret .= '<legend>'.$this->process_text($this->my_current_language->local_new_map_text_entry_fieldset_label).'</legend>';
                                $def_text = $this->process_text($this->my_current_language->local_new_map_text_entry_default_text);
                                $ret .= '<div class="location_text_input_div">';
                                    $ret .= '<input type="text" class="location_text_input_item_blurred" value="'.$def_text.'" id="'.$in_uid.'_location_text" onfocus="c_ms_'.$in_uid.'.focusLocationTextExt(this, document.getElementById(\''.$in_uid.'_location_submit\'), false)" onblur="c_ms_'.$in_uid.'.focusLocationTextExt(this, document.getElementById(\''.$in_uid.'_location_submit\'), true)" onkeyup="c_ms_'.$in_uid.'.enterTextIntoLocationTextExt(this, document.getElementById(\''.$in_uid.'_location_submit\'))" />';
                                $ret .= '</div>';
                                $ret .= '<div class="location_text_submit_div">';
                                    $ret .= '<input type="button" disabled="disabled" class="location_text_submit_button" value="'.$this->process_text($this->my_current_language->local_new_map_location_submit_button_text).'" id="'.$in_uid.'_location_submit" onclick="c_ms_'.$in_uid.'.lookupLocationExt(document.getElementById(\''.$in_uid.'_location_text\'), this)" />';
                                $ret .= '</div>';
                            $ret .= '</fieldset>';
                        $ret .= '</fieldset>';
                    $ret .= '</form>';
                $ret .= '</div>';
            $ret .= '</div>';
        $ret .= '</div>';
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief  This returns a div of search options to be applied to the map search.       *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_map_search_search_options(
        $in_options_id, ///< The ID for the options to use for this implementation.
        $in_uid         ///< This is the UID of the enclosing div.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options = $this->getBMLTOptions_by_id($in_options_id);
        $this->adapt_to_lang($options['lang']);
        $ret = '<div class="bmlt_map_container_div_search_options_div" id="'.$in_uid.'_options">';
            $ret .= '<div class="bmlt_map_options_1">';
                $ret .= '<a class="bmlt_map_reveal_options" id="'.$in_uid.'_options_1_a" href="javascript:var a=document.getElementById(\''.$in_uid.'_options_1_a\');var b=document.getElementById(\''.$in_uid.'_options_1\');if(b &amp;&amp; a){if(b.style.display==\'none\'){a.className=\'bmlt_map_hide_options\';b.style.display=\'block\'}else{a.className=\'bmlt_map_reveal_options\';b.style.display=\'none\'}};c_ms_'.$in_uid.'.recalculateMapExt()"><span>'.$this->process_text($this->my_current_language->local_new_map_option_1_label).'</span></a>';
                $ret .= '<div class="bmlt_map_container_div_search_options_div" id="'.$in_uid.'_options_1" style="display:none">';
                    $ret .= '<form action="#" method="get" onsubmit="return false">';
                        $ret .= '<fieldset class="bmlt_map_container_div_search_options_div_weekdays_fieldset">';
                            $ret .= '<legend>'.$this->process_text($this->my_current_language->local_new_map_weekdays).'</legend>';
                            $ret .= '<div class="bmlt_map_container_div_search_options_weekday_checkbox_div"><input title="'.$this->process_text($this->my_current_language->local_new_map_all_weekdays_title).'" type="checkbox" id="weekday_'.$in_uid.'_0" checked="checked" onchange="c_ms_'.$in_uid.'.recalculateMapExt(this)" />';
                            $ret .= '<label title="'.$this->process_text($this->my_current_language->local_new_map_all_weekdays_title).'" for="weekday_'.$in_uid.'_0">'.$this->process_text($this->my_current_language->local_new_map_all_weekdays).'</label></div>';
        for ($index = 1; $index < count($this->my_current_language->local_weekdays); $index++) {
            $weekday_index = ($index - 1) + $options['startWeekday'];
            
            if ($weekday_index > 7) {
                $weekday_index -= 7;
            }
            
            $weekday = $this->my_current_language->local_weekdays[$weekday_index];
            $ret .= '<div class="bmlt_map_container_div_search_options_weekday_checkbox_div">';
                $ret .= '<input title="'.$this->process_text($this->my_current_language->local_new_map_weekdays_title.$weekday).'." type="checkbox" id="weekday_'.$in_uid.'_'.htmlspecialchars($weekday_index).'" onchange="c_ms_'.$in_uid.'.recalculateMapExt(this)" />';
                $ret .= '<label title="'.$this->process_text($this->my_current_language->local_new_map_weekdays_title.$weekday).'." for="weekday_'.$in_uid.'_'.htmlspecialchars($weekday_index).'">'.$this->process_text($weekday).'</label>';
            $ret .= '</div>';
        }
                        $ret .= '</fieldset>';
                        $ret .= '<fieldset class="bmlt_map_container_div_search_options_div_formats_fieldset">';
                            $ret .= '<legend>'.$this->process_text($this->my_current_language->local_new_map_formats).'</legend>';
                            $ret .= '<div class="bmlt_map_container_div_search_options_formats_checkbox_div">';
                                $ret .= '<input title="'.$this->process_text($this->my_current_language->local_new_map_all_formats_title).'" type="checkbox" id="formats_'.$in_uid.'_0" checked="checked" onchange="c_ms_'.$in_uid.'.recalculateMapExt(this)" />';
                                $ret .= '<label title="'.$this->process_text($this->my_current_language->local_new_map_all_formats_title).'" for="formats_'.$in_uid.'_0">'.$this->process_text($this->my_current_language->local_new_map_all_formats).'</label>';
                            $ret .= '</div>';
                            $this->my_driver->set_m_root_uri($options['root_server']);
                            $error = $this->my_driver->get_m_error_message();
                            
        if ($error) {
        } else {
            $formats = $this->my_driver->get_server_formats();
        
            if (!$this->my_driver->get_m_error_message()) {
                $index = 1;
                foreach ($formats as $id => $format) {
                    $ret .= '<div class="bmlt_map_container_div_search_options_formats_checkbox_div"><input type="checkbox" value="'.intval($id).'" id="formats_'.$in_uid.'_'.$index.'" onchange="c_ms_'.$in_uid.'.recalculateMapExt(this)" title="'.$this->process_text('('.$format['name_string'] .') '.$format['description_string']).'" />';
                    $ret .= '<label title="'.$this->process_text('('.$format['name_string'] .') '.$format['description_string']).'" for="formats_'.$in_uid.'_'.$index.'">'.$this->process_text($format['key_string']).'</label></div>';
                    $index++;
                }
            }
        }
                        $ret .= '</fieldset>';
                    $ret .= '</form>';
                $ret .= '</div>';
            $ret .= '</div>';
        $ret .= '</div>';
        return $ret;
    }

    /************************************************************************************//**
    *   \brief  This returns the global JavaScript stuff for the new map search that only   *
    *           only needs to be loaded once.                                               *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_map_search_global_javascript_stuff( $in_options_id  ///< The ID of our currently selected options.
                                                            )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options = $this->getBMLTOptions_by_id($in_options_id);
        $this->adapt_to_lang($options['lang']);
        $gKey = '';

        if (isset($options['google_api_key']) && ('' != $options['google_api_key']) && ('INVALID' != $options['google_api_key'])) {
            $gKey = $options['google_api_key'];
        }

        // Include the Google Maps API files.
        $ret = '</script><script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key='.$gKey;
        if (isset($options['region_bias']) && $options['region_bias']) {
            $ret .= '&region='.strtoupper($options['region_bias']);
        }
        $ret .= '"></script>';
        // Declare the various globals and display strings. This is how we pass strings to the JavaScript, as opposed to the clunky way we do it in the root server.
        $ret .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_cannot_determine_location = \''.$this->process_text($this->my_current_language->local_cannot_determine_location).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_no_meetings_found = \''.$this->process_text($this->my_current_language->local_mobile_fail_no_meetings).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_server_error = \''.$this->process_text($this->my_current_language->local_server_fail).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_address_lookup_fail = \''.$this->process_text($this->my_current_language->local_cant_find_address).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_center_marker_curent_radius_1 = \''.$this->process_text($this->my_current_language->local_new_map_js_center_marker_current_radius_1).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_center_marker_curent_radius_2_km = \''.$this->process_text($this->my_current_language->local_new_map_js_center_marker_current_radius_2_km).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_center_marker_curent_radius_2_mi = \''.$this->process_text($this->my_current_language->local_new_map_js_center_marker_current_radius_2_mi).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_map_link_text = \''.$this->process_text($this->my_current_language->local_map_link).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_weekdays = [';
        $ret .= "'".$this->process_text(join("','", $this->my_current_language->local_weekdays))."'";
        $ret .= '];' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_weekdays_short = [';
        $ret .= "'".$this->process_text(join("','", $this->my_current_language->local_weekdays_short))."'";
        $ret .= '];' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_diameter_choices = ['.join(",", $this->my_current_language->local_new_map_js_diameter_choices).'];' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_formats = \''.$this->process_text($this->my_current_language->local_formats).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_Noon = \''.$this->process_text($this->my_current_language->local_noon).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_Midnight = \''.$this->process_text($this->my_current_language->local_midnight).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_debug_mode = '.( defined('DEBUG_MODE') ? 'true' : 'false' ).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_prompt = \''.$this->process_text($this->my_current_language->local_mobile_distance).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_prompt_suffix = \''.$this->process_text($this->my_current_language->local_new_map_center_marker_distance_suffix).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_center_marker_desc = \''.$this->process_text($this->my_current_language->local_new_map_center_marker_description).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_BMLTPlugin_files_uri = \''.htmlspecialchars($this->get_ajax_mobile_base_uri()).'?\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= "var c_g_BMLTPlugin_images = '".htmlspecialchars($this->get_plugin_path()."/google_map_images")."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= "var c_g_BMLTPlugin_default_location_text = '".$this->process_text($this->my_current_language->local_new_map_text_entry_default_text)."';" . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= '</script>';
        $ret .= '<script src="'.htmlspecialchars($this->get_plugin_path()).'map_search.js" type="text/javascript"></script>';

        return $ret;
    }

    /************************************************************************************//**
    *   \brief  This returns the global JavaScript stuff for the new map search that only   *
    *           only needs to be loaded once.                                               *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_nouveau_map_search_global_javascript_stuff($in_options_id  ///< The ID of our currently selected options.
                                                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options = $this->getBMLTOptions_by_id($in_options_id);
        $this->adapt_to_lang($options['lang']);
        // Include the Google Maps API V3 files.
        $gKey = '';

        if (isset($options['google_api_key']) && ('' != $options['google_api_key']) && ('INVALID' != $options['google_api_key'])) {
            $gKey = $options['google_api_key'];
        }

        $ret = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key='.$gKey;
        if (isset($options['region_bias']) && $options['region_bias']) {
            $ret .= '&region='.strtoupper($options['region_bias']);
        }
        $ret .= '"></script>';
        $ret .= '<script src="'.htmlspecialchars($this->get_plugin_path()).'nouveau_map_search.js" type="text/javascript"></script>';

        return $ret;
    }

    /************************************************************************************//**
    *   \brief  This returns the JavaScript stuff that needs to be loaded into each of the  *
    *           new map search instances.                                                   *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_map_search_local_javascript_stuff(
        $in_options_id, ///< The ID for the options to use for this implementation.
        $in_uid         ///< The unique ID for this instance
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options = $this->getBMLTOptions_by_id($in_options_id);
        $this->adapt_to_lang($options['lang']);

        // Declare the various globals and display strings. This is how we pass strings to the JavaScript, as opposed to the clunky way we do it in the root server.
        $ret = '<script type="text/javascript">';
        $ret .= 'var c_ms_'.$in_uid.' = null;';
        $ret .= 'var c_g_distance_units_are_km_'.$in_uid.' = '.((strtolower($options['distance_units']) == 'km' ) ? 'true' : 'false').';';
        $ret .= 'var c_g_distance_units_'.$in_uid.' = \''.((strtolower($options['distance_units']) == 'km' ) ? $this->process_text($this->my_current_language->local_mobile_kilometers) : $this->process_text($this->my_current_language->local_mobile_miles) ).'\';';
        $ret .= 'var c_g_BMLTPlugin_throbber_img_src_'.$in_uid." = '".htmlspecialchars($this->get_plugin_path().'themes/'.$options['theme'].'/images/Throbber.gif').(defined('_DEBUG_MODE_') ? "';\n" : "';");
        $ret .= 'var c_g_BMLTRoot_URI_JSON_SearchResults_'.$in_uid." = '".htmlspecialchars($this->get_ajax_base_uri())."?redirect_ajax_json=".urlencode('switcher=GetSearchResults')."&bmlt_settings_id=$in_options_id';\n";
        $ret .= '</script>';

        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief This is a function that filters the content, and replaces a portion with the *
    *   "changes" dump.                                                                     *
    *                                                                                       *
    *   \returns a string, containing the content.                                          *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function display_changes(  $in_content      ///< This is the content to be filtered.
                                )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options_id = $this->cms_get_page_settings_id($in_content);
        
        $options = $this->getBMLTOptions_by_id($options_id);
        $this->adapt_to_lang($options['lang']);
        $root_server_root = $options['root_server'];

        $in_content = str_replace(array ( '&#038;', '&#038;#038;', '&#038;amp;', '&amp;#038;', '&amp;', '&amp;amp;' ), '&', $in_content);   // This stupid kludge is because WordPress does an untoward substitution. Won't do anything unless WordPress has been naughty.
        while ($params = self::get_shortcode($in_content, 'bmlt_changes')) {
            $param_array = explode('##-##', $params);    // You can specify a settings ID, by separating it from the URI parameters with a ##-##.
            
            $params = null;
            
            if (is_array($param_array) && (count($param_array) > 1)) {
                $options = $this->getBMLTOptions_by_id($param_array[0]);
                $this->adapt_to_lang($options['lang']);
                $params = $param_array[1];
            } else {
                $params = (count($param_array) > 0) ? $param_array[0] : null;
            }
            
            if ($params && $options['root_server']) {
                $params = explode('&', $params);
                
                $start_date = null;
                $end_date = null;
                $meeting_id = null;
                $service_body_id = null;
                $single_uri = null;
                
                foreach ($params as $one_param) {
                    list ( $key, $value ) = explode('=', $one_param, 2);
                    
                    if ($key && $value) {
                        switch ($key) {
                            case 'start_date':
                                $start_date = strtotime($value);
                                break;
                            
                            case 'end_date':
                                $end_date = strtotime($value);
                                break;
                            
                            case 'meeting_id':
                                $meeting_id = intval($value);
                                break;
                            
                            case 'service_body_id':
                                $service_body_id = intval($value);
                                break;
                            
                            case 'single_uri':
                                $single_uri = $value;
                                break;
                        }
                    }
                }
                $this->my_driver->set_m_root_uri($options['root_server']);
                $error = $this->my_driver->get_m_error_message();
                
                if ($error) {
                    if (ob_get_level()) {
                        ob_end_clean(); // Just in case we are in an OB
                    }
                    echo "<!-- BMLTPlugin ERROR (display_changes)! Can't set the Satellite Driver root! ".htmlspecialchars($error)." -->";
                } else {
                    set_time_limit(120); // Change requests can take a loooong time...
                    $changes = $this->my_driver->get_meeting_changes($start_date, $end_date, $meeting_id, $service_body_id);

                    $error = $this->my_driver->get_m_error_message();
                    
                    if ($error) {
                        if (ob_get_level()) {
                            ob_end_clean(); // Just in case we are in an OB
                        }
                        echo "<!-- BMLTPlugin ERROR (display_changes)! Error during get_meeting_changes Call! ".htmlspecialchars($error)." -->";
                    } else {
                        $the_new_content = '<div class="bmlt_change_record_div">';
                        foreach ($changes as $change) {
                            $the_new_content .= $this->setup_one_change($change, $single_uri);
                        }
                        
                        $the_new_content .= '</div>';
                        
                        $in_content = self::replace_shortcode($in_content, 'bmlt_changes', $the_new_content);
                    }
                }
            }
        }
        return $in_content;
    }

    /************************************************************************************//**
    *   \brief Returns the XHTML for one single change record.                              *
    *                                                                                       *
    *   \returns A string. The DOCTYPE to be displayed.                                     *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function setup_one_change(
        $in_change_array,       ///< One change record
        $in_single_uri = null   ///< If there was a specific single meeting URI, we pass it in here.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '<dl class="bmlt_change_record_dl" id="bmlt_change_dl_'.htmlspecialchars($in_change_array['change_type']).'_'.intval($in_change_array['date_int']).'_'.intval($in_change_array['meeting_id']).'">';
            $ret .= '<dt class="bmlt_change_record_dt bmlt_change_record_dt_date">'.self::process_text($this->my_current_language->local_change_label_date).'</dt>';
                $ret .= '<dd class="bmlt_change_record_dd bmlt_change_record_dd_date">'.date($this->my_current_language->local_change_date_format, intval($in_change_array['date_int'])).'</dd>';
            
        if (isset($in_change_array['meeting_name']) && $in_change_array['meeting_name']) {
            $ret .= '<dt class="bmlt_change_record_dt bmlt_change_record_dt_name">'.self::process_text($this->my_current_language->local_change_label_meeting_name).'</dt>';
                $ret .= '<dd class="bmlt_change_record_dd bmlt_change_record_dd_name">';
                    
            if (isset($in_change_array['meeting_id']) && $in_change_array['meeting_id'] && isset($in_single_uri) && $in_single_uri) {
                $ret .= '<a href="'.htmlspecialchars($in_single_uri).$in_change_array['meeting_id'].'" rel="nofollow">';
                    $ret .= self::process_text(html_entity_decode($in_change_array['meeting_name']));
                $ret .= '</a>';
            } else {
                $ret .= self::process_text(html_entity_decode($in_change_array['meeting_name']));
            }
                    
                $ret .= '</dd>';
        }
        if (isset($in_change_array['service_body_name']) && $in_change_array['service_body_name']) {
            $ret .= '<dt class="bmlt_change_record_dt bmlt_change_record_dt_service_body_name">'.self::process_text($this->my_current_language->local_change_label_service_body_name).'</dt>';
                $ret .= '<dd class="bmlt_change_record_dd bmlt_change_record_dd_service_body_name">'.self::process_text(html_entity_decode($in_change_array['service_body_name'])).'</dd>';
        }
        if (isset($in_change_array['user_name']) && $in_change_array['user_name']) {
            $ret .= '<dt class="bmlt_change_record_dt bmlt_change_record_dt_service_body_admin_name">'.self::process_text($this->my_current_language->local_change_label_admin_name).'</dt>';
                $ret .= '<dd class="bmlt_change_record_dd bmlt_change_record_dd_service_body_admin_name">'.self::process_text(html_entity_decode($in_change_array['user_name'])).'</dd>';
        }
        if (isset($in_change_array['details']) && $in_change_array['details']) {
            $ret .= '<dt class="bmlt_change_record_dt bmlt_change_record_dt_description">'.self::process_text($this->my_current_language->local_change_label_description).'</dt>';
                $ret .= '<dd class="bmlt_change_record_dd bmlt_change_record_dd_description">'.self::process_text(html_entity_decode($in_change_array['details'])).'</dd>';
        }
        $ret .= '</dl>';
        
        return $ret;
    }

    /************************************************************************************//**
    *                              FAST MOBILE LOOKUP ROUTINES                              *
    *                                                                                       *
    *   Our mobile support is based on the fast mobile client. It has been adapted to fit   *
    *   into a WordPress environment.                                                       *
    ****************************************************************************************/

    /************************************************************************************//**
    *   \brief Checks the UA of the caller, to see if it should return XHTML Strict or WML. *
    *                                                                                       *
    *   NOTE: This is very, very basic. It is not meant to be a studly check, like WURFL.   *
    *                                                                                       *
    *   \returns A string. The DOCTYPE to be displayed.                                     *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public static function BMLTPlugin_select_doctype(  $in_http_vars   ///< The query variables
                                            )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '';
        
        function isDeviceWML1($in_http_vars)
        {
            return BMLTPlugin::mobile_sniff_ua($in_http_vars) == 'wml';
        }
    
        function isDeviceWML2($in_http_vars)
        {
            return BMLTPlugin::mobile_sniff_ua($in_http_vars) == 'xhtml_mp';
        }
            
        function isMobileDevice($in_http_vars)
        {
            $language = BMLTPlugin::mobile_sniff_ua($in_http_vars);
            return ($language != 'xhtml') && ($language != 'smartphone');
        }
        
        // If we aren't deliberately forcing an emulation, we figure it out for ourselves.
        if (!isset($in_http_vars['WML'])) {
            if (isDeviceWML1($in_http_vars)) {
                $in_http_vars['WML'] = 1;
            } elseif (isDeviceWML2($in_http_vars)) {
                $in_http_vars['WML'] = 2;
            } elseif (isMobileDevice($in_http_vars)) {
                $in_http_vars['WML'] = 1;
            }
        }
        
        // We may specify a mobile XHTML (WML 2) manually.
        if (isset($in_http_vars['WML'])) {
            if ($in_http_vars['WML'] == 2) {    // Use the XHTML MP header
                $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">';
            } else // Default is WAP
                {
                $ret = '<'; // This is because some servers are dumb enough to interpret the embedded prolog as PHP delimiters.
                $ret .= '?xml version="1.0"?';
                $ret .= '><!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN" "http://www.wapforum.org/DTD/wml_1.1.xml">';
            }
        } else {
            // We return a fully-qualified XHTML 1.0 Strict page.
            $ret = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        }
        
        if (!isset($in_http_vars['WML']) || ($in_http_vars['WML'] != 1)) {
            $ret .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"';
            if (!isset($in_http_vars['WML'])) {
                $ret .= ' lang="en"';
            }
            $ret .= '>';
        } else {
            $ret .= '<wml>';
        }
        
        $ret .= '<head>';
    
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Output the necessary Javascript. This is only called for a "pure javascript" *
    *   do_search invocation (smartphone interactive map).                                  *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_fast_mobile_lookup_javascript_stuff()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
        
        $this->adapt_to_lang($options['lang']);
        $gKey = '';

        if (isset($options['google_api_key']) && ('' != $options['google_api_key']) && ('INVALID' != $options['google_api_key'])) {
            $gKey = $options['google_api_key'];
        }

        $ret = '';

        // Include the Google Maps API V3 files.
        $ret .= '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.$gKey.'&region='.strtoupper($options['region_bias']).'"></script>';
        
        // Declare the various globals and display strings. This is how we pass strings to the JavaScript, as opposed to the clunky way we do it in the root server.
        $ret .= '<script type="text/javascript">' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_cannot_determine_location = \''.$this->process_text($this->my_current_language->local_cannot_determine_location).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_no_meetings_found = \''.$this->process_text($this->my_current_language->local_mobile_fail_no_meetings).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_server_error = \''.$this->process_text($this->my_current_language->local_server_fail).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_address_lookup_fail = \''.$this->process_text($this->my_current_language->local_cant_find_address).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_map_link_text = \''.$this->process_text($this->my_current_language->local_map_link).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_weekdays = [';
        $ret .= "'".$this->process_text(join("','", $this->my_current_language->local_weekdays))."'";
        $ret .= '];' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_formats = \''.$this->process_text($this->my_current_language->local_formats).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_Noon = \''.$this->process_text($this->my_current_language->local_noon).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_Midnight = \''.$this->process_text($this->my_current_language->local_midnight).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_debug_mode = '.( defined('DEBUG_MODE') ? 'true' : 'false' ).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $h = null;
        $m = null;
        list ( $h, $m ) = explode(':', date("G:i", time() + ($options['time_offset'] * 60 * 60) - ($options['grace_time'] * 60)));
        $ret .= 'var c_g_hour = '.intval($h).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_min = '.intval($m).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_military_time = '.($options['military_time'] ? 'true' : 'false' ).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_Nouveau_start_week = '.((isset($options['startWeekday']) && $options['startWeekday']) ? $options['startWeekday'] : self::$default_startWeekday ).';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_prompt = \''.$this->process_text($this->my_current_language->local_mobile_distance).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_units_are_km = '.((strtolower($options['distance_units']) == 'km' ) ? 'true' : 'false').';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_distance_units = \''.((strtolower($options['distance_units']) == 'km' ) ? $this->process_text($this->my_current_language->local_mobile_kilometers) : $this->process_text($this->my_current_language->local_mobile_miles) ).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_BMLTPlugin_files_uri = \''.htmlspecialchars($this->get_ajax_mobile_base_uri()).'?\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_bmlt_settings_id='.intVal(((isset($this->my_http_vars['bmlt_settings_id']) && $this->my_http_vars['bmlt_settings_id']) ? $this->my_http_vars['bmlt_settings_id'] : '')) .';' . (defined('_DEBUG_MODE_') ? "\n" : '');

        $img_url = htmlspecialchars($this->get_plugin_path()."google_map_images");
        
        $ret .= "var c_g_BMLTPlugin_images = '$img_url';" . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= 'var c_g_googleURI = \'https://maps.google.com/maps/api/js?key='.$key.'&region='.strtoupper($options['region_bias']).'\';' . (defined('_DEBUG_MODE_') ? "\n" : '');
        $ret .= '</script>';
       
        $ret .= '<script src="'.htmlspecialchars($this->get_plugin_path()).'fast_mobile_lookup.js" type="text/javascript"></script>';

        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Output whatever header stuff is necessary for the available UA               *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_fast_mobile_lookup_header_stuff()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '';
        $url = $this->get_plugin_path();
            
        $ret .= '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';    // WML 1 only cares about the charset and cache.
        $ret .= '<meta http-equiv="Cache-Control" content="max-age=300"  />';               // Cache for 5 minutes.
        $ret .= '<meta http-equiv="Cache-Control" content="no-transform"  />';              // No Transforms.

        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {   // If full XHTML
            // Various meta tags we need.
            $ret .= '<meta http-equiv="Content-Script-Type" content="text/javascript" />';      // Set the types for inline styles and scripts.
            $ret .= '<meta http-equiv="Content-Style-Type" content="text/css" />';
            $ret .= '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />'; // Make sure iPhone screens stay humble.
            
            $url = $this->get_plugin_path();
            
            $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
            
            $this->adapt_to_lang($options['lang']);
            $url = htmlspecialchars($url.'themes/'.$options['theme'].'/');
            
            $ret .= '<link rel="stylesheet" media="all" href="'.$url.'fast_mobile_lookup.css" type="text/css" />'.(defined('_DEBUG_MODE_') ? "\n" : "");
            
            // If we have a shortcut icon, set it here.
            if (defined('_SHORTCUT_LOC_')) {
                $ret .= '<link rel="SHORTCUT ICON" href="'.$this->process_text('_SHORTCUT_LOC_').'" />';
            }
            
            // Set the appropriate page title.
            if (isset($this->my_http_vars['do_search'])) {
                $ret .= '<title>'.$this->process_text($this->my_current_language->local_mobile_results_page_title).'</title>';
            } else {
                $ret .= '<title>'.$this->process_text($this->my_current_language->local_mobile_results_page_title).'</title>';
            }
        }
        
        $ret .= '</head>';

        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Returns the XHTML/WML for the Map Search form. These are the three "fast     *
    *   lookup" links displayed at the top (Note display:none" in the style).               *
    *   This is to be revealed by JavaScript.                                               *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_draw_map_search_form()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret = '<div class="search_intro" id="hidden_until_js" style="display:none">';
            $ret .= '<h1 class="banner_h1">'.$this->process_text($this->my_current_language->local_GPS_banner).'</h1>';
            $ret .= '<h2 class="banner_h2">'.$this->process_text($this->my_current_language->local_GPS_banner_subtext).'</h2>';
            $ret .= '<div class="link_one_line"><a rel="nofollow" accesskey="1" href="'.htmlspecialchars($this->get_ajax_mobile_base_uri()).'?BMLTPlugin_mobile&amp;do_search&amp;bmlt_settings_id='.htmlspecialchars($this->my_http_vars['bmlt_settings_id']).((isset($this->my_http_vars['base_url']) && $this->my_http_vars['base_url']) ? '&amp;base_url='.urlencode($this->my_http_vars['base_url']) : '').'">'.$this->process_text($this->my_current_language->local_search_all).'</a></div>';
            $ret .= '<div class="link_one_line"><a rel="nofollow" accesskey="2" href="'.htmlspecialchars($this->get_ajax_mobile_base_uri()).'?BMLTPlugin_mobile&amp;do_search&amp;qualifier=today&amp;bmlt_settings_id='.htmlspecialchars($this->my_http_vars['bmlt_settings_id']).((isset($this->my_http_vars['base_url']) && $this->my_http_vars['base_url']) ? '&amp;base_url='.urlencode($this->my_http_vars['base_url']) : '').'">'.$this->process_text($this->my_current_language->local_search_today).'</a></div>';
            $ret .= '<div class="link_one_line"><a rel="nofollow" accesskey="3" href="'.htmlspecialchars($this->get_ajax_mobile_base_uri()).'?BMLTPlugin_mobile&amp;do_search&amp;qualifier=tomorrow&amp;bmlt_settings_id='.htmlspecialchars($this->my_http_vars['bmlt_settings_id']).((isset($this->my_http_vars['base_url']) && $this->my_http_vars['base_url']) ? '&amp;base_url='.urlencode($this->my_http_vars['base_url']) : '').'">'.$this->process_text($this->my_current_language->local_search_tomorrow).'</a></div>';
            $ret .= '<hr class="meeting_divider_hr" />';
        $ret .= '</div>';
        
        return $ret;
    }
    
    /************************************************************************************//**
    *   \brief Returns the XHTML/WML for the Address Entry form                             *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_draw_address_search_form()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret = '<form class="address_input_form" method="get" action="'.htmlspecialchars($this->get_ajax_mobile_base_uri()).'"';
            if (!isset($this->my_http_vars['WML'])) {
                // This fills the form with a "seed" text (standard accessibility practice). We do it this way, so we don't clutter the form if no JavaScript is available.
                $ret .= ' onsubmit="if((document.getElementById(\'address_input\').value==\'';
                $ret .= $this->process_text($this->my_current_language->local_enter_an_address);
                $ret .= '\')||!document.getElementById(\'address_input\').value){alert(\''.$this->process_text($this->my_current_language->local_enter_address_alert).'\');document.getElementById(\'address_input\').focus();return false}else{if(document.getElementById(\'hidden_until_js\').style.display==\'block\'){document.getElementById(\'do_search\').value=\'1\'}}"';
            }
            $ret .= '>';
            $ret .= '<div class="search_address">';
            // The default, is we return a list. This is changed by JavaScript.
            $ret .= '<input type="hidden" name="BMLTPlugin_mobile" />';
            
            if (isset($this->my_http_vars['base_url']) && $this->my_http_vars['base_url']) {
                $ret .= '<input type="hidden" name="base_url" value="'.htmlspecialchars($this->my_http_vars['base_url']).'" />';
            }
                
            $ret .= '<input type="hidden" name="bmlt_settings_id" value="'.htmlspecialchars($this->my_http_vars['bmlt_settings_id']).'" />';
            $ret .= '<input type="hidden" name="do_search" id="do_search" value="the hard way" />';
            $ret .= '<h1 class="banner_h2">'.$this->process_text($this->my_current_language->local_search_address_single).'</h1>';
            if (!isset($this->my_http_vars['WML'])) {  // This is here to prevent WAI warnings.
                $ret .= '<label for="address_input" style="display:none">'.$this->process_text($this->my_current_language->local_enter_address_alert).'</label>';
            }
            if (isset($this->my_http_vars['WML'])) {
                $ret .= '<input type="hidden" name="WML" value="2" />';
            }
        } else {
            $ret = '<p>';   // WML rides the short bus.
        }
        
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret .= '<div class="address_top" id="address_input_line_wrapper">';
            $ret .= '<div class="link_one_line input_line_div" id="address_input_line_div">';
            if (!isset($this->my_http_vars['WML'])) {
                $ret .= '<div class="link_one_line" id="hidden_until_js2" style="display:none">';
                $ret .= '<input type="checkbox" id="force_list_checkbox"';
                $ret .= ' onchange="if(this.checked){document.getElementById ( \'hidden_until_js\' ).style.display = \'none\';document.getElementById(\'address_input\').focus();}else{document.getElementById ( \'hidden_until_js\' ).style.display = \'block\'}" /><label for="force_list_checkbox"';
                $ret .= '> '.$this->process_text($this->my_current_language->local_list_check).'</label>';
                $ret .= '</div>';
            }
            $ret .= '</div>';
        }
            
        $ret .= '<input type="text" name="address"';
        
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret .= ' id="address_input" class="address_input" size="64" value=""';
            if (!isset($this->my_http_vars['WML'])) {
                $ret .= ' onfocus="if(!this.value||(this.value==\''.$this->process_text($this->my_current_language->local_enter_an_address).'\'))this.value=\'\'"';
                $ret .= ' onkeydown="if(!this.value||(this.value==\''.$this->process_text($this->my_current_language->local_enter_an_address).'\'))this.value=\'\'"';
                $ret .= ' onblur="if(!this.value)this.value=\''.$this->process_text($this->my_current_language->local_enter_an_address).'\'"';
            }
        } else {
            $ret .= ' size="32" format="*m"';
        }
        
        $ret .= ' />';
        
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret .= '</div>';
        } else {
            $ret .= '</p>';
        }
        
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret .= '<div class="link_form_elements">';
            $ret .= '<div class="link_one_line">';
            $ret .= '<input checked="checked" id="search_all_days" type="radio" name="qualifier" value="" />';
            $ret .= '<label for="search_all_days"> '.$this->process_text($this->my_current_language->local_search_all_address).'</label>';
            $ret .= '</div>';
            $ret .= '<div class="link_one_line">';
            $ret .= '<input id="search_today" type="radio" name="qualifier" value="today" />';
            $ret .= '<label for="search_today"> '.$this->process_text($this->my_current_language->local_search_today).'</label>';
            $ret .= '</div>';
            $ret .= '<div class="link_one_line">';
            $ret .= '<input id="search_tomorrow" type="radio" name="qualifier" value="tomorrow" />';
            $ret .= '<label for="search_tomorrow"> '.$this->process_text($this->my_current_language->local_search_tomorrow).'</label>';
            $ret .= '</div>';
            $ret .= '</div>';
            $ret .= '<div class="link_one_line_submit">';
            if (!isset($this->my_http_vars['WML'])) {  // This silly thing is to prevent WAI warnings.
                $ret .= '<label for="submit_button" style="display:none">'.$this->process_text($this->my_current_language->local_search_submit_button).'</label>';
            }
            $ret .= '<input id="submit_button" type="submit" value="'.$this->process_text($this->my_current_language->local_search_submit_button).'"';
            if (!isset($this->my_http_vars['WML'])) {
                $ret .= ' onclick="if((document.getElementById(\'address_input\').value==\'';
                $ret .= $this->process_text($this->my_current_language->local_enter_an_address);
                $ret .= '\')||!document.getElementById(\'address_input\').value){alert(\''.$this->process_text($this->my_current_language->local_enter_address_alert).'\');document.getElementById(\'address_input\').focus();return false}else{if(document.getElementById(\'hidden_until_js\').style.display==\'block\'){document.getElementById(\'do_search\').value=\'1\'}}"';
            }
            $ret .= ' />';
            $ret .= '</div>';
            $ret .= '</div>';
            $ret .= '</form>';
        } else {
            $ret .= '<p>';
            $ret .= '<select name="qualifier" value="">';
            $ret .= '<option value="">'.$this->process_text($this->my_current_language->local_search_all_address).'</option>';
            $ret .= '<option value="today">'.$this->process_text($this->my_current_language->local_search_today).'</option>';
            $ret .= '<option value="tomorrow">'.$this->process_text($this->my_current_language->local_search_tomorrow).'</option>';
            $ret .= '</select>';
            $ret .= '</p>';
            $ret .= '<p>';
            $ret .= '<anchor>';
            $ret .= '<go href="'.htmlspecialchars($this->get_ajax_mobile_base_uri()).'" method="get">';
            $ret .= '<postfield name="address" value="$(address)"/>';
            $ret .= '<postfield name="qualifier" value="$(qualifier)"/>';
            $ret .= '<postfield name="do_search" value="the hard way" />';
            $ret .= '<postfield name="WML" value="1" />';
            $ret .= '<postfield name="BMLTPlugin_mobile" value="1" />';
            if (isset($this->my_http_vars['base_url']) && $this->my_http_vars['base_url']) {
                $ret .= '<postfield type="hidden" name="base_url" value="'.htmlspecialchars($this->my_http_vars['base_url']).'" />';
            }
            $ret .= '<postfield name="bmlt_settings_id" value="'.$this->my_http_vars['bmlt_settings_id'].'" />';
            $ret .= '</go>';
            $ret .= $this->process_text($this->my_current_language->local_search_submit_button);
            $ret .= '</anchor>';
            $ret .= '</p>';
        }
        
        return $ret;
    }

    /************************************************************************************//**
    *   \brief Renders one WML card                                                         *
    *                                                                                       *
    *   \returns A string. The WML 1.1 to be displayed.                                     *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_render_card(
        $ret,                   ///< The current XHTML tally (so we can count it).
        $index,                 ///< The page index of the meeting.
        $count,                 ///< The total number of meetings.
        $meeting                ///< The meeting data.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $ret .= '<card id="card_'.$index.'" title="'.htmlspecialchars($meeting['meeting_name']).'">';
        

        if ($count > 1) {
            $next_card = null;
            $prev_card = null;
            $myself = null;
            $vars = array();
            
            unset($_REQUEST['access_card']);
            
            foreach ($_REQUEST as $name => $val) {
                $text = urlencode($name).'='.urlencode($val);
                array_push($vars, $text);
            }
            
            $myself = htmlspecialchars($this->get_ajax_mobile_base_uri()).'?'.join('&amp;', $vars).'&amp;access_card=';
        
            if ($index < $count) {
                $next_card = $myself.strval($index + 1);
            }
            
            if ($index > 1) {
                $prev_card = $myself.strval($index - 1);
            }

            $ret .= '<p><table columns="3"><tr>';
            $ret .= '<td>';
            if ($prev_card) {
                $ret .= '<small><anchor>'.$this->process_text($this->my_current_language->local_prev_card).'<go href="'.$prev_card.'"/></anchor></small>';
            }
            $ret .= '</td><td>&nbsp;</td><td>';
            if ($next_card) {
                $ret .= '<small><anchor>'.$this->process_text($this->my_current_language->local_next_card).'<go href="'.$next_card.'"/></anchor></small>';
            }
            
            $ret .= '</td></tr></table></p>';
        }
    
        $ret .= '<p><big><strong>'.htmlspecialchars($meeting['meeting_name']).'</strong></big></p>';
        $ret .= '<p>'.$this->process_text($this->my_current_language->local_weekdays[$meeting['weekday_tinyint']]).' '.htmlspecialchars(date('g:i A', strtotime($meeting['start_time']))).'</p>';
        if ($meeting['location_text']) {
            $ret .= '<p><b>'.htmlspecialchars($meeting['location_text']).'</b></p>';
        }
        
        $ret .= '<p>';
        if ($meeting['location_street']) {
            $ret .= htmlspecialchars($meeting['location_street']);
        }
        
        if ($meeting['location_neighborhood']) {
            $ret .= ' ('.htmlspecialchars($meeting['location_neighborhood']).')';
        }
        $ret .= '</p>';
        
        if ($meeting['location_municipality']) {
            $ret .= '<p>'.htmlspecialchars($meeting['location_municipality']);
        
            if ($meeting['location_province']) {
                $ret .= ', '.htmlspecialchars($meeting['location_province']);
            }
            
            if ($meeting['location_postal_code_1']) {
                $ret .= ' '.htmlspecialchars($meeting['location_postal_code_1']);
            }
            $ret .= '</p>';
        }
        
        $distance = null;
        
        if ($meeting['distance_in_km']) {
            $distance = round(((strtolower($options['distance_units']) == 'km') ? $meeting['distance_in_km'] : $meeting['distance_in_miles']), 1);
            
            $distance = strval($distance).' '.((strtolower($options['distance_units']) == 'km' ) ? $this->process_text($this->my_current_language->local_mobile_kilometers) : $this->process_text($this->my_current_language->local_mobile_miles) );

            $ret .= '<p><b>'.$this->process_text($this->my_current_language->local_mobile_distance).':</b> '.htmlspecialchars($distance).'</p>';
        }
                                                        
        if ($meeting['location_info']) {
            $ret .= '<p>'.htmlspecialchars($meeting['location_info']).'</p>';
        }
                    
        if ($meeting['comments']) {
            $ret .= '<p>'.htmlspecialchars($meeting['comments']).'</p>';
        }
        
        $ret .= '<p><b>'.$this->process_text($this->my_current_language->local_formats).':</b> '.htmlspecialchars($meeting['formats']).'</p>';
        
        $ret .= '</card>';
        
        return $ret;
    }

    /************************************************************************************//**
    *   \brief Runs the lookup.                                                             *
    *                                                                                       *
    *   \returns A string. The XHTML to be displayed.                                       *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function BMLTPlugin_fast_mobile_lookup()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        /************************************************************************************//**
        *   \brief Sorting Callback                                                             *
        *                                                                                       *
        *   This will sort meetings by weekday, then by distance, so the first meeting of any   *
        *   given weekday is the closest one, etc.                                              *
        *                                                                                       *
        *   \returns -1 if a < b, 1, otherwise.                                                 *
        ****************************************************************************************/
        function mycmp(
            $in_a_meeting,  ///< These are meeting data arrays. The elements we'll be checking will be 'weekday_tinyint' and 'distance_in_XX'.
            $in_b_meeting
        ) {
            $ret = 0;
            
            if ($in_a_meeting['weekday_tinyint'] != $in_b_meeting['weekday_tinyint']) {
                $ret = ($in_a_meeting['weekday_tinyint'] < $in_b_meeting['weekday_tinyint']) ? -1 : 1;
            } else {
                $dist_a = intval(round(strtolower(($options['distance_units']) == 'mi') ? $in_a_meeting['distance_in_miles'] : $in_a_meeting['distance_in_km'], 1) * 10);
                $dist_b = intval(round((strtolower($options['distance_units']) == 'mi') ? $in_b_meeting['distance_in_miles'] : $in_b_meeting['distance_in_km'], 1) * 10);
    
                if ($dist_a != $dist_b) {
                    $ret = ($dist_a < $dist_b) ? -1 : 1;
                } else {
                    $time_a = preg_replace('|:|', '', $in_a_meeting['start_time']);
                    $time_b = preg_replace('|:|', '', $in_b_meeting['start_time']);
                    $ret = ($time_a < $time_b) ? -1 : 1;
                }
            }
            
            return $ret;
        }
        $ret = self::BMLTPlugin_select_doctype($this->my_http_vars);
        $ret .= $this->BMLTPlugin_fast_mobile_lookup_header_stuff();   // Add styles and/or JS, depending on the UA.
        $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
        $this->adapt_to_lang($options['lang']);
        
        // If we are running XHTML, then JavaScript works. Let's see if we can figure out where we are...
        // If the client can handle JavaScript, then the whole thing can be done with JS, and there's no need for the driver.
        // Also, if JS does not work, the form will ask us to do it "the hard way" (i.e. on the server).
        if ($this->my_http_vars['address'] && isset($this->my_http_vars['do_search']) && (($this->my_http_vars['do_search'] == 'the hard way') || (isset($this->my_http_vars['WML']) && ($this->my_http_vars['WML'] == 1)))) {
            if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {   // Regular XHTML requires a body element.
                $ret .= '<body>';
            }
            
            $this->my_driver->set_m_root_uri($options['root_server']);
            $error = $this->my_driver->get_m_error_message();
            
            if ($error) {
                if (ob_get_level()) {
                    ob_end_clean(); // Just in case we are in an OB
                }
                die('<h1>ERROR (BMLTPlugin_fast_mobile_lookup: '.htmlspecialchars($error).')</h1>');
            }
            
            $qualifier = strtolower(trim($this->my_http_vars['qualifier']));
            
            // Do the search.
            
            if ($this->my_http_vars['address']) {
                $this->my_driver->set_current_transaction_parameter('SearchString', $this->my_http_vars['address']);
                $error_message = $this->my_driver->get_m_error_message();
                if ($error_message) {
                    $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                } else {
                    $this->my_driver->set_current_transaction_parameter('StringSearchIsAnAddress', true);
                    $error_message = $this->my_driver->get_m_error_message();
                    if ($error_message) {
                        $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                    } else {
                        if ($qualifier) {
                            $weekdays = '';
                            $h = 0;
                            $m = 0;
                            $time = time() + ($options['time_offset'] * 60 * 60);
                            $today = intval(date("w", $time)) + 1;
                            // We set the current time, minus the grace time. This allows us to be running late, yet still have the meeting listed.
                            list ( $h, $m ) = explode(':', date("G:i", time() - ($options['grace_period'] * 60)));
                            if ($qualifier == 'today') {
                                $weekdays = strval($today);
                            } else {
                                $weekdays = strval(($today < 7) ? $today + 1 : 1);
                            }
                            $this->my_driver->set_current_transaction_parameter('weekdays', array($weekdays));
                            $error_message = $this->my_driver->get_m_error_message();
                            if ($error_message) {
                                $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                            } else {
                                if ($h || $m) {
                                    $this->my_driver->set_current_transaction_parameter('StartsAfterH', $h);
                                    $error_message = $this->my_driver->get_m_error_message();
                                    if ($error_message) {
                                        $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                                    } else {
                                        $this->my_driver->set_current_transaction_parameter('StartsAfterM', $m);
                                        $error_message = $this->my_driver->get_m_error_message();
                                        if ($error_message) {
                                            $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                                        }
                                    }
                                }
                            }
                        }
                        
                        if ($error_message) {
                            $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                        } else {
                            $this->my_driver->set_current_transaction_parameter('SearchStringRadius', intval($options['default_geo_width']));
                            $error_message = $this->my_driver->get_m_error_message();
                            if ($error_message) {
                                $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                            } else // The search is set up. Throw the switch, Igor! ...yeth...mawther....
                                {
                                $search_result = $this->my_driver->meeting_search();

                                $error_message = $this->my_driver->get_m_error_message();
                                if ($error_message) {
                                    $ret .= $this->process_text($this->my_current_language->local_server_fail).' "'.htmlspecialchars($error_message).'"';
                                } elseif (isset($search_result) && is_array($search_result) && isset($search_result['meetings'])) {
                                    // Yes! We have valid search data!
                                    if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {   // Regular XHTML
                                        $ret .= '<div class="multi_meeting_div">';
                                    }
                                    
                                    $index = 1;
                                    $count = count($search_result['meetings']);
                                    usort($search_result['meetings'], 'mycmp');
                                    if (isset($_REQUEST['access_card']) && intval($_REQUEST['access_card'])) {
                                        $index = intval($_REQUEST['access_card']);
                                    }
                                        
                                    if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {   // Regular XHTML
                                        $index = 1;
                                        foreach ($search_result['meetings'] as $meeting) {
                                            $ret .= '<div class="single_meeting_div">';
                                            $ret .= '<h1 class="meeting_name_h2">'.htmlspecialchars($meeting['meeting_name']).'</h1>';
                                            $ret .= '<p class="time_day_p">'.$this->process_text($this->my_current_language->local_weekdays[$meeting['weekday_tinyint']]).' ';
                                            $time = explode(':', $meeting['start_time']);
                                            $am_pm = ' AM';
                                            $distance = null;
                                            
                                            if ($meeting['distance_in_km']) {
                                                $distance = round(((strtolower($options['distance_units']) == 'km') ? $meeting['distance_in_km'] : $meeting['distance_in_miles']), 1);
                                                
                                                $distance = strval($distance).' '.((strtolower($options['distance_units']) == 'km' ) ? $this->process_text($this->my_current_language->local_mobile_kilometers) : $this->process_text($this->my_current_language->local_mobile_miles) );
                                            }

                                            $time[0] = intval($time[0]);
                                            $time[1] = intval($time[1]);
                                            
                                            if (($time[0] == 23) && ($time[1] > 50)) {
                                                $ret .= $this->process_text($this->my_current_language->local_midnight);
                                            } elseif (($time[0] == 12) && ($time[1] == 0)) {
                                                $ret .= $this->process_text($this->my_current_language->local_noon);
                                            } else {
                                                if (($time[0] > 12) || (($time[0] == 12) && ($time[1] > 0))) {
                                                    $am_pm = ' PM';
                                                }
                                                
                                                if ($time[0] > 12) {
                                                    $time[0] -= 12;
                                                }
                                            
                                                if ($time[1] < 10) {
                                                    $time[1] = "0$time[1]";
                                                }
                                                
                                                $ret .= htmlspecialchars($time[0].':'.$time[1].$am_pm);
                                            }
                                            
                                            $ret .= '</p>';
                                            if ($meeting['location_text']) {
                                                $ret .= '<p class="locations_text_p">'.htmlspecialchars($meeting['location_text']).'</p>';
                                            }
                                            
                                            $ret .= '<p class="street_p">';
                                            if ($meeting['location_street']) {
                                                $ret .= htmlspecialchars($meeting['location_street']);
                                            }
                                            
                                            if ($meeting['location_neighborhood']) {
                                                $ret .= '<span class="neighborhood_span"> ('.htmlspecialchars($meeting['location_neighborhood']).')</span>';
                                            }
                                            $ret .= '</p>';
                                            
                                            if ($meeting['location_municipality']) {
                                                $ret .= '<p class="town_p">'.htmlspecialchars($meeting['location_municipality']);
                                            
                                                if ($meeting['location_province']) {
                                                    $ret .= '<span class="state_span">, '.htmlspecialchars($meeting['location_province']).'</span>';
                                                }
                                                
                                                if ($meeting['location_postal_code_1']) {
                                                    $ret .= '<span class="zip_span"> '.htmlspecialchars($meeting['location_postal_code_1']).'</span>';
                                                }
                                                $ret .= '</p>';
                                                if (!isset($this->my_http_vars['WML'])) {
                                                    $ret .= '<p id="maplink_'.intval($meeting['id_bigint']).'" style="display:none">';
                                                    $url = '';

                                                    $comma = false;
                                                    if ($meeting['meeting_name']) {
                                                        $url .= urlencode($meeting['meeting_name']);
                                                        $comma = true;
                                                    }
                                                        
                                                    if ($meeting['location_text']) {
                                                        $url .= ($comma ? ',+' : '').urlencode($meeting['location_text']);
                                                        $comma = true;
                                                    }
                                                    
                                                    if ($meeting['location_street']) {
                                                        $url .= ($comma ? ',+' : '').urlencode($meeting['location_street']);
                                                        $comma = true;
                                                    }
                                                    
                                                    if ($meeting['location_municipality']) {
                                                        $url .= ($comma ? ',+' : '').urlencode($meeting['location_municipality']);
                                                        $comma = true;
                                                    }
                                                        
                                                    if ($meeting['location_province']) {
                                                        $url .= ($comma ? ',+' : '').urlencode($meeting['location_province']);
                                                    }
                                                    
                                                    $gKey = '';
        
                                                    if (isset($options['google_api_key']) && ('' != $options['google_api_key']) && ('INVALID' != $options['google_api_key'])) {
                                                        $gKey = $options['google_api_key'];
                                                    }

                                                    $url = 'https://maps.google.com/maps?key='.$gKey.'&region='.strtoupper($options['region_bias']).'&q='.urlencode($meeting['latitude']).','.urlencode($meeting['longitude']) . '+(%22'.str_replace("%28", '-', str_replace("%29", '-', $url)).'%22)';
                                                    $url .= '&ll='.urlencode($meeting['latitude']).','.urlencode($meeting['longitude']);
                                                    $ret .= '<a rel="external nofollow" accesskey="'.$index.'" href="'.htmlspecialchars($url).'" title="'.htmlspecialchars($meeting['meeting_name']).'">'.$this->process_text($this->my_current_language->local_map_link).'</a>';
                                                    $ret .= '<script type="text/javascript">document.getElementById(\'maplink_'.intval($meeting['id_bigint']).'\').style.display=\'block\';var c_BMLTPlugin_settings_id = '.htmlspecialchars($this->my_http_vars['bmlt_settings_id']).';</script>';

                                                    $ret .= '</p>';
                                                }
                                            }
                                                        
                                            if ($meeting['location_info']) {
                                                $ret .= '<p class="location_info_p">'.htmlspecialchars($meeting['location_info']).'</p>';
                                            }
                                                        
                                            if ($meeting['comments']) {
                                                $ret .= '<p class="comments_p">'.htmlspecialchars($meeting['comments']).'</p>';
                                            }
                                            
                                            if ($distance) {
                                                $ret .= '<p class="distance_p"><strong>'.$this->process_text($this->my_current_language->local_mobile_distance).':</strong> '.htmlspecialchars($distance).'</p>';
                                            }
                                                
                                            $ret .= '<p class="formats_p"><strong>'.$this->process_text($this->my_current_language->local_formats).':</strong> '.htmlspecialchars($meeting['formats']).'</p>';
                                            $ret .= '</div>';
                                            if ($index++ < $count) {
                                                if (!isset($this->my_http_vars['WML'])) {
                                                    $ret .= '<hr class="meeting_divider_hr" />';
                                                } else {
                                                    $ret .= '<hr />';
                                                }
                                            }
                                        }
                                    } else // WML 1 (yuch) We do this, because we need to limit the size of the pages to fit simple phones.
                                        {
                                        $meetings = $search_result['meetings'];
                                        $indexed_array = array_values($meetings);
                                        $ret = $this->BMLTPlugin_render_card($ret, $index, $count, $indexed_array[$index - 1], false);
                                    }
                                    
                                    if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {   // Regular XHTML
                                        $ret .= '</div>';
                                    }
                                } else {
                                    $ret .= '<h1 class="failed_search_h1';
                                    if (isset($this->my_http_vars['WML']) && $this->my_http_vars['WML']) {   // We use a normally-positioned element in WML.
                                        $ret .= '_wml';
                                    }
                                    $ret .= '">'.$this->process_text($this->my_current_language->local_mobile_fail_no_meetings).'</h1>';
                                }
                            }
                        }
                    }
                }
            } else {
                $ret .= '<h1 class="failed_search_h1">'.$this->process_text($this->my_current_language->local_enter_address_alert).'</h1>';
            }
        } elseif (isset($this->my_http_vars['do_search']) && !((($this->my_http_vars['do_search'] == 'the hard way') || (isset($this->my_http_vars['WML']) && ($this->my_http_vars['WML'] == 1))))) {
            $ret .= '<body id="search_results_body"';
            if (!isset($this->my_http_vars['WML'])) {
                $ret .= ' onload="WhereAmI (\''.htmlspecialchars(strtolower(trim($this->my_http_vars['qualifier']))).'\',\''.htmlspecialchars(trim($this->my_http_vars['address'])).'\')"';
            }
            $ret .= '>';

            $ret .= $this->BMLTPlugin_fast_mobile_lookup_javascript_stuff();

            $ret .= '<div id="location_finder" class="results_map_div">';
            
            $url = $this->get_plugin_path();
            
            $throbber_loc .= htmlspecialchars($url.'themes/'.$options['theme'].'/images/Throbber.gif');
            
            $ret .= '<div class="throbber_div"><img id="throbber" src="'.htmlspecialchars($throbber_loc).'" alt="AJAX Throbber" /></div>';
            $ret .= '</div>';
        } else {
            if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
                $ret .= '<body id="search_form_body"';
                if (!isset($this->my_http_vars['WML'])) {
                    $ret .= ' onload="if( (typeof ( navigator ) == \'object\' &amp;&amp; typeof ( navigator.geolocation ) == \'object\') || (window.blackberry &amp;&amp; blackberry.location.GPSSupported) || (typeof ( google ) == \'object\' &amp;&amp; typeof ( google.gears ) == \'object\') ){document.getElementById ( \'hidden_until_js\' ).style.display = \'block\';document.getElementById ( \'hidden_until_js2\' ).style.display = \'block\';};document.getElementById(\'address_input\').value=\''.$this->process_text($this->my_current_language->local_enter_an_address).'\'"';
                }
                $ret .= '>';
                $ret .= '<div class="search_div"';
                if (!isset($this->my_http_vars['WML'])) {
                    $ret .= ' cellpadding="0" cellspacing="0" border="0"';
                }
                $ret .= '>';
                $ret .= '<div class="GPS_lookup_row_div"><div>';
                if (!isset($this->my_http_vars['WML'])) {
                    $ret .= $this->BMLTPlugin_draw_map_search_form();
                }
                $ret .= '</div></div>';
                $ret .= '<div><div>';
                $ret .= $this->BMLTPlugin_draw_address_search_form();
                $ret .= '</div></div></div>';
            } else {
                $ret .= '<card title="'.$this->process_text($this->my_current_language->local_mobile_results_form_title).'">';
                $ret .= $this->BMLTPlugin_draw_address_search_form();
                $ret .= '</card>';
            }
        }
        
        if (!isset($this->my_http_vars['WML']) || ($this->my_http_vars['WML'] != 1)) {
            $ret .= '</body>';  // Wrap up the page.
            $ret .= '</html>';
            if (isset($this->my_http_vars['WML']) && ($this->my_http_vars['WML'] == 2)) {
                $ret = "<"."?xml version='1.0' encoding='UTF-8' ?".">".$ret;
                header('Content-type: application/xhtml+xml');
            }
        } else {
            $ret .= '</wml>';
            header('Content-type: text/vnd.wap.wml');
        }
        
        return $ret;
    }
    
    /************************************************************************************//**
    *                                    THE CONSTRUCTOR                                    *
    *                                                                                       *
    *   \brief Constructor. Enforces the SINGLETON, and sets up the callbacks.              *
    *                                                                                       *
    *   You will need to make sure that you call this with a parent::__construct() call.    *
    ****************************************************************************************/
    public function __construct()
    {
        if (!isset(self::$g_s_there_can_only_be_one) || (self::$g_s_there_can_only_be_one === null)) {
            self::$g_s_there_can_only_be_one = $this;
            
            $options = $this->getBMLTOptions_by_id($this->my_http_vars['bmlt_settings_id']);
            
            $this->my_http_vars = array_merge_recursive($_GET, $_POST);
                
            if (!(isset($this->my_http_vars['search_form']) && $this->my_http_vars['search_form'] )
                && !(isset($this->my_http_vars['do_search']) && $this->my_http_vars['do_search'] )
                && !(isset($this->my_http_vars['single_meeting_id']) && $this->my_http_vars['single_meeting_id'] )
                ) {
                $this->my_http_vars['search_form'] = true;
            }
            
            $this->my_http_vars['script_name'] = preg_replace('|(.*?)\?.*|', "$1", $_SERVER['REQUEST_URI']);
            $this->my_http_vars['satellite'] = $this->my_http_vars['script_name'];
            $this->my_http_vars['supports_ajax'] = 'yes';
            $this->my_http_vars['no_ajax_check'] = 'yes';

            // We need to start off by setting up our driver.
            $this->my_driver = new bmlt_satellite_controller;

            global $bmlt_localization;
            
            $this->adapt_to_lang($bmlt_localization);
            
            if ($this->my_driver instanceof bmlt_satellite_controller) {
                $this->set_callbacks(); // Set up the various callbacks and whatnot.
            } else {
                echo "<!-- BMLTPlugin ERROR (__construct)! Can't Instantiate the Satellite Driver! Please reinstall the plugin! -->";
            }
        } else {
            echo "<!-- BMLTPlugin Warning: __construct() called multiple times! -->";
        }
    }
    
    /************************************************************************************//**
    *                            THE CMS-SPECIFIC FUNCTIONS                                 *
    *                                                                                       *
    * These may be overridden by the subclasses. Some are required to be overridden.        *
    ****************************************************************************************/
    
    /************************************************************************************//**
    *   \brief This function fetches the settings ID for a page (if there is one).          *
    *                                                                                       *
    *   If $in_check_mobile is set to true, then ONLY a check for mobile support will be    *
    *   made, and no other shortcodes will be checked.                                      *
    *                                                                                       *
    *   \returns a mixed type, with the settings ID.                                        *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function cms_get_page_settings_id(
        $in_content,               ///< Required (for the base version) content to check.
        $in_check_mobile = false   ///< True if this includes a check for mobile. Default is false.
    ) {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        $my_option_id = null;
        
        if ($in_content) {  // The default version requires content.
            // We only return a mobile ID if we have the shortcode, we're asked for it, we're not already handling mobile, and we have a mobile UA.
            if ($in_check_mobile && !isset($this->my_http_vars['BMLTPlugin_mobile']) && (self::mobile_sniff_ua($this->my_http_vars) != 'xhtml') && ($params = self::get_shortcode($in_content, 'bmlt_mobile'))) {
                if ($params === true) { // If no mobile settings number was provided, we use the default.
                    $options = $this->getBMLTOptions(1);
                    $my_option_id = strval($options['id']);
                } else {
                    $my_option_id = $params;
                }
            } elseif (!$in_check_mobile) { // A mobile check ignores the rest.
                if (($params = self::get_shortcode($in_content, 'bmlt_table')) || ($params = self::get_shortcode($in_content, 'bmlt_simple')) || ($params = self::get_shortcode($in_content, 'bmlt_changes'))) {
                    $param_array = explode('##-##', $params);
                    
                    if (is_array($param_array) && (count($param_array) > 1)) {
                        $my_option_id = $param_array[0];
                    }
                }
        
                if ($params = self::get_shortcode($in_content, 'bmlt')) {
                    $my_option_id = ( $params !== true ) ? $params : $my_option_id;
                }
        
                if ($params = self::get_shortcode($in_content, 'bmlt_map')) {
                    $my_option_id = ( $params !== true ) ? $params : $my_option_id;
                }
            }
        }
        
        return $my_option_id;
    }
    
    /************************************************************************************//**
    *   \brief  Process the given string.                                                   *
    *           NOTE: A good start has been given, but there's a very good chance this will *
    *           need to be overridden.                                                      *
    *                                                                                       *
    *   This allows easier translation of displayed strings. All strings displayed by the   *
    *   plugin should go through this function.                                             *
    *                                                                                       *
    *   \returns a string, processed by the CMS.                                            *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function process_text(   $in_string  ///< The string to be processed.
                                    )
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        return htmlspecialchars($in_string);
    }
    
    /************************************************************************************//**
    *   \brief  Return an HTTP path to the AJAX callback target.                            *
    *           NOTE: A good start has been given, but there's a very good chance this will *
    *           need to be overridden.                                                      *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function get_ajax_base_uri()
    {
        // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
        // We try to account for SSL and unusual TCP ports.
        $port = null;
        $https = false;
        $from_proxy = array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER);
        if ($from_proxy) {
            // If the port is specified in the header, use it. If not, default to 80
            // for http and 443 for https. We can't trust what's in $_SERVER['SERVER_PORT']
            // because something in front of the server is fielding the request.
            $https = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
            if (array_key_exists("HTTP_X_FORWARDED_PORT", $_SERVER)) {
                $port = intval($_SERVER['HTTP_X_FORWARDED_PORT']);
            } elseif ($https) {
                $port = 443;
            } else {
                $port = 80;
            }
        } else {
            $port = $_SERVER['SERVER_PORT'];
            // IIS puts "off" in the HTTPS field, so we need to test for that.
            $https = (!empty($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] !== 'off') || ($port == 443)));
        }

        // This implements an "emergency" override" of the standard port system.
        // In some servers, a misconfiguration could report an improper port for HTTPS.
        // In this case, the server admin could define BMLT_HTTPS_PORT to be the integer TCP port to use for HTTPS.
        // If specified, it would be used instead of whatever port is being reported by the server.
        // Example:
        // define ( 'BMLT_HTTPS_PORT', 443 );
        // in the wp-config file.
        $port = ($https && defined(BMLT_HTTPS_PORT) && BMLT_HTTPS_PORT) ? BMLT_HTTPS_PORT : $port;

        $server_path = $_SERVER['SERVER_NAME'];
        $my_path = $_SERVER['PHP_SELF'];
        $server_path .= trim((($https && ($port != 443)) || (!$https && ($port != 80))) ? ':'.$port : '', '/');
        $server_path = 'http'.($https ? 's' : '').'://'.$server_path.$my_path;
        return $server_path;
    }

    /************************************************************************************//**
    *   \brief  Sets up the admin and handler callbacks. Override this for your CMS.        *
    *           NOTE: This may be ignored, but most CMSes have a system of callbacks, and   *
    *           this function is a good place to establish those callbacks.                 *
    *           It is called at the end of the base class constructor.                      *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function set_callbacks()
    {
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief Return an HTTP path to the AJAX callback target for the mobile handler.      *
    *                                                                                       *
    *   \returns a string, containing the path. Defaults to the base URI.                   *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function get_ajax_mobile_base_uri()
    {
        return $this->get_ajax_base_uri();
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief Return an HTTP path to the AJAX callback target.                             *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function get_admin_ajax_base_uri()
    {
        return $this->get_ajax_base_uri();
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief Return an HTTP path to the basic admin form submit (action) URI              *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function get_admin_form_uri()
    {
        return $this->get_ajax_base_uri();
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief  Return an HTTP path to the plugin directory.                                *
    *           NOTE: A good start has been given, but there's a very good chance this will *
    *           need to be overridden.                                                      *
    *                                                                                       *
    *   \returns a string, containing the path.                                             *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    protected function get_plugin_path()
    {
        return dirname($this->get_ajax_base_uri());
    }
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *                      ABSTRACT (REQUIRED OVERRIDE) FUNCTIONS                           *
    ****************************************************************************************/

    /************************************************************************************//**
    *   \brief This gets the admin options from the database (allows CMS abstraction).      *
    *                                                                                       *
    *   \returns an associative array, with the option settings.                            *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    abstract protected function cms_get_option($in_option_key); ///< The key for the option
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief This gets the admin options from the database (allows CMS abstraction).      *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    abstract protected function cms_set_option(
        $in_option_key,   ///< The name of the option
        $in_option_value  ///< the values to be set (associative array)
    );
    // phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    /************************************************************************************//**
    *   \brief Deletes a stored option (allows CMS abstraction).                            *
    ****************************************************************************************/
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    abstract protected function cms_delete_option($in_option_key); ///< The key for the option
}
// phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
/***********************************************************************/
/** \brief  This is an open-source JSON encoder that allows us to support
    older versions of PHP (before the <a href="http://us3.php.net/json_encode">json_encode()</a> function
    was implemented). It uses json_encode() if that function is available.

    This is from <a href="http://www.bin-co.com/php/scripts/array2json/">Bin-Co.com</a>.

    This crap needs to be included to be aboveboard and legal. You can still re-use the code, but
    you need to make sure that the comments below this are included:


    Copyright (c) 2004-2007, Binny V Abraham

    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification, are permitted provided
    that the following conditions are met:

    *   Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    *   Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer
        in the documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
    BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
    IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
    PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
    OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
    EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
function array2json(
    $arr    ///< An associative string, to be encoded as JSON.
) {
    if (function_exists('json_encode')) {
        return json_encode($arr); //Lastest versions of PHP already has this functionality.
    }
    
    $parts = array();
    $is_list = false;

    //Find out if the given array is a numerical array
    $keys = array_keys($arr);
    $max_length = count($arr)-1;
    if (($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
        $is_list = true;
        for ($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
            if ($i != $keys[$i]) { //A key fails at position check.
                $is_list = false; //It is an associative array.
                break;
            }
        }
    }

    foreach ($arr as $key => $value) {
        if (is_array($value)) { //Custom handling for arrays
            if ($is_list) {
                $parts[] = array2json($value); /* :RECURSION: */
            } else {
                $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
            }
        } else {
            $str = '';
            if (!$is_list) {
                $str = '"' . $key . '":';
            }

            //Custom handling for multiple data types
            if (is_numeric($value)) {
                $str .= $value; //Numbers
            } elseif ($value === false) {
                $str .= 'false'; //The booleans
            } elseif ($value === true) {
                $str .= 'true';
            } elseif (isset($value) && $value) {
                $str .= '"' . addslashes($value) . '"'; //All other things
            }
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?)

            $parts[] = $str;
        }
    }
    
    $json = implode(',', $parts);
    
    if ($is_list) {
        return '[' . $json . ']'; //Return numerical JSON
    } else {
        return '{' . $json . '}'; //Return associative JSON
    }
}

/************************************************************************************//**
*   \brief Very quick check for mobile client.                                          *
*   \returns a Boolean. TRUE, if the client is mobile.                                  *
****************************************************************************************/
function BMLTPlugin_weAreMobile($in_http_vars   ///< The HTTP query variables, as an associative array.
                                )
{
    $language = BMLTPlugin::mobile_sniff_ua($in_http_vars);
    return ($language == 'wml') || ($language == 'xhtml_mp') || ($language == 'smartphone');
}
