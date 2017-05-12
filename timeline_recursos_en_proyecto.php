<?php
$proyecto = $_GET['proyecto'];
require_once("Conexion_MySql.php");

/*$sql = "select VP.projectname,VR.module,VC.crmid,VC.smownerid,CONCAT(VU.first_name,' ',VU.last_name) as recurso, VP.startdate as inicial , VP.targetenddate as final from vtiger_project as VP
inner join vtiger_crmentityrel as VR
inner join vtiger_crmentity AS VC
inner join vtiger_users as VU
on VP.projectid = VR.crmid
and VR.relcrmid = VC.crmid
and VC.smownerid = VU.id
where VR.relmodule ='ProjectTask' and VP.projectstatus not in ('Cancelado','Terminado','Detenido' ) group by VP.projectname order by VC.smownerid  
			";*/

$sql ="select VP.projectname,VR.module,VC.crmid,VC.smownerid,CONCAT(VU.first_name,' ',VU.last_name) as recurso, VP.startdate as inicial , ifnull(VP.targetenddate,'2017-03-31') as final from vtiger_project as VP
inner join vtiger_crmentityrel as VR
inner join vtiger_crmentity AS VC
inner join vtiger_users as VU
inner join vtiger_projecttask as VT
on VP.projectid = VR.crmid
and VR.relcrmid = VC.crmid
and VC.smownerid = VU.id
and VR.relcrmid = VT.projecttaskid
where VR.relmodule ='ProjectTask' and VP.projectstatus not in ('Cancelado','Terminado','Detenido' ) and VC.deleted = 0 and VT.projecttaskstatus in ('In Progress') group by VP.projectname, recurso order by VC.smownerid";


$result = mysqli_query($con,$sql);


if (!$result){
	die("ERROR AL EJECUTAR LA CONSULTA: ");
}

//echo "Se ejecuto la consulta";
//return $resultado;

$array_consulta="";

while($row = mysqli_fetch_assoc($result))
{
	$fInicial = "new Date(" .str_replace('-',',',$row['inicial']) . ")";
	$fFinal = "new Date(" .str_replace('-',',',$row['final']) . ")";
	$difDate_proyect =$row['projectname'] . " Periodo:" . date("d-m-Y", strtotime($row['inicial'])). " - " . date("d-m-Y", strtotime($row['final'])) ;
	
	$array_consulta=$array_consulta . "['".$row['recurso']."', '".$difDate_proyect."' ,'". $row['projectname']. "', ".$fInicial."," .$fFinal ."],";
}

$array_consulta = substr_replace($array_consulta,"",-1);



/*$string =  " google.charts.load('current', {'packages':['timeline']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var container = document.getElementById('timeline_recursos');
        var chart = new google.visualization.Timeline(container);
        var dataTable = new google.visualization.DataTable();

        dataTable.addColumn({ type: 'string', id: 'President' });
        dataTable.addColumn({ type: 'date', id: 'Start' });
        dataTable.addColumn({ type: 'date', id: 'End' });
        dataTable.addRows([
          ". $array_consulta . "]);

        chart.draw(dataTable);
      }	" ;*/

      $string = "  google.charts.load(\"current\", {packages:[\"timeline\"]});
  google.charts.setOnLoadCallback(drawChart);
  function drawChart() {

    var container = document.getElementById('timeline_recursos');
    var chart = new google.visualization.Timeline(container);
    var dataTable = new google.visualization.DataTable();
    dataTable.addColumn({ type: 'string', id: 'Recurso' });
    dataTable.addColumn({ type: 'string', id: 'Proyecto' });
     dataTable.addColumn({ type: 'string', role: 'tooltip' });  		
    dataTable.addColumn({ type: 'date', id: 'Start' });
    dataTable.addColumn({ type: 'date', id: 'End' });
    dataTable.addRows([
      ".$array_consulta."
    ]);

    chart.draw(dataTable);
  }";



echo $string;






?>