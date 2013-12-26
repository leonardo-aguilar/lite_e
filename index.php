<?php

   header('Content-Type: text/html; charset=UTF-8');

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	require_once ("config.php");
	require_once ($GLOBALS["controller"] . "/FileSystemSet.php");

?>

<html>
   <head>
      <title>Titulo</title>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      
      <link rel="stylesheet" type="text/css" href="style/ui-lightness/jquery-ui-1.10.3.custom.css" >
      <link rel="stylesheet" type="text/css" href="style/general/main.css">
      
      <script language="javascript" src="javascript/jquery-1.9.1.js"></script>
      <script language="javascript" src="javascript/jquery-ui-1.10.3.custom.js"></script>
      <script language="javascript" src="javascript/utils.js"></script>

      <script language="javascript" type="text/javascript">
         
         $(function() {
         
            document.oncontextmenu = function() {return false;};
            
            $(".LearningObjectTitle").mousedown(function(e){ 
               if( e.button == 2 ) {
                  var selectedLoPath = $($(this).parent().find("input")[0]).prop("value").split("|")[0];
                  $("#MetadataDialog").data("SelectedLO", selectedLoPath);
                  $("#MetadataDialog").dialog("open"); 
                  return false; 
               } 
               return true; 
            }); 
			
			$(".Project span.ProjectTitle").click (function () {
				$(this).parent().children("div").toggle();
			});
			
			$(".Project").each(function() { 
               $(this).children("div[class=LearningObject], div[class=Project]").hide(); 
            }); 
         
            $( "#menu" ).menu({
               position: {at: "left bottom"}
            });
            
            $("input[type='checkbox'][id^='ScenesCheckboxGroup']").change(function() {
               
               instruction = "remove"
               
               if(this.checked) {
                  ++SelectedScenes;
                  instruction = "add";
               }
               else --SelectedScenes;
               
               TakeUnitsNames (instruction, $(this).prop("value"));
               $("#counter").html("Escenas seleccionadas: " + SelectedScenes);
               
            });
               
            $("#MetadataDialog").dialog ({
               autoOpen:   false,
               height:     450,
               width:      600,
               modal:      true,
               open:       function (event, ui) {
                  var selectedLoPath = $(this).data("SelectedLO");
                  var request = "<?php printf ($GLOBALS["wwwroot"]) ?>/ajax/Ajax_MetadataInterface.php?loPath=" + selectedLoPath;
                  $(this).load(request);
               },
               buttons: {
                  "Cancelar cambios":  function () { $(this).dialog("close"); },
                  "Salvar cambios":    function () {
                     PrepareFormData ();
                     $("#MetadataForm").submit();
					 $(this).dialog("close"); 
                  },
                  Cancel: function () {
                     if ($("#MetadataChanged").val() == "true") {
                        if (confirm ("Has realizado cambios en los metadatos ¿deseas salvar los cambios antes de salir?")) {
                           PrepareFormData ();
                           $("#MetadataForm").submit();
                        }
                     }
                     $(this).dialog("close");
                  }
               },
            });
            
            
            
            $("#ContainerConfigurationDialog").dialog({
               autoOpen: false,
               height: 450,
               width: 400,
               modal: true,
               buttons: {
                  "Restablecer valores": function() { RestoreContainerDefaults (); },
                  "Compilar escenas": function() {
                     var bValid = true;
                     
                     if ( bValid ) {
                        $(this).dialog( "close" );
                     
                        $("#ContainerConfigurationForm input").each(function() {
                           if ($(this).attr("type") == "checkbox")
                              $("#ContentSelector").append("<input type='hidden' name='" + 
                                 $(this).attr('id')+"' value='" + $(this).prop("checked") + "' />");
                           else
                              $("#ContentSelector").append("<input type='hidden' name='" + 
                                 $(this).attr('id')+"' value='" + $(this).val() + "' />");
                        });
               
                        var unitsNames = "";
                        var unitNameSchema = "UnitId%UnitName|";
                        for (var unitId in SelectedEscenesUnitsNames) {
                           unitsNames += unitNameSchema.replace(/UnitId/g, unitId)
                                             .replace(/UnitName/g, SelectedEscenesUnitsNames[unitId].UnitName);
                        }
                        $("#UnitsNames").val(unitsNames);
                        $("#ContentSelector").submit();
                     }
                  },
                  Cancel: function() { $(this).dialog("close"); }
               }
            });
         
            RestoreContainerDefaults ();
            CleanSceneSelection ();
            
         });
      </script>

      <style>
         .ui-menu { overflow: hidden;}
         .ui-menu .ui-menu { overflow: visible !important; }
         .ui-menu > li { float: left; display: block; width: auto !important; }
         .ui-menu > li { margin: 3px 3px !important; padding: 0 0 !important; }
         .ui-menu > li > a { float: left; display: block; clear: both; overflow: hidden;}
         .ui-menu .ui-menu-icon { margin-top: 0 !important;}
         .ui-menu .ui-menu .ui-menu li { float: left; display: block;}
         
         
         .ui-dialog .ui-state-error { padding: .3em; }
         .validateTips { border: 1px solid transparent; padding: 0.3em; }
         
         #ContainerConfigurationDialog span {
            display:          block;
            margin-bottom:    6px;
            padding:          .4em;
         }
         
         #ContainerConfigurationDialog td { padding: 3px; }
         #ContainerConfigurationDialog td.label { width: 180px; text-align: right; }
         #ContainerConfigurationDialog input { margin-bottom: 3px; width: 95%; padding: 1px; }

         #MetadataDialog span { display: block; margin-bottom: 6px; padding: .4em; }
         
         #MetadataDialog td { padding: 3px; }
         #MetadataDialog td.label { width: 180px; text-align: right; }
         #MetadataDialog input { margin-bottom: 3px; width: 95%; padding: 1px; }
         
      </style>

	</head>
	<body>
	<div id="ContainerConfigurationDialog" title="Configuración del contenedor">
	    <form id="ContainerConfigurationForm">
            <table >
                    <tr>
                        <td class="label">Mostrar botón de cerrar</td>
                        <td><input type="checkbox" name="SystemCloseButton" id="SystemCloseButton" /></td>
                    </tr>
                    <tr>
                        <td class="label">Mostrar posición de la unidad</td>
                        <td><input type="checkbox" name="ShowUnitPosition" id="ShowUnitPosition" /></td>
                    </tr>
                    <tr>
                        <td class="label">Mostrar flechas de navegación</td>
                        <td><input type="checkbox" name="ShowNavigationArrows" id="ShowNavigationArrows" /></td>
                    </tr>
                    <tr>
                        <td class="label">Autoajustar botones</td>
                        <td><input type="checkbox" name="AdjustButtons" id="AdjustButtons" /></td>
                    </tr>
                    <tr>
                        <td class="label">Redondear botones</td>
                        <td><input type="checkbox" name="RoundedCorners" id="RoundedCorners" /></td>
                    </tr>
                    <tr>
                        <td class="label">Tamaño fijo de botones</td>
                        <td><input type="text" name="FixedButtonWidth" id="FixedButtonWidth" class="ui-widget-content ui-corner-all" /></td>
                    </tr>
                    <tr>
                        <td class="label">Anchura del contenedor</td>
                        <td><input type="text" name="ContentFrameWidth" id="ContentFrameWidth" class="ui-widget-content ui-corner-all" /></td>
                    </tr>
                    <tr>
                        <td class="label">Altura del contenedor</td>
                        <td><input type="text" name="ContentFrameHeight" id="ContentFrameHeight" class="ui-widget-content ui-corner-all" /></td>
                    </tr>
                    <tr>
                        <td class="label">Título para la página</td>
                        <td><input type="text" name="PageTitle" id="PageTitle" class="ui-widget-content ui-corner-all" /></td>
                    </tr>
                    <tr>
                        <td class="label">Título para la nueva unidad</td>
                        <td><input type="text" name="UnitTitle" id="UnitTitle" class="ui-widget-content ui-corner-all" /></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;" colspan="2">Títulos para unidades</td>
                    </tr>
                    <tr>
                        <td colspan="2"><div id="UnitsTitles"></div></td>
                    </tr>
            </table>
        </form>
    </div>
    <div id="MetadataDialog"></div>
    <div class="MainMenu">
            <ul id="menu">
                <li><a href="#">Selección de escenas</a>
                    <ul>
                        <li><a href="javascript:ShowSceneSelection();">Mostrar...</a></li>
                        <li><a href="javascript:CleanSceneSelection();">Limpiar selección...</a></li>
                        <li><a href="javascript:ExportSceneSelection();">Exportar selección...</a></li>
                    </ul>
                </li>
                <li class="ui-state-disabled"><a href="#">Selección de unidades</a>
                    <ul>
                        <li><a href="#">Mostrar...</a></li>
                        <li><a href="#">Limpiar selección...</a></li>
                        <li><a href="#">Exportar selección...</a></li>
                    </ul>
                </li>
            </ul>
		</div>
		<div class="ContentArea">
			<iframe id="ContentFrame" name="ContentFrame" src=""></iframe>
		</div>
		<div class="MenuArea">
		<form action="./template/index.php" method="post" id="ContentSelector" name="ContentSelector" target="ContentFrame">

		<input type='hidden' name='SaveContainer' id='SaveContainer' value='' />
   <input type='hidden' name='UnitsNames' id='UnitsNames' value='' />
        <?php

	        $util = new FileSystemSet($GLOBALS["repository"]);
	        $util->PrintInfo();

        ?>
        </form>
    </div>
    <div class="locker"></div>
    <div id="counter">Escenas seleccionadas: 0</div>
	</body>
</html>
