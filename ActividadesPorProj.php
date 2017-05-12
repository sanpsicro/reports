<?php

require_once("Conexion_MySql.php");

$sql = "SELECT concat(V4.first_name,' ',V4.last_name) AS recurso,V3.projectstatus AS estatus_proyecto,V3.projectname AS proyecto ,V0.label AS actividad,V1.projecttaskstatus AS estatus_actividad, DATE_FORMAT(V1.startdate,'%d-%m-%Y') as inicial, DATE_FORMAT(V1.enddate,'%d-%m-%Y') as final, DATE_FORMAT(VX.cf_773,'%d-%m-%Y') as final2 FROM vtiger_crmentity AS V0 
INNER JOIN vtiger_projecttask AS V1 ON  V0.crmid=V1.projecttaskid
INNER JOIN vtiger_crmentityrel AS V2 ON V1.projecttaskid=V2.relcrmid
INNER JOIN vtiger_project AS V3 ON V1.projectid=V3.projectid
INNER JOIN vtiger_users AS V4 on V0.smownerid=V4.id
inner join vtiger_projecttaskcf as VX on V1.projecttaskid = VX.projecttaskid 
WHERE V3.projectstatus NOT IN ('Cancelado','Terminado','Detenido') AND V1.projecttaskstatus NOT IN ('Canceled','Completed') and V0.deleted = 0
ORDER BY proyecto";

$result = $con->query($sql);

$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."[
	'". $row['proyecto']."',
	'".$row['estatus_proyecto']."',
	'".addslashes($row['recurso'])."',
	'".$row['actividad']."',
	'".$row['estatus_actividad']."' , '".$row['inicial'] ."','".$row['final2'] . "'],";
}



echo"
google.charts.setOnLoadCallback(actividades_por_proyecto);

function actividades_por_proyecto() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Proyecto');
		data.addColumn('string', 'Estatus del proyecto');
	data.addColumn('string', 'Recurso');
	data.addColumn('string', 'Actividad');
	data.addColumn('string', 'Estatus actividad');
    data.addColumn('string', 'fecha inicial');
		data.addColumn('string', 'fecha final prevista');
	
   
	data.addRows([
	".$array_consulta ."
	]);

	var table = new google.visualization.Table(document.getElementById('actividades_por_proyecto'));

	table.draw(data, {showRowNumber: true, width: '', height: ''});
}
";


?>