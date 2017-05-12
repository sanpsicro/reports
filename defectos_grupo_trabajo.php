<?php
$proyecto = $_GET['proyecto'];
//$proyecto = 'SAO Bancario';
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

	/*$sql = "SELECT IF(grupo IS NULL or grupo = '', 'No asignado', grupo) as grupo ,ifnull(total_abiertos,0) AS total_abiertos,ifnull(total_cerrados,0) AS total_cerrados FROM 
(
SELECT  V1.cf_884 AS grupo, V2.total_abiertos FROM

(
SELECT cf_884 FROM `vtiger_cf_884`  ORDER BY sortorderid
) AS V1

LEFT OUTER JOIN

(
SELECT COUNT(VF.cf_884) AS total_abiertos,VF.cf_884 FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VP.projectid = VC.crmid
AND VC.crmid= VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.defectosid=VF.defectosid
WHERE VP.projectname='".$proyecto."' 
AND VD.cf_817 NOT IN ('Cerrado','Diferido','Rechazado') and VC.deleted = 0
GROUP BY VF.cf_884
    ) AS V2
    
  
ON V1.cf_884 = V2.cf_884
    ) AS K1
    
LEFT OUTER JOIN     


(
SELECT COUNT(VF.cf_884) AS total_cerrados,VF.cf_884 FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VP.projectid = VC.crmid
AND VC.crmid= VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.defectosid=VF.defectosid
WHERE VP.projectname='".$proyecto."'  
AND VD.cf_817  IN ('Cerrado','Diferido','Rechazado') and VC.deleted = 0
GROUP BY VF.cf_884
    ) AS K2
    
ON K1.grupo=K2.cf_884
WHERE total_abiertos IS NOT null or total_cerrados IS NOT null";*/

$sql = "select if( cf_884 = '','No Asignado',cf_884) as grupo ,SUM((CASE WHEN cf_817 in ('Abierto','Asignado','Desplegado en Ambiente QA','Devolver','Reabierto','Reparado') THEN num ELSE 0 END)) AS  total_abiertos , SUM((CASE WHEN cf_817 in ('Cerrado','Diferido','Rechazado') THEN num ELSE 0 END)) AS  total_cerrados from (
select projectname,vtiger_defectos.cf_817 ,COUNT(vtiger_defectos.cf_817) as num, cf_884  from vtiger_project
inner join  vtiger_defectos on vtiger_project.projectid = vtiger_defectos.proyecto_id
inner join  vtiger_defectoscf on vtiger_defectos.defectosid = vtiger_defectoscf.defectosid
inner join  vtiger_crmentity on vtiger_defectos.defectosid = vtiger_crmentity.crmid 
where projectname = '".$proyecto."' and  vtiger_crmentity.deleted = 0 
and vtiger_defectoscf.cf_894 like '".$depto."'
group by  vtiger_defectos.cf_817, cf_884) as t
group by cf_884";
		
$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta";
		//return $resultado;
		

		
$array_consulta= "";
$categories="";
while($row = mysqli_fetch_assoc($result))
{
	/*$array_consulta=$array_consulta ."['".$row['grupo']."',".$row['total_abiertos'].",
									   ".$row['total_cerrados']."],";*/
	
	$categories =$categories . "'" . $row['grupo']. "',";
	$dataOpen = $dataOpen . $row['total_abiertos'] .",";
	$dataClosed = $dataClosed . $row['total_cerrados'] .",";
	
}
$dataClosed = substr_replace($dataClosed ,"",-1);
$dataOpen = substr_replace($dataOpen,"",-1);
$categories = substr_replace($categories,"",-1);
/*$array_consulta=substr_replace($array_consulta,"",-1);*/
	
	/*$string = "
  
    google.charts.setOnLoadCallback(grupo_trabajo_barras);
function grupo_trabajo_barras() {
	
	var data = google.visualization.arrayToDataTable([
			['Grupo de trabajo', 'Abiertos', 'Cerrados'],
			".$array_consulta."
			]);
			

	var options = {
		chart: {
			title: 'Defectos por grupo trabajo',
			subtitle: '',
		}
	};

	var chart = new google.charts.Bar(document.getElementById('grupo_trabajo_barras'));

	chart.draw(data, options);
}

";*/




$string = "$(function () {
    Highcharts.chart('grupo_trabajo_barras', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total de Defectos por grupo trabajo'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                ". $categories ."
            ],
            crosshair: true
        },
        yAxis: {
            allowDecimals: false,
            min: 0,
            title: {
                text: 'Defectos'
            }
        },
        tooltip: {
            headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
            pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
                '<td style=\"padding:0\"><b>{point.y:.0f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Abierto',
            data: [". $dataOpen."],
             dataLabels: {
                enabled: true,
                rotation: 0,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.0f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Arial'
                }
            }		

        }, {
            name: 'Cerrado',
            data: [".$dataClosed."],
             dataLabels: {
                enabled: true,
                rotation: 0,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.0f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Arial'
                }
            }		

        }]
    });
});";

echo $string;

?>

