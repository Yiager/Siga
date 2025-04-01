<?php

include "../conect.php";
require("../fpdf/phpqrcode/qrlib.php");
$conexion = conectar();

$fechaActual = date("d-m-Y");

header('Content-Type: text/html; charset=UTF-8');

session_start();
if (!isset($_SESSION['id_usuario'])) {
	header("Location:index.php");
}

setlocale(LC_TIME, 'spanish');

$id = $_GET['id'];

$DatosSolvencia = "SELECT 
						solvencias.*, 
						correos.* 
					FROM 
						solvencias 
						INNER JOIN correos ON correos.nombre = solvencias.Nombres 
					WHERE 
						solvencias.id = '$id' ";

$respuesta = mysqli_query($conexion, $DatosSolvencia);
$datos = mysqli_fetch_assoc($respuesta);

$Tipo = $datos['Tipo'];
$nombre = $datos['Nombres'];
$direccion = $datos['Direccion'];
$fechaEmision = $datos['FechaActual'];
$periodo = $datos['Periodo'];
$fechaDesde = $datos['FechaDesde'];
$fechaHasta = $datos['FechaHasta'];
$uso = $datos['Uso'];
$concepto = "";
$Solvencia = "";
$correo = $datos['correo'];
$codigo = $datos['codigo'];
$nro = "     ".$codigo."     ";

if($Tipo == "Catastro"){
	$concepto = "   INMUEBLE   ";
	$Solvencia = "     Residencia     ";
}else{
	$concepto = "   ACTIVIDAD ECONÓMICA  ";
	$Solvencia = "     Comercio     ";
}

use phpmailer\phpmailer\PHPMailer;
use phpmailer\phpmailer\SMTP;
use phpmailer\phpmailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 0;                      
    $mail->isSMTP();                                          
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;               
    $mail->SMTPSecure = 'ssl';                    
    $mail->Username   = 'lossaliasdat@gmail.com';               
    $mail->Password   = 'dquqpukmsfwcqblm';                               
    $mail->Port       = '465';       
    $mail->AddEmbeddedImage('../img/logoNuevoComp.jpg', 'logoN');                            

    //Recipients
    $mail->setFrom('lossaliasdat@gmail.com', 'Direccion Administracion Tributaria');
    $mail->addAddress($correo);     

   
    $mail->isHTML(true);                                 
    $mail->Subject = 'FROM: lossaliasdat@gmail.com';
    $mail->Body    = "<img src='cid:logoN' alt='Logo' width='150' height='100'/>"."<br><br>". "Saludos, sr(a) contribuyente ".$nombre.", que corresponde al numero de ".$Tipo." ". $codigo." se ha emitido una solvencia para uso ".$uso.", ubicado en ".$direccion.", el dia ".$fechaEmision.", válido desde el dia ".$fechaDesde." hasta ".$fechaHasta." por un período ".$periodo." por la Dirección de Administración Tributaria de la Alcaldia del Municipio Los Salias"."<br><br>"."Dirección de Administración Tributaria de la Alacaldia del Municipio Los Salias"."<br>"."lossaliasdat@gmail.com";

    $mail->send();
   
} catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
}

// $to = "$correo";
// $subject = "Emisión de solvencia";
// $message = "Saludos, sr(a) contribuyente ".$nombre.", que corresponde al numero de ".$Tipo." ". $codigo." se ha emitido una solvencia para uso ".$uso.", ubicado en ".$direccion.", el dia ".$fechaEmision.", válido desde el dia ".$fechaDesde." hasta ".$fechaHasta." por un período ".$periodo." por la Dirección de Administración Tributaria de la Alcaldia del Municipio Los Salias". "\n\n" ."Dirección de Administración Tributaria de la Alacaldia del Municipio Los Salias". "\n". "lossaliasdat@gmail.com";
// $headers = "FROM: lossaliasdat@gmail.com". "\r\n";

// mail($to, $subject, utf8_decode($message), $headers);

//////////////////////////////////////////////////////////////

$fechaDesdeN = date($datos['FechaDesde']);
$fechaD = strtotime($fechaDesdeN);
$añoDesde ="   ". date("Y", $fechaD)."   ";
$mesDesde = date("m", $fechaD);
$diaDesde = date("d", $fechaD);

$nombreMesDesde = date_create_from_format("!m", $mesDesde);
$mesDesde = "    ".strtoupper(strftime("%B", $nombreMesDesde->getTimestamp()))."    ";
 
////////////////////////////////////////////////////////////

$fechaHastaN = date($datos['FechaHasta']);
$fechaH = strtotime($fechaHastaN);
$añoHasta = "    ".date("Y", $fechaH)."    ";
$mesHasta = date("m", $fechaH);
$diaHasta = "    ".date("d", $fechaH)."    ";

$nombreMesHasta = date_create_from_format("!m", $mesHasta);
$mesHasta = " ".strtoupper(strftime("%B", $nombreMesHasta->getTimestamp()))."    ";

//////////////////////////////////////////////////////////

$contenido  = 'BEGIN:VCARD'."\n";
$contenido .= 'VERSION:2.1'."\n";
$contenido .= 'FN:'."El contribuyente: ".$nombre."\n";
$contenido .= 'FN:'."Ubicado en: ".$direccion."\n";
$contenido .= 'FN:'."El dia: ".$fechaEmision."\n";
$contenido .= 'FN:'."Emitio una solvencia desde: ".$fechaDesde."\n";
$contenido .= 'FN:'."hasta: ".$fechaHasta."\n";
$contenido .= 'ADR;TYPE=work;'.'LABEL="'."para uso".$uso.'":'."\n";
$contenido .= 'FN:'."Periodo: ".$periodo."\n";
$contenido .= 'END:VCARD';

$codigoQR = QRcode::png($contenido, "../Qrs/QrNro".$id.".png", QR_ECLEVEL_L, 3);

require("../fpdf/WriteTag.php");

$pdf=new PDF_WriteTag();

$pdf->SetFont('arial','',12);
$pdf->AddPage();

// Stylesheet
$pdf->SetStyle("p","arial","N",9,"0,0,0");
$pdf->SetStyle("h1","times","N",18,"102,0,102",0);
$pdf->SetStyle("a","times","BU",9,"0,0,255");
$pdf->SetStyle("pers","times","I",0,"255,0,0");
$pdf->SetStyle("place","arial","N",10,"0,0,0");
$pdf->SetStyle("vb","arial","UB",10,"0,0,0");

// Title
$pdf->SetLineWidth(0.5);
$pdf->SetFillColor(255,255,204);
$pdf->SetDrawColor(102,0,102);

$txt = "
<p>
Desde el     <vb>".$diaHasta."</vb>      de     <vb>".$mesDesde."</vb>      de <vb>". $añoDesde. "</vb>       
Hasta el     <vb>".$diaHasta."</vb>      de      <vb>".$mesHasta."</vb>       de       <vb>". $añoHasta. "</vb>     .  
</p>";

$txt2 = "
<p>
Se encuentra solvente con esta Administración, en lo que respecta al pago de impuesto sobre:   <vb>".$concepto."</vb>    , se 
expide la presente solvencia al ciudadano(a): <vb>".$nombre."</vb>  propietario 
(a):    <vb>".$Solvencia."</vb>     ubicado en: 
<vb>".$direccion."</vb> 
Uso: <vb>".$uso."</vb> 
</p>";

if ($Tipo == "Catastro") {

	$txt3 = "<place> 
	Boletin de Catastro Nro:    <vb>".$nro."</vb>     .
	</place>
	<place>
	Boletin de Licencia Nro:_______________
	</place>";
	
}else{
	$txt3 = "
	<place> 
	Boletin de Catastro Nro:_______________
	</place>
	<place>
	Boletin de Licencia Nro:    <vb>".$nro."</vb>     .
	</place>";
}

$pdf->SetFont("Arial", "", 8);
$pdf->Cell(55,3, "REPUBLICA BOLIVARIANA DE VENEZUELA", 0, 1, "C");
$pdf->Cell(55,3, "ESTADO BOLIVARIANO DE MIRANDA", 0, 1, "C");
$pdf->Cell(55,3, "ALCALDIA DEL MUNICIPIO LOS SALIAS", 0, 1, "C");
$pdf->setXY(18,30);
$pdf->Image("../img/logoNuevo.jpg", 18, 20, 40, 20, 'JPG');
$pdf->SetFont("Arial", "B", 8);
$pdf->setXY(10,20);
$pdf->Cell(55,46, "www.alcaldialossalias.gob.ve", 0, 1, "C");
$pdf->SetFont("Arial", "", 6);
$pdf->setXY(10,20);
$pdf->Cell(55,52, "RIF.: G-20003907-8", 0, 1, "C");
$pdf->SetFont("Arial", "", 10);
$pdf->SetXY(135, 5);
$pdf->WriteTag(68, 5,$txt3 ,0,"J",0,0);
$pdf->SetFont("Arial", "", 8);
$pdf->SetXY(90, 20);
$pdf->MultiCell(60,4, utf8_decode("Esta solvencia esta sujeta a investigación posterior ordenada por la Autoridad competente y en consecuencia puede ser ANULADA"));
$pdf->Ln(15);
$pdf->SetFont("Arial", "", 8);
$pdf->WriteTag(190,5,$txt ,0,"J",0,0);
$pdf->Ln(5);
$pdf->SetFont("Arial", "U", 14);
$pdf->Cell(180,5, "CERTIFICADO DE SOLVENCIA", 0, 1, "C");
$pdf->WriteTag(190,10,utf8_decode($txt2) ,0,"J",0,0);
$pdf->Ln(10);
$pdf->SetFont("Arial", "", 8);
$pdf->Cell(65,3, "___________________________________________", 0, 1, "C");
$pdf->Cell(65,3, utf8_decode("DIRECTOR DE ADMINISTRACIÓN TRIBUTARIA"), 0, 1, "C");
$pdf->Cell(315,-8, "___________________________________________", 0, 1, "C");
$pdf->Cell(315,14, utf8_decode("JEFE DE LIQUIDACIÓN"), 0, 1, "C");
$pdf->Cell(195,-5, "ORIGINAL CLIENTE", 0, 1, "C");

$pdf->Image("../Qrs/QrNro".$id.".png", 160, 18, 30, 30,"PNG" );

$pdf->Ln(10);
$pdf->SetXY(10, 130);
$pdf->Cell(150, 20, "-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------", 0, 1, "C");
$pdf->Ln(0);


$pdf->SetFont("Arial", "", 8);
$pdf->Cell(55,3, "REPUBLICA BOLIVARIANA DE VENEZUELA", 0, 1, "C");
$pdf->Cell(55,3, "ESTADO BOLIVARIANO DE MIRANDA", 0, 1, "C");
$pdf->Cell(55,3, "ALCALDIA DEL MUNICIPIO LOS SALIAS", 0, 1, "C");
$pdf->setXY(18,150);
$pdf->Image("../img/logoNuevo.jpg", 18, 160, 40, 20, 'JPG');
$pdf->SetFont("Arial", "B", 8);
$pdf->setXY(10,160);
$pdf->Cell(55,46, "www.alcaldialossalias.gob.ve", 0, 1, "C");
$pdf->SetFont("Arial", "", 6);
$pdf->setXY(10,160);
$pdf->Cell(55,52, "RIF.: G-20003907-8", 0, 1, "C");
$pdf->SetFont("Arial", "", 10);
$pdf->SetXY(135, 150);
$pdf->WriteTag(68, 5,$txt3 ,0,"J",0,0);
$pdf->SetFont("Arial", "", 8);
$pdf->SetXY(90, 162);
$pdf->MultiCell(60,4, utf8_decode("Esta solvencia esta sujeta a investigación posterior ordenada por la Autoridad competente y en consecuencia puede ser ANULADA"));

$pdf->Ln(20);
$pdf->SetFont("Arial", "", 8);
$pdf->WriteTag(190,5,$txt ,0,"J",0,0);
$pdf->Ln(2);

$pdf->SetFont("Arial", "U", 14);
$pdf->Cell(180,5, "CERTIFICADO DE SOLVENCIA", 0, 1, "C");
$pdf->WriteTag(190,10,utf8_decode($txt2) ,0,"J",0,0);
$pdf->Ln(10);
$pdf->SetFont("Arial", "", 8);
$pdf->Cell(65,3, "___________________________________________", 0, 1, "C");
$pdf->Cell(65,3, utf8_decode("DIRECTOR DE ADMINISTRACIÓN TRIBUTARIA"), 0, 1, "C");
$pdf->Cell(315,-8, "___________________________________________", 0, 1, "C");
$pdf->Cell(315,14, utf8_decode("JEFE DE LIQUIDACIÓN"), 0, 1, "C");
$pdf->Cell(195,-5, "COPIA ARCHIVO", 0, 1, "C");

$pdf->Image("../Qrs/QrNro".$id.".png", 165, 165, 30, 30,"PNG" );

$pdf->OutPut();
?>