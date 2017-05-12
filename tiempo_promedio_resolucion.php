<?php
require_once("Conexion_MySql.php");

$proyecto = $_GET['proyecto'];

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


	$sql = "SELECT AVG(t.horas) AS promedio,t.severidad AS severidad FROM
(
SELECT DATEDIFF(VC2.modifiedtime,VC2.createdtime)*24 AS horas, VD.cf_784 as severidad
FROM vtiger_project AS VP
INNER JOIN vtiger_crmentity AS VC
INNER JOIN vtiger_crmentityrel AS VR
INNER JOIN vtiger_crmentity AS VC2
INNER JOIN vtiger_defectos AS VD
    inner join vtiger_defectoscf
ON VP.projectid = VC.crmid
AND VC.crmid=VR.crmid
AND VR.relcrmid=VC2.crmid
AND VC2.crmid = VD.defectosid
    and VD.defectosid = vtiger_defectoscf.defectosid
WHERE VP.projectname = '".$proyecto."'
AND VD.cf_817 = 'Cerrado' and VC.deleted = 0
    and vtiger_defectoscf.cf_894 like '".$depto."'
    ) AS t 
GROUP BY t.severidad
		 
";
		//limit 50  
	$result = mysqli_query($con,$sql);
	
	
	if (!$result){
			die("ERROR AL EJECUTAR LA CONSULTA: ");
			}
		
		//echo "Se ejecuto la consulta	";
		//return $resultado;
		
$array_consulta= "";
while($row = mysqli_fetch_assoc($result))
{
	$array_consulta=$array_consulta ."[
	'". $row['severidad']."',
	'".$row['promedio']." hrs'],";
}

$array_consulta=substr_replace($array_consulta,"",-1);
	
	$string = "
      google.charts.setOnLoadCallback(tiempoPromedioResol);

      function tiempoPromedioResol() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Severidad');
		data.addColumn('string', 'Tiempo promedio');
	
			
			
        data.addRows([
				   ". $array_consulta ." 
        ]);

        var table = new google.visualization.Table(document.getElementById('tiempoPromedioResol'));

        table.draw(data, {showRowNumber: false, width: '80%', height: '100%'});
	  };
";
echo $string;

?>
