<?php

include "../login.php";
$conexion = conectar();

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

$columnas = ["entradas.Codigo", "entradas.Fecha", "entradas.Almacen", "entradas.Proveedor", "entradas.NroCompra", "entradas.FechaCompra", 
"entradas.NroFactura", "entradas.FechaFactura", "entradas.MontoBase", "entradas.MontoIVA", 
"entradas.MontoTotal", "entradas.TipoEntrada", "entradas.Estado", "entradas.InvInicial", "prove.empresa", "almacen.Almacen"];

$id = "Codigo";

$tabla = "entradas";

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

$sql = "SELECT SQL_CALC_FOUND_ROWS entradas.*, prove.empresa, almacen.Almacen, almacen.id FROM $tabla INNER JOIN prove ON entradas.Proveedor = prove.id INNER JOIN almacen ON entradas.almacen = almacen.id  $where $sqlLimit";
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
		$Codigo1 = $fila['Codigo'];
		$Estado = $fila['Estado'];
		$InventarioIni = $fila['InvInicial'];
		$fileFactura = $fila['rutaFactura'];
		$fileOrden = $fila['rutaOrden'];


		$salida["data"].= "<tr>";
		if($Estado == "Procesada"){

								$salida['data'].= "<td > 
										<form  method='POST' style='display: inline-block;'>

											<input type='checkbox' name='check' value='$Codigo1' style='display:none;' disabled > 

											<button type='submit'  style=' border:none;
											background-color: rgba(0,0,0,0);'  name='ProcesaEntrada' disabled> <abbr title='Entrada Procesada'> <span class='icono'>&#10004;</span> </abbr> </button> 
										</form>
								 	</td>";

								}else{

								$salida['data'].="
									<td > 

								<form  method='POST' style='display: inline-block;'>

										<input type='checkbox' name='check' value='$Codigo1' style='display:none;' checked> 

										<button type='submit'  style=' border:none; font-size:1.5em; 
										background-color: rgba(0,0,0,0);'  name='ProcesaEntrada'> <abbr title='Procesar entrada'> <span class='icono'>&#10004;</span></abbr> </button> 
								</form>

								</td>

								 ";
							}


							if($InventarioIni == "Si" || $Estado == "Procesada" ){

								 	$salida['data'].= "<td> 

								 	<form  method='POST' style='display: inline-block;'>

								 		<input type='checkbox' name='check1' value='$Codigo1' disabled style='display:none;'> 

										<button type='submit' style=' margin-left:5%; border:none; font-size:1.5em; background-color: rgba(0,0,0,0)'  name='Inv-Ini' disabled> <abbr title='Agregado al inventario inicial'> <span class='icono'>&#8505;</span> </abbr> </button> 

									</form>
										
								 	</td>";

								}else{

									$salida['data'].= "<td> 

									<form  method='POST' style='display: inline-block;'>

										<input type='checkbox' name='check1' value='$Codigo1' style='display:none;' checked> 

										<button type='submit' style=' margin-left:5%; border:none; font-size:1.5em; background-color: rgba(0,0,0,0)'  name='Inv-Ini'> <abbr title='Agregar inventario inicial'> <span class='icono'>&#8505;</span> </abbr> </button> 

										</form>
								 	</td>";
								}

								$salida['data'].= "<td>

								<a style='text-decoration:none;' href='/Almacen/VerEntrada.php?pagina=1&id=".$Codigo1."' color: black;'> <abbr title='Ver entrada'> <span class='icono'>&#9737;</span></abbr> </a> 

							</td>";

		$salida["data"].='<td>'.$Codigo1 .'</td>';
		$salida["data"].='<td>'.$fila['Fecha'] .'</td>';
		$salida["data"].='<td>'.$fila['empresa'] .'</td>';
		$salida["data"].='<td>'.$fila['Almacen'] .'</td>';
		$salida["data"].='<td>'.$fila['TipoEntrada'] .'</td>';
		$salida["data"].='<td>'.$fila['Estado'] .'</td>';
		$salida["data"].='<td>'.$fila['InvInicial'] .'</td>';

		$salida["data"].='<td><a href="/Almacen'.$fileFactura.'"> Factura </a><br>
							<a href="/Almacen'.$fileOrden.'"> Orden C. </a> </td> .</td>';


								if($Estado == "Procesada"){

									$salida['data'].= "
									<td> <a style='display:none;' href='/Almacen/editar/editarEntrada.php?id=".$Codigo1."'> Editar </a>  
									 	<a style='display:none;' href='/Almacen/eliminar/eliminarEntrada.php?id=".$Codigo1."'> Eliminar </a> </td> ";
									

								}else{

								$salida['data'].= "
								<td > <a style='text-decoration:none;' href='/Almacen/editar/editarEntrada.php?id=".$Codigo1."'><abbr title='Editar entrada'> <span class='icono'>&#9998;</span></abbr></a>  
									 <a style='text-decoration:none;' href = '/Almacen/eliminar/eliminarEntrada.php?id=".$Codigo1."'> 
									 <abbr title='Eliminar entrada'><span class='icono'>&#10006;</span></abbr></a> </td>";

								 } 

								 $salida['data'].= "<td> <a style='text-decoration:none;' href='/Almacen/EntradaDetalle.php?pagina=1&id=".$Codigo1."&Estado=".$Estado."'> <abbr title='Detalles de la entrada'>  <span class='icono'>&#10063;</span> </a>  </abbr>
								 </td>";

								 $salida['data'].= "<td> <a style='text-decoration:none;' href='/Almacen/Reportes/reportesEntrada.php?id=".$Codigo1."' target='_blank'><abbr title='Reporte de entrada'> <span class='icono'>&#9782;</span></a> </abbr> </td>";


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