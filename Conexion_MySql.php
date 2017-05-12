<?php 

	/* mandamos llamar la función que conecta con el mysql */
	/*conectar_mysql("localhost","root","","test");*/
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "crmdatabase";	
	
	$con = new mysqli($host,$user,$pass);
		if (! $con)	{
		die ("ERROR EN LA CONEXION CON MYSQL: ".connect_error);
		}
		//echo "Connected successfully IVAN";
	//return $con;
		
	$base = mysqli_select_db($con,$db);
		if(!$base){
		die ("ERROR AL CONECTAR CON LA BASE DE DATOS: ".connect_error);
	}
	//echo "Se selecciono correctamente la BASE de DATOS";
	return $base;
	
	
	
	/* ahora mandamos la sentencia sql a la funcion que ejecuta
	ejecutar_sql($con,$sql);*/
	
	


 ?>