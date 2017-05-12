<?php
$proyecto = $_GET['proyecto'];
require_once("Conexion_MySql.php");

	$sql = "

SELECT t.recurso AS recurso,COUNT(t.recurso) AS total_actividades FROM (

SELECT concat(V4.first_name,' ',V4.last_name) AS recurso,V3.projectstatus AS estatus_proyecto,V3.projectname AS proyecto ,V0.label AS actividad,V1.projecttaskstatus AS estatus_actividad FROM vtiger_crmentity AS V0 
INNER JOIN vtiger_projecttask AS V1 ON  V0.crmid=V1.projecttaskid
INNER JOIN vtiger_crmentityrel AS V2 ON V1.projecttaskid=V2.relcrmid
INNER JOIN vtiger_project AS V3 ON V1.projectid=V3.projectid
INNER JOIN vtiger_users AS V4 on V0.smownerid=V4.id
WHERE V3.projectstatus NOT IN ('Cancelado','Terminado','') AND V1.projecttaskstatus NOT IN ('Canceled','Completed') and V0.deleted = 0
ORDER BY proyecto) AS t
GROUP BY t.recurso";
		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."{name:'". $row['recurso']."',y:".$row['total_actividades']."},";
}

$array_consulta=substr_replace($array_consulta,"",-1);


	
/*	$string = "

   google.charts.setOnLoadCallback(actividades_en_recursos);
   function actividades_en_recursos() { 
   var data = google.visualization.arrayToDataTable([ 
   ['Recursos','Total de Actividades'],
   ". $array_consulta ." 
   ]); 
   var options = { title: 'Actividades por recurso',
   pieSliceText: 'value'}; 
   var chart = new google.visualization.PieChart(document.getElementById('actividades_en_recursos')); 
   chart.draw(data, options); 
   };


";*/

$string = "$(function () {
    Highcharts.chart('actividades_en_recursos', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Actividades por Recurso'
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

echo $string;

?>