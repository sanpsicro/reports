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


	$sql = "select
			    VD.cf_859 id_def,
				VD.defectosid Id_Defecto,
				VD.defecto Defecto,
				VD.cf_784 Severidad,
				VD.cf_817 Estado,
				CONCAT(VU.first_name,' ',VU.last_name) Asignado_A,
				VC.createdtime Fecha_Creacion,
			    VF.cf_870 prioridad,
			    VD.cf_790 fin_analisis,
			    VF.cf_892 fin_resolucion
    
			from vtiger_crmentity VC
			inner join vtiger_users VU
			inner join vtiger_defectos VD
			inner join vtiger_project VP
			inner join vtiger_defectoscf VF
           
            on 1=1
		and VC.smownerid = VU.id 
		and VD.defectosid = VC.crmid 
		and VP.projectid = VD.proyecto_id
		and VF.defectosid=VD.defectosid	
		where 1=1
		and VC.setype = 'Defectos'
		and VP.projectname = '".$proyecto."'
		        AND  VD.cf_817 NOT IN ('Rechazado','Diferido','Cerrado') 
			    and  VF.cf_894 like '".$depto."'
        UNION 
 select       
        VD.cf_859 id_def,
        VD.defectosid Id_Defecto,
				VD.defecto Defecto,
				VD.cf_784 Severidad,
				VD.cf_817 Estado,
				VG.groupname Asignado_A,
				VC.createdtime Fecha_Creacion,
                VF.cf_870 prioridad,
			    VD.cf_790 fin_analisis,
			    VF.cf_892 fin_resolucion
				
			from vtiger_crmentity VC
			
			inner join vtiger_defectos VD
			inner join vtiger_project VP
            inner join vtiger_groups VG
			inner join vtiger_defectoscf VF	
            on 1=1
		and VC.smownerid = VG.groupid 
		and VD.defectosid = VC.crmid 
		and VP.projectid = VD.proyecto_id
        and VF.defectosid=VD.defectosid				
		where 1=1
		and VC.setype = 'Defectos'
		and VP.projectname = '".$proyecto."'
				AND  VD.cf_817 NOT IN ('Rechazado','Diferido','Cerrado')
                and  VF.cf_894 like '".$depto."' 
			    
		 
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
	'". $row['id_def']."',
	'".addslashes($row['Defecto'])."',
	'".$row['Severidad']."',
	'".$row['Estado']."',
	'".$row['prioridad']."',
	'".$row['Asignado_A']."',
			'".$row['Fecha_Creacion']."',
			'".$row['fin_analisis']."',
	'".$row['fin_resolucion']."'],";
}

$array_consulta=substr_replace($array_consulta,"",-1);
	
	$string = "
      google.charts.setOnLoadCallback(DefectosCerrados);

      function DefectosCerrados() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Id');
		data.addColumn('string', 'Defecto');
		data.addColumn('string', 'Severidad');
		data.addColumn('string', 'Estado');
			data.addColumn('string', 'Prioridad');
		data.addColumn('string', 'Asignado a');
		    data.addColumn('string', 'Fecha de creacion');
			data.addColumn('string', 'Fecha fin analisis');
			data.addColumn('string', 'Fecha fin resolucion');
			
			
			
        data.addRows([
				   ". $array_consulta ." 
        ]);

        var table = new google.visualization.Table(document.getElementById('defectos_close'));

        table.draw(data, {showRowNumber: false, width: '80%', height: '100%'});
	  };
";
echo $string;

?>