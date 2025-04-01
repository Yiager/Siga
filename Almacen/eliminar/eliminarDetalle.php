<?php
//incluir variable de conexion y variable de sesion del usuario
include '../head.php';

//identificadores
$id = $_GET['id'];	//identificador del detalle
$Codigo = $_GET['Codigo'];	//identificador de la entrada
$Estado = $_GET['Estado']; //Estado de la entrada

//conexion a la base de datos para elimninar el detalle de la entrada
$sql = $conexion->prepare("DELETE FROM entradadetalle WHERE ID = ? ");
$sql->bind_param("i", $id);
$sql->execute();
$sql->close();

//Actualizar los montos de la entrada 
$sqlSumaMontos = $conexion->prepare("UPDATE 
					entradas 
				  SET 
				  	MontoBase = 
				  	(
				  		SELECT 
				  			SUM(MontoB) 
				  		FROM 
				  			entradadetalle 
				  		WHERE 
				  			entradadetalle.CodigoEntrada = entradas.Codigo
				  	), 
				  	MontoIVA = 
				  	(
				  		SELECT 
				  			SUM(MontoI) 
				  		FROM 
				  			entradadetalle 
				  		WHERE 
				  			entradadetalle.CodigoEntrada = entradas.Codigo
				  	), 
				  	MontoTotal = 
				  	(
				  		SELECT 
				  			SUM(MontoT) 
				  		FROM 
				  			entradadetalle 
				  		WHERE entradadetalle.CodigoEntrada = entradas.Codigo
				  	) 
				 WHERE 
				 	entradas.Codigo = ? ");

	$sqlSumaMontos->bind_param("i", $Codigo);
	$sqlSumaMontos->execute();
	$sqlSumaMontos->close();

	//redireccionar a la pagina de los detalles de la entrada seleccionada
	$url = "../EntradaDetalle.php?pagina=1&id=$Codigo&Estado=$Estado";
	header("Location:$url");

?>
