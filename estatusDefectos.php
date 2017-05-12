<?php
require("Conexion_MySql.php");

$proyecto = $_GET['proyecto'];
$depto = $_GET['depto'];
//$proyecto = 'SAO Bancario';
switch($depto){
	case "Todos":	
		$depto = "%%";
	     break;
	case "QA":
		$depto = "Ciclo ".$depto;
		break;
	case "UAT":
		$depto = "Ciclo ".$depto;
		break;	      
}      




$severidad = ['Critico','Alta','Media','Baja'];
$array_consulta="";
$arrlength = count($severidad);
$totalCol = [0,0,0,0,0,0,0,0,0,0,0,0];
for ($i = 0; $i < $arrlength; $i++) {
	

	$estado=$severidad[$i];
	
	$query = "CALL defectos_estado_3('".$proyecto."', '".$estado."', '".$depto."')";
	$result = mysqli_query($con,$query) or die("Query fail: " . mysqli_error());
	
	if (!($result)){
		die("ERROR AL EJECUTAR LA CONSULTA: ");
	}
	
	$array_fila="['".$estado."',";
	$thisTotal = array();
	
	while($row = mysqli_fetch_assoc($result))
	{
	  $array_fila=$array_fila.$row['TOTAL'].",";
	  array_push($thisTotal,$row['TOTAL']);
		
	}
	
	$totalCol = array_map(function () {
		return array_sum(func_get_args());
	}, $totalCol, $thisTotal);
	
		
	
	$array_fila=substr_replace($array_fila,"",-1)."]";
	$result->close();
	$con->next_result();
	$array_consulta=$array_consulta.$array_fila.",";
	

	
}

  $totalCol="['Total',".implode(",",$totalCol)."]";
  $array_consulta = $array_consulta.$totalCol ;
 // $array_consulta= substr_replace($array_consulta,"",-1);
 


	
	$string = "
      google.charts.setOnLoadCallback(estatusDefectos);

      function estatusDefectos() {
			
			var cssClassNames = {
			'showRowNumber': 'false', 
			'width': '80%', 
			'height': '100%',
			'text-align': 'center'
   };

  var options = {'showRowNumber': false, 'allowHtml': true, 'cssClassNames': cssClassNames};
			
        var data = new google.visualization.DataTable();
		data.addColumn('string', '');	
	   data.addColumn('number', 'Asignado');
		data.addColumn('number', 'Cerrado');
			data.addColumn('number', 'Desplegado en ambiente QA');
			data.addColumn('number', 'Devolver');
			data.addColumn('number', 'Diferido');
		data.addColumn('number', 'En reparación');
			data.addColumn('number', 'En validación');
			data.addColumn('number', 'Nuevo');
			data.addColumn('number', 'Reabierto');
			data.addColumn('number', 'Rechazado');
		   data.addColumn('number', 'Reparado');
			data.addColumn('number', 'Total');
			
			
        data.addRows([
				   ". $array_consulta ." 
        ]);
        
		
				   		
				   		
        var table = new google.visualization.Table(document.getElementById('estatusDefectos'));

        table.draw(data, options);
	  };
				   		
";
echo $string;

?>
