<?php

include('../conect.php');

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header('Location:../index.php');
}
//se reciben el identificador de el concejo comunal y la cedula del vocero a eliminar
$id = $_GET['id'];
$CI = $_GET['CI'];

//Se elimina el vocero de la tabla
$sqlEliminar = "DELETE FROM voceros WHERE Cedula = '$CI' ";
$Respuesta = mysqli_query($conexion, $sqlEliminar);

//Se redirecciona a la vista de los datos del concejo comunal correspondiente
header("Location:../DatosConcejo.php?id=$id");

?>