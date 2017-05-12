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

	/*$sql = "SELECT veces_reabierto, count(veces_reabierto) as reabiertos  FROM
(
SELECT VP.projectname,VL.ID_defecto ,COUNT(VL.ID_defecto) AS veces_reabierto FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_defectos AS VD
INNER JOIN vtiger_defectos_laststatus AS VL
ON VP.projectid=VC.crmid
AND VC.crmid=VR.crmid
AND VR.relcrmid=VD.defectosid
AND VD.cf_859=VL.ID_defecto
WHERE VP.projectname = '".$proyecto."'
AND VL.estado_nuevo = 'Reabierto'
GROUP BY VL.ID_defecto
    ) AS T 
    
 GROUP BY veces_reabierto
 order by reabiertos			
			"; */

$sql = "select Count(cf_794) reabiertos,cf_794-1 as veces_reabierto from vtiger_project as T1  
inner join vtiger_defectos as  T2 on T1.projectid=T2.proyecto_id
inner join vtiger_defectoscf on T2.defectosid = vtiger_defectoscf.defectosid
where projectname= '".$proyecto."' and cf_794 >= 2
and vtiger_defectoscf.cf_894 like '".$depto."'
group by cf_794";
		
	
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta";
		//return $resultado;
		
$array_consulta="";
while($row = mysqli_fetch_assoc($result))
{
$array_consulta=$array_consulta . "[".$row['veces_reabierto']." , ".$row['reabiertos']."],";
}

$array_consulta = substr_replace($array_consulta,"",-1);


	
	$string =  "$(function () {
    Highcharts.chart('conteoReabiertos', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total de Defectos reabiertos'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
			 title: {
                text: 'Número de reaperturas'
            },
            labels: {
                rotation: 0,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
			
        },
        yAxis: {
			allowDecimals: false,
            min: 0,
            title: {
                text: 'Conteo de Defectos'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Defectos: <b>{point.y:.0f} </b>'
        },
        series: [{
            name: 'Defectos Reabiertos',
            data: [ "  . $array_consulta .  " ],
            dataLabels: {
                enabled: true,
                rotation: 0,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.0f}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
});
			" ;
  
/*$string = 	"		
     google.charts.setOnLoadCallback(conteoReabiertos);

      function conteoReabiertos() {
        var data = new google.visualization.arrayToDataTable([
		['Numero de defectos','Numero de veces reabierto'],
		". $array_consulta ." 
			]);

        var options = {
          title: 'Conteo de reabiertos',
          width: 450,
          legend: { position: 'left' },
          //chart: { subtitle: 'popularity by percentage' },
          axes: {
            x: {
              10: { side: 'top', label: 'Numero de defectos'} // Top x-axis.
            }
          },
          bar: { groupWidth: '35%' }
        };

        var chart2 = new google.charts.Bar(document.getElementById('conteoReabiertos'));
        // Convert the Classic options to Material options.
        chart2.draw(data, google.charts.Bar.convertOptions(options));
      };
	  

"; */


echo $string;

?>