<?php

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}
$id = $_GET['id'];

$EliminarPer = $conexion->prepare("DELETE FROM permisos WHERE id = ? ");
$EliminarPer->bind_param("s", $id);
$EliminarPer->execute();
$EliminarPer->close();
$respuesta = mysqli_query($conexion, $EliminarPer);

header("Location:../permisos.php");

?>