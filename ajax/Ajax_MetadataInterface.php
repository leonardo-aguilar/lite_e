<?php

   header('Content-Type: text/html; charset=UTF-8');

   ini_set('display_errors', 'On');
   error_reporting(E_ALL);

   require_once ($_SERVER['DOCUMENT_ROOT'] . "/lite_e/config.php");
   require_once ($GLOBALS["controller"] . "/FileSystemSet.php");

   $baseDirectory = isset($_GET["loPath"]) ? $_GET["loPath"] : NULL;

   $baseDirectory = $baseDirectory === NULL ? (isset($_POST["loPath"]) ?
                        $_POST["loPath"] : NULL) : $baseDirectory;

   $fileSystemSet = new FileSystemSet ($baseDirectory);
   $loMetadata = $fileSystemSet->Metadata();


   if (isset($_POST["Action"]) && $_POST["Action"] == "Save") {

      $loMetadata->Project ($_POST["ProjectName"]);
      $loMetadata->Title ($_POST["ObjectTitle"]);
      $loMetadata->Description ($_POST["ObjectDescription"]);
      $loMetadata->Keywords ($_POST["ObjectKeywords"]);
      $loMetadata->Thumbnails ($_POST["ObjectThumbnails"]);
      $loMetadata->Credits ($_POST["ObjectCredits"]);
      $loMetadata->Info ($_POST["ObjectInfo"]);
      $loMetadata->SchoolLevel ($_POST["SchoolLevel"]);
      $loMetadata->SchoolArea ($_POST["SchoolArea"]);
      $loMetadata->SchoolTheme ($_POST["SchoolTheme"]);
      $loMetadata->ObjectPlataform ($_POST["ObjectPlataform"]);

      $loMetadata->SaveChanges();

      return;
   }



   // Funcion para formatear la salida del título del recurso
   // En caso de no existir metadatos, pega el nombre que viene
   // de la lista de recursos.
   function FormatMetadataTitle ($loMetadata, $fileSystemSet) {
      $loTitle =  $loMetadata->Title() !== NULL ?
                  $loMetadata->Title() : $fileSystemSet->Title();

      return $loTitle;
   }

   function PrintThumbnailsControls ($loMetadata) {
      if ($loMetadata->Thumbnails() !== NULL) {
         $thumbnailsControlsCount = 0;
         $patterns = array("/ElementID/", "/ElementValue/");

         $thumbnails = explode(",", trim($loMetadata->Thumbnails()));

         foreach($thumbnails as $thumbnail) {
            $thumbnailsControlsCount++;
            $sustitutions = array ($thumbnailsControlsCount, trim($thumbnail));

            $thumbnailControl = preg_replace($patterns, $sustitutions,
                                    "<input type=\"text\" name=\"ObjectThumbnail_ElementID\" id=\"ObjectThumbnail_ElementID\" " .
                                    "class=\"ui-widget-content ui-corner-all\" value=\"ElementValue\"/>" .
                                    "<img src=\"style/general/icons/delete_item.png\" alt=\"Eliminar\" id=\"ObjectThumbnailDelete_ElementID\" " .
                                    "class=\"WidgetControl\" onclick=\"DeleteElement('thumbnail', 'ElementID')\"><br/>");

            printf($thumbnailControl);
         }
      }
   }

   function PrintKeywordsControls ($loMetadata) {
      if ($loMetadata->Keywords() !== NULL) {
         $keywordsControlsCount = 0;
         $patterns = array("/ElementID/", "/ElementValue/");

         $keywords = explode(",", trim($loMetadata->Keywords()));

         foreach($keywords as $keyword) {
            $keywordsControlsCount++;
            $sustitutions = array ($keywordsControlsCount, trim($keyword));

            $keywordControl = preg_replace($patterns, $sustitutions,
                                    "<input type=\"text\" name=\"ObjectKeyword_ElementID\" id=\"ObjectKeyword_ElementID\" " .
                                    "class=\"ui-widget-content ui-corner-all\" value=\"ElementValue\"/>" .
                                    "<img src=\"style/general/icons/delete_item.png\" alt=\"Eliminar\" id=\"ObjectKeywordDelete_ElementID\" " .
                                    "class=\"WidgetControl\" onclick=\"DeleteElement('keyword', 'ElementID')\"><br/>");

            printf($keywordControl);
         }
      }
   }

?>

<script language="javascript" type="text/javascript">
   function SetUpForm (isLo) {
      if (isLo != 1) {
         $("#ObjectTitle").css("display", "none");
         $(".WidgetControl").css("display", "none");
         $("#IsProject").prop("checked", true);
      }
   }

   RefreshMetadataChangeManager();
   SetUpForm(<?php printf($fileSystemSet->HasIndex()); ?>);

</script>

<div class="MetadataFormTitle">Metadatos para <b><?php printf(FormatMetadataTitle($loMetadata, $fileSystemSet)); ?></b><br/></div>

<form id="MetadataForm" action="<?php printf($GLOBALS["wwwroot"] . "/ajax/Ajax_MetadataInterface.php"); ?>" method="post" target="ContentFrame">
   <input type="hidden" id="Action" name="Action" value="" />
   <input type="hidden" id="MetadataChanged" name="MetadataChanged" value="false" />
   <input type="hidden" id="loPath" name="loPath" value="<?php printf($fileSystemSet->GetBaseDirectory()); ?>" />
   <input type="hidden" id="ObjectPlataform" name="ObjectPlataform" value="DESCARTES" />
   <input type="hidden" id="ObjectKeywords" name="ObjectKeywords" value="" />
   <input type="hidden" id="ObjectThumbnails" name="ObjectThumbnails" value="" />

   <table class="MetadataTable">
      <tr>
         <td class="label">Es proyecto</td>
         <td style="width: 350px"><input type="checkbox" name="IsProject" id="IsProject" class="ui-state-disabled" disabled /></td>
      </tr>
      <tr>
         <td class="label">Nombre del proyecto</td>
         <td><input type="text" name="ProjectName" id="ProjectName" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->Project()); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Título del recurso</td>
         <td><input type="text" name="ObjectTitle" id="ObjectTitle" class="ui-widget-content ui-corner-all"
               value="<?php printf(FormatMetadataTitle($loMetadata, $fileSystemSet)); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Descripción breve</td>
         <td><textarea name="ObjectDescription" id="ObjectDescription" class="ui-widget-content ui-corner-all"
               style="height: 80px; width: 330px; word-break: break-word;"><?php printf($loMetadata->Description()); ?></textarea></td>
      </tr>
      <tr>
         <td class="label">Palabras clave
            <img src="style/general/icons/add_item.png" alt="agregar" class="WidgetControl" onclick="AddKeyword();">
         </td>
         <td>
            <div id="ObjectKeywords_Container">
               <?php PrintKeywordsControls ($loMetadata); ?>
            </div>
         </td>
      </tr>
      <tr>
         <td class="label">Vistas previas
            <img src="style/general/icons/add_item.png" alt="agregar" class="WidgetControl" onclick="AddThumbnail();">
         </td>
         <td>
            <div id="ObjectThumbnails_Container">
               <?php PrintThumbnailsControls ($loMetadata); ?>
            </div>
         </td>
      </tr>
      <tr>
         <td class="label">Página de créditos</td>
         <td><input type="text" name="ObjectCredits" id="ObjectCredits" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->Credits()); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Página de información</td>
         <td><input type="text" name="ObjectInfo" id="ObjectInfo" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->Info()); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Nivel escolar</td>
         <td><input type="text" name="SchoolLevel" id="SchoolLevel" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->SchoolLevel()); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Área escolar</td>
         <td><input type="text" name="SchoolArea" id="SchoolArea" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->SchoolArea()); ?>" /></td>
      </tr>
      <tr>
         <td class="label">Tema</td>
         <td><input type="text" name="SchoolTheme" id="SchoolTheme" class="ui-widget-content ui-corner-all"
               value="<?php printf($loMetadata->SchoolTheme()); ?>" /></td>
      </tr>
   </table>
</form>
