<?php
$proyecto = $_GET['proyecto'];
require_once("Conexion_MySql.php");

	$sql = "SELECT V2.projectstatus,COUNT(V1.label) AS total_actividades, V1.label AS proyecto , V0.* FROM `vtiger_crmentityrel` AS V0 
INNER JOIN vtiger_crmentity AS V1 ON V0.crmid = V1.crmid
INNER JOIN vtiger_project AS V2 ON V0.crmid=V2.projectid
INNER JOIN vtiger_projecttask AS V3 ON V0.relcrmid=V3.projecttaskid
WHERE V0.module='Project' AND V0.relmodule='projecttask' AND V2.projectstatus NOT IN ('','Terminado','Cancelado') AND V3.projecttaskstatus NOT IN ('Completed','Canceled')
GROUP BY V1.label 
ORDER BY crmid";
		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	/*$array_consulta=$array_consulta ."['". $row['proyecto']."',".$row['total_actividades']."],";*/
	$array_consulta=$array_consulta ."{name:'". $row['proyecto']."',y:".$row['total_actividades']."},";
}

$array_consulta=substr_replace($array_consulta,"",-1);


	
	/*$string = "

   google.charts.setOnLoadCallback(actividades_en_proyecto);
   function actividades_en_proyecto() { 
   var data = google.visualization.arrayToDataTable([ 
   ['Actividades','Totales'],
   ". $array_consulta ." 
   ]); 
   var options = { title: 'Actividades por proyecto',
   pieSliceText: 'value'}; 
   var chart = new google.visualization.PieChart(document.getElementById('actividades_en_proyecto')); 
   chart.draw(data, options); 
   };


";*/

$string = "$(function () {
    Highcharts.chart('actividades_en_proyecto', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Actividades por proyecto'
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
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
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