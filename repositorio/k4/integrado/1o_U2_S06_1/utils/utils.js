// JavaScript Document

var anchoDeVentana=0;
var AltoDeVentana=0;

function popup(url) 
{
 var anchoCont  = 790;
 var altoCont = 560;
 var anchoVentana=800;
 var altoVentana=644;
 var izq   = (screen.width  - anchoVentana)/2;
 var arriba    = (screen.height - altoVentana)/2;
 var params = 'width='+anchoCont+', height='+altoCont;
 params += ', top='+arriba+', left='+izq;
 params += ', directories=no';
 params += ', location=no';
 params += ', menubar=no';
 params += ', resizable=no';
 params += ', scrollbars=no';
 params += ', status=no';
 params += ', toolbar=no';
 newwin=window.open(url,'InteractiveWin', params);
 if (window.focus) {newwin.focus()}
 //alert('popUp');
 return false;
}

 function acomoda(){
	  document.getElementById("contenedor").style.top  = '50%';
	  document.getElementById("contenedor").style.left  = '50%';
	  document.getElementById("contenedor").style.marginTop='-280px';
	  document.getElementById("contenedor").style.marginLeft='-395px';
	  
	 var theWidth, theHeight;
	// Window dimensions: 
	if (window.innerWidth) {
	theWidth=window.outerWidth;
	//Firefox
	}
	else if (document.documentElement && document.documentElement.clientWidth) {
	theWidth=document.documentElement.clientWidth;
	}
	else if (document.body) {
	theWidth=document.body.clientWidth+10;
	//IE
	}
	if (window.innerHeight) {
	theHeight=window.outerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight) {
	theHeight=document.documentElement.clientHeight;
	}
	else if (document.body) {
	theHeight=document.body.clientHeight+40;
	} 
	
	altoDeVentana=theHeight;
	anchoDeVentana=theWidth;
	
	}

 
 function lanzaFull(){
      var myX = 0;
      var myY = 0;
      window.moveTo(myX, myY);
	  window.resizeTo(screen.width,screen.height);
	  document.getElementById("contenedor").style.top  = '50%';
	  document.getElementById("contenedor").style.left  = '50%';
	  document.getElementById("contenedor").style.marginTop='-280px';
	  document.getElementById("contenedor").style.marginLeft='-395px';
  }
  
  function contraeFull(){
  	var ancho  = anchoDeVentana;
 	var alto = altoDeVentana;
 	var myX   = (screen.width  - ancho)/2;
 	var myY   = (screen.height - alto)/2;
 	window.resizeTo(ancho,alto);
    window.moveTo(myX,myY);
	//alert('noFull');
  }
  
  
 function abreCreditos(url)
  {
 var anchoCont  = 700;
 var altoCont = 700;
 var anchoVentana=anchoCont;
 var altoVentana=altoCont;
 var izq   = 40;
 var arriba    = 40;
 var params = 'width='+anchoCont+', height='+altoCont;
 params += ', top='+arriba+', left='+izq;
 params += ', directories=no';
 params += ', location=no';
 params += ', menubar=no';
 params += ', resizable=no';
 params += ', scrollbars=yes';
 params += ', status=no';
 params += ', toolbar=no';
 credWin=window.open(url,'creditoWin', params);
 credWin.focus()
 return false;
}

function abreSugerencias(url)
  {
 var anchoCont  = 700;
 var altoCont = 700;
 var anchoVentana=anchoCont;
 var altoVentana=altoCont;
 var izq   = 40;
 var arriba    = 40;
 var params = 'width='+anchoCont+', height='+altoCont;
 params += ', top='+arriba+', left='+izq;
 params += ', directories=no';
 params += ', location=no';
 params += ', menubar=no';
 params += ', resizable=no';
 params += ', scrollbars=yes';
 params += ', status=no';
 params += ', toolbar=no';
 sugWin=window.open(url,'sugerenciasWin', params);
 sugWin.focus()
 return false;
}
