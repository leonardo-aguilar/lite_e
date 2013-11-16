<?php  // Config

unset($CFG);
global $CFG;

$CFG = new stdClass();

// Base de datos
$CFG->dbhost    		= "localhost";
$CFG->dbname    		= "lite_deliverables";
$CFG->dbuser    		= "lite";
$CFG->dbpass    		= "lite";
$CFG->dbport			= "3306";

// Ubicaciones
$CFG->wwwroot   		= "http://localhost/lite_e/";
$CFG->dataroot  		= "/Users/Leonardo/Documents/Trabajo/Freelance/LITE/2013_PreparacionDeEntregas/oas";
$CFG->dataroot			= realpath($CFG->dataroot);

$CFG->path_rootdir 		= dirname(dirname(__FILE__));
$CFG->path_data			= $CFG->path_rootdir . "/datalayer/";
$CFG->path_controller	= $CFG->path_rootdir . "/controller/";
$CFG->path_model		= $CFG->path_rootdir . "/model/";
$CFG->path_view			= $CFG->path_rootdir . "/view/";

// Debug
$CFG->debug 			= true;

?>
