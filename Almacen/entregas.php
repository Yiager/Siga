
<!DOCTYPE html>
<html lang="es">
<title> Entregas </title>
<body>

<header>
	<h2> Entregas </h2>
</header>

<?php
include("nav.php");

$Almacen = $_GET['AlmacenEn'];
$Producto = $_GET['productoEn'];
$FechaInicial = $_GET['inicial'];
$FechaFinal = $_GET['final'];
$Dep = $_GET['Dep'];

$por_pagina = 8;

$mostrar_ent = ($_GET['pagina']-1)*$por_pagina;

if(!$_GET){
	header('Location:entregas.php?pagina=1');
}

?>

<div id="table">
	
	<table>
		<div class="botones">
			<a href="/Almacen/Reportes/EntregasImp.php?almacen=<?= $Almacen ?>&producto=<?= $Producto?>&inicial=<?= $FechaInicial?>&final=<?= $FechaFinal ?>&dep=<?= $Dep ?>" class="impEntrega" > Imprimir </a>
		</div>
	
		<thead>
			<th>Fecha</th>
			<th>Departamento</th>
			<th>Almacen</th>
			<th>Producto</th>
			<th>Cantidad</th>
			<th>Monto</th>
			<th>Pond Uni</th>
		</thead>

			<tbody>
					<?php

						$sqlP = $conexion->prepare("SELECT 
									salidas.*, 
									productos.*, 
									medidas.*, 
									almacen.*, 
									salidadetalle.*, 
									departamentos.* 
								FROM 
									salidadetalle 
									INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
									INNER JOIN medidas ON productos.id_medida = medidas.id 
									INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
									INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
									INNER JOIN departamentos ON salidas.Dep = departamentos.id 
								WHERE 
									salidas.Dep = ?
									AND salidadetalle.Deposit = ?
									AND salidadetalle.Objeto = ?
									AND salidas.Fecha BETWEEN ? AND ?
								LIMIT 
									?, ?");

						$sqlP->bind_param("iiissii", $Dep, $Almacen, $Producto, $FechaInicial, $FechaFinal, $mostrar_ent, $por_pagina);
						$sqlP->execute();
						$sqlP->store_result();
						$sqlCon = $sqlP->num_rows;
						$sqlP->close();
						$pagina = ceil($sqlCon/$por_pagina);

						$sqlInv = $conexion->prepare("SELECT 
										salidas.*, 
										productos.*, 
										medidas.*, 
										almacen.*, 
										salidadetalle.*, 
										departamentos.* 
									FROM 
										salidadetalle 
										INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
										INNER JOIN medidas ON productos.id_medida = medidas.id 
										INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
										INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
										INNER JOIN departamentos ON salidas.Dep = departamentos.id 
									WHERE 
										salidas.Dep = ? 
										AND salidadetalle.Deposit = ?
										AND salidadetalle.Objeto = ? 
										AND salidas.Fecha BETWEEN ? AND ?
									LIMIT 
										?, ?");
						$sqlInv->bind_param("iiissii", $Dep, $Almacen, $Producto, $FechaInicial, $FechaFinal, $mostrar_ent, $por_pagina);
						$sqlInv->execute();
						$filas = $sqlInv->get_result();
						$sqlInv->close();

						while($mostrar = $filas->fetch_assoc()){
							
							$Cantidad = $mostrar['Cantidad'];
							$Precio = $mostrar['Precio'];
					?>							

						<tr> 
							<td> <?= $mostrar['Fecha']; ?> </td>
							<td> <?= $mostrar['departamento']; ?> </td>
							<td> <?= $mostrar['Almacen']; ?> </td>
							<td> <?= $mostrar['Articulo']; ?> </td>
							<td> <?= $mostrar['Cantidad']; ?> </td>
							<td> <?= $mostrar['Precio']; ?> </td>
							<td> <?= $Cantidad * $Precio; ?> </td>
						</tr>

						<?php } ?>
			</tbody>

		</table>

		<ul class="paginador" >
				<?php for($i=0; $i<$pagina; $i++): ?>
					<li> 
						<a href="entregas.php?pagina=<?= $i + 1; ?>&AlmacenEn=<?= $Almacen ?>&productoEn=<?= $Producto ?>&inicial=<?= $FechaInicial?>&final=<?= $FechaFinal ?>&Dep=<?= $Dep ?>"> 
							<?= $i + 1; ?></a> 
					</li>
				<?php endfor ?>
		</ul> 
</div>

</body>
</html>