<?php

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}
$id = $_GET['id'];

$EliminarUser = $conexion->prepare("DELETE FROM usuarios WHERE id = ? ");
$EliminarUser->bind_param("i", $id);
$EliminarUser->execute();
$EliminarUser->close();
$respuesta = mysqli_query($conexion, $EliminarUser);

header("Location:../usuarios.php");

?>