<?php

function conectar(){

//Varibales de conexion

global $server, $user, $pass, $bd;

$server = 'localhost';
$user = 'root';
$pass = '';
$bd = 'proyecto';

//variable de conexion a la base de datos 
//con los datos de las variables

$conexion = new mysqli($server,$user,$pass,$bd);

if(mysqli_connect_errno()){

	echo "No conectado ", mysqli_connect_error();
	exit();

	}

else{
	//echo "Conectado";

	}

	return $conexion;

}



?>