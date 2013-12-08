/*
*********************************************************************
* Variables globales
*********************************************************************
*/

var SelectedScenes = 0;

// Nombre del iframe de contenido
var ContentFrameName 	= "ContentFrame";
var NavigationPanelName = "navigationButtons";

/*
*********************************************************************
* Metodos para el manejo de las escenas
*********************************************************************
*/

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
        $("#SaveContainer").val("false");
        $("#ContainerConfigurationDialog").dialog("open");
    }
}

function ExportSceneSelection () {
    if (SelectedScenes == 0) {
        alert ("Debes seleccionar al menos una escena.");
        return;
    } else {
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



