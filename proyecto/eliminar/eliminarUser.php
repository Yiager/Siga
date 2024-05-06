<?php

include('../conect.php');

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header('Location:../index.php');
}

$idUser = $_GET['id'];

$sqlEliminar = "DELETE FROM usuarios WHERE id = '$idUser' ";
$Respuesta = mysqli_query($conexion, $sqlEliminar);


header("Location:../User.php");

?>