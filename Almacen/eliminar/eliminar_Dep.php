<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificador de el departamento
$id = $_GET['id'];

//conexion a la base de datos para elimninar el departamento
$sqlVerificar = $conexion->prepare("SELECT 
						* 
				FROM 
					salidas 
				WHERE 
					EXISTS
						(
							SELECT 
								id 
							FROM 
								departamentos 
							WHERE 
								salidas.Dep = ? 
						) ");
$sqlVerificar->bind_param("i", $id);
$sqlVerificar->execute();
$datos = $sqlVerificar->get_result();
$sqlVerificar->close();

$dep = $datos->fetch_assoc();
//verificar que la clave del departamento no este en uso

if($dep == null){
	$sql = $conexion->prepare("DELETE FROM departamentos WHERE id = ? ");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->close();
	
	echo "<script> 
			alert('¡Eliminado exitosamente!');
			window.location.href = '../Departamento.php';
		</script>";
}else{
	echo "<script> 
			alert('No se puede eliminar, clave en uso');
			window.location.href = '../Departamento.php';
		</script>";
}

?>

