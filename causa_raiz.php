

<select id="seleccion" name="proyecto" class="txt_proy" required >
				<option  disabled selected value="seleccionar proyecto">- Seleccionar Proyecto</option>
                <?php
                    
                    include_once "Conexion_MySql.php";
					
                    $sql = "SELECT projectname, projectstatus  FROM vtiger_project
inner join vtiger_crmentity on vtiger_project.projectid = vtiger_crmentity.crmid
WHERE projectstatus in ('En Progreso','En liberacion') and vtiger_crmentity.deleted = 0 order by projectname";
                   
                    $rec =$con->query($sql);
                 
                    
                    while ($row = mysqli_fetch_assoc($rec)) {
                    	$proyecto = $row['projectname'];
                    	$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
                    	$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
                    	$proyecto = str_replace($search, $replace, $proyecto);
                        echo "<option  name='".$row['projectstatus'] ."' value='" .$proyecto . "'>" . utf8_encode($row['projectname']) .  "</option>";
                    }
                    ?>                            
</select> 



