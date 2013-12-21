/*
*********************************************************************
* Declaración del sistema de registro en la consola del navegador   *
*********************************************************************
*/
var jsDebug = true;

function jsTrace (message) {
	if(jsDebug)
		if (window.console)
			console.log(message);
}

function inspectObj(obj){
	for(var a in obj){
		if(!confirm(a+" = "+obj[a]))
			break;
	}
}

var StringBuilder = {
    New: function() {
        var data = [];
        var counter = 0;
        return {
            append: function(s) { data[counter++] = s; return this; },
            remove: function(i, j) { data.splice(i, j || 1); return this; },
            insert: function(i, s) { data.splice(i, 0, s); return this; },
            toString: function(s) { return data.join(s || ""); return this; } };
        }
};


/*
*********************************************************************
* Variables globales
*********************************************************************
*/

// Control de visibilidad del botón "CERRAR"
var SystemCloseButton 	= true;
var ShowUnitPosition  	= true;
var ShowNavigationArrows= true;

// Titulos de la pagina y la unidad interactiva
var PageTitle 			= "";
var UnitTitle			= "";

var AdjustButtons		= false;
var RoundedCorners		= false;
var DefaultButtonWidth	= 95;
var FixedButtonWidth	= -1;
var ContentFrameWidth	= -1;

// Escenas de descartes declaradas (archivos HTML y el título que
// los agrupa
var DescartesUnits;
var DescartesScenes;
// El indice dentro de la lista de la escena que se esta mostrando
var currentSceneIndex;

// Nombre del iframe de contenido
var ContentFrameName 	= "jsApplet";
var NavigationPanelName = "navigationButtons";

/*
*********************************************************************
* Metodos para el manejo de las escenas
*********************************************************************
*/

function declareNewUnit(unit) {
	var dataComplete = false;

	if(DescartesUnits == null || DescartesUnits == undefined)
		DescartesUnits = new Array();

	if(unit.Name != null && unit.Files != null && unit.Files.length > 0)
		dataComplete = true;

	var currentIndex = DescartesUnits.length;

	if(dataComplete) {
		DescartesUnits[currentIndex] = unit;
	} else
		jsTrace ("La información de la escena no está completa " + scene.Name);

}

function unitHasScene(unit, sceneFile) {
	var fileIndex = -1;

	for(var currentSceneFile in unit.Files) {
		if(unit.Files[currentSceneFile] == sceneFile) {
			fileIndex = parseInt(currentSceneFile) + 1;
			break;
		}
	}

	return fileIndex;
}

function printUnitInfo(unit) {
	jsTrace("**** Unidad interactiva: " + unit.Name);
	jsTrace("Número de escenas: " + unit.Files.length);
	jsTrace("Archivos html relacionados: ");
	for(var htmlFile in unit.Files)
		jsTrace("Archivo : " + unit.Files [htmlFile]);
}

function checkUnits() {
	jsTrace("**** Unidades interactivas: " + DescartesUnits.length);
	jsTrace("Información disponible: ");
	for(var unit in DescartesUnits) {
		printUnitInfo(DescartesUnits[unit]);
	}
}

function closeButtonVisibilityControl () {
	if(!SystemCloseButton)
		$("a[id=closeWindowButton]").css("display", "none");
	jsTrace("Estado del botón: " + $("a[id=closeWindowButton]").css("display"));

}

function navigationArrowsVisibilityControl () {
	if(DescartesScenes.length == 1 || !ShowNavigationArrows)
		$("div[id=navigationArrows]").css("display", "none");
	jsTrace("Estado de flechas de navegación: " + $("div[id=navigationArrows]").css("display"));
}

function unitPositionVisibilityControl () {
	if(!ShowUnitPosition)
		$("span[id=unitPosition]").css("display", "none");
	jsTrace("Estado de posicion: " + $("span[id=unitPosition]").css("display"));
}

function setTitles () {
	if(PageTitle.length == 0)
		PageTitle = "Descartes";

	$("title").text(PageTitle);

	if(UnitTitle.length != 0)
		$("span[id=unitsTitle]").text(UnitTitle);

}

function initializeContainer() {

	initializePage();
	setTitles ();
	closeButtonVisibilityControl();
	navigationArrowsVisibilityControl();
	unitPositionVisibilityControl();

}

/**
	Inicia las variables y parcea la lista de secciones con sus respectivas
	rutas a las escenas que contienen en el tag <ul id = 'listaSecciones'>
*/
function initializePage() {
	DescartesScenes = new Array();

	var navigationPanel = $("#" + NavigationPanelName);

	for(var unitIndex in DescartesUnits) {
		var currentUnit = DescartesUnits[unitIndex];
		var currentUnitIndexFile = currentUnit.Files[0];
		var unitLink = createButton (DescartesScenes.length, unitIndex, currentUnit.Name);

		for(var sceneIndex in currentUnit.Files) {
			DescartesScenes[DescartesScenes.length] = currentUnit.Files[sceneIndex];
		}
		navigationPanel.append(unitLink);
	}

	ContentFrameWidth = ContentFrameWidth == -1 ? "100%" : ContentFrameWidth;
	$("#" + ContentFrameName).attr("width", ContentFrameWidth);

	setScene(0);
}

function createButton (sceneIndex, unitIndex, unitName) {
	var htmlText = StringBuilder.New();
	var buttonWidth = !RoundedCorners ? "width: " + DefaultButtonWidth + "px;" : "";

	if (AdjustButtons && FixedButtonWidth == -1) {
		buttonWidth = calculateButtonWidth();
	} else if (FixedButtonWidth != -1) {
		buttonWidth = "width: " + FixedButtonWidth + "px;"
	}

	htmlText.append("<div id='nbId_" + unitIndex + "' ");
	htmlText.append("onClick='setScene(" + sceneIndex + ");' >");

	if(RoundedCorners) htmlText.append("<div class='leftBorder'></div>");
	htmlText.append("<div class='body' style='" + buttonWidth  + "'>" + unitName + "</div>");
	if(RoundedCorners) htmlText.append("<div class='rightBorder'></div>");

	htmlText.append("</div>");

	return htmlText.toString("");

}

function calculateButtonWidth () {
	var buttonWidth = 0;
	var buttonWidthText  = "";
	var unitsLength = DescartesUnits.length;
	var availableArrowSpace = ShowNavigationArrows ? 0 : 89;
	var availableCloseSpace = SystemCloseButton ? 0 : 29;

	buttonWidth = ((600 + availableArrowSpace + availableCloseSpace) - (unitsLength * 5)) / unitsLength;

	if (RoundedCorners) buttonWidth -= 12;

	buttonWidthText = "width: " + buttonWidth + "px;";

	return buttonWidthText;
}

/*
*********************************************************************
* Funciones para los botones de herramientas
*********************************************************************
*/

function cerrar(){
	try {
		window.close();
	} catch(e){
		jsTrace("No se pudo cerrar la ventana\n"+e);
	}
}

function verDocumentacion() {
	var cfgWin = 	"width=530, height=400, resizable=no, location=no, menubar=no, ";
	cfgWin += 		"status=no, titlebar=no, toolbar=no, scrollbars=1";
	window.open ('docs/info.html', 'documentacion', cfgWin);
}

function verCreditos() {
	var cfgWin = 	"width=410, height=400, resizable=no, location=no, menubar=no, ";
	cfgWin += 		"status=no, titlebar=no, toolbar=no, scrollbars=1";
	window.open ('docs/creditos.html', 'creditos', cfgWin);
}

/*
*********************************************************************
* Funciones para la navegacion
*********************************************************************
*/

function prevScene () {
	if (currentSceneIndex > 0)
		setScene(currentSceneIndex - 1);
}

function nextScene () {
	if (currentSceneIndex < DescartesScenes.length - 1)
		setScene(currentSceneIndex + 1);
}

function setScene(sceneIndex) {
	if(sceneIndex != currentSceneIndex)
	{
		currentSceneIndex = sceneIndex;
		var iframeC = $("#" + ContentFrameName);
		var currentSceneFile =  DescartesScenes[sceneIndex];
		iframeC.attr("src", currentSceneFile);
		resetScenesClass(currentSceneFile);
	}
}

function resetScenesClass (sceneFile) {
	var currentUnitIndex = 0;
	for(var unitIndex in DescartesUnits) {
		var currentDiv = $("#nbId_" + unitIndex);
		var currentUnit = DescartesUnits[unitIndex];
		var unitCssClass = "navigationButton";

		var currentFileIndex = unitHasScene(currentUnit, sceneFile)

		if(currentFileIndex != -1) {
			$("span[id=unitPosition]").text(currentUnit.Name + " " + currentFileIndex +
				" de " + currentUnit.Files.length);

			unitCssClass = "selectedNavigationButton";
		}

		currentDiv.attr("class", unitCssClass);
	}
}

