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
         if (unitExists)
            currentUnitInfo.SceneCount++;
         else
            SelectedEscenesUnitsNames[unitId] = currentUnitInfo;
         break;
      case "remove":
         if (currentUnitInfo.SceneCount === 1)
            SelectedEscenesUnitsNames = SelectedEscenesUnitsNames.splice(unitId, 1);
         else
            currentUnitInfo.SceneCount--;
         break;
   }
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
    // var locker = $('.locker');
    // locker.css('display', 'block');

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

    $("#SystemCloseButton").prop("checked", true);
    $("#ShowUnitPosition").prop("checked", true);
    $("#ShowNavigationArrows").prop("checked", true);
    $("#AdjustButtons").prop("checked", true);
    $("#RoundedCorners").prop("checked", false);

    $("#FixedButtonWidth").val("-1");
    $("#ContentFrameWidth").val("790");
    $("#ContentFrameHeight").val("520");
    $("#PageTitle").val("Nueva página");
    $("#UnitTitle").val("Nueva unidad");

}



