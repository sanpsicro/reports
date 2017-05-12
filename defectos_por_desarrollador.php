<?php
error_reporting(ALL);
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

  

// 	$sql = "select  Asignado_A,GROUP_CONCAT(Total_Defectos SEPARATOR '|') as cantidad,GROUP_CONCAT(CA SEPARATOR '|') as severidad from
// (select 
// 			VD.cf_784 AS CA,	COUNT(VD.defecto) Total_Defectos,
// 				CONCAT(VU.first_name,' ',VU.last_name) Asignado_A
// 			from vtiger_crmentity VC
// 			inner join vtiger_defectos VD
// 			inner join vtiger_users VU
// 			inner join vtiger_project VP
// 			on 1=1
// 			and VD.defectosid = VC.crmid
// 			and VC.smownerid = VU.id
// 			and VD.proyecto_id = VP.projectid
// 			where 1=1
// 			AND  VD.cf_817 not IN  ('Rechazado','Diferido','Cerrado') and VC.deleted = 0" // previamente era cualquier defecto que no estuviera en los estados anteriores
// 			." and VP.projectname = '".$proyecto."'
// 			group by CA, VU.first_name

 
//              UNION
            
//          select 
// 			VD.cf_784 AS CA,	COUNT(VD.defecto) Total_Defectos,
// 				VG.groupname Asignado_A
// 			from vtiger_crmentity VC
// 			inner join vtiger_defectos VD
// 			inner join vtiger_groups VG
// 			inner join vtiger_project VP
// 			on 1=1
// 			and VD.defectosid = VC.crmid
// 			and VC.smownerid = VG.groupid
// 			and VD.proyecto_id = VP.projectid
// 			where 1=1
// 			AND  VD.cf_817  not IN ('Rechazado','Diferido','Cerrado') and VC.deleted = 0 "
// 			."and VP.projectname = '".$proyecto."'
// 			group by CA,VG.groupname
// 			) as x
            
//             group by Asignado_A";
	
$sql = "
		select  Asignado_A,GROUP_CONCAT(Total_Defectos SEPARATOR '|') as cantidad,GROUP_CONCAT(CA SEPARATOR '|') as severidad from
(select 
			VD.cf_784 AS CA,	COUNT(VD.defecto) Total_Defectos,
				CONCAT(VU.first_name,' ',VU.last_name) Asignado_A
			from vtiger_crmentity VC
			inner join vtiger_defectos VD
			inner join vtiger_users VU
			inner join vtiger_project VP
            inner join vtiger_defectoscf
 			on 1=1
			and VD.defectosid = VC.crmid
			and VC.smownerid = VU.id
			and VD.proyecto_id = VP.projectid
 and VD.defectosid = vtiger_defectoscf.defectosid
			where 1=1
			AND  VD.cf_817 not IN  ('Rechazado','Diferido','Cerrado') and VC.deleted = 0
			and VP.projectname = '".$proyecto."'
 and vtiger_defectoscf.cf_894 like '".$depto."'
			group by CA, VU.first_name

 
             UNION
            
         select 
			VD.cf_784 AS CA,	COUNT(VD.defecto) Total_Defectos,
				VG.groupname Asignado_A
			from vtiger_crmentity VC
			inner join vtiger_defectos VD
			inner join vtiger_groups VG
			inner join vtiger_project VP
 inner join vtiger_defectoscf
			on 1=1
			and VD.defectosid = VC.crmid
			and VC.smownerid = VG.groupid
			and VD.proyecto_id = VP.projectid
 and VD.defectosid = vtiger_defectoscf.defectosid
			where 1=1
			AND  VD.cf_817  not IN ('Rechazado','Diferido','Cerrado') and VC.deleted = 0 
			and VP.projectname = '".$proyecto."'
  and vtiger_defectoscf.cf_894 like '".$depto."'
			group by CA,VG.groupname
			) as x
            
            group by Asignado_A";

	
		
    $result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
   
		//echo "Se ejecuto la consulta";
		//return $resultado;
$categories="";

$chainC = "";
$chainM = "";
while($row = mysqli_fetch_assoc($result)){
	$dataC = "0";
	$dataM="0";
	
	$categories = $categories . "'" .$row['Asignado_A'] . "'," ;
	
	if(strpos($row['severidad'],"|")){
		$sevArray= explode("|", $row['severidad']);
		$cantArray = explode("|",$row['cantidad']);
		
		
		
		
	$i=0;
	foreach($sevArray as $value) {
		switch ($value) {
			case 'Alta':
				$dataC = $dataC + (int)$cantArray[$i] ;
				break;
			case 'Baja':
				$dataM = $dataM +  (int)$cantArray[$i] ;
				break;
			case 'Crítico':
				$dataC = $dataC + (int)$cantArray[$i] ;
				break;
			case 'Media':
				$dataM = $dataM +  (int)$cantArray[$i] ;
				break;
		}
		
		$i= $i+1;
	} 	
		
		
		
	}else{
		
		
			switch ($row['severidad']) {
				case 'Alta':
					$dataC = $dataC +  (int)$row['cantidad'];
					
					break;
				case 'Baja':
					
					$dataM = $dataM + (int)$row['cantidad'];
					break;
				case 'Crítico':
					$dataC = $dataC +  (int)$row['cantidad'];
					
					break;
				case 'Media':
				
					$dataM = $dataM + (int)$row['cantidad'];
					break;
			}
		
		
	}
	
	
	$chainC = $chainC . $dataC . ",";
	$chainM = $chainM . $dataM . ",";
	 
	 
}	

$categories=substr_replace($categories,"",-1);
$chainC = substr_replace($chainC,"",-1);
$dataM = substr_replace($dataM,"",-1);


/*$array_consulta=substr_replace($array_consulta,"",-1);*/
	
/*	$string = "
  
     google.charts.setOnLoadCallback(defectos_por_desarrollador);

      function defectos_por_desarrollador() {
        var data = new google.visualization.arrayToDataTable([
		['Desarrollador','Numero de defectos'],
		". $array_consulta ." 
			]);

        var options = {
          title: 'Defectos asignados a',
          width: 450,
          legend: { position: 'left' },
          //chart: { subtitle: 'popularity by percentage' },
          axes: {
            x: {
              10: { side: 'top', label: 'Desarrollador'} // Top x-axis.
            }
          },
          bar: { groupWidth: '35%' }
				
        };
				


        var chart2 = new google.charts.Bar(document.getElementById('defectos_asignados_a'));
        // Convert the Classic options to Material options.
        chart2.draw(data, google.charts.Bar.convertOptions(options));
      };
	  

";*/

/*$string =  "$(function () {
    Highcharts.chart('defectos_asignados_a', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defectos'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -90,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
		
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Numero Defectos'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Defectos asignados a: <b>{point.y:.0f} </b>'
        },
        series: [{
            name: 'Defectos por desarrollador',
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
			" ;*/


$string = "$(function () {
    Highcharts.chart('defectos_asignados_a', {
        chart: {
            type: 'column'
        },
        title: {
            text: ' Defectos agrupados por \"criticos-altos\" y \"medios-bajos\" por recurso'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                ". $categories ."
            ],
            crosshair: true,
            labels: {
            rotation: -90
        }   		
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
            name: 'Criticos y altos ',
            data: [". $chainC."],
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
            name: 'Medios y bajos',
            data: [".$chainM."],
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