/*
*********************************************************************
* Variables globales
*********************************************************************
*/

var SelectedScenes = 0;
var SelectedEscenesUnitsNames;

// Nombre del iframe de contenido
var ContentFrameName 	= "ContentFrame";
var NavigationPanelName = "navigationButtons";

/*
*********************************************************************
* Metodos para el manejo de las escenas
*********************************************************************
*/

function TakeUnitsNames (instruction, unitInfo) {

   var unitId = unitInfo.split("|")[2];
   var unitName = $("#LOT_" + unitId).text().trim();
   var unitExists = (unitId in SelectedEscenesUnitsNames);
   var currentUnitInfo = unitExists ? SelectedEscenesUnitsNames[unitId] :
                          {UnitName: unitName, SceneCount: 1};
   
   switch (instruction) {
      case "add":
         SetLOTitleSelected (unitId, true);
         
         if (unitExists)
            currentUnitInfo.SceneCount++;
         else
            SelectedEscenesUnitsNames[unitId] = currentUnitInfo;
         break;
      case "remove":
         if (currentUnitInfo.SceneCount === 1) {
            delete SelectedEscenesUnitsNames[unitId];
            SetLOTitleSelected (unitId, false);
         }
         else
            currentUnitInfo.SceneCount--;
         break;
   }
}

function SetLOTitleSelected (unitId, selected) {
   
   $backgroundValue = selected ? "rgba(180,180,180,.25)" : "";
   $("#LOT_" + unitId).css ("background-color", $backgroundValue);
   
}

function CreateUnitsNameSetter () {
   $("#UnitsTitles").html("");
   var elementSchema = "<input type='text' id='UTN_ElementID' name='UTN_ElementID' " + 
                        "class='ui-widget-content ui-corner-all' style='width: 250px;' " +
                        "value='ElementValue' /><br/>";
   
   for (var unitId in SelectedEscenesUnitsNames) {
      $("#UnitsTitles")
          .append(elementSchema.replace (/ElementID/g, unitId)
            .replace("ElementValue", SelectedEscenesUnitsNames[unitId].UnitName));
   }
   
   $("input[id^=UTN_]").change(function () {
      UpdateUnitName ($(this).prop("id").replace("UTN_", ""), $(this).prop("value"));
   });
}

function UpdateUnitName (unitId, newName) {
   SelectedEscenesUnitsNames[unitId].UnitName = newName;
}
            
function SetContentFrame (sceneUrl) {
    var iFrameC = $("#" + ContentFrameName);
    currentScene = iFrameC.attr("src");

	if(sceneUrl != currentScene)
	{
		iFrameC.attr("src", sceneUrl);
	}
}

function ShowSceneSelection () {
    if (SelectedScenes == 0) {
        alert ("Debes seleccionar al menos una escena.");
        return;
    } else {
        CreateUnitsNameSetter ();
        $("#SaveContainer").val("false");
        $("#ContainerConfigurationDialog").dialog("open");
    }
}

function ExportSceneSelection () {
   if (SelectedScenes == 0) {
      alert ("Debes seleccionar al menos una escena.");
      return;
   } else {
      CreateUnitsNameSetter ();
      $("#SaveContainer").val("true");
      $("#ContainerConfigurationDialog").dialog("open");
   }
}

function CleanSceneSelection () {
   
   $("[id^=ScenesCheckboxGroup]").each(function () {
      $(this).attr("checked", false);
   });
   
   $("[id^=loContents_]").each(function() {
      $(this).css("display", "none");
   });
   
   SelectedEscenesUnitsNames = new Array();
   SelectedScenes = 0;
   $("#counter").html("Escenas seleccionadas: " + SelectedScenes);

}

function ToggleContentView (contentId) {

   var container = $("#" + contentId);
   var state = container.css("display");
   var newState = state == "none" ? "inline" : "none";
   container.css("display", newState);
   
   $("[id^=loContents_]").each(function( index ) {
      if ($(this).attr("id") != contentId)
      $(this).css("display", "none");
   });

}

function RestoreContainerDefaults () {

   $("#SystemCloseButton").prop("checked", false);
   $("#ShowUnitPosition").prop("checked", true);
   $("#ShowNavigationArrows").prop("checked", false);
   $("#AdjustButtons").prop("checked", true);
   $("#RoundedCorners").prop("checked", false);
   
   $("#FixedButtonWidth").val("-1");
   $("#ContentFrameWidth").val("790");
   $("#ContentFrameHeight").val("520");
   $("#PageTitle").val("Nueva página");
   $("#UnitTitle").val("Nueva unidad");

}

function AddKeyword () {
      
   var keywordTemplate = "<input type=\"text\" name=\"ObjectKeyword_ElementID\" id=\"ObjectKeyword_ElementID\" " +
                           "class=\"ui-widget-content ui-corner-all\" />" +
                           "<img src=\"style/general/icons/delete_item.png\" alt=\"Eliminar\" id=\"ObjectKeywordDelete_ElementID\" "+
                           "class=\"WidgetControl\" onclick=\"DeleteElement(\'keyword\', \'ElementID\')\"><br/>";
                           
   var keywordControls = $("input[id^=ObjectKeyword_]");
   var keywordControlsLength = keywordControls.length;
   var nextKeywordId = keywordControlsLength > 0 ?
                           parseInt($(keywordControls[keywordControlsLength - 1])
                              .prop("id").replace("ObjectKeyword_", "")) + 1 : 1;
   
   $("#ObjectKeywords_Container").append(keywordTemplate.replace(/ElementID/g, nextKeywordId));
   
   RefreshMetadataChangeManager ();
}

function AddThumbnail () {
   
   var thumbnailTemplate = "<input type=\"text\" name=\"ObjectThumbnail_ElementID\" id=\"ObjectThumbnail_ElementID\" " +
                              "class=\"ui-widget-content ui-corner-all\" />" +
                              "<img src=\"style/general/icons/delete_item.png\" alt=\"Eliminar\" id=\"ObjectThumbnailDelete_ElementID\" "+
                              "class=\"WidgetControl\" onclick=\"DeleteElement(\'thumbnail\', \'ElementID\')\"><br/>";
   
   var thumbnailsControls = $("input[id^=ObjectThumbnail_]");
   var thumbnailsControlsLength = thumbnailsControls.length;
   var nextThumbnailId = thumbnailsControlsLength > 0 ?
                           parseInt($(thumbnailsControls[thumbnailsControlsLength - 1])
                              .prop("id").replace("ObjectThumbnail_", "")) + 1 : 1;
                              
   $("#ObjectThumbnails_Container").append(thumbnailTemplate.replace(/ElementID/g, nextThumbnailId));
   
   RefreshMetadataChangeManager ();
}

function DeleteElement (elementType, elementId) {
   alert (elementId);
   var elementTypeString = "";
   var elementsDeleteButtonString = "";
   
   switch (elementType) {
      case "keyword":
         elementTypeString = "#ObjectKeyword_";
         elementsDeleteButtonString = "#ObjectKeywordDelete_";
         break;
      case "thumbnail":
         elementTypeString = "#ObjectThumbnail_";
         elementsDeleteButtonString = "#ObjectThumbnailDelete_";
         break;
   }
   $(elementTypeString + elementId).remove();
   $(elementsDeleteButtonString + elementId).remove();
}

function RefreshMetadataChangeManager () {
   $("#MetadataForm").find("input").change ( function () {
      $("#MetadataChanged").val("true");
   });
   
   $("#MetadataForm").find("textarea").change ( function () {
      $("#MetadataChanged").val("true");
   });
}

