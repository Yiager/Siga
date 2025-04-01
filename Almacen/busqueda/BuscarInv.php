<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["inventario.Almacen", "inventario.Producto", "inventario.Cant", "inventario.InvIni", "inventario.Entrada", "inventario.Salida", "inventario.Total", "inventario.PrecioUE", "inventario.PrePon", "inventario.PrePonUni", "productos.Articulo", "almacen.Almacen", "medidas.Medida"];

$Almacen = "Almacen";

$tabla = "inventario";

$buscador = isset($_POST['buscar']) ? $conexion->real_escape_string($_POST['buscar']) : null;

$where = "";

if ($buscador != null) {
	$where .= "WHERE (";

	$contador = count($columnas);
	for ($i=0; $i < $contador; $i++) { 
		$where .= $columnas[$i] . " LIKE '%".$buscador."%' OR ";
	}

	$where = substr_replace($where, " ", -3);

	$where.= "AND inventario.Producto = productos.idProducto )";
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

$sql = "SELECT SQL_CALC_FOUND_ROWS inventario.*, productos.*, medidas.*, almacen.* FROM $tabla 
										INNER JOIN productos ON inventario.Producto = productos.idProducto 
										INNER JOIN medidas ON productos.id_medida = medidas.id 
										INNER JOIN almacen ON inventario.Almacen = almacen.id  $where $sqlLimit";
$respuesta = $conexion->query($sql);
$filas = $respuesta->num_rows;

$sqlFiltro = "SELECT FOUND_ROWS()";
$resFiltro = $conexion->query($sqlFiltro);
$filaFiltro = $resFiltro->fetch_array();
$totalFiltro = $filaFiltro[0];

$sqlTotal = "SELECT COUNT($Almacen) FROM $tabla";
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
		$TotalPondUni = $fila['PrePonUni'] * $fila['Total'];

		$salida["data"].= "<tr>";
		$salida["data"].='<td>'.$fila['Almacen'] .'</td>';
		$salida["data"].='<td>'.$fila['Articulo'] .'</td>';
		$salida["data"].='<td>'.$fila['Cant'] .'</td>';
		$salida["data"].='<td>'.$fila['Medida'] .'</td>';
		$salida["data"].='<td>'.$fila['InvIni'] .'</td>';
		$salida["data"].='<td>'.$fila['Entrada'] .'</td>';
		$salida["data"].='<td>'.$fila['Salida'] .'</td>';
		$salida["data"].='<td>'.$fila['Total'] .'</td>';
		$salida["data"].='<td>'.$fila['PrecioUE'] .'</td>';
		$salida["data"].='<td>'.$fila['PrePon'] .'</td>';
		$salida["data"].='<td>'.$fila['PrePonUni'] .'</td>';
		$salida["data"].='<td>'.$TotalPondUni.'</td>';
		$salida["data"].= "</tr>";
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