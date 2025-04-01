<?php

include "../conect.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}
$id = $_GET['id'];

$EliminarSol = $conexion->prepare("DELETE FROM solvencias WHERE id = ? ");
$EliminarSol->bind_param("i", $id);
$EliminarSol->execute();
$EliminarSol->close();
$respuesta = mysqli_query($conexion, $EliminarSol);
header('Location:'.getenv('HTTP_REFERER'));

?>