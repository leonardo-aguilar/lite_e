<?php

   ini_set('display_errors', 'On');
   error_reporting(E_ALL);

   require_once ($_SERVER['DOCUMENT_ROOT'] . "/lite_e/config.php");
   require_once ($GLOBALS["controller"] . "/FileSystemSet.php");

   $SaveContainer = isset($_POST["SaveContainer"]) && $_POST["SaveContainer"] == "true";

   // Si se va a guardar el nuevo contenedor, se captura el buffer de salida
   // y se guarda la información para generar un nuevo archivo "index.html"
   if ($SaveContainer) {
      ob_start();
      mb_internal_encoding('UTF-8');
   }

   $layoutTemplate_1   = "\t\t<tr>\r" .
                           "\t\t\t<td width=\"25%\" align=\"center\">\r" .
                           "\t\t\t\t<a href=\"URL_REPLACEMENT\" target=\"_blank\"><small>CODE_REPLACEMENT</small><br/>\r" .
                           "\t\t\t\t\t<img src=\"ICON_REPLACEMENT\" width=\"180\" height=\"120\" " .
                              "id=\"RollOverImage_CODE_REPLACEMENT\" onMouseOut=\"cancelRollover()\" " .
                              "onMouseOver=\"startRollover('RollOverImage_CODE_REPLACEMENT', ICONS_REPLACEMENT)\" /><br/>\r" .
                           "\t\t\t\t\t<img src=\"images/trlogo.png\" />\r" .
                           "\t\t\t\t</a>\r" .
                           "\t\t\t</td>\r" .
                           "\t\t\t<td width=\"5%\" align=\"left\"></td>\r" .
                           "\t\t\t<td width=\"70%\" align=\"left\">\r" .
                           "\t\t\t\t<a href=\"URL_REPLACEMENT\" target=\"_blank\"><big>NAME_REPLACEMENT</big></a><br/><br/>\r" .
                           "\t\t\t\tDESCRIPITION_REPLACEMENT<br/><br/>\r" .
                           "\t\t\t\t<strong>Área:</strong>AREA_REPLACEMENT<br/>\r" .
                           "\t\t\t\t<strong>Nivel:</strong>LEVEL_REPLACEMENT<br/>\r" .
                           "\t\t\t\t<strong>Proyecto:</strong>PROJECT_REPLACEMENT<br/>\r" .
                           "\t\t\t</td>\r" .
                           "\t\t</tr>\r" .
                           "\t\t<tr><td><br/></td></tr>\r\r";

   $patterns = array( "/URL_REPLACEMENT/", "/CODE_REPLACEMENT/", "/ICON_REPLACEMENT/",
                        "/NAME_REPLACEMENT/", "/DESCRIPITION_REPLACEMENT/", "/AREA_REPLACEMENT/",
                        "/LEVEL_REPLACEMENT/", "/PROJECT_REPLACEMENT/", "/ICONS_REPLACEMENT/");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title><?php printf("%s", isset($_POST["IndexPageTitle"]) ? $_POST["IndexPageTitle"] : "Título de página"); ?></title>
	<link href="css/descmovil.css" rel="stylesheet" type="text/css" />
	<script language="javascript" src="javascript/jquery-1.9.1.js"></script>
	<script language="javascript">
	   var RolloverInterval = 1000;
	   var RolloverImageInterval = null;
	   var RolloverImageSrcSet = null;
	   var CurrentImageSrcIndex = 0;
	   var CurrentRolloverImageId = null;

	   function log (mensaje) {

	   }

	   function startRollover (imageId, imageSet) {
	      if (RolloverImageSrcSet == null) {
            CurrentRolloverImageId = imageId;
            RolloverImageSrcSet = imageSet;
            CurrentImageSrcIndex = 0;

            if (RolloverImageSrcSet.length > 1)
               RolloverImageInterval = setInterval (function() { changeImage(); }, RolloverInterval);
         }
	   }

	   function cancelRollover () {
	      RolloverImageTimeout = null;
	      RolloverImageSrcSet = null;
	      CurrentRolloverImageId = null;

	      clearInterval(RolloverImageInterval);
	      RolloverImageInterval = null;
	   }

	   function changeImage () {
         $("#" + CurrentRolloverImageId).prop("src", RolloverImageSrcSet[CurrentImageSrcIndex]);
         CurrentImageSrcIndex = CurrentImageSrcIndex < (RolloverImageSrcSet.length - 1) ?
                                 (CurrentImageSrcIndex + 1) : 0;
	   }

	</script>
</head>
<body>
   <table class="titulo">
      <tr>
         <td width="15%"><a href="http://www.unam.mx/"><img src="images/dgee.gif" class="tit_thmb"></a></td>
         <td width="7%"><a href="http://www.conacyt.gob.mx"><img src="images/conacyt.jpg" class="tit_thmb" ></a></td>
         <td width="50%" align="center">
            <nobr><b><?php printf("%s", isset($_POST["IndexUnitTitle"]) ? $_POST["IndexUnitTitle"] : "Título de unidad"); ?></b></nobr><br>
         </td>
         <td width="10%"><a href="http://www.unadmexico.mx/"><img src="images/unadm.png" class="tit_thmb"></a></td>
         <td width="8%"><a href="http://lite.org.mx/"><img src="images/lite.png" class="tit_thmb" width="133" height="72"></a></td>
      </tr>
   </table>
   <br/>

   <table width="100%" class="pie">
      <tr>
         <td><ul>Para visualizar estas unidades interactivas es necesario usar un navegador de &uacute;ltima generaci&oacute;n<br/>
               que implemente el Canvas de HTML5, como por ejemplo: Google Chrome, Mozila Firefox o Safari</ul>
         </td>
      </tr>
   </table>
   <br/>

   <table width="100%" height="120" class="bloque">
      <tr><td><br/><br/></td></tr>

<?php

   $zipFileName = " ";
   $checkboxes = isset($_POST["UnitsCheckboxGroup"]) ? (array) $_POST["UnitsCheckboxGroup"] : array();
   $fileSystemSets = array();

   foreach ($checkboxes as $value) {

      $values = explode ("|", $value);
      $fileSystemPath = trim ($values[0]);
      $UnitId = trim ($values[1]);

      $fileSystemSet = new FileSystemSet($fileSystemPath);
      $fileSystemSetId = $fileSystemSet->GetSetId();
      $fileSystemSets[$fileSystemSetId] = $fileSystemSet;

      $zipFileName = $zipFileName . "|" . $fileSystemSetId;

      if ($SaveContainer) {
         $currentUrl = $fileSystemSet->IndexEntry()->EntryRelativeUrl($fileSystemSet->BaseDirectoryName());
      } else {
         $currentUrl = $fileSystemSet->IndexEntry()->EntryUrl();
      }

      $fileStringTemplate = $layoutTemplate_1;


      $thumbnailsArray = explode(",", $fileSystemSet->Thumbnails());

      $iterator = count($thumbnailsArray);
      $lastThumbnail = "";
      $thusmbnailsString = "[ ";

      foreach ($thumbnailsArray as $thumbnail) {
         $iterator--;
         $comaString = $iterator == 0 ? "" : ", ";

         $url = trim($thumbnail);

         if (!$SaveContainer) {
            $tmp = str_ireplace($fileSystemSet->BaseDirectoryName(), "", $fileSystemSet->BaseDirectory()) . $url;
            $url = Utils::GetFileUrl ($GLOBALS["path_rootdir"], $GLOBALS["wwwroot"], $tmp);
         }

         $lastThumbnail = $iterator == 0 ? $url : $lastThumbnail;
         $thusmbnailsString .= "'" . $url . "'" . $comaString;
		}

		$thusmbnailsString .= "]";

      $sustitutions = array($currentUrl, $fileSystemSet->GetSetId(), $lastThumbnail,
                              $fileSystemSet->Title(), $fileSystemSet->Description(), $fileSystemSet->Area(),
                              $fileSystemSet->Level(), $fileSystemSet->Project(), $thusmbnailsString);

      $loString = preg_replace($patterns, $sustitutions, $fileStringTemplate);

      printf("%s", $loString);

   }


?>

      <tr><td><br/><br/></td></tr>
   </table>
   <br/>

   <table width="100%" class="pie">
      <tr>
         <td><ul>Para visualizar estas unidades interactivas es necesario usar un navegador de &uacute;ltima generaci&oacute;n<br/>
               que implemente el Canvas de HTML5, como por ejemplo: Google Chrome, Mozila Firefox o Safari</ul>
         </td>
      </tr>
   </table>
   <br/>

</body>
</html>

<?php

   if ($SaveContainer) {

      // Se crea una carpeta con nombre arbitrario en el sistema de archivos
      $hashFolder = "index_" . substr(md5($zipFileName), 5, 8);
      $newUnitPath = $GLOBALS["output"] . "/" . $hashFolder;

      if (file_exists($newUnitPath)) {
         @rmdir($newUnitPath);
      }

      @mkdir($newUnitPath, 0777);

      $templateFileSystemSet = new FileSystemSet($GLOBALS["templates"] . "/indexes");
      $templateFileSystemSet->Duplicate($newUnitPath, true);

      foreach ($fileSystemSets as $fileSystemSet)
         $fileSystemSet->Duplicate($newUnitPath, false);

      // Se obtiene el contenido del buffer de escritura
      // y se guarda el achivo "index.html" generado
      $page = ob_get_contents();

      $fp = fopen($newUnitPath . "/index.html", "w");
      fwrite($fp, $page);
      fclose($fp);

      Utils::CompressFolder($newUnitPath, $newUnitPath . ".zip");
      Utils::RemoveDirAndContents($newUnitPath);

      $zipUrl = Utils::GetFileUrl ($GLOBALS["path_rootdir"],
                  $GLOBALS["wwwroot"], $newUnitPath . ".zip");

      header("Location: " . $zipUrl );
   }

?>
