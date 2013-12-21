<?php  // Config

unset ($CFG);
global $CFG;

$CFG = new stdClass();

// Base de datos
$GLOBALS["dbhost"]    			= "localhost";
$GLOBALS["dbname"]    			= "lite_deliverables";
$GLOBALS["dbuser"]    			= "lite";
$GLOBALS["dbpass"]    			= "lite";
$GLOBALS["dbport"]				= "3306";

// Ubicaciones
$GLOBALS["wwwroot"]   			= "http://" . $_SERVER['HTTP_HOST'] . "/lite_e";

$GLOBALS["path_rootdir"] 		= $_SERVER['DOCUMENT_ROOT'] . "/lite_e";
$GLOBALS["path_datalayer"]		= $GLOBALS["path_rootdir"] . "/datalayer";
$GLOBALS["repository"]			= $GLOBALS["path_rootdir"] . "/repositorio";
$GLOBALS["libdir"]				= $GLOBALS["path_rootdir"] . "/lib";
$GLOBALS["controller"]			= $GLOBALS["path_rootdir"] . "/controller";
$GLOBALS["model"]				= $GLOBALS["path_rootdir"] . "/entities";
$GLOBALS["view"]				= $GLOBALS["path_rootdir"] . "/view";
$GLOBALS["output"]				= $GLOBALS["path_rootdir"] . "/output";
$GLOBALS["template"]			= $GLOBALS["path_rootdir"] . "/template";

// Debug
$GLOBALS["debug"] 				= true;

$GLOBALS["system_slash"]        = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "\\" : "/";

// archivo: functions.php
function noCache() {
      header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
}

