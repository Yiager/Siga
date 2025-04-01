<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador de la salida
$id = $_GET['id'];

//conexion a la base de datos para elimninar la salida
$sql = $conexion->prepare("DELETE FROM salidas WHERE SalidaID = ? ");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

//conexion a base de datos para eliminar todos los detalles de la salida seleccionada
$sql2 = $conexion->prepare("DELETE FROM salidadetalle WHERE CodigoSalida = ? ");
$sql2->bind_paran("i", $id);
$sql2->execute();
$sql2->close();

//redireccionar a la pagina de salidas
header('location:../Salida.php');

?>



