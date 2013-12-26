<?php

   header('Content-Type: text/html; charset=UTF-8');

   ini_set('display_errors', 'On');
   error_reporting(E_ALL);
   
   require_once ($_SERVER['DOCUMENT_ROOT'] . "/lite_e/config.php");
   require_once ($GLOBALS["controller"] . "/FileSystemSet.php");
   
   $SaveContainer = isset($_POST["SaveContainer"]) && $_POST["SaveContainer"] == "true";

   // Si se va a guardar el nuevo contenedor, se captura el buffer de salida
   // y se guarda la informaci�n para generar un nuevo archivo "index.html"
   if ($SaveContainer) {
      ob_start();
      mb_internal_encoding('UTF-8');
   }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
   <title>Generador de &iacute;ndices</title>
   <link href="css/estilo.css" rel="stylesheet" type="text/css" />
   <script language="javascript" src="javascript/jquery-1.9.1.js"></script>
   <script language="javascript" src="javascript/utils.js" ></script>
   <script language="javascript" type="text/javascript">
   
      SystemCloseButton =  <?php printf(isset($_POST["SystemCloseButton"]) ? $_POST["SystemCloseButton"] : "true"); ?>;
      // Controla la propiedad de visibilidad del la posición de navegación
      ShowUnitPosition 	= <?php printf(isset($_POST["ShowUnitPosition"]) ? $_POST["ShowUnitPosition"] : "true"); ?>;
      // Controla la propiedad de visibilidad de las flechas de navegación.
      ShowNavigationArrows= <?php printf(isset($_POST["ShowNavigationArrows"]) ? $_POST["ShowNavigationArrows"] : "true"); ?>;
      
      // Autoajuste de los botones.
      AdjustButtons		= <?php printf(isset($_POST["AdjustButtons"]) ? $_POST["AdjustButtons"] : "true"); ?>;
      // Activa los bordes redondos para botones
      RoundedCorners		= <?php printf(isset($_POST["RoundedCorners"]) ? $_POST["RoundedCorners"] : "false"); ?>;
      
      // Tamaño fijo de botones
      // Al establecer un valor (en pixeles) se desactiva el ajuste automático de botones.
      FixedButtonWidth	= <?php printf(isset($_POST["FixedButtonWidth"]) ? $_POST["FixedButtonWidth"] : "-1"); ?>;
      
      // Cambiar tamaño por omisión del contenedor de escenas.
      // El valor por omisión es -1. Los valores permitidos son enteros
      // mayores a 790 en el ancho y 520 en lo alto.
      ContentFrameWidth	= <?php printf(isset($_POST["ContentFrameWidth"]) ? $_POST["ContentFrameWidth"] : "790"); ?>;
      ContentFrameHeight  = <?php printf(isset($_POST["ContentFrameHeight"]) ? $_POST["ContentFrameHeight"] : "520"); ?>;
      
      
      // Título de la página
      PageTitle 			= <?php printf("'%s'", isset($_POST["PageTitle"]) ? $_POST["PageTitle"] : "Título de página"); ?>;
      // Título de la unidad de Descartes
      UnitTitle			= <?php printf("'%s'", isset($_POST["UnitTitle"]) ? $_POST["UnitTitle"] : "Título de unidad"); ?>;
<?php
			
   $checkboxes = isset($_POST["ScenesCheckboxGroup"]) ? (array) $_POST["ScenesCheckboxGroup"] : array();
   $postedUnitsNames = isset($_POST["UnitsNames"]) ? $_POST["UnitsNames"] : null;
   $unitsInfo = array();
   $zipFileName = "";
   
   if ($postedUnitsNames !== null) {
      $tmpUnitsNames = explode("|", $postedUnitsNames);
      foreach($tmpUnitsNames as $unitName) {
         if (strlen($unitName) > 0) {
            $unitInfo = explode ("%", $unitName);
            $unitsInfo[$unitInfo[0]] = $unitInfo[1];
         }
      }
   }
   
   $fileSystemSets = array();
   $sceneAssocFileSystemSet = array();
			
   foreach ($checkboxes as $value) {
   
      $values = explode ("|", $value);
      $fileSystemPath = trim ($values[0]);
      $sceneId = trim ($values[1]);
      
      $fileSystemSet = new FileSystemSet($fileSystemPath);
      $fileSystemSetId = $fileSystemSet->GetSetId();
      
      $keyExists = (isset($fileSystemSets[$fileSystemSetId]) ||
                     array_key_exists($fileSystemSetId, $fileSystemSets));
      
      if(!$keyExists) $fileSystemSets[$fileSystemSetId] = $fileSystemSet;
      
      $sceneAssocFileSystemSet[$fileSystemSetId][] = $sceneId;
   }

   // Para declarar las escenas y unidades, se recorre el arreglo de unidades declarado
   // en el bloque anterior y se exploran tadas las escenas asociadas
   foreach ($sceneAssocFileSystemSet as $key => $value) {
   
      $zipFileName = $zipFileName . $key . "=" . implode ("|", $value);
      
   
      // Número de escenas asociadas
      $length = count($value);
      // Se selecciona una unidad y se registran todos sus elementos (escenas)
      $fileSystemSet = $fileSystemSets[$key];
      $browsableEntries = $fileSystemSet->BrowsableEntries();
      
      // Se escribe el nombre de la nueva unidad y
      // el inicio de la declaración de los archivos
      printf ("\n\r\t\tdeclareNewUnit( scene = { Name: '%s', Files: [ ", $unitsInfo[$key]);
      foreach ($value as $browsableEntryId) {
         $length -= 1;
         
         // La escena actual, de no estar entre las entradas "navegables",
         // es la escena con el nombre de archivo "index.html"
         $currentEntry = (isset ($fileSystemSet->BrowsableEntries()[$browsableEntryId]) ||
                           array_key_exists($browsableEntryId, $fileSystemSet->BrowsableEntries())) ?
                           $fileSystemSet->GetBrowsableEntry($browsableEntryId) :
                           $fileSystemSet->GetIndexEntry();
                           
         // Se solicita la URL que corresponda según la acción deseada,
         // mostrar o publicar la selección
         $currentUrl = "";
         if ($SaveContainer) {
            $currentUrl = $currentEntry->EntryRelativeUrl($fileSystemSet->GetBaseDirectoryName());
         } else {
            $currentUrl = $currentEntry->EntryUrl();
         }
         // Se escribe la URL del recurso seleccionado
         printf ("\n\r\t\t\t'%s'", $currentUrl);
         if ($length != 0) printf (", ");
      }
      // Se cierra la declaración de la unidad
      printf ("]} );\r\n");
   }
			
?>

      $(function() { initializeContainer(); });

   </script>
    
</head>

<body>
   <div id="container">
      <div id="header"><span id="unitsTitle"></span><span id="unitPosition"></span></div>
      <div id="content"><iframe id="jsApplet"></iframe></div>
      <div id="footer">
         <div id="navigation">
            <div id="navigationButtons" ></div>
            <div id="tools">
               <a id="closeWindowButton" href="javascript:cerrar();void(0);">x</a>
               <a id="cprght" href="javascript:verCreditos();void(0);">c</a>
               <a id="info" href="javascript:verDocumentacion();void(0);">i</a>
            </div>
            <div id="navigationArrows" >
               <div id="back" class="arrowButton" onClick="prevScene();void(0);"></div>
               <div id="forward" class="arrowButton" onClick="nextScene();void(0);"></div>
            </div>
         </div>
      </div>
   </div>

</body>
</html>

<?php

   if ($SaveContainer) {
   
      // Se crea una carpeta con nombre arbitrario en el sistema de archivos
      
      $hashFolder = substr(md5($zipFileName), 5, 8);
      $newUnitPath = $GLOBALS["output"] . "/" . $hashFolder;
      
      if (file_exists($newUnitPath)) {
         @rmdir($newUnitPath);
      }
      
      @mkdir($newUnitPath, 0777);
      
      $templateFileSystemSet = new FileSystemSet($GLOBALS["template"]);
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
        