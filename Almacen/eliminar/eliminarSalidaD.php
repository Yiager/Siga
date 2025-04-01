<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

$id = $_GET['id']; //identificador de el detalle
$codigo = $_GET['Codigo'];	//identificador de la salida
$status = $_GET['Estado']; //Estado de la salida
$Almacen = $_GET['Almacen']; //Almacen que se encuentra la salida

//conexion a la base de datos para elimninar el detalle de la salida
$sql = $conexion->prepare("DELETE FROM salidadetalle WHERE IdSalida = ? ");
$sql->bind_param("i", $codigo);
$sql->execute();
$sql->close();

//Redireccionar a la pantalla de los detalles de la salida seleccionada
$url = "../salidaDetalle.php?pagina=1&id=$id&Estado=$status&Almacen=$Almacen";
header("Location: $url");

?>

