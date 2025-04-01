<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["entradadetalle.ID", "entradadetalle.CodigoEntrada", "entradadetalle.CodigoProducto", "entradadetalle.Cantidad", "entradadetalle.Unidades", "entradadetalle.Salidas", "entradadetalle.Existencia", "entradadetalle.Precio", "entradadetalle.MontoB", "entradadetalle.PorcentajeBase", "entradadetalle.MontoI", "entradadetalle.MontoT", "productos.Articulo", "productos.idProducto"];

$id = "entradadetalle.ID";

$tabla = "entradadetalle";

$buscador = isset($_POST['buscar']) ? $conexion->real_escape_string($_POST['buscar']) : null;
$Estado = isset($_POST['Estado']) ? $conexion->real_escape_string($_POST['Estado']) : 0;
$idEnt = isset($_POST['Codigo']) ? $conexion->real_escape_string($_POST['Codigo']) : 0;

$where = "";

if ($buscador != null) {
	$where .= "WHERE entradadetalle.ID LIKE '%$buscador%' 
											OR entradadetalle.CodigoEntrada LIKE '%$buscador%' 
											OR entradadetalle.Cantidad LIKE '%$buscador%' 
											OR entradadetalle.Unidades LIKE '%$buscador%' 
											OR entradadetalle.Salidas LIKE '%$buscador%' 
											OR entradadetalle.Existencia LIKE '%$buscador%'
											OR entradadetalle.Precio LIKE '%$buscador%'
											OR entradadetalle.MontoB LIKE '%$buscador%'
											OR entradadetalle.PorcentajeBase LIKE '%$buscador%'
											OR entradadetalle.MontoT LIKE '%$buscador%'
											OR entradadetalle.MontoI LIKE '%$buscador%'
											OR productos.Articulo LIKE '%$buscador%'
									 		AND entradadetalle.CodigoEntrada = $idEnt ";
}else{
	$where .= " WHERE entradadetalle.CodigoEntrada = $idEnt ";
}

$limit = 8;
$pagina = isset($_POST['pagina']) ? $conexion->real_escape_string($_POST['pagina']) : 0;

if ($pagina == 0) {
	$inicio = 0;
	$pagina = 1;
}else{
	$inicio = ($pagina-1) * $limit;
}

$sqlLimit = "LIMIT $inicio, $limit";

$sql = "SELECT SQL_CALC_FOUND_ROWS entradadetalle.*, entradas.Codigo, productos.idProducto, productos.Articulo  FROM $tabla 
										INNER JOIN entradas ON entradadetalle.CodigoEntrada =  entradas.Codigo 
										INNER JOIN productos ON entradadetalle.CodigoProducto = productos.idProducto $where $sqlLimit";
										
$respuesta = $conexion->query($sql);
$filas = $respuesta->num_rows;

$sqlFiltro = "SELECT FOUND_ROWS() ";
$resFiltro = $conexion->query($sqlFiltro);
$filaFiltro = $resFiltro->fetch_array();
$totalFiltro = $filaFiltro[0];

$sqlTotal = "SELECT COUNT($id) FROM $tabla WHERE CodigoEntrada = '$idEnt' ";
$resTotal = $conexion->query($sqlTotal);
$filaTotal = $resTotal->fetch_array();
$total = $filaTotal[0];

$salida = [];
$salida["total"] = $total;
$salida["totalFiltro"] = $totalFiltro;
$salida["data"] = "";
$salida["paginacion"] = ""; 

if ($filas > 0) {
	while($fila = $respuesta->fetch_assoc()){
		$Codigo1 = $fila['ID'];
		$Codigo2 = $fila['Codigo'];

		$salida["data"].= "<tr>";
		$salida["data"].='<td>'.$fila['ID'] .'</td>';
		$salida["data"].='<td>'.$fila['Codigo'] .'</td>';
		$salida["data"].='<td>'.$fila['Articulo'] .'</td>';
		$salida["data"].='<td>'.$fila['Cantidad'] .'</td>';
		$salida["data"].='<td>'.$fila['Unidades'] .'</td>';
		$salida["data"].='<td>'.$fila['Salidas'] .'</td>';
		$salida["data"].='<td>'.$fila['Existencia'] .'</td>';
		$salida["data"].='<td>'.$fila['Precio'] .'</td>';
		$salida["data"].='<td>'.$fila['MontoB'] .'</td>';
		$salida["data"].='<td>'.$fila['PorcentajeBase'] .'</td>';
		$salida["data"].='<td>'.$fila['MontoI'] .'</td>';
		$salida["data"].='<td>'.$fila['MontoT'].'</td>';


								if($Estado == "Procesada"){

									$salida['data'].= "<td> <a style='display:none;' href='/Almacen/editar/editarDetalle.php?id=".$Codigo1."&Codigo=".$Codigo2."' > <span class='icono'>&#9998;</span> </a>  
								 <a style='display:none;' href='/Almacen/eliminar/eliminarDetalle.php?id=".$Codigo1."&Codigo=".$Codigo2."' > <span class='icono'>&#10006;</span> </a> </td>";


								}else{


								$salida['data'].= "<td> <a href='/Almacen/editar/editarDetalle.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."' > <abbr title='Editar detalle de entrada'> <span class='icono'>&#9998;</span> </a> </abbr> 
								 <a style='border:none; color:royalblue; background-color: rgba(0,0,0,0);' href = '/Almacen/eliminar/eliminarDetalle.php?id=".$Codigo1."&Codigo=".$Codigo2."&Estado=".$Estado."' > <abbr title='Eliminiar detalle de entrada'> <span class='icono'>&#10006;</span></a> </abbr> </td>";

								}
				$salida["data"] .= "</tr>";
	}
}else{
	$salida["data"].= "<tr> <td> Sin Resultados </td>
				<td></td>
				<td></td>
			 </tr>";
}


if ($salida["total"] > 0) {
	
	$totalPaginas = ceil($salida["total"]/$limit);

	$salida["paginacion"].= "<ul class='paginador'>";

	$numeroInicio = 1;

	if ($pagina - 4 > 1) {

		$numeroInicio = $pagina - 4;
	}

	$numeroFin = $numeroInicio + 9;

	if ($numeroFin > $totalPaginas) {
		$numeroFin = $totalPaginas;
	}

	for ($i=$numeroInicio; $i <= $numeroFin ; $i++) { 
		if ($pagina == $i) {
			$salida["paginacion"].="<li > <a class='activa' href='#'>".$i." </a> </li>";
		}else{
			$salida["paginacion"].="<li> <a href='#' onclick='siguientePagina(".$i.")'>".$i." </a> </li>";
		}
		
	}

	$salida["paginacion"].= "</ul>";

}

echo json_encode($salida, JSON_UNESCAPED_UNICODE);

?>