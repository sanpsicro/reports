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
		$depto = "vtiger_defectoscf.cf_894 LIKE Ciclo ".$depto. "or vtiger_defectoscf.cf_894 LIKE Regresion ". $depto." or vtiger_defectoscf.cf_894 LIKE smoketest" ;
		break;
	case "UAT":
		$depto = "vtiger_defectoscf.cf_894 LIKE Ciclo ".$depto. "or vtiger_defectoscf.cf_894 LIKE Regresion ". $depto ;
		break;
}

/*$sql = "select *, sum(defectos_asoc) as total from (

SELECT vtiger_crmentity.smownerid, vtiger_role.rolename as equipo ,COUNT(smownerid) as defectos_asoc
 FROM `vtiger_crmentityrel` 
 inner JOIN
vtiger_project as VP
inner join 
vtiger_crmentity
inner join 
vtiger_user2role
inner JOIN
vtiger_role
on  vtiger_crmentityrel.crmid = VP.projectid  
and vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid 
and vtiger_crmentity.smownerid = vtiger_user2role.userid 
and vtiger_user2role.roleid = vtiger_role.roleid
where  VP.projectname = '".$proyecto."'  and module = 'Project' and relmodule = 'Defectos' group by smownerid

union 

select vtiger_crmentity.smownerid, vtiger_role.rolename as equipo, COUNT(smownerid) as defectos_asoc from vtiger_crmentityrel
inner JOIN vtiger_project as VP
inner join vtiger_crmentity
inner join vtiger_group2role
inner join vtiger_role
on  vtiger_crmentityrel.crmid = VP.projectid
and vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid 
and vtiger_crmentity.smownerid = vtiger_group2role.groupid 
and vtiger_group2role.roleid = vtiger_role.roleid
where VP.projectname = '".$proyecto."' and vtiger_crmentityrel.relmodule = 'Defectos' and  vtiger_crmentityrel.module = 'Project') as t1 where smownerid is not null group by equipo order by total";*/

$sql = "select *, sum(defectos_asoc) as total from (

SELECT  vtiger_crmentity.smownerid, vtiger_role.rolename as equipo ,COUNT(smownerid) as defectos_asoc
 FROM 
 `vtiger_crmentityrel` 
 inner JOIN
vtiger_project as VP
inner join 
vtiger_crmentity
inner join 
vtiger_user2role
inner JOIN
vtiger_role
inner join 
vtiger_defectos
inner join
vtiger_defectoscf  
on   vtiger_crmentityrel.crmid = VP.projectid  
and vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid 
and vtiger_crmentity.smownerid = vtiger_user2role.userid 
and vtiger_user2role.roleid = vtiger_role.roleid
and vtiger_crmentityrel.relcrmid = vtiger_defectos.defectosid
and vtiger_defectos.defectosid = vtiger_defectoscf.defectosid
where  VP.projectname = '".$proyecto."' and vtiger_defectoscf.cf_894 like '".$depto."' and module = 'Project' and relmodule = 'Defectos' and vtiger_defectos.cf_817 not in ('Cerrado','Diferido','Rechazado') group by smownerid

union 

select  vtiger_crmentity.smownerid, vtiger_role.rolename as equipo, COUNT(smownerid) as defectos_asoc from
vtiger_crmentityrel
inner JOIN vtiger_project as VP
inner join vtiger_crmentity
inner join vtiger_group2role
inner join vtiger_role
inner JOIN vtiger_defectos
inner join vtiger_defectoscf
on   vtiger_crmentityrel.crmid = VP.projectid
and vtiger_crmentityrel.relcrmid = vtiger_crmentity.crmid 
and vtiger_crmentity.smownerid = vtiger_group2role.groupid 
and vtiger_group2role.roleid = vtiger_role.roleid
and vtiger_crmentityrel.relcrmid = vtiger_defectos.defectosid
and vtiger_defectos.defectosid = vtiger_defectoscf.defectosid    
where VP.projectname = '".$proyecto."' and vtiger_defectoscf.cf_894 like '".$depto."' and vtiger_crmentityrel.relmodule = 'Defectos' and vtiger_defectos.cf_817 not in ('Cerrado','Diferido','Rechazado') and  vtiger_crmentityrel.module = 'Project') as t1 where smownerid is not null group by equipo order by total";

$result = mysqli_query($con,$sql);


if (!$result){
	die("ERROR AL EJECUTAR LA CONSULTA: ");
}

//echo "Se ejecuto la consulta";
//return $resultado;

$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."['".$row['equipo']."',
									   ".$row['total']."],";
}

$array_consulta=substr_replace($array_consulta,"",-1);

/*$string = "

     google.charts.setOnLoadCallback(defectos_por_equipo);

      function defectos_por_equipo() {
        var data = new google.visualization.arrayToDataTable([
		['equipo','Numero de defectos'],
		". $array_consulta ."
			]);

        var options = {
          title: 'Defectos por Equipo',
          width: 450,
          legend: { position: 'left' },
          //chart: { subtitle: 'popularity by percentage' },
          axes: {
            x: {
              10: { side: 'top', label: 'equipo'} // Top x-axis.
            }
          },
          bar: { groupWidth: '35%' }
        };

        var chart2 = new google.charts.Bar(document.getElementById('defectos_por_equipo'));
        // Convert the Classic options to Material options.
        chart2.draw(data, google.charts.Bar.convertOptions(options));
      };
	 

";*/

$string =  "$(function () {
    Highcharts.chart('defectos_por_equipo', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Defectos Abiertos por equipo'
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
                    fontFamily: 'Arial'
                }
            }
		
        },
        yAxis: {
		  allowDecimals: false,
            min: 0,
            title: {
                text: 'Defectos '
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Defectos: <b>{point.y:.0f} </b>'
        },
        series: [{
            name: 'Defectos por equipo',
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
                    fontFamily: 'Arial'
                }
            }
        }]
    });
});
			" ;

echo $string;



?>