<?php
//Conexion con BD de XAMPP
function conectar()
{
	global $user, $pass, $server, $db;

	$user = "root";
	$pass = "";
	$server = "localhost";
	$db = "permisos";

	$conexion = new mysqli($server, $user, $pass, $db);

	if(mysqli_connect_errno())
	{
		echo "No se puede conectar: ", mysqli_connect_error();
		exit();
	}else
	{
		//echo "Conectado";
	}
	return $conexion;
}

// function odbcCon(){

// 	$dsn = "permisos";
// 	$usuario = "";
// 	$clave = "";

// 	$conDBF = odbc_connect($dsn, $usuario, $clave);

// 	if(!$conDBF){
// 		exit("Error inerperado al conectar con la base de datos!");
// 	}

// 	return $conDBF;

// }


// function csvCon(){

// 	$archivo = fopen("dbf/tblicen02.csv", "r");

// 	if ($archivo == true) {
// 		echo "Conectado";
// 	}else{
// 		exit("Error al conectar");
// 	}

// }

?>