<?php

   ini_set('display_errors', 'On');
	error_reporting(E_ALL);

   require_once ($_SERVER['DOCUMENT_ROOT'] . "/lite_e/config.php");
   require_once ($GLOBALS["controller"] . "/FileSystemSet.php");

   header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
   header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   header("Cache-Control: no-store, no-cache, must-revalidate");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");

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
         $stringInputPattern = "<input type=\"text\" name=\"ObjectKeyword_ElementID\" id=\"ObjectKeyword_ElementID\" " .
                           "class=\"ui-widget-content ui-corner-all\" value=\"ElementValue\"/>" .
                           "<img src=\"style/general/icons/delete_item.png\" alt=\"Eliminar\" id=\"ObjectKeywordDelete_ElementID\" " .
                           "class=\"WidgetControl\" onclick=\"DeleteElement('keyword', 'ElementID')\"><br/>";

         $keywords = explode(",", trim($loMetadata->Keywords()));

         foreach($keywords as $keyword) {
            $keywordsControlsCount++;
            $sustitutions = array ($keywordsControlsCount, trim($keyword));

            $keywordControl = preg_replace($patterns, $sustitutions, $stringInputPattern);

            printf($keywordControl);
         }
      }
   }

   function PrintThumbnailsUrls ($fileSystemSet) {
      $pngEntriesNumber = count($fileSystemSet->PngEntries());

      if ($pngEntriesNumber > 0) {

         $iterator = $pngEntriesNumber;

         $relativeUrls = "";
         $absoluteUrls = "";
         $fileNames = "";

         foreach ($fileSystemSet->PngEntries() as $pngEntry) {
            $iterator--;
            $comaString = $iterator == 0 ? " " : ", ";
            $relativeUrls .= "\"" . $pngEntry->EntryRelativeUrl($fileSystemSet->BaseDirectoryName()) . "\"" . $comaString;
            $absoluteUrls .= "\"" . $pngEntry->EntryUrl() . "\"" . $comaString;
            $fileNames .= "\"" . $pngEntry->EntryFileName() . "\"" . $comaString;
         }

         printf ("{ RelativeUrls: [%s], AbsoluteUrls: [%s], FileNames: [%s]}",
            $relativeUrls, $absoluteUrls, $fileNames);
      }
   }

   function PrintBrowsablesUrls ($fileSystemSet) {
      $browsableEntriesNumber = count($fileSystemSet->BrowsableEntries());

      if ($browsableEntriesNumber > 0) {

         $iterator = $browsableEntriesNumber;

         $relativeUrls = "";
         $absoluteUrls = "";
         $fileNames = "";

         foreach ($fileSystemSet->BrowsableEntries() as $browsableEntry) {
            $comaString = $iterator == $browsableEntriesNumber ? " " : ", ";

            if (!$browsableEntry->IsDescartes()) {
               $relativeUrls .= $comaString . "\"" . $browsableEntry->EntryRelativeUrl($fileSystemSet->BaseDirectoryName()) . "\"";
               $absoluteUrls .= $comaString . "\"" . $browsableEntry->EntryUrl() . "\"";
               $fileNames .= $comaString . "\"" . $browsableEntry->EntryFileName() . "\"";
            }

            $iterator--;
         }

         printf ("{ RelativeUrls: [%s], AbsoluteUrls: [%s], FileNames: [%s]}",
            $relativeUrls, $absoluteUrls, $fileNames);
      }
   }

?>

<style>
div.ObjectSelectorBox {
   margin:                 3px;
}

.ObjectSelectorBox input {
   margin-right:           5px;
}
</style>

<script language="javascript" type="text/javascript">

   var CurrentControlId = "";
   var thumbnailsUrls = <?php PrintThumbnailsUrls($fileSystemSet); ?>;
   var browsablesUrls = <?php PrintBrowsablesUrls($fileSystemSet); ?>;

   var urlSelectorTemplate = "<div class=\"ObjectSelectorBox\">" +
                                 "<input type=\"radio\" name=\"ObjectSelectorRadio\" id=\"ObjectSelectorRadio\" value=\"RELATIVE_URL\">" +
                                 "<a href=\"ABSOLUTE_URL\" target='_blank'>FILE_NAME</a></div>";

   function RefreshUrlSelectors () {

      $('input[id^=ObjectCredits], input[id^=ObjectInfo], input[id^=ObjectThumbnail_]').click(function(e) {
         CurrentControlId = $(this).prop("id");
         objectKind = CurrentControlId.indexOf ("ObjectThumbnail") > -1 ? "thumb" : "browsable";
         valuesObject = objectKind == "thumb" ? thumbnailsUrls : browsablesUrls;
         selectorDesc = objectKind == "thumb" ? "Thumbnail" : "Navegable";
         $("#ValueSelection").empty();

         for (i = 0; i < valuesObject.RelativeUrls.length; i++) {

            newElementString = urlSelectorTemplate.replace(/RELATIVE_URL/g, valuesObject.RelativeUrls[i]);
            newElementString = newElementString.replace(/ABSOLUTE_URL/g, valuesObject.AbsoluteUrls[i]);
            newElementString = newElementString.replace(/FILE_NAME/g, valuesObject.FileNames[i]);

            $("#ValueSelection").append($(newElementString));

         }

         $("#ValueSelection").dialog('open');
      });

   }

   function SetUpForm (isLo) {
      if (isLo != 1) {
         $("#ObjectTitle").css("display", "none");
         $(".WidgetControl").css("display", "none");
         $("#IsProject").prop("checked", true);
      }
   }

   $("#ValueSelection").dialog({
      autoOpen: false,
      height: 250,
      width: 250,
      modal: true,
      buttons: {
         "Guardar selección": function() {
            if ($("input[name='ObjectSelectorRadio']:checked").length == 1) {
               $("#" + CurrentControlId).val($("input[name='ObjectSelectorRadio']:checked").val());
            } else if (!confirm ("¿Deseas cerrar sin seleccionar un elemento?"))
               return;

            $(this).dialog("close");
         },
         Cancel: function() { $(this).dialog("close"); }
      }
   });

   RefreshMetadataChangeManager();
   SetUpForm(<?php printf($fileSystemSet->HasIndex()); ?>);


   /*function SetControlValue (controlId, newValue) {
      alert (controlId + ": " + newValue);
      var isValueRepeated = false;

      if (controlId.indexOf("ObjectThumbnail") != -1)
         alert("ObjectThumbnail");
      }

      $(controlId).val(newValue);
   }

   $('#ObjectCredits').click(function () {
      $(this).tooltip('close');
      $(this).removeClass("on");
   });

   $("#ObjectCredits").on('mouseout', function (e) {

   });
*/

</script>

<div id="ValueSelection"></div>
<div class="MetadataFormTitle">Metadatos para <b><?php printf(FormatMetadataTitle($loMetadata, $fileSystemSet)); ?></b><br/></div>

<form id="MetadataForm" action="<?php printf($GLOBALS["wwwroot"] . "/ajax/Ajax_MetadataInterface.php"); ?>" method="post" target="ContentFrame">
   <input type="hidden" id="Action" name="Action" value="" />
   <input type="hidden" id="MetadataChanged" name="MetadataChanged" value="false" />
   <input type="hidden" id="loPath" name="loPath" value="<?php printf($fileSystemSet->BaseDirectory()); ?>" />
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
