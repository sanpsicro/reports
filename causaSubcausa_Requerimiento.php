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

	/*$sql = "SELECT IF(k.cf_862 = 'Reque', 'Requerimiento', k.cf_862) as causa_raiz, ifnull(total_cerrados,0) as total_cerrados,ifnull(total_abiertos,0) as total_abiertos FROM 
(

SELECT p.cf_862,total_cerrados FROM 


(SELECT substring_index(cf_862,'rimiento',1) as cf_862 FROM vtiger_cf_862 ORDER BY cf_862id LIMIT 6) as p

LEFT OUTER JOIN 

(
SELECT COUNT(t.causa_raiz) AS total_cerrados ,t.*, substring_index(causa_raiz,'rimento',1) as cf_862 FROM ( 
SELECT  VD.cf_784,VP.projectid,substring_index(cf_862,':',1) AS causa_raiz  FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VP.projectid=VC.crmid
AND VC.crmid=VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.defectosid=VF.defectosid
WHERE VR.relmodule='Defectos'
AND label='".$proyecto."'
AND VD.cf_817  IN ('Cerrado','Diferido','Rechazado')) AS t
GROUP BY t.causa_raiz) AS p2

ON p.cf_862= p2.cf_862
) AS k

LEFT OUTER JOIN 

(
SELECT p.cf_862,total_abiertos FROM 


(SELECT substring_index(cf_862,'rimiento',1) as cf_862 FROM vtiger_cf_862 ORDER BY cf_862id LIMIT 6) as p

LEFT OUTER JOIN 

(
SELECT COUNT(t.causa_raiz) AS total_abiertos ,t.*, substring_index(causa_raiz,'rimento',1) as cf_862 FROM ( 
SELECT  VD.cf_784,VP.projectid,substring_index(cf_862,':',1) AS causa_raiz  FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectoscf AS VF
ON VP.projectid=VC.crmid
AND VC.crmid=VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.defectosid=VF.defectosid
WHERE VR.relmodule='Defectos'
AND label='".$proyecto."'
AND VD.cf_817  NOT IN ('Cerrado','Diferido','Rechazado')) AS t
GROUP BY t.causa_raiz) AS p2

ON p.cf_862= p2.cf_862
) AS k2

ON k.cf_862=k2.cf_862 
ORDER BY causa_raiz"; */

$sql = "select causa_raiz , SUM(abiertos) as total_abiertos, SUM(cerrados) as total_cerrados from (
select causa as causa_raiz, (case when cf_817 in ('Abierto','Asignado','Desplegado en Ambiente QA','Devolver','Reabierto','Reparado') then totales else 0 end) as abiertos ,   (case when cf_817 in ('Cerrado','Diferido','Rechazado') then totales else 0 end) as cerrados  from (
select projectname,vtiger_defectos.cf_817, Count(vtiger_defectos.cf_817) as totales  ,substring_index(cf_862,':',-1) as causa from vtiger_project 
inner join vtiger_defectos on  vtiger_project.projectid = vtiger_defectos.proyecto_id
inner join vtiger_defectoscf on vtiger_defectos.defectosid = vtiger_defectoscf.defectosid
 inner join vtiger_crmentity on vtiger_defectos.defectosid = vtiger_crmentity.crmid 
where vtiger_project.projectname = '".$proyecto."' and vtiger_crmentity.deleted = 0
and vtiger_defectoscf.cf_894 like '".$depto."' and cf_862 like '%Requerimiento:%'
group by causa,vtiger_defectos.cf_817) as t ) as T
group by causa_raiz";
		
$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta";
		//return $resultado;
		
/*$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."['".$row['causa_raiz']."',".$row['total_abiertos'].", 
									   ".$row['total_cerrados']."],";
}

$array_consulta=substr_replace($array_consulta,"",-1);*/
			
			$array_consulta= "";
			$categories="";
			while($row = mysqli_fetch_assoc($result))
			{
				/*$array_consulta=$array_consulta ."['".$row['grupo']."',".$row['total_abiertos'].",
				 ".$row['total_cerrados']."],";*/
			
				$categories =$categories . "'" . $row['causa_raiz']. "',";
				$dataOpen = $dataOpen . $row['total_abiertos'] .",";
				$dataClosed = $dataClosed . $row['total_cerrados'] .",";
			
			}
			$dataClosed = substr_replace($dataClosed ,"",-1);
			$dataOpen = substr_replace($dataOpen,"",-1);
			$categories = substr_replace($categories,"",-1);
	
	/*$string = "
  
    google.charts.setOnLoadCallback(causa_raiz);
function causa_raiz() {
	
	var data = google.visualization.arrayToDataTable([
			['Causa raíz', 'Abiertos', 'Cerrados'],
			".$array_consulta."
			]);
				

	var options = {
		chart: {
			title: 'Defectos por causa raíz',
			subtitle: '',
		}
	};

	var chart = new google.charts.Bar(document.getElementById('causa_raiz_barras'));

	chart.draw(data, options);
}

";*/
	
	
	$string = "$(function () {
    Highcharts.chart('causaRaiz_Requerimiento', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total de Defectos por causa raiz \" Requerimiento \"'
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
            data: [".$dataOpen."]
	
        }, {
            name: 'Cerrado',
            data: [".$dataClosed."]
	
        }]
    });
});";
echo $string;

?>

