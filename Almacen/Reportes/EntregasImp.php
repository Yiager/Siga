<?php 
include('../login.php');
include('../fpdf/pdf_mc_table.php');
$conexion = conectar();

//Almacen, producto, fecha inicial y hasta de las entregas y el departamento 
$Almacen = $_GET['almacen'];
$Producto = $_GET['producto'];
$Inicial = $_GET['inicial'];
$Final = $_GET['final'];
$Dep = $_GET['dep'];
//Fecha en la que se solicita el reporte
$FechaReporte = date('d/m/Y');

//************************************Seleccionar el departamento actual************************************
$sqlDep = $conexion->prepare("SELECT * FROM departamentos WHERE id = ? ");
$sqlDep->bind_param("i", $id);
$sqlDep->execute();
$datos = $sqlDep->get_result();
$sqlDep->close();

$traerDep = $datos->fetch_assoc();
$dep = $traerDep['departamento'];

$pdf = new PDF_MC_table();
$pdf->AddPage();
$pdf->SetFont('Arial', '', '12');
$pdf->Image('../img/logoNuevo.jpg' , 5 ,5, 50);
$pdf->Ln(25);
//***********Seleccionar todas las entregas que se han hecho de los productos desde los detalles de las salidas***********
$ConexionSQL = $conexion->prepare("SELECT 
					salidas.*, 
					productos.*, 
					medidas.*, 
					almacen.*, 
					salidadetalle.* 
				FROM 
					salidadetalle 
					INNER JOIN productos ON salidadetalle.Objeto = productos.idProducto 
					INNER JOIN medidas ON productos.id_medida = medidas.id 
					INNER JOIN almacen ON salidadetalle.Deposit = almacen.id 
					INNER JOIN salidas ON salidadetalle.CodigoSalida = salidas.SalidaID 
				WHERE 
					salidas.Dep = ? 
					AND salidadetalle.Deposit = ? 
					AND salidadetalle.Objeto = ? 
					AND salidas.Fecha BETWEEN ? AND ?  "); 

$ConexionSQL->bind_param("iiiss", $Dep, $Almacen, $Producto, $Inicial, $Final);
$ConexionSQL->execute();
$datos = $ConexionSQL->get_result();
$ConexionSQL->close();
	
	$pdf->SetFont('Arial', '', '12');
	$pdf->Cell(130);
	$pdf->Cell(60, 10, 'Fecha de reporte: '.$FechaReporte, 1, 1, 'C', 0);
	$pdf->Cell(90);
	$pdf->SetFont('Arial', 'B', '14');
	$pdf->Cell(10, 10, 'Departamento: '. $dep, 0, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->Cell(90);
	$pdf->SetFont('Arial', 'B', '14');
	$pdf->Cell(10, 10, 'Entregas', 0, 1, 'C', 0);
	$pdf->Ln(5);
	$pdf->SetWidths(Array(30,30,30,20,20,25,25));
	//Centrar contenido de celda
	$pdf->SetAligns(Array('C','C','C','C','C','C','C'));
	$pdf->SetLineHeight(5);
	$pdf->Cell(5);
	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(30, 10, 'Fecha', 1, 0, 'C', 0);
	$pdf->Cell(30, 10, 'Almacen', 1, 0, 'C', 0);
	$pdf->Cell(30, 10, 'Producto', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Medida', 1, 0, 'C', 0);
	$pdf->Cell(20, 10, 'Cantidad', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Precio', 1, 0, 'C', 0);
	$pdf->Cell(25, 10, 'Precio Uni.', 1, 1, 'C', 0);

while($traer = $datos->fetch_assoc()){

	$PreUni = $traer['Cantidad'] * $traer['Precio'];

	$pdf->Cell(5);
	$pdf->SetFont('Arial','',12);

	$pdf->Row(Array(
		$traer['Fecha'],
		$traer['Almacen'],
		$traer['Articulo'],
		utf8_decode($traer['Medida']),
		$traer['Cantidad'],
		$traer['Precio'],
		$PreUni
	));
}

$pdf->Output();

?>