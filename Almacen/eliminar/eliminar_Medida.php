<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador de la unidad de medida
$id = $_GET['id'];

//conexion a la base de datos para elimninar la unidad de medida
$sqlVerificar = $conexion->prepare("SELECT 
					* 
				FROM 
					productos 
				WHERE 
					EXISTS
						(
							SELECT 
								id
							FROM 
								medidas 
							WHERE 
								productos.id_medida = ?
						) ");
$sqlVerificar->bind_param("i", $id);
$sqlVerificar->execute();
$datos = $sqlVerificar->get_result();
$sqlVerificar->close();

$Unidad = $datos->fetch_assoc();

//verificar que la clave de la unidad no este en uso
if($Unidad == null){
	$sql = $conexion->prepare("DELETE FROM medidas WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->close();
	
	echo "<script>
			alert('¡Eliminado exitosamente!');
			window.location.href = '../unidad_medida.php';
		</script>";
}else{
	echo "<script> 
			alert('No se puede eliminar, clave en uso');
			window.location.href = '../unidad_medida.php';
		</script>";
}

?>

