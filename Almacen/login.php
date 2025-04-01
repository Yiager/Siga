<?php

function conectar(){

	//Varibales de conexion
	global $server, $user, $pass, $bd;
	$server = 'localhost';
	$user = 'root';
	$pass = '';
	$bd = 'almacen';

	$conexion = new mysqli($server,$user,$pass,$bd);

	if(mysqli_connect_errno()){
		echo "No conectado ", mysqli_connect_error();
		exit();
	}else{
		//echo "Conectado";
	}
		return $conexion;
}

?>