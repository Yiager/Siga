<?php

include('../conect.php');

$conexion = conectar();

session_start();

if(!isset($_SESSION['idUser'])){
	header('Location:../index.php');
}

//La variable contiene el identificador de la fila del concejo comunal en la base de datos
$idCC = $_GET['id'];

//consulta a la base de datos para eliminar la fila correspondiente al id
$sqlEliminar = "DELETE FROM ccomunal WHERE id_cc = '$idCC' ";
$Respuesta = mysqli_query($conexion, $sqlEliminar);

//Redireccionar a la pagina de concejos comunales
header("Location:../ConcejoC.php");

?>