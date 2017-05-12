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

	$sql = "
SELECT cf_862 AS causa_raiz,COUNT(cf_862) AS Totales FROM vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VE
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VC.crmid=VE.crmid
AND VE.relcrmid=VD.defectosid 
AND VF.defectosid=VD.defectosid
WHERE VE.relmodule='Defectos'
AND label='".$proyecto."'
AND VD.cf_817 NOT IN ('Cerrado','Diferido','Rechazado')
and VF.cf_894 like '".$depto."'
GROUP BY cf_862";
		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	/*$array_consulta=$array_consulta ."['". $row['causa_raiz']."',".$row['Totales']."],";*/
	
	$array_consulta=$array_consulta ."{name:'". $row['causa_raiz']."',y:".$row['Totales']."},";
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
	
	/*$string = "

   google.charts.setOnLoadCallback(DefectosPorCausaRaiz);
   function DefectosPorCausaRaiz() { 
   var data = google.visualization.arrayToDataTable([ 
   ['Defectos','Totales'],
   ". $array_consulta ." 
   ]); 
   var options = { title: 'Defectos por causa raiz',
   pieSliceText:  'value-and-percentage',
   pieSliceTextStyle: {color: 'white', fontName: 'arial', fontSize: 10}		
   }; 
   var chart = new google.visualization.PieChart(document.getElementById('DefectosPorCausaRaiz')); 
   chart.draw(data, options); 
   };


";*/

$string = "$(function () {
    Highcharts.chart('DefectosPorCausaRaiz', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Defectos Abiertos por causa raiz'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.0f}%</b>'
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