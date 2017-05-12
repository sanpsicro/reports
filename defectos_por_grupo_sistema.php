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

	/*$sql = "SELECT  VF.cf_886 as grupo_sistema , COUNT(VF.cf_886) as total FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VP.projectid=VC.crmid
AND VC.crmid=VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.defectosid=VF.defectosid
WHERE VP.projectname = '".$proyecto."'
AND VD.cf_782 NOT IN ('Cerrado','Diferido','Rechazado')
GROUP BY VF.cf_886";*/

$sql = "select cf_886 as grupo_sistema, count(cf_886) as total from vtiger_project
inner join vtiger_defectos on vtiger_project.projectid = vtiger_defectos.proyecto_id 
inner join vtiger_defectoscf on vtiger_defectos.defectosid = vtiger_defectoscf.defectosid
inner join vtiger_crmentity on vtiger_defectos.defectosid = vtiger_crmentity.crmid
where vtiger_defectos.cf_817 not in ('Cerrado','Diferido','Rechazado') and vtiger_project.projectname = '".$proyecto."' and vtiger_crmentity.deleted = 0
and vtiger_defectoscf.cf_894 like '".$depto."'
group by cf_886";		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	/*$array_consulta=$array_consulta ."['". $row['grupo_sistema']."',".$row['total']."],";*/
	$array_consulta=$array_consulta ."{name:'". $row['grupo_sistema']."',y:".$row['total']."},";
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

   google.charts.setOnLoadCallback(defectosGrupoSistema);
   function defectosGrupoSistema() { 
   var data = google.visualization.arrayToDataTable([ 
   ['Defectos','Totales'],
   ". $array_consulta ." 
   ]); 
   var options = { title: 'Defectos por grupo sistema',
   pieSliceText:  'value-and-percentage',
   pieSliceTextStyle: {color: 'white', fontName: 'arial', fontSize: 10}		
   }; 
   var chart = new google.visualization.PieChart(document.getElementById('defectosGrupoSistema')); 
   chart.draw(data, options); 
   };


";*/

$string = "$(function () {
    Highcharts.chart('defectosGrupoSistema', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Defectos Abiertos por grupo de aplicaciones'
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