<?php
namespace Dompdf;
require_once 'require/dompdf/autoload.inc.php';

$dompdf = new Dompdf();

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\assets\AppAsset;

use yii\helpers\Url;

//$rows = Yii::$app->db->createCommand("SELECT * FROM facturas")->queryAll(); 


///conexion
/*error_reporting(E_ALL ^ E_DEPRECATED);
$conexion= mysqli_connect('localhost', 'root', 'r00t123') or die(mysqli_error());
mysqli_select_db($conexion, 'codye043_facturacion') or die(mysqli_error($conexion));*/
$config = include("../config/db.php");
$dsn = $config['dsn'];
preg_match('/dbname=([^;]*)/', $dsn, $matches);
$dbname = $matches[1];
preg_match('/host=([^\;]+)/', $dsn, $matches2);
$host = $matches2[1];
$conexion= mysqli_connect($host,  $config['username'],  $config['password']) or die(mysqli_error());
//$conexion= mysqli_connect('127.0.0.1',  $config['username'],  $config['password']) or die(mysqli_error());
mysqli_select_db($conexion, $dbname) or die(mysqli_error($conexion));
//exit;
//fin conexion


$css='<style>
	html {
		margin: 0px;
	}
	.seccionRedonda{
		border:solid 1px #C7C3C3; border-radius: 15px; padding: 15px; vertical-align: top;
		
	}
	.seccionConceptos{
		padding: 15px 27px; vertical-align: top;
	}
	.sellos{
		padding-left:40px;
		padding-right:27px;
		vertical-align: top;
	}
	hr{
		border: solid 0px; border-top:dotted 1px;
	}
	.factura{
		border:solid 0px; 
		font-family: Arial, Helvetica, sans-serif; 
		font-size:11px; 

	}
	.conceptos{
		font-size:12px;
		font-family: Arial;
	}
	.encabezado{
		border: solid 1px;
		background-color: #C4C1C1;
		font-weight:bold;
	}
	.encabezado td{
		padding: 5px;
		font-size:11px;
	}
	.totales{
		text-align: right;
	}
	.totales td{
		padding-top:0px;
	}
	.footer{
	position: absolute; bottom:0px; width:100%; padding-bottom:12px;
	font-family: Arial; font-size:11px;
	}
	.modoPrueba{
		color:red;
		font-size:18px;
		position:absolute;
		right: 105px;
	}
	.estadoFactura{
		font-size:13px;
		position:absolute;
		right: 45px;
	}
	.colorRojo{
		color:red;
	}
</style>';
// 


$res = mysqli_query($conexion, "SELECT f.*, e.rfcEstablecimiento, e.razonSocialEstablecimiento, e.aliasEstablecimiento, 
				   rf.nombreRegimenFiscal, rf.descripcionRegimenFiscal, 
				   fp.formaPago, fp.descripcion, 
				   mp.metodoPago, mp.descripcion as descripcionPago, 
				   f.modo, f.clienteID, 
				   c.clienteRFC, c.clienteRazonSocial, 
				   uc.usoCFDI, uc.descripcion as descripcionUsoCFDI,
				   c.codigoPostalCliente
			FROM Facturas f
			INNER JOIN Establecimientos e ON e.establecimientoID = f.establecimientoID
			INNER JOIN FERegimenFiscal rf ON e.regimenFiscalID = rf.regimenFiscalID
			INNER JOIN Clientes c ON f.clienteID = c.clienteID
			INNER JOIN FEFormaPago fp ON f.formaPagoID = fp.formaPagoID
			INNER JOIN FEUsoCFDI uc ON f.usoCfdiID = uc.usoCfdiID
			INNER JOIN FEMetodoPago mp ON f.metodoPagoID = mp.metodoPagoID
			WHERE f.facturaID =".$_GET['id']);



if(mysqli_num_rows($res)>0){
	$k = 0;
	$row = mysqli_fetch_array($res);
	$facturaID = $row['facturaID'];
	$empresa = $row['aliasEstablecimiento'] ? $row['razonSocialEstablecimiento'] . " (" . $row['aliasEstablecimiento'] . ")" : $row['razonSocialEstablecimiento'];
	$rfcEmisor = $row['rfcEstablecimiento'];
	$regimenFiscalEmisorCodigo = "(" . $row['nombreRegimenFiscal'] . " " . $row['descripcionRegimenFiscal'] . ")";
	$regimenFiscalEmisor = $row['descripcionRegimenFiscal'];
	$fechaEmision = strtotime($row['fechaFactura']);
	$certificado = $row['certificado'];
	$folio = $row['serie'] . " " . $row['folio'];
	$tipoComprobante = $row['tipoComprobante'];
	$folioFiscal = $row['uuid'];
	$formaPago = "(" . $row['formaPago'] . ") " . $row['descripcion'];
	$metodoPago = "(" . $row['metodoPago'] . ") " . $row['descripcionPago'];
	$sello = $row['sello'];
	$selloSAT = $row['selloSAT'];
	$cadenaoriginal = $row['cadenaoriginal'];
	$fechaTimbrado = strtotime($row['fechaTimbrado']);
	$subTotalFactura = $row['subTotalFactura'];
	$ivaFactura = $row['ivaFactura'];
	$isrFactura = $row['isrFactura'];
	$totalFactura = $row['totalFactura'];
	$importeImagenQR = $totalFactura;
	$lugarExpedicion = $row['lugarExpedicion'];
	$rfcCliente = $row['clienteRFC'];
	$razonSocialCliente = $row['clienteRazonSocial'];
	$nombreUsoCFDI = $row['usoCFDI'];
	$descripcionUsoCFDI = $row['descripcionUsoCFDI'];
	$direccionCliente = $row['codigoPostalCliente'];
	$activoFactura = $row['activoFactura'];
	$empresaID = isset($row['empresaID']) ? $row['empresaID'] : 0;

	
			$tipoComprobanteNombre = $tipoComprobante === "I" ? "Ingreso" : ($tipoComprobante === "E" ? "Egreso" : "Pago");
	
			// $tipoRelacion = "";
			// if ($res['tipoRelacionID']) {
			// 	$tipoRelacionData = Yii::$app->db->createCommand("SELECT * FROM sattiporelacion WHERE tipoRelacionID = :id")
			// 		->bindValue(':id', $res['tipoRelacionID'])
			// 		->queryOne();
			// 	if ($tipoRelacionData) {
			// 		$tipoRelacion = "<strong>Tipo de relación:</strong> (" . $tipoRelacionData['nombreTipoRelacion'] . ") " . $tipoRelacionData['descripcionTipoRelacion'];
			// 	}
			// }
	
			// $cfdiRelacionados = "";
			// if ($res['cfdiRelacionados']) {
			// 	$cadenaCfdi = explode(",", $res['cfdiRelacionados']);
			// 	if (count($cadenaCfdi) > 0) {
			// 		$cfdiRelacionados = "<strong>Cfdi relacionados: </strong>" . implode("<br />", $cadenaCfdi);
			// 	}
			// }
	
	$observaciones='';
	if($row['observaciones'] != "")
		$observaciones='<strong>Observaciones: </strong> '.utf8_encode($row['observaciones']);
	
	
	$sello2= "";
	$selloSAT2= "";
	$cadenaoriginal2= "";
	$contarSello=0;
	if(strlen($sello)>122){
		$contarSello= strlen($sello)/3;
		$sello2= substr($sello, 0, $contarSello)."<br />".substr($sello, $contarSello, $contarSello)."<br />".substr($sello, $contarSello*2, $contarSello)."<br />".substr($sello, $contarSello*3, $contarSello);
	}
	if(strlen($selloSAT)>122){
		$contarSello= strlen($selloSAT)/3;
		$selloSAT2= substr($selloSAT, 0, $contarSello)."<br />".substr($selloSAT, $contarSello, $contarSello)."<br />".substr($selloSAT, $contarSello*2, $contarSello)."<br />".substr($selloSAT, $contarSello*3, $contarSello);
	}
	if(strlen($cadenaoriginal)>122){
		//$cadenaoriginal= base64_encode($cadenaoriginal);
		$contarSello= strlen($cadenaoriginal)/3;
		$cadenaoriginal2= substr($cadenaoriginal, 0, 56).(substr($cadenaoriginal, 56, ($contarSello-56)))."<br />".
			(substr($cadenaoriginal, $contarSello, $contarSello))."<br />".
			(substr($cadenaoriginal, $contarSello*2, $contarSello))."<br />".
			substr($cadenaoriginal, $contarSello*3, $contarSello);
	}
	$observaciones = $row['observaciones'] ? "<strong>Observaciones: </strong> " . utf8_encode($row['observaciones']) : "";
	
			// Función dividirSello corregida
			function dividirSello($sello) {
				$longitud = strlen($sello);
				if ($longitud === 0) {
					return ''; // Retorna vacío si el sello está vacío
				}
				$contarSello = (int) ($longitud / 3);
				return substr($sello, 0, $contarSello) . "<br />" . substr($sello, $contarSello, $contarSello) . "<br />" . substr($sello, $contarSello * 2, $contarSello) . "<br />" . substr($sello, $contarSello * 3, $contarSello);
			}
	
			$sello2 = strlen($sello) > 122 ? dividirSello($sello) : $sello;
			$selloSAT2 = strlen($selloSAT) > 122 ? dividirSello($selloSAT) : $selloSAT;
			$cadenaoriginal2 = strlen($cadenaoriginal) > 122 ? dividirSello($cadenaoriginal) : $cadenaoriginal;
	
			$modoTimbrado = $row['modo'] === "PRUEBAS" ? '<label class="modoPrueba">Modo Pruebas</label>' : '';
			$colorRojo = $activoFactura === "Cancelada" ? "colorRojo" : "";
			$activoFactura = '<label class="estadoFactura ' . $colorRojo . '">' . $activoFactura . '</label>';
			
			include("imagen.php");
			// $imagenEmpresa4 = "";
			$colorTextHeader = "black";
			$styleCodyFondoHeader = 'background-repeat: no-repeat; margin:-1px 0 0 -1px; padding:0px;';
			
			$imagenEmpresa4 = '<div align="center" ><img src="logos/footer_1_20240625055043.png" alt="Logo" style="align-items: center;"></div>';

	$html= $css.'
	<table width="613" align="center" class="factura" style="'.$styleCodyFondoHeader.'">
		<tr>
			<td colspan="2" align="left" style="height:130px; padding:20px 25px 0 25px; color:'.$colorTextHeader.'; line-height:17px; font-size:11px;">
				'.$imagenEmpresa4.'
				<div style="margin-bottom: 10px; border: solid 0px;"><b>Datos del emisor:</b></div>
				<div style="position: relative;">
					'.$empresa.'
					<br />
					Rfc: '.$rfcEmisor.'
					<br />
					Régimen fiscal: '.$regimenFiscalEmisorCodigo.'
					<br />
					'.$regimenFiscalEmisor.'
					<br />
					<div style="position: absolute; top:20px; right: 20px; color: '.$colorTextHeader.';">
						<label style="margin-right: 13px;">
							<strong>Folio:</strong> '.$folio.'
						</label>
						<br />
						<label>
							<strong>Fecha de emisión:</strong> '.date('d-m-Y h:i:s', $fechaEmision).'
						</label>
						<br />
						<label>
							<strong>Certificado:</strong>
							'.$certificado.'
						</label>
					</div>
				</div>
			</td>
		</tr>
		<tr style="line-height:20px;">
			<td valign="top" width="50%">
				<div class="seccionRedonda" style="margin-left:25px; height:135px;">
					<strong>Datos del receptor</strong>
					<hr>
					<span><strong>RFC:</strong> '.$rfcCliente.'</span>
					<br>
					<span><strong>Nombre receptor:</strong> '.$razonSocialCliente.'</span>
					<br>
					<span><strong>Uso CFDI:</strong> ('.$nombreUsoCFDI.") ".$descripcionUsoCFDI.'</span>
					<br>
					<span><strong>Dirección:</strong> '.$direccionCliente.' </span>
				</div>
			</td>
			<td valign="top">
				<div class="seccionRedonda" style="margin-right:25px;">
					<strong>Datos de la factura</strong>
					'.$modoTimbrado.'
					'.$activoFactura.'
					<hr>

					<span><strong>Folio fiscal:</strong>
					'.$folioFiscal.'</span>
					<br />
					<span><strong>Tipo de comprobante:</strong> ('.$tipoComprobante.') '.$tipoComprobanteNombre.'</span>
					<br />
					<span><strong>Forma de pago:</strong> '.$formaPago.'</span>
					<br />
					<span><strong>Método de pago:</strong> '.$metodoPago.'</span>
					<br />
					<span><strong>Moneda:</strong> Peso Mexicano</span>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="seccionConceptos">
				<strong>Conceptos</strong>
				<hr>
				<table width="570" class="conceptos" style="border:solid 0px;">
					<tr class="encabezado">
						<td width="60">
							Clave producto y/o servicio
						</td>
						<td width="60">
							Clave de unidad
						</td>
						<td>
							Descripción
						</td>
						<td width="35">
							Cantidad
						</td>
						<td width="70" align="right">
							PU
						</td>
						<td width="70" align="right">
							Importe
						</td>
					</tr>';
	$resDet = mysqli_query($conexion, "
								SELECT d.*, p.nombreProducto, p.codigoProducto, cu.claveUnidad, cps.descripcion as claveProductoServicio
								FROM FacturasDetalles d
								INNER JOIN Productos p ON d.productoID = p.productoID
								LEFT JOIN FEClaveProdServ cps ON p.claveProdServID = cps.claveProdServID
								LEFT JOIN FEClaveUnidad cu ON p.claveUnidadID = cu.claveUnidadID
								WHERE d.facturaID = ".$_GET['id']);

							foreach ($resDet as $row) {
								$html .= '
									<tr>
										<td>' . htmlspecialchars($row['claveProductoServicio']) . '</td>
										<td>' . htmlspecialchars($row['claveUnidad']) . '</td>
										<td>' . htmlspecialchars($row['nombreProducto']) . '</td>
										<td>' . htmlspecialchars($row['cantidadDetalleFactura']) . '</td>
										<td align="right">$' . number_format($row['precioDetalleFactura'], 2) . '</td>
										<td align="right">$' . number_format($row['subTotalDetalleFactura'], 2) . '</td>
									</tr>';
							}
				$html.='
								<tr>
									<td colspan="4">
									</td>
									<td colspan="2">
										<hr>
									</td>
								</tr>
								<tr class="totales">
									<td colspan="4">
									</td>
									<td align="right"><strong>  Subtotal</strong></td>
									<td align="right">$<span id="total_sub">'.number_format($subTotalFactura, 2).'</span> 
									</td>
								</tr>
								
									<!--<tr>
									<td colspan="4">
									</td>
									<td align="right"><strong>Descuento</strong></td>
									<td align="right">$<span id="total_descuento">00.00</span>
									</td>
								</tr>-->
							
								<tr>
									<td colspan="4" align="left">
										'.$tipoRelacion.'
									</td>
									<td align="right"><strong>Iva</strong></td>
									<td align="right">$<span id="total_iva">'.number_format($ivaFactura, 2).'</span>
									</td>	
								</tr>
								<!--<tr>
									<td colspan="4">
									'.$cfdiRelacionados.'
									</td>
									<td align="right"><strong>ISR</strong></td>
									 <td align="right">$<span id="total_isr">'.number_format($isrFactura, 2).'</span> 
									</td>
								</tr>-->
								<tr>
									<td colspan="4" align="left">
									</td>
									<td align="right" valign="top"><strong>Total</strong></td>
									<td align="right" valign="top">$<span id="total_suma">'.number_format($totalFactura, 2).'</span>
									</td>
								</tr>
							</table>
							<hr />
						</td>
					</tr>
					<tr>
						<td class="sellos" colspan="2" width="500" style="hyphens: auto; word-wrap: break-word; word-break: break-word; font-size:11px;">
							<strong>Sello digital del CFDI:</strong>
							<br />
							'.$sello2.'

							<br />
							<strong>Sello digital del SAT:</strong>
							<br />
							'.$selloSAT2.'
							
						</td>
					</tr>
					<tr>
						<td colspan="2" valign="top">
							<table class="sellos">
								<tr>
									<td width="80" style="border;" valign="top">
									<img src="require/images/QRCode/'.$_GET['id'].'.png" width="100" />
									
										
									</td>
									<td style="hyphens: auto; word-wrap: break-word; word-break: break-word; font-size:11px;">
										<strong>Cadena Original del complemento de certificación digital del SAT:</strong>
										<br />
										'.$cadenaoriginal2.'
										<br />
										<br />
									
										<strong>RFC del proveedor de certificación:</strong> PPD101129EA3
										&nbsp; &nbsp; &nbsp; &nbsp; 
										<br />
										<strong>Fecha y hora de certificación:</strong> '.date('d-m-Y h:i:s', $fechaTimbrado).'
										<br />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="left" style="padding-top: 5px; padding-left:45px;" class="seccionConceptos">
							'.$observaciones.'
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="padding-top: 20px;">

						</td>
					</tr>
				</table>
				<div class="footer" align="left" style="padding-left: 110px;">
					<strong>Expedido en:</strong> '.$lugarExpedicion.'
				</div>
				<div class="footer" align="right" style="background-image: url(require/img/bannercodyfooter.jpg); background-repeat: no-repeat; height:70px; background-position: right; color:black; padding-right:25px; font-family: Arial, Helvetica, sans-serif;" valign="bottom">
				<br />
				<br />
				<br />
				<div style="padding-left: 25px;">
				<strong>Este documento es una representación impresa de un CFDI</strong>
				</div>
				<br />
				</div>';
}
else{
	$html= $css.'Error al consultar la factura seleccionada';

}

//echo $html;


$dompdf->loadHtml($html);
$dompdf->setPaper('L', 'Letter');
//$pdf = new PDF_HTML('L', 'mm', 'Letter');
$dompdf->render();

$pdfOutput = $dompdf->output();  // contenido del PDF
$pdfPath = 'SAT/factura'.$facturaID.'.pdf';
//file_put_contents($pdfPath, $pdfOutput);


//$dompdf->set_option('defaultFont', 'Arial');
//$dompdf->stream("mi_archivo.pdf");
$dompdf->stream("",array("Attachment" => false));
file_put_contents($pdfPath, $pdfOutput);
//$dompdf->set_option('defaultFont', 'Arial');
//$dompdf->stream("mi_archivo.pdf");
// exit(0);/**/
?>