<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador del usuario
$id = $_GET['id'];

//conexion a la base de datos para elimninar el usuario
$sql = $conexion->prepare("DELETE FROM usuarios WHERE id = ? ");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

//Redireccionar a la pagina de usuarios
header('location:../usuarios.php');

?>




