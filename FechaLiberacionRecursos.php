<?php

require_once("Conexion_MySql.php");

/*$sql = "SELECT VP.projectname as proyecto,VR.crmid, CONCAT(T1.first_name, ' ' ,T1.last_name) AS NombreRec, T2.label, DATE_FORMAT(MAX(T4.cf_773),'%d-%m-%Y')  as FinPre, T3.projecttaskid Taskid FROM vtiger_users AS T1
					INNER JOIN vtiger_crmentity AS T2 ON T1.id = T2.smownerid
					INNER JOIN vtiger_projecttask AS T3 ON T2.crmid = T3.projecttaskid
                    inner join vtiger_crmentityrel as VR on VR.relcrmid = T3.projecttaskid
                    inner join vtiger_project as VP on VR.crmid = VP.projectid
                    inner join vtiger_projecttaskcf as T4 on T3.projecttaskid=T4.projecttaskid
					Where T4.cf_773 is not null  and T3.projecttaskstatus='In Progress' or T3.projecttaskstatus='Open'
                    and T2.deleted = 0
					GROUP BY NombreRec Order by FinPre ASC
					";*/

$sql = "SELECT VP.projectname as proyecto,VR.crmid, CONCAT(T1.first_name, ' ' ,T1.last_name) AS NombreRec, T2.label, DATE_FORMAT(MAX(T4.cf_773),'%d-%m-%Y')  as FinPre, T3.projecttaskid Taskid FROM vtiger_users AS T1
					INNER JOIN vtiger_crmentity AS T2 ON T1.id = T2.smownerid
					INNER JOIN vtiger_projecttask AS T3 ON T2.crmid = T3.projecttaskid
                    inner join vtiger_crmentityrel as VR on VR.relcrmid = T3.projecttaskid
                    inner join vtiger_project as VP on VR.crmid = VP.projectid
                    inner join vtiger_projecttaskcf as T4 on T3.projecttaskid=T4.projecttaskid
					Where T4.cf_773 is not null  and T3.projecttaskstatus in ('In Progress','Abierta') and VP.projectstatus in ('En Progreso','En Liberacion')
                    and T2.deleted = 0
                    GROUP BY NombreRec Order by FinPre ASC";

$result = $con->query($sql);

$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."[
	'". $row['NombreRec']."',
	'".addslashes($row['FinPre'])."'],";
	/*'".$row['label']."',
	'".$row['Taskid']."' ,' ". $row['proyecto']."'],";*/
}



echo"
google.charts.setOnLoadCallback(fecha_lib_recursos);

function fecha_lib_recursos() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Recurso');
	data.addColumn('string', 'Fecha de liberacion');
	" /*data.addColumn('string', 'Actividad');
	data.addColumn('string', 'ID actividad');
    data.addColumn('string', 'Proyecto');*/ . "
	data.addRows([
	".$array_consulta ."
	]);

	var table = new google.visualization.Table(document.getElementById('fecha_lib_recursos'));

	table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
}
";


?>