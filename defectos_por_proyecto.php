<?php
$proyecto = $_GET['proyecto'];
require_once("Conexion_MySql.php");

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

	$sql = "SELECT 
DISTINCT(VD.cf_817) Estatus,
				count(VD.cf_817) Totales
			FROM vtiger_defectos VD INNER JOIN vtiger_project VP
				ON VD.proyecto_id = VP.projectid
			inner join vtiger_crmentity on VD.defectosid = vtiger_crmentity.crmid
            inner join vtiger_defectoscf on VD.defectosid = vtiger_defectoscf.defectosid
				WHERE 1=1 and vtiger_crmentity.deleted = 0
			    AND  VD.cf_817 NOT IN ('Rechazado','Diferido','Cerrado') 
			    and vtiger_defectoscf.cf_894 like '".$depto."'
				AND VP.projectname = '".$proyecto."'  			
			GROUP by VD.cf_817";
		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	/*$array_consulta=$array_consulta ."['". $row['Estatus']."',".$row['Totales']."],";*/
	$array_consulta=$array_consulta ."{name:'". $row['Estatus']."',y:".$row['Totales']."},";
}

$array_consulta=substr_replace($array_consulta,"",-1);


// $string = "
      // google.charts.load('current', {'packages':['corechart']});
      // google.charts.setOnLoadCallback(drawChart);
      // function drawChart() {

        // var data = google.visualization.arrayToDataTable([
         // ". $array_consulta ."
        // ]);

        // var options = {
          // title: 'Defectos abiertos por estado'
        // };

        // var chart = new google.visualization.PieChart(document.getElementById('drawChart'));

        // chart.draw(data, options);
      // }";
      
    $string = "$(function () {
    Highcharts.chart('DefectosPorProyecto', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Defectos abiertos por estado'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: ' {point.y}',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                },
    		showInLegend: true
            }
        },
        series: [{
            name: 'Porcentaje',
            colorByPoint: true,
            data: [". $array_consulta ."]
        }]
    });
});";
	
	/*$string = "

   google.charts.setOnLoadCallback(DefectosPorProyecto);
   function DefectosPorProyecto() { 
   var data = google.visualization.arrayToDataTable([ 
   ['Defectos','Totales'],
   ". $array_consulta ." 
   ]); 
   var options = { title: 'Defectos abiertos por estado',
   pieSliceText:  'value-and-percentage',
   pieSliceTextStyle: {color: 'white', fontName: 'arial', fontSize: '10'}		
   }; 
   var chart = new google.visualization.PieChart(document.getElementById('DefectosPorProyecto')); 
   chart.draw(data, options);
   };
   		


";*/
    
echo $string;

?>