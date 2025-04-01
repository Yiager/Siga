<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador de la entrada
$id = $_GET['id'];

//conexion a la base de datos para elimninar la entrada
$sql = $conexion->prepare("DELETE FROM entradas WHERE Codigo = ?");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

//conexion a la base de datos para eliminar todos los detalles de la entrada seleccionada
$sql2 = $conexion->prepare("DELETE FROM entradadetalle WHERE CodigoEntrada = ? ");	
$sql2->bind_param("i", $id);
$sql2->execute();
$sql2->close();

//redireccionar a la pagina de entradas
header('location:../Entradas.php');

?>
