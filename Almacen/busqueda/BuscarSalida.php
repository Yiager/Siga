<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["salidas.SalidaID", "salidas.Fecha", "salidas.Dep", "salidas.Persona", "salidas.Deposito", "salidas.Estado", "almacen.id", "almacen.Almacen", "departamentos.departamento", "departamentos.id"];

$id = "SalidaID";

$tabla = "salidas";

$buscador = isset($_POST['buscar']) ? $conexion->real_escape_string($_POST['buscar']) : null;

$where = "";

if ($buscador != null) {
	$where .= "WHERE (";

	$contador = count($columnas);
	for ($i=0; $i < $contador; $i++) { 
		$where .= $columnas[$i] . " LIKE '%".$buscador."%' OR ";
	}

	$where = substr_replace($where, " ", -3);

	$where.= ")";
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

$sql = "SELECT SQL_CALC_FOUND_ROWS salidas.*, departamentos.*, almacen.* FROM $tabla 
											INNER JOIN departamentos ON salidas.Dep = departamentos.id 
											INNER JOIN almacen ON salidas.Deposito = almacen.id  $where $sqlLimit";
$respuesta = $conexion->query($sql);
$filas = $respuesta->num_rows;

$sqlFiltro = "SELECT FOUND_ROWS()";
$resFiltro = $conexion->query($sqlFiltro);
$filaFiltro = $resFiltro->fetch_array();
$totalFiltro = $filaFiltro[0];

$sqlTotal = "SELECT COUNT($id) FROM $tabla";
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
		$Codigo = $fila['SalidaID'];
		$Estado = $fila['Estado'];
		$Deposito = $fila['Deposito'];

		$salida["data"].= "<tr>";

		if($Estado == "Procesada"){

										$salida['data'].= "<td > 
											<form method='POST' style='display: inline-block;'>

												<input type='checkbox' name='check' value='$Codigo' disabled style='display:none;'> 

												<button type='submit'  style='border:none;  
												background-color: rgba(0,0,0,0);'  name='ProcesaSalida' disabled> <abbr title='Salida procesada'> <span class='icono'>&#10004;</span> </abbr> </button> 

												</form>
												
											 </td>";

										}else{

											$salida['data'].= "<td > 
												<form method='POST' style='display: inline-block;'>

												<input type='checkbox' name='check' value='$Codigo' checked style='display:none;'> 

												<button type='submit' style=' border:none;
												background-color: rgba(0,0,0,0);' name='ProcesaSalida'> <abbr title='Procesar salida'> <span class='icono'>&#10004;</span> </abbr> </button> 

												</form> 
											 </td>";
									}

		$salida["data"].='<td>'.$fila['SalidaID'] .'</td>';
		$salida["data"].='<td>'.$fila['Fecha'] .'</td>';
		$salida["data"].='<td>'.$fila['departamento'] .'</td>';
		$salida["data"].='<td>'.$fila['Persona'] .'</td>';
		$salida["data"].='<td>'.$fila['Almacen'] .'</td>';
		$salida["data"].='<td>'.$fila['Estado'] .'</td>';


						if($Estado == "Procesada"){

									$salida['data'].= "<td '> 
									<a style='display:none;' href='/Almacen/editar/editarSalidaD.php?id=<?php echo $Codigo ?> '> Editar </a>  
								 	<a style='display:none;' href='/Almacen/eliminar/eliminarSalidaD.php?id=<?php echo $Codigo ?>' > Eliminar </a> </td>";

								} else{


									$salida['data'].= "<td > <a href='/Almacen/editar/editarSalida.php?id=$Codigo'> <abbr title='Editar salida'> <span class='icono'>&#9998;</span></a> </abbr> 
									<a class='btnEliminar' href = '/Almacen/eliminar/eliminarSalida.php?id=$Codigo'> <abbr title='Eliminar salida'> <span class='icono'>&#10006;</span></a> </abbr> </td>";
								}

								$salida['data'].= "<td> <a href='/Almacen/salidaDetalle.php?pagina=1&id=".$Codigo."&Estado=".$Estado."&Almacen=".$Deposito."'> <abbr title='Detalles de salida'><span class='icono'>&#10063;</span></abbr> </a>  
									 </td>";

								$salida['data'].= "<td> <a  href='/Almacen/Reportes/reportesSalida.php?id=".$Codigo."&Estado=".$Estado."' target='_blank'> <abbr title='Reporte de salida'> <span class='icono'>&#9782;</span> </abbr> </a>  </td>";

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