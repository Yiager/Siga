<?php 

include("login.php");

$conexion = conectar();

session_start();
if(!isset($_SESSION['id_usuario'])){
	header("location: index.php");
}

$iduser = $_SESSION['id_usuario'];

$sqlpet = $conexion->prepare("SELECT 
				* 
			FROM 
				usuarios 
			WHERE 
				id = ? ");
$sqlpet->bind_param("i", $iduser);
$sqlpet->execute();
$fila = $sqlpet->get_result();
$sqlpet->close();

$row = $fila->fetch_assoc();

$user = $row['Tipo'];
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<link rel="stylesheet" media="only screen and (max-width: 1080px)" href="css/adaptable.css">
		<link rel="stylesheet" media="only screen and (min-width: 1080px) and (max-width:1288px)" href="css/adaptableMed.css">
		<link rel="stylesheet" type="text/css" href="/Almacen/css/estiloMenu.css">
		<link rel="stylesheet" type="text/css" href="/Almacen/css/Estilo.css">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="icon" href="img/SIGA.png">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Kdam+Thmor+Pro&family=Pixelify+Sans&family=Roboto:wght@100&display=swap" rel="stylesheet">
	</head>
</html>