// Lista de escenas desiponibles
var listaEscenas;	

// Lista de botones para la secciones
var liSecciones;	

// El indice dentro de la lista de la escena que se esta mostrando 
var indexActual;	

/*
	Este objeto se usara como arreglo asosiativo para registar variables
	y sus valores desde las escenas. Tambien de aqui se sacaran las
	variables para inyectarlas en el applet que la solicite
	
	Ver metodos submitVarsToApplet(), registraVar(nameVar, valueVar)
*/
var hashVariables;	 

var nEscenasIntro;

/*
*********************************************************************
* Metodos para el manejo de las escenas 
*********************************************************************
*/


/**
	Inicia las variables y parcea la lista de secciones con sus respectivas
	rutas a las escenas que contienen en el tag <ul id = 'listaSecciones'> 
*/
function initPage(){

	var idULEscenas = 'listaSecciones';
	var ULEscenas = document.getElementById(idULEscenas); 
	var listaLiTmp = ULEscenas.getElementsByTagName('li');
	var auxIndex = 0;
	
	listaEscenas = new Array();
	hashVariables = new Object;
	liSecciones = new Array();
	indexActual = -1;
	nEscenasIntro = 0;
	for(var i = 0;i<listaLiTmp.length ;i++){
		tmpLi = listaLiTmp[i];
		grandParent = tmpLi.parentNode;
	
		if(grandParent == ULEscenas){		
			var linkTmp = document.createElement('a');
			liSecciones[liSecciones.length] = tmpLi;
			var span2 = tmpLi.getElementsByTagName('span');
			if(span2.length <=0){
				alert("No se encontro la etiqueta para la seccion en :\n"+tmpLi.innerHTML);
				continue;
			}
			span2 = span2[0];
			linkTmp.href="javascript:ponEscena("+auxIndex+");void(0);";
			linkTmp.innerHTML = span2.innerHTML;
			span2.innerHTML = "";
			span2.appendChild(linkTmp);	
		} else {
			while((grandParent != ULEscenas) && (grandParent != null))
				grandParent = grandParent.parentNode;
				
			if(grandParent == ULEscenas){
				listaEscenas[listaEscenas.length] = tmpLi;
				auxIndex = auxIndex + 1;
				if(liSecciones.length<=1){
					nEscenasIntro++;
				}
			} 
		}
	}


	ponEscena(0);

}



/*
*********************************************************************
* Funciones para los botones de herramientas
*********************************************************************
*/

function cerrar(){
	try{
		window.close();
	}catch(e){
		alert("No se pudo cerrar la ventana\n"+e);
	}
}



function AbrirCentrado(Url,NombreVentana,width,height,extras) {
var largo = width;
var altura = height;
var adicionales= extras;
var top = (screen.height-altura)/2;
var izquierda = (screen.width-largo)/2; nuevaVentana=window.open(''+ Url + '',''+ NombreVentana + '','width=' + largo + ',height=' + altura + ',top=' + top + ',left=' + izquierda + ',features=' + adicionales + '');
nuevaVentana.focus();
}

function verCreditos(){
	var largo = 750;
    var altura = 380;
		
	var adicionales= ' ';
	var top = (screen.height-altura)/2;
	var izquierda = (screen.width-largo)/2; 
	var cfgWin = "resizable=no,location=no,menubar=no,status=no,titlebar=no,toolbar=no";
	nuevaVentana=window.open(''+ 'docs/creditos.html' + '',''+ 'creditos' + '','width=' + largo + ',height=' + altura + ',top=' + top + ',left=' + izquierda + ',features=' + adicionales + ''+ cfgWin);
	nuevaVentana.focus();
	
}

function verDocumentacion(pagina){
	var pagina='docs/info.html'
	var largo = 800;
        var altura = 600;
	var adicionales= ' ';
	var top = 10;
	var izquierda = (screen.width-largo)/2; 
	var cfgWin = "resizable=yes,location=no,menubar=no,status=no,titlebar=no,toolbar=no, scrollbars=yes";
	nuevaVentana=window.open(''+ pagina + '',''+ 'documentacion' + '','width=' + largo + ',height=' + altura + ',top=' + top + ',left=' + izquierda + ',features=' + adicionales + ''+ cfgWin);
	nuevaVentana.focus();
}

/*
*********************************************************************
* Funciones para la navegacion
*********************************************************************
*/

function anteriorEscena(){
	if (indexActual>0) {
		var nextIndex =	Math.max(indexActual-1,0);
		ponEscena(nextIndex);
	}
}

function siguienteEscena(){
	if (indexActual+1<listaEscenas.length) {
		var nextIndex =	Math.min(indexActual+1,listaEscenas.length-1);
		ponEscena(nextIndex);
	}
}


function ponEscena(indexEscena){
	indexActual = indexEscena;
	var idContenedor = 'containerApplet';
	var iframeC = document.getElementById(idContenedor);
	if(!iframeC){
		alert("No se pudo encontrar el iframe : '"+idContenedor+"'");
		return;
	}

	limpiaSeleccionSeccion();
	var otraClase = (indexActual < nEscenasIntro)?'primerElementoAct':'seccionActiva'
	var tmpLi = listaEscenas[indexActual];
	var tmpParent = tmpLi.parentNode;
	while(tmpParent.tagName.toLowerCase() != 'li'){
		tmpParent = tmpParent.parentNode ;
	}
	tmpParent.className = otraClase;
	iframeC.src = tmpLi.innerHTML;
}



function limpiaSeleccionSeccion(){
	for(var i = 0; i<liSecciones.length;i++){
		liSecciones[i].className = (i==0)?"primerElemento":'';
	}
}







/*
*********************************************************************
* Registro y volcado al applet de variables
*********************************************************************
*/

function registraVar(nameVar, valueVar){
	var arrayNames = nameVar.split(',');
	var arrayValues = valueVar.split(',');
	
	
	if(arrayNames.length != arrayValues.length){
		alert("El numero de elementos de las listas no coincide");
	}
	
	for (var i = 0; i < arrayNames.length;i++){
		var key = arrayNames[i];
		var val = arrayValues[i];

		hashVariables[key] = val;
	}
}



function submitVarsToApplet(){
	var idContenedor = 'containerApplet';
	var iframeAct = document.getElementById(idContenedor);
	var strDump = '';
	for(var a in hashVariables){
		var valueTmp = hashVariables[a];
		if(''.match(/^\d+(\.\d+)?/i)){
			valueTmp = parseFloat(valueTmp);
		} else {
			valueTmp = "'"+valueTmp+"'";
		}
		strDump += a+"="+valueTmp+"\n";
	}
	
	var listaApplets = null;
	if(iframeAct.contentDocument)
		listaApplets = iframeAct.contentDocument.getElementsByTagName('applet');
	else
		listaApplets = iframeAct.contentWindow.document.getElementsByTagName('applet');
		
	
	if(listaApplets.length > 0){
		var applet = listaApplets[0]; 
		try{
			applet.undump(strDump);
			applet.refresh();
		} catch(e){
			alert(e);
		}
	}
}




/*
*********************************************************************
* Funcion de debug
*********************************************************************
*/
function inspectObj(obj){
	for(var a in obj){
		if(!confirm(a+" = "+obj[a]))
			break;
	}
}