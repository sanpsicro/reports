<?php
$proyecto = $_GET['proyecto'];
require_once("Conexion_MySql.php");

	/*$sql = "select count(name) as total_proyectos, name as nombre, tstatus from (
select V4.projecttaskid, V4.projecttaskstatus as tstatus ,V0.projectname, V2.crmid ,V1.module, V1.relmodule,  V2.smownerid, concat(V3.first_name, ' ' ,V3.last_name ) as name from vtiger_project as V0
left join vtiger_crmentityrel as V1 on V0.projectid = V1.crmid
inner join vtiger_crmentity as V2 on V1.relcrmid = V2.crmid
inner join vtiger_users as V3 on V2.smownerid = V3.id
inner join vtiger_projecttask as V4 on V1.relcrmid = V4.projecttaskid
where V1.module= 'Project' and V1.relmodule='projecttask' and V0.projectstatus in ('En Progreso','En Liberacion') and V2.deleted = 0 and V4.projecttaskstatus in ('Open','In Progress')) as t
group by name
";*/

$sql = "select GROUP_CONCAT(projectname) as proyectos,count(projectname) as total_proyectos, name as nombre, tstatus from (
select V4.projecttaskid, V4.projecttaskstatus as tstatus ,V0.projectname, V2.crmid ,V1.module, V1.relmodule,  V2.smownerid, concat(V3.first_name, ' ' ,V3.last_name ) as name from vtiger_project as V0
left join vtiger_crmentityrel as V1 on V0.projectid = V1.crmid
inner join vtiger_crmentity as V2 on V1.relcrmid = V2.crmid
inner join vtiger_users as V3 on V2.smownerid = V3.id
inner join vtiger_projecttask as V4 on V1.relcrmid = V4.projecttaskid
where V1.module= 'Project' and V1.relmodule='projecttask' and V0.projectstatus in ('En Progreso','En Liberacion') and V2.deleted = 0 and V4.projecttaskstatus in ('In Progress') group by projectname, name) as t group by name";
		
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."['".$row['nombre'].":".$row['proyectos']."', 
									   ".$row['total_proyectos']."],";
}

$array_consulta=substr_replace($array_consulta,"",-1);
	
/*	$string = "
  
     google.charts.setOnLoadCallback(proyecto_por_recurso);

      function proyecto_por_recurso() {
        var data = new google.visualization.arrayToDataTable([
		['Recurso','Numero de proyectos'],
		". $array_consulta ." 
			]);

        var options = {
          title: 'Proyectos por recurso',
          width: 1100,
          legend: { position: 'left' },
          //chart: { subtitle: 'popularity by percentage' },
          axes: {
            x: {
              10: { side: 'top', label: 'Recurso'} // Top x-axis.
            }
          },
          bar: { groupWidth: '25%' }
        };

        var chart2 = new google.charts.Bar(document.getElementById('proyecto_por_recurso'));
        // Convert the Classic options to Material options.
        chart2.draw(data, google.charts.Bar.convertOptions(options));
      };
	  

";*/
	
	
	$string = "$(function () {
    Highcharts.chart('proyecto_por_recurso', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Proyectos por Recurso'
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
                text: 'Proyectos'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Proyectos: <b>{point.y:.0f} </b>'
        },
        series: [{
            name: 'Proyecto',
            data: [ "  . $array_consulta .  " ],
            dataLabels: {
                enabled: true,
                rotation: 0,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y:.0f}', // zero decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
});
			";
	
echo $string;

?>