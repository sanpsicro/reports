<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html >
<head>
<style>
.radio-toolbar input[type="radio"] {
  display: none;
  vertical-align: middle;
}

.radio-toolbar label {

  display: inline-block;
  cursor: pointer;
  position: relative;
  padding-left: 25px;
  margin-right: 15px;
  font-size: 13px;

}

.radio-toolbar input[type="radio"]:checked+label {
  border-radius: 8px;
}

label:before {
  content: "";
  display: inline-block;
 
  width: 16px;
  height: 16px;
 
  margin-right: 10px;
  position: absolute;
  left: 0;
  bottom: 1px;
  background-color: #D7D7D7;
  box-shadow: inset 0px 2px 3px 0px rgba(0, 0, 0, .3), 0px 1px 0px 0px rgba(255, 255, 255, .8);
}

input[type=radio]:checked + label:before {
    content: "\2022";
    color: #5D5D5D;
    font-size: 30px;
    text-align: center;
    line-height: 18px;
}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/form2.css"> 
<title>Generar Reporte</title>
</head>

<body >
<div class="container"   >  
  <form id="form_report" action="/Reports/Reportes_Diarios/generator_executable.php" method="post">
    <h3>REPORTES</h3>
    <!-- <h4>Selecionar proyecto</h4> -->
    <fieldset>
	        
			  <input type="radio" name="radio" value="reporte_diario">Reporte Diario<br/>
			  <input type="radio" name="radio" value="uso_recursos">Uso de Recursos<br/>
			  <input type="radio" name="radio" value="causa_raiz">Reporte por Causa raiz<br/>
			  
	         <div id="contenedorDepartamento" class="radio-toolbar" style="display:none;">
				<input type="radio" name="Depto" value="Todos"  id="radio1" checked>
				<label for="radio1">Todos los defectos</label> 
				<input type="radio" name="Depto" value="QA" id="radio2"> 
				<label for="radio2">Defectos de QA</label>
				<input type="radio" name="Depto" value="UAT" id="radio3"> 
				<label for="radio3">Defectos de UAT</label><br>
			</div>
			  
			 <div id = "contenedorMenu">
             </div>	
           
			 
             <span>
			 <h4 font="arial" id ="estatus" style="display: none;"></h4>
			 </span>
    </fieldset>
      <button style="display: none;" name="submit" type="submit" id="contact-submit" data-submit="...Sending">Generar</button>
     
    
  </form>
</div>

<script>
$(document).ready(function(){
   var $form = $('form');
   $form.submit(function(){$.post($(this).attr('action'), $(this).serialize(),  function(data){window.open('http://172.16.3.50:8888/Reports/Reportes_HTML/'.concat(data),'_blank'); },'html'); 
       
           var proyecto =$('select').val();
		   try{
			   document.getElementById('seleccion').style.display;
			   
               var bool=true;			   } 
		   catch(error ){
			   var bool =false}
		   
		   if( bool ){
			  if(document.getElementById('seleccion').style.display == 'none'){ alert("Reporte de uso de recursos generado"); }else{ 
			  alert("Reporte de ".concat(proyecto).concat(" generado"));}
		   }else{
			  alert("Reporte de uso de recursos generado"); 
		   }
		  $('select').val('seleccionar proyecto');
		  document.getElementById('contact-submit').style.display='none';
          document.getElementById('estatus').style.display='none';
		  $('select').hide();
		  $('input[name=radio]:checked').prop('checked', false);
		  $('#contenedorDepartamento').hide();

     
      return false;
	   
   });
   
 
   
});

$('#form_report').on('change', '#seleccion', function() {
  document.getElementById('contact-submit').style.display='';
  }
);


$('#form_report').on('change', '#seleccion', function() {
  document.getElementById('estatus').style.display='';
  var estatus = $('#seleccion').find(":selected").attr('name');
  document.getElementById('estatus').innerHTML= 'Estatus del proyecto: '.concat(estatus);
  
 }
);



 
 
 
 
 $(document).ready(function(){
 $('#form_report input').on('change',  function() {
   var reporte = $('input[name=radio]:checked').val();	 
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
     document.getElementById("contenedorMenu").innerHTML = xhttp.responseText;
    }
  };
  xhttp.open("GET", reporte.concat(".php"), true);
  xhttp.send();

});
 });
 

 
$('input[type="radio"]').change(function() {
   if($('input[name=radio]:checked').val()== 'reporte_diario'){
	   /*document.getElementById('contenedorDepartamento').style.display = '';*/
	   $('#contenedorDepartamento').show('slow');
	   document.getElementById('contact-submit').style.display='none';
	   document.getElementById('contact-submit').style.background='#4CAF50';
 }else if($('input[name=radio]:checked').val()== 'uso_recursos'){
	 document.getElementById('contact-submit').style.display='none';
	 document.getElementById('estatus').style.display='none';
	 $('select').hide('slow');
	 
	 /*document.getElementById('contenedorDepartamento').style.display = 'none';*/
	 $('#contenedorDepartamento').hide('slow');
	  document.getElementById('contact-submit').style.display='';
	  document.getElementById('contact-submit').style.background='purple';
	
 }else if($('input[name=radio]:checked').val()== 'causa_raiz') {
	 
	 $('#contenedorDepartamento').show('slow');
	   document.getElementById('contact-submit').style.display='none';
	   document.getElementById('contact-submit').style.background='#4CAF50';
	 }
});





 
 

  
 
</script>

<?php
/* $file_name = 'ReporteDiario25_05_2016-11_07_19.html';
$file_url = 'http://172.16.3.50:8888/Reports/Reportes_Diarios/ReporteDiario25_05_2016-11_07_19.html' . $file_name;
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$file_name."\""); 
readfile($file_url);
exit; */
?>

</body>
</html>