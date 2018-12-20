<?php
/***********************************************************************/
/**     \file   loadTable_StyleFiles.php

    \brief  This file reads in a CSS file, and optimizes it by stripping
    out comments and whitespace. It will also try to GZ compress the output
    using the standard OB functions. It can make a HUGE difference in size.

    The way it works is that you call it from the <link/> element (don't
    specify a "type" attribute), and give it a GET parameter of filename,
    which will equal the file path to the CSS file.

    For security purposes, the file must always be a ".css" file, and you can't
    go out of the directory in which this file is located.

    This file is part of the BMLT Common Satellite Base Class Project. The project GitHub
    page is available here: https://github.com/MAGSHARE/BMLT-Common-CMS-Plugin-Class

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

function loadTable_StyleFile($in_theme_dirname)
{
    $pathname = dirname(__FILE__)."$in_theme_dirname/table_styles.css";
    $opt = file_get_contents($pathname);
    $opt = preg_replace("|\/\*.*?\*\/|s", "", $opt);
    $opt = preg_replace("|\s+|s", " ", $opt);
    return $opt;
}

$opt = loadTable_StyleFile("");

$dir_res = opendir(dirname(__FILE__).'/themes');

if ($dir_res) {
    while (false !== ( $dir_name = readdir($dir_res) )) {
        if (!preg_match('/^\./', $dir_name) && is_dir(dirname(__FILE__).'/themes/'.$dir_name) && file_exists(dirname(__FILE__).'/themes/'.$dir_name.'/table_styles.css')) {
            $theme = isset($_GET['theme']) ? $_GET['theme'] : '';
            
            if (!$theme || ($dir_name == $theme)) {
                $opt .= loadTable_StyleFile("/themes/$dir_name");
            }
        }
    }

    header("Content-type: text/css;charset=utf-8");

    $handler = null;

    if (zlib_get_coding_type() === false) {
        $handler = 'ob_gzhandler';
    }
        
    ob_start($handler);
    echo $opt;
    ob_end_flush();
}
