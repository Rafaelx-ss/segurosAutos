<?php

namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use Exception;
use SimpleXMLElement;
use DateTime;
use ZipArchive;

class Utils extends Component
{
    /**
     * Analiza y valida un archivo XML de CFDI.
     *
     * @param string $fileContent El contenido del archivo XML a analizar.
     * @return array<string, mixed> Retorna un array con los datos del CFDI validado.
     * @throws Exception Si el archivo no es un CFDI válido o si la versión no es soportada.
     */
    public function parseAndValidateXML($fileContent)
    {
        try {
            // Parsear XML
            $xml = new SimpleXMLElement($fileContent);

            // Validar que sea CFDI
            $namespaces = $xml->getNamespaces(true);
            if (!isset($namespaces['cfdi'])) {
                throw new Exception('El archivo no es un CFDI válido');
            }

            // Registrar namespaces
            // Usamos cfdi/4 para compatibilidad, pero el parser lee 3.3 también
            $xml->registerXPathNamespace('cfdi', 'http://www.sat.gob.mx/cfd/4');
            $xml->registerXPathNamespace('tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
            $xml->registerXPathNamespace('cartaporte31', 'http://www.sat.gob.mx/CartaPorte31');
            $xml->registerXPathNamespace('cartaporte30', 'http://www.sat.gob.mx/CartaPorte30');
            $xml->registerXPathNamespace('cartaporte20', 'http://www.sat.gob.mx/CartaPorte20');

            // Validar versión CFDI
            $version = (string)$xml['Version'];
            if (!in_array($version, ['3.3', '4.0'])) {
                throw new Exception('Versión de CFDI no soportada: ' . $version);
            }

            // Extraer datos básicos del comprobante
            $tipoComprobanteAttr = (string)$xml['TipoDeComprobante'];
            $tipoComprobante = ($tipoComprobanteAttr === 'I') ? 'Ingreso' : (($tipoComprobanteAttr === 'E') ? 'Egreso' : (($tipoComprobanteAttr === 'T') ? 'Traslado' : null));

            $data = [
                'version' => $version,
                'serie' => (string)$xml['Serie'],
                'folio' => (string)$xml['Folio'],
                'fecha' => (string)$xml['Fecha'],
                'subTotal' => (float)$xml['SubTotal'],
                'total' => (float)$xml['Total'],
                'moneda' => (string)$xml['Moneda'],
                'tipoComprobante' => $tipoComprobante,
                'metodoPago' => (string)$xml['MetodoPago'],
                'formaPago' => (string)$xml['FormaPago'],
                'lugarExpedicion' => (string)$xml['LugarExpedicion']
            ];

            // Validar tipo de comprobante
            if ($data['tipoComprobante'] == null) {
                throw new Exception('El tipo de comprobante no es válido');
            }

            // --- Extraer datos del emisor (CORRECCIÓN: Validar existencia) ---
            $emisores = $xml->xpath('//cfdi:Emisor');
            if (empty($emisores)) {
                throw new Exception('No se encontró información del emisor');
            }
            $emisor = $emisores[0];

            $data['emisor'] = [
                'rfc' => (string)$emisor['Rfc'],
                'nombre' => (string)$emisor['Nombre'],
                'regimenFiscal' => (string)$emisor['RegimenFiscal']
            ];

            // --- Extraer datos del receptor (CORRECCIÓN: Validar existencia) ---
            $receptores = $xml->xpath('//cfdi:Receptor');
            if (empty($receptores)) {
                throw new Exception('No se encontró información del receptor');
            }
            $receptor = $receptores[0];

            $data['receptor'] = [
                'rfc' => (string)$receptor['Rfc'],
                'nombre' => (string)$receptor['Nombre'],
                'usoCFDI' => (string)$receptor['UsoCFDI']
            ];

            // Extraer conceptos
            $conceptos = $xml->xpath('//cfdi:Concepto');
            if (empty($conceptos)) {
                throw new Exception('No se encontraron conceptos en el CFDI');
            }

            $data['conceptos'] = [];
            foreach ($conceptos as $concepto) {
                $conceptoData = [
                    'claveProdServ'   => (string)$concepto['ClaveProdServ'],
                    'cantidad'        => (float)$concepto['Cantidad'],
                    'claveUnidad'     => (string)$concepto['ClaveUnidad'],
                    'unidad'          => (string)$concepto['Unidad'],
                    'descripcion'     => (string)$concepto['Descripcion'],
                    'valorUnitario'   => (float)$concepto['ValorUnitario'],
                    'importe'         => (float)$concepto['Importe'],
                    'impuestosTrasladados' => []
                ];

                // Extraer impuestos del concepto (cfdi:Impuestos)
                $impuestosConcepto = $concepto->xpath('./cfdi:Impuestos');
                if (!empty($impuestosConcepto)) {
                    // Puede haber traslados (cfdi:Traslados/cfdi:Traslado) dentro de cfdi:Impuestos
                    $traslados = $impuestosConcepto[0]->xpath('./cfdi:Traslados/cfdi:Traslado');
                    foreach ($traslados as $traslado) {
                        $conceptoData['impuestosTrasladados'][] = [
                            'base'        => isset($traslado['Base']) ? (float)$traslado['Base'] : null,
                            'impuesto'    => (string)$traslado['Impuesto'],
                            'tipoFactor'  => (string)$traslado['TipoFactor'],
                            'tasaOCuota'  => isset($traslado['TasaOCuota']) ? (float)$traslado['TasaOCuota'] : null,
                            'importe'     => isset($traslado['Importe']) ? (float)$traslado['Importe'] : null,
                        ];
                    }
                }
                $data['conceptos'][] = $conceptoData;
            }

            // --- Extraer impuestos (CORRECCIÓN: Validar existencia) ---
            $impuestosArray = $xml->xpath('//cfdi:Impuestos');

            $data['totalImpuestosTrasladados'] = (isset($impuestosArray[1])) ? ((float)$impuestosArray[1]['TotalImpuestosTrasladados'] ?? 0) : 0;

            // --- Extraer timbre fiscal (CORRECCIÓN: Validar existencia) ---
            $timbres = $xml->xpath('//tfd:TimbreFiscalDigital');
            if (empty($timbres)) {
                throw new Exception('No se encontró el timbre fiscal digital');
            }
            $timbre = $timbres[0];

            $data['uuid'] = (string)$timbre['UUID'];
            $data['fechaTimbrado'] = (string)$timbre['FechaTimbrado'];
            $data['rfcProvCertif'] = (string)$timbre['RfcProvCertif'];


            // --- Extraer datos del complemento CartaPorte (soporte para versión 2.0, 3.0 y 3.1) ---
            $cartaPorte = null;
            $cartaPorteVersion = null;

            // Intentar con CartaPorte 3.1, 3.0 y 2.0
            $cartaPorte31 = $xml->xpath('//cartaporte31:CartaPorte');
            if (!empty($cartaPorte31)) {
                $cartaPorte = $cartaPorte31[0];
                $cartaPorteVersion = '3.1';
            } else {
                $cartaPorte30 = $xml->xpath('//cartaporte30:CartaPorte');
                if (!empty($cartaPorte30)) {
                    $cartaPorte = $cartaPorte30[0];
                    $cartaPorteVersion = '3.0';
                } else {
                    $cartaPorte20 = $xml->xpath('//cartaporte20:CartaPorte');
                    if (!empty($cartaPorte20)) {
                        $cartaPorte = $cartaPorte20[0];
                        $cartaPorteVersion = '2.0';
                    }
                }
            }

            if ($cartaPorte) {
                $data['cartaPorte'] = [
                    'idCCP' => (string)$cartaPorte['IdCCP'],
                    'totalDistRec' => (string)$cartaPorte['TotalDistRec'],
                    'transpInternac' => (string)$cartaPorte['TranspInternac'],
                    'version' => (string)$cartaPorte['Version']
                ];

                // --- Extraer mercancías de CartaPorte (soporte para las tres versiones) ---
                $mercancias = null;
                $mercanciasXPath = '';

                if ($cartaPorteVersion === '3.1') {
                    $mercanciasXPath = '//cartaporte31:Mercancias';
                } elseif ($cartaPorteVersion === '3.0') {
                    $mercanciasXPath = '//cartaporte30:Mercancias';
                } else {
                    $mercanciasXPath = '//cartaporte20:Mercancias';
                }

                $mercanciasArray = $xml->xpath($mercanciasXPath);

                if (!empty($mercanciasArray)) {
                    $mercancias = $mercanciasArray[0];
                    $data['cartaPorte']['mercancias'] = [
                        'numTotalMercancias' => (string)$mercancias['NumTotalMercancias'],
                        'pesoBrutoTotal' => (string)$mercancias['PesoBrutoTotal'],
                        'unidadPeso' => (string)$mercancias['UnidadPeso']
                    ];

                    // Extraer cada mercancía individual
                    $mercanciaListXPath = '';
                    if ($cartaPorteVersion === '3.1') {
                        $mercanciaListXPath = '//cartaporte31:Mercancia';
                    } elseif ($cartaPorteVersion === '3.0') {
                        $mercanciaListXPath = '//cartaporte30:Mercancia';
                    } else {
                        $mercanciaListXPath = '//cartaporte20:Mercancia';
                    }

                    $mercanciasList = $xml->xpath($mercanciaListXPath);

                    $data['cartaPorte']['mercancias']['lista'] = [];
                    foreach ($mercanciasList as $mercancia) {
                        $data['cartaPorte']['mercancias']['lista'][] = [
                            'bienesTransp' => (string)$mercancia['BienesTransp'],
                            'cantidad' => (float)$mercancia['Cantidad'],
                            'claveUnidad' => (string)$mercancia['ClaveUnidad'],
                            'descripcion' => (string)$mercancia['Descripcion'],
                            'moneda' => (string)$mercancia['Moneda'],
                            'pesoEnKg' => (float)$mercancia['PesoEnKg'],
                            'unidad' => (string)$mercancia['Unidad'],
                            'valorMercancia' => (float)$mercancia['ValorMercancia']
                        ];
                    }
                }

                // --- Extraer información de autotransporte si existe (soporte para las tres versiones) ---
                $autotransporte = null;
                $autotransporteXPath = '';

                if ($cartaPorteVersion === '3.1') {
                    $autotransporteXPath = '//cartaporte31:Autotransporte';
                } elseif ($cartaPorteVersion === '3.0') {
                    $autotransporteXPath = '//cartaporte30:Autotransporte';
                } else {
                    $autotransporteXPath = '//cartaporte20:Autotransporte';
                }

                $autotransporteArray = $xml->xpath($autotransporteXPath);

                if (!empty($autotransporteArray)) {
                    $autotransporte = $autotransporteArray[0];
                    $data['cartaPorte']['autotransporte'] = [
                        'numPermisoSCT' => (string)$autotransporte['NumPermisoSCT'],
                        'permSCT' => (string)$autotransporte['PermSCT']
                    ];

                    // Información del vehículo
                    $identificacionVehicular = null;
                    $identificacionVehicularXPath = '';

                    if ($cartaPorteVersion === '3.1') {
                        $identificacionVehicularXPath = '//cartaporte31:IdentificacionVehicular';
                    } elseif ($cartaPorteVersion === '3.0') {
                        $identificacionVehicularXPath = '//cartaporte30:IdentificacionVehicular';
                    } else {
                        $identificacionVehicularXPath = '//cartaporte20:IdentificacionVehicular';
                    }

                    $identificacionVehicularArray = $xml->xpath($identificacionVehicularXPath);

                    if (!empty($identificacionVehicularArray)) {
                        $identificacionVehicular = $identificacionVehicularArray[0];
                        $data['cartaPorte']['autotransporte']['vehiculo'] = [
                            'anioModeloVM' => (string)$identificacionVehicular['AnioModeloVM'],
                            'configVehicular' => (string)$identificacionVehicular['ConfigVehicular'],
                            'pesoBrutoVehicular' => (string)$identificacionVehicular['PesoBrutoVehicular'],
                            'placaVM' => (string)$identificacionVehicular['PlacaVM']
                        ];
                    }

                    // Información de seguros
                    $seguros = null;
                    $segurosXPath = '';

                    if ($cartaPorteVersion === '3.1') {
                        $segurosXPath = '//cartaporte31:Seguros';
                    } elseif ($cartaPorteVersion === '3.0') {
                        $segurosXPath = '//cartaporte30:Seguros';
                    } else {
                        $segurosXPath = '//cartaporte20:Seguros';
                    }

                    $segurosArray = $xml->xpath($segurosXPath);

                    if (!empty($segurosArray)) {
                        $seguros = $segurosArray[0];
                        $data['cartaPorte']['autotransporte']['seguros'] = [
                            'aseguraRespCivil' => (string)$seguros['AseguraRespCivil'],
                            'polizaRespCivil' => (string)$seguros['PolizaRespCivil']
                        ];
                    }

                    // Información de remolques
                    $remolques = null;
                    $remolquesXPath = '';

                    if ($cartaPorteVersion === '3.1') {
                        $remolquesXPath = '//cartaporte31:Remolque';
                    } elseif ($cartaPorteVersion === '3.0') {
                        $remolquesXPath = '//cartaporte30:Remolque';
                    } else {
                        $remolquesXPath = '//cartaporte20:Remolque';
                    }

                    $remolquesList = $xml->xpath($remolquesXPath);

                    if (!empty($remolquesList)) {
                        $data['cartaPorte']['autotransporte']['remolques'] = [];
                        foreach ($remolquesList as $remolque) {
                            $data['cartaPorte']['autotransporte']['remolques'][] = [
                                'placa' => (string)$remolque['Placa'],
                                'subTipoRem' => (string)$remolque['SubTipoRem']
                            ];
                        }
                    }
                }
            }

            return $data;
        } catch (Exception $e) {
            if ($e instanceof Exception && strpos($e->getMessage(), 'String could not be parsed as XML') !== false) {
                throw new Exception('El archivo no es un XML válido');
            }
            throw $e;
        }
    }

    /**
     * Formatea el valor del volumen según la especificación de Control Volumétrico del SAT.
     * Asegura tres decimales y establece la unidad de medida estándar (UM03).
     *
     * @param float|string $valor El volumen a formatear (puede ser un float o un string numérico).
     * @return array<string, string> Retorna un array con el valor numérico formateado y la unidad.
     * @psalm-return array{ValorNumerico: string, UnidadDeMedida: 'UM03'}
     */
    public function formatoVolumen($valor)
    {
        return [
            'ValorNumerico' => number_format((float)$valor, 3, '.', ''),
            'UnidadDeMedida' => 'UM03'
        ];
    }

    /**
     * Formatea el valor de la moneda según la especificación del SAT.
     * Asegura tres decimales.
     * 
     * @param float|string $valor El valor a formatear (puede ser un float o un string numérico).
     * @return string Retorna el valor formateado con tres decimales.
     */
    public function formatoMoneda($valor, $decimales = 3, $separadorMil = '')
    {
        return number_format((float)$valor, $decimales, '.', $separadorMil);
    }

    /**
     * Formatea la fecha según el formato ISO 8601.
     *
     * @param string $valor La fecha a formatear.
     * @return string|null Retorna la fecha formateada en formato ISO 8601 o null si la fecha es inválida.
     */
    public function formatoFecha($valor)
    {
        if (!$valor) return null;
        $objDateTime = new DateTime($valor);
        return $objDateTime->format('c'); // ISO 8601
    }

    /**
     * Formatea la hora según el formato ISO 8601.
     *
     * @param string $valor La hora a formatear.
     * @return string|null Retorna la hora formateada en formato ISO 8601 o null si la hora es inválida.
     */
    public function formatoHora($valor)
    {
        if (!$valor) return null;
        $objDateTime = new DateTime($valor);
        return $objDateTime->format('H:i:sP'); // ISO8601 formated datetime
    }


    /**
     * Formatea la geolocalización según la especificación del SAT.
     *
     * @param float $lat La latitud a formatear.
     * @param float $long La longitud a formatear.
     * @return array<string, string> Retorna un array con la latitud y la longitud formateadas.
     * @psalm-return array{GeolocalizacionLatitud: string, GeolocalizacionLongitud: string}
     */
    public function formatoGeo($lat, $long)
    {
        if ($lat > 0 && $long > 0) {
            return [
                'GeolocalizacionLatitud' => number_format($lat, 7, '.', ''),
                'GeolocalizacionLongitud' => number_format($long, 7, '.', '')
            ];
        }
        return null;
    }

    /**
     * Valida el RFC según la especificación del SAT.
     * Utiliza expresiones regulares para validar el RFC.
     *
     * @param string $rfc El RFC a validar.
     * @return string|null Retorna el RFC validado o null si es inválido.
     */
    public function validaRfc($rfc)
    {
        if (empty($rfc)) return null;
        $rfc = strtoupper(trim($rfc));
        if (!preg_match('/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/', $rfc)) {
            return null;
        }
        return $rfc;
    }

    /**
     * Valida el UUID según la especificación del SAT.
     * Utiliza expresiones regulares para validar el UUID.
     *
     * @param string $uuid El UUID a validar.
     * @return string|null Retorna el UUID validado o null si es inválido.
     */
    public function validaUuid($uuid)
    {
        if (empty($uuid)) return null;
        $uuid = strtoupper(trim($uuid));
        if (!preg_match('/^[a-f0-9A-F]{8}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{12}$/', $uuid)) {
            return null;
        }
        return $uuid;
    }

    /**
     * Mapea el tipo de comprobante según el contexto.
     *
     * @param string $tipo El tipo de comprobante a mapear.
     * @param string $contexto El contexto del tipo de comprobante.
     * @return string Retorna el tipo de comprobante mapeado.
     */
    public function mapeoTipoCfdi($tipo, $contexto = 'Entrega')
    {
        if (empty($tipo)) {
            return ($contexto === 'Recepcion') ? 'Egreso' : 'Ingreso';
        }

        $tipo = strtoupper(trim($tipo));

        // Normaliza los valores más comunes del SAT
        switch ($tipo) {
            case 'I':   // Ingreso
            case 'INGRESO':
                return 'Ingreso';

            case 'E':   // Egreso
            case 'EGRESO':
                return 'Egreso';

            case 'P':   // Pago
            case 'RP':  // Recepción de Pagos
            case 'N':   // Nómina
            case 'T':   // Traslado
            default:
                // Si el contexto es Recepción (compras), marca como Egreso
                return ($contexto === 'Recepcion') ? 'Egreso' : 'Ingreso';
        }
    }

    /**
     * Genera un GUID único.
     *
     * @return string Retorna el GUID generado.
     */
    public function generarGUID()
    {
        return strtoupper(sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        ));
    }

    /**
     * Crea el nombre del archivo JSON y ZIP.
     *
     * @param string $tipo El tipo de comprobante.
     * @param string $rfcEstablecimiento El RFC del establecimiento.
     * @param string $rfcProveedor El RFC del proveedor.
     * @param string $periodo El periodo del reporte.
     * @param string $claveInstalacion La clave de instalación.
     * @param string $tipoReporte El tipo de reporte.
     * @return string Retorna el nombre del archivo JSON y ZIP.
     */
    public function creaNombreArchivoJsonZip($tipo, $rfcEstablecimiento, $rfcProveedor, $periodo, $claveInstalacion, $tipoReporte)
    {
        $nombre = $tipo . '_' . $this->generarGUID() . '_' .
            strtoupper($rfcEstablecimiento) . '_' .
            strtoupper($rfcProveedor) . '_' .
            date('Y-m-d', strtotime($periodo)) . '_' .
            strtoupper($claveInstalacion) . '_' .
            strtoupper($tipoReporte) . '_JSON.zip';
        return $nombre;
    }

    /**
     * Guarda el archivo JSON y ZIP.
     *
     * @param array $jsonraiz Los datos a guardar en el archivo JSON.
     * @param string $nombreArchivo El nombre del archivo JSON.
     * @param string $ruta La ruta donde se guardará el archivo JSON y ZIP.
     * @return bool Retorna true si el archivo se guardó correctamente, false en caso contrario.
     * @throws Exception Si no hay datos para guardar el archivo JSON.
     */
    public function guardaArchivo($jsonraiz, $nombreArchivo, $ruta)
    {
        try {
            if (!$jsonraiz) {
                throw new Exception("No hay datos para guardar el archivo JSON.");
            }

            $json = json_encode($jsonraiz, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $nombreJson = substr($nombreArchivo, 0, strpos($nombreArchivo, ".")) . '.json';

            $jsonFiltrado = $this->filtraJson($json);

            // Guardar JSON temporal
            file_put_contents($ruta . $nombreJson, $jsonFiltrado);

            // Crear archivo ZIP
            $zip = new ZipArchive();
            $zip->open($ruta . $nombreArchivo, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            $zip->addFile($ruta . $nombreJson, $nombreJson);
            $zip->close();

            // Eliminar JSON temporal
            unlink($ruta . $nombreJson);

            return true;
        } catch (Exception $e) {
            Yii::error("Error al guardar archivo ZIP: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Filtra el JSON eliminando nulos y caracteres residuales.
     *
     * @param string $json El JSON a filtrar.
     * @return string Retorna el JSON filtrado.
     */
    public function filtraJson($json)
    {
        // Limpia nulos y caracteres residuales
        return preg_replace('/,\s*"[^"]+":null|\||"[^"]+":null,?/', '', $json);
    }

    /**
     * Elimina los espacios en blanco de una cadena.
     *
     * @param string $cadena La cadena a limpiar.
     * @return string Retorna la cadena sin espacios en blanco.
     */
    public function noEspacios($cadena)
    {
        return str_replace(' ', '', $cadena);
    }



    public function ConsumirApi($establecimientoID, $enpointToken = '/api/ApiToken.php', $enpointConsume, $metodo = 'GET', $parametros = null, $aplicacion = 'BonoboGrupo')
    {
        /**
         * @Consulta el usuario y contraseña segun el establecimiento y la aplicación.
         * 
         * @param string $establecimientoID El ID del establecimiento.
         * @param string $aplicacion La aplicación a consumir.
         */

        $queryCredenciales = Yii::$app->db->createCommand(
            "SELECT
						ConexionesApis.rutaApi,
						ConexionesApis.usuario,
						ConexionesApis.password
					FROM
						ConexionesApis
						INNER JOIN ConexionesApisEstablecimientos ON ConexionesApisEstablecimientos.conexionApiID = ConexionesApis.conexionApiID
						INNER JOIN AplicacionesConexionApi ON AplicacionesConexionApi.aplicacionConexionApiID = ConexionesApis.aplicacionConexionApiID
					WHERE
						descripcion = :aplicacion
						AND establecimientoID = :establecimientoID"
        )->bindValues([
            ':aplicacion' => $aplicacion,
            ':establecimientoID' => $establecimientoID
        ])->queryOne();

        if (!isset($queryCredenciales['usuario']) || !isset($queryCredenciales['password']) || !isset($queryCredenciales['rutaApi'])) {
            return [
                "resultado" => false,
                "mensaje" => "Tuvimos un problema al verificar las credenciales, intenta de nuevo",
                "datos" => [],
                "desarrollo" => null
            ];
        }

        // if (empty($queryCredenciales['password'])) {

        //     $password = 'EGzqUWbXXDAi5YoBxWZdGPz50DNcAnu057GgbovIzaE/u6MXSjoJgl+IBRKxodDXUv1LIb+xYHeZFG6pON1bG3vJgU2aTd5/Tq8wZ44xeowvzgSLS+QLlOMBsK4KzihkHu4G/NxPgGdS3IEHaH52sod/3HUEv8vORZDov197WHjHRUMI77qSLtf/fPGqWEOEzhx8ksrgazfefQaKSLsHT/wmopVSp1gey26DWFi0py42NMMEf68QvgtRQwNhjc33ZC0DcgvbwTygRTOWrnl1CwTX4W/Wv3kjMjMcwWKkD7Zx849pQl0C9sZ/nbJsd3syex2AXmRWP/FzDgC0Ib2cUw==';

        //     $updatePassword = Yii::$app->db->createCommand("UPDATE ConexionesApis SET password = :password WHERE usuario = :usuario")->bindValues([
        //         ':password' => $password,
        //         ':usuario' => $queryCredenciales['usuario']
        //     ])->execute();

        //     if ($updatePassword) {
        //         $queryCredenciales['password'] = $password;
        //     }
        // }

        // Cargar llaves solo si la variable no existe globalmente
        if (!isset($GLOBALS['llaveprivada']) && !(isset($llaveprivada))) {
            require_once(Yii::$app->basePath . "/clientemigracion/config/llaves.php");
            // Guardar en GLOBALS para que persista entre llamadas
            if (isset($llaveprivada)) {
                $GLOBALS['llaveprivada'] = $llaveprivada;
            }
        }

        // Usar la variable de GLOBALS si existe, sino la local
        $llaveprivada = isset($GLOBALS['llaveprivada']) ? $GLOBALS['llaveprivada'] : (isset($llaveprivada) ? $llaveprivada : null);

        if (empty($llaveprivada)) {
            return [
                "resultado" => false,
                "mensaje" => "Error: No se pudo cargar la llave privada",
                "datos" => [],
                "desarrollo" => null
            ];
        }

        $Username = $queryCredenciales['usuario'];
        openssl_private_decrypt(base64_decode($queryCredenciales['password']), $PasswordGetdec, $llaveprivada);
        $Password = $PasswordGetdec;
        $baseUrl = $queryCredenciales['rutaApi'];
        $dataMacAddress = 'd4:61:9d:01:85:18';

        // Usar require_once para evitar errores al usar en bucles
        if (!class_exists('ApiHttpClient')) {
            require_once(Yii::$app->basePath . "/clientemigracion/libs/ApiHttpClient.php");
        }

        // Verificar que la clase se cargó correctamente
        if (!class_exists('ApiHttpClient')) {
            return [
                "resultado" => false,
                "mensaje" => 'Tuvimos un problema el cargar dependencias, por favor consulte a soporte.',
                "datos" => [],
                "desarrollo" => null
            ];
        }

        \ApiHttpClient::Init($baseUrl);
        \ApiHttpClient::$UserName = $Username;
        \ApiHttpClient::$Password = $Password;
        \ApiHttpClient::$MacAddress = $dataMacAddress;

        \ApiHttpClient::SolicitaToken($enpointToken);

        try {
            if (!\ApiHttpClient::$Resultado) {
                return [
                    "resultado" => false,
                    "mensaje" => \ApiHttpClient::$Mensaje,
                    "datos" => [],
                    "desarrollo" => null
                ];
            }
        } catch (Exception $e) {
            return [
                "resultado" => false,
                "mensaje" => $e->getMessage(),
                "datos" => [],
                "desarrollo" => null
            ];
        }

        $response = \ApiHttpClient::ConsumeApi($enpointConsume, $metodo, $parametros);

        return json_decode($response, true);
    }


    public function subirArchivoAWS($filebase64, $filename, $establecimientoID, $nombreSitio)
    {

        $parametros = json_encode(array(
            "datosEnvioAWS" => array(
                "nombreDestinoS3" => "ArchivosBonobo/" . $nombreSitio . "/" . $establecimientoID . "/" . $filename,
                "archivoBase64" => $filebase64
            )
        ));

        return $this->ConsumirApi($establecimientoID, '/api/ApiToken.php', '/api/ApiUploadAWS.php', 'POST', $parametros, 'BonoboGrupo');
    }


    /**
     * @method Obtiene el nombre del sitio de la URL actual.
     * 
     * @description Utiliza expresiones regulares para obtener el nombre del sitio de la URL actual.
     * @Sin importar si es http o https
     * @Sin importar si estas en un entorno de desarrollo o producción
     * 
     * @return string Retorna el nombre del sitio.
     */
    public function getNombreSitio()
    {
        //URL
        $urlActual = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $nombreSitio = '';
        if (preg_match('#(?:/sites/|://[^/]+/)([^/]+)/web/#', $urlActual, $matches)) {
            $nombreSitio = $matches[1];
        }
        return $nombreSitio;
    }


    /**
     * @method Obtiene el nombre de un archivo de AWS o local.
     * 
     * @description Utiliza expresiones regulares para obtener el nombre del archivo.
     * @description Si es URL, extraemos el nombre con la regex y lo mostramos
     * @description Si NO es URL, mostramos tal cual
     * 
     * @param string $archivo El archivo a obtener el nombre.
     * 
     * @return string Retorna el nombre del archivo.
     */
    public function getNombreArchivoAWSOLocal($archivo)
    {

        // Si es URL, extraemos el nombre con la regex y lo mostramos
        if (filter_var($archivo, FILTER_VALIDATE_URL)) {
            if (preg_match('/[^\/]+$/', $archivo, $matches)) {
                return $matches[0]; // nombre del archivo, p.ej. example.pdf
            }
            // Si por alguna razón no matchea, muestra la URL completa como fallback
            return $archivo;
        }

        // Si NO es URL, mostramos tal cual (tu comportamiento actual)
        return $archivo;
    }

    /**
     * Formatea el SIIC con ceros a la izquierda
     * 
     * @param string $SIIC SIIC original
     * @return string SIIC formateado
     */
    public function formatearSIIC($SIIC)
    {
        return str_pad($SIIC, 10, '0', STR_PAD_LEFT);
    }

    /**
     * Calcula el VPC desde la comisión.
     * 
     * @param float $comision La comisión a calcular.
     * @return float Retorna el VPC calculado.
     */
    public function vpcDesdeComision(float $comision): float
    {
        $comision = round($comision, 2);
        if ($comision >= 0.0 && $comision <= 9.99) {
            return ($comision * 700.0) + 14.0;
        }
        return 99999.0;
    }

    /**
     * Calcula la comisión desde el VPC.
     * 
     * @param float $vpc El VPC a calcular.
     * @return float Retorna la comisión calculada.
     */
    public function comisionDesdeVpc(float $vpc): float
    {
        if ((int)$vpc === 99999) {
            return 0.0;
        }
        return round(($vpc - 14.0) / 700.0, 2);
    }

    /* 
     * Determina si el usuario tiene alguno de los perfiles especificados.
     * 
     * @param int|array $perfilIDs El ID del perfil o un array de IDs de perfiles.
     * @return bool Retorna true si el usuario tiene alguno de los perfiles, false en caso contrario.
     */
    public function tienePerfilUsuario($perfilIDs)
    {
        // Normalizar a array si se pasa un solo ID
        if (!is_array($perfilIDs)) {
            $perfilIDs = [(int)$perfilIDs];
        } else {
            $perfilIDs = array_map('intval', $perfilIDs);
        }

        // Si el array está vacío, retornar false
        if (empty($perfilIDs)) {
            return false;
        }

        $usuarioID = (int)(Yii::$app->user->identity->usuarioID ?? 0);
        $perfiles = \app\models\Pcompuestos::find()
            ->alias('PC')
            ->innerJoin('Perfiles P', 'P.perfilID = PC.perfilID')
            ->where(['PC.usuarioID' => $usuarioID, 'PC.regEstado' => 1])
            ->select(['P.perfilID', 'P.nombrePerfil'])
            ->asArray()
            ->all();
        $perfilIDsUsuario = array_map('intval', array_column($perfiles, 'perfilID'));

        // Verificar si alguno de los perfiles solicitados está en los perfiles del usuario
        return !empty(array_intersect($perfilIDs, $perfilIDsUsuario));
    }

    /**
     * Genera un dropdown con scroll interno para acciones en grids.
     * 
     * @param array $opciones Array de opciones. Cada opción debe tener:
     *   - 'texto': Texto a mostrar
     *   - 'url': URL del enlace
     *   - 'icono': (opcional) Clase de icono Font Awesome (ej: 'fa-edit')
     *   - 'onclick' o 'onClick': (opcional) Código JavaScript a ejecutar al hacer clic
     * @param string $idUnico ID único para el dropdown (opcional, se genera automáticamente si no se proporciona)
     * @return string HTML del dropdown con CSS y JavaScript incluidos
     */
    public function generarDropdownAcciones($opciones, $idUnico = null)
    {
        if (empty($opciones) || !is_array($opciones)) {
            return '';
        }

        // Generar ID único si no se proporciona
        if ($idUnico === null) {
            $idUnico = 'dropdown-' . uniqid();
        }

        // Variable estática para asegurar que el CSS/JS solo se incluya una vez
        static $estilosIncluidos = false;

        $html = '';

        // Incluir estilos y JavaScript solo una vez
        if (!$estilosIncluidos) {
            $html .= $this->generarEstilosDropdown();
            $html .= $this->generarJavaScriptDropdown();
            $estilosIncluidos = true;
        }

        // Generar el HTML del dropdown
        $html .= '<div class="dropdown-wrapper position-relative" style="display: inline-block;">';
        $html .= '<button class="btn-dropdown-acciones bg-transparent border-0" type="button" id="btn-' . $idUnico . '" aria-haspopup="true" aria-expanded="false" style="cursor: pointer; color: #495057; padding: 5px 10px; border-radius: 4px; transition: background-color 0.2s;">';
        $html .= '<i class="fa fa-ellipsis-v fa-lg"></i>';
        $html .= '</button>';
        $html .= '<div id="' . $idUnico . '" class="custom-scrollable-dropdown" aria-labelledby="btn-' . $idUnico . '">';

        // Generar opciones con IDs únicos
        $index = 0;
        foreach ($opciones as $opcion) {
            $texto = isset($opcion['texto']) ? $opcion['texto'] : '';
            $url = isset($opcion['url']) ? $opcion['url'] : '#';
            $itemId = $idUnico . '-item-' . $index;
            
            // Preparar atributos del enlace
            $atributos = [
                'id' => $itemId,
                'class' => 'dropdown-item',
                'title' => $texto,
                'data' => [
                    'data-pjax' => '0',
                ],
            ];
            
            // Agregar onclick si existe (soporta 'onclick' y 'onClick')
            $onclick = isset($opcion['onclick']) ? $opcion['onclick'] : (isset($opcion['onClick']) ? $opcion['onClick'] : null);
            if ($onclick !== null) {
                $atributos['onclick'] = $onclick;
                // Si la URL es '#' y hay onclick, prevenir navegación por defecto
                if ($url === '#') {
                    $atributos['href'] = '#';
                    $atributos['onclick'] = $onclick . '; return false;';
                }
            }

            // Construir contenido con divs: contenedor principal, div para icono y div para texto
            $contenido = '<div class="dropdown-item-content">';
            $contenido .= '<div class="dropdown-item-icon">';
            if (isset($opcion['icono']) && !empty($opcion['icono'])) {
                $contenido .= '<i class="fa ' . Html::encode($opcion['icono']) . '"></i>';
            }
            $contenido .= '</div>';
            $contenido .= '<div class="dropdown-item-text">' . Html::encode($texto) . '</div>';
            $contenido .= '</div>';

            $html .= Html::a($contenido, $url, $atributos);
            $index++;
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Genera los estilos CSS para el dropdown con scroll interno.
     * 
     * @return string HTML con los estilos CSS
     */
    private function generarEstilosDropdown()
    {
        return '
        <style id="custom-dropdown-acciones-styles">
            .custom-scrollable-dropdown {
                position: absolute;
                top: 0;
                right: 100%;
                z-index: 1000;
                min-width: 180px;
                max-width: 220px;
                max-height: 300px;
                overflow-y: auto;
                overflow-x: hidden;
                border: 1px solid rgba(0,0,0,.15);
                border-radius: 4px;
                box-shadow: 0 2px 8px rgba(0,0,0,.15);
                background-color: #fff;
                margin-right: 0;
                padding: 4px 0;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
                overscroll-behavior: contain;
                display: none;
            }
            .custom-scrollable-dropdown.show {
                display: block !important;
            }
            .custom-scrollable-dropdown::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scrollable-dropdown::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 3px;
            }
            .custom-scrollable-dropdown::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 3px;
            }
            .custom-scrollable-dropdown::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
            .custom-scrollable-dropdown .dropdown-item {
                border-bottom: 1px solid #f0f0f0;
                white-space: nowrap;
                padding: 6px 12px;
                color: #495057;
                text-decoration: none;
                display: block;
                transition: background-color 0.2s;
            }
            .custom-scrollable-dropdown .dropdown-item-content {
                display: flex;
                align-items: center;
                width: 100%;
                margin: 0;
                padding: 4px;
            }
            .custom-scrollable-dropdown .dropdown-item-icon {
                width: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 6px;
                flex-shrink: 0;
                padding: 0;
            }
            .custom-scrollable-dropdown .dropdown-item-icon i {
                font-size: inherit;
                margin: 0;
                padding: 0;
            }
            .custom-scrollable-dropdown .dropdown-item-text {
                flex: 1;
                margin: 0;
                padding: 0;
            }
            .custom-scrollable-dropdown .dropdown-item:last-child {
                border-bottom: none;
            }
            .custom-scrollable-dropdown .dropdown-item:hover {
                background-color: #f8f9fa !important;
                color: #212529 !important;
            }
            .custom-scrollable-dropdown .dropdown-item:active {
                background-color: #e9ecef !important;
            }
            .btn-dropdown-acciones:hover {
                background-color: #f8f9fa !important;
            }
            .dropdown-wrapper {
                position: relative;
            }
            .custom-scrollable-dropdown.show.fixed-position {
                position: fixed !important;
            }
        </style>
        ';
    }

    /**
     * Genera el JavaScript para el funcionamiento del dropdown.
     * 
     * @return string HTML con el JavaScript
     */
    private function generarJavaScriptDropdown()
    {
        return '
        <script type="text/javascript">
        (function() {
            if (typeof window.dropdownAccionesInicializado !== "undefined") {
                return; // Ya está inicializado
            }
            window.dropdownAccionesInicializado = true;

            // Esperar a que jQuery esté disponible
            function initDropdownAcciones() {
                if (typeof jQuery === "undefined") {
                    setTimeout(initDropdownAcciones, 100);
                    return;
                }

                var $ = jQuery;
                var openDropdowns = [];

                // Función para abrir/cerrar dropdown manualmente
                function toggleDropdown(button) {
                    var $button = $(button);
                    var $wrapper = $button.closest(".dropdown-wrapper");
                    var $dropdown = $wrapper.find(".custom-scrollable-dropdown");
                    
                    if ($dropdown.hasClass("show")) {
                        // Cerrar
                        $dropdown.removeClass("show fixed-position").css({
                            "position": "",
                            "top": "",
                            "left": "",
                            "right": "",
                            "bottom": ""
                        });
                        $button.attr("aria-expanded", "false");
                        openDropdowns = openDropdowns.filter(function(el) {
                            return el !== $dropdown[0];
                        });
                    } else {
                        // Cerrar otros dropdowns abiertos
                        $(".custom-scrollable-dropdown.show").each(function() {
                            $(this).removeClass("show fixed-position").css({
                                "position": "",
                                "top": "",
                                "right": "",
                                "left": "",
                                "bottom": ""
                            });
                        });
                        openDropdowns = [];
                        
                        // Abrir este dropdown
                        $dropdown.addClass("show");
                        $button.attr("aria-expanded", "true");
                        
                        // Calcular posición usando getBoundingClientRect para mayor precisión
                        var buttonRect = $button[0].getBoundingClientRect();
                        var buttonHeight = buttonRect.height;
                        var buttonWidth = buttonRect.width;
                        var windowHeight = window.innerHeight;
                        var windowWidth = window.innerWidth;

                        // Forzar que el dropdown se muestre temporalmente para obtener su ancho real
                        $dropdown.css({"position": "absolute", "visibility": "hidden", "display": "block"});
                        var dropdownWidth = $dropdown.outerWidth() || 200;
                        var dropdownHeight = $dropdown.outerHeight() || 300;
                        $dropdown.css({"position": "", "visibility": "", "display": ""});
                        
                        // Calcular posición fija (a la izquierda del botón, misma altura)
                        // getBoundingClientRect ya devuelve posición relativa al viewport
                        var topPosition = buttonRect.top;
                        // Posición: el dropdown termina justo donde empieza el botón, con un pequeño espacio
                        var leftPosition = buttonRect.left - dropdownWidth - 5;

                        // Ajustar si se sale por arriba
                        if (topPosition < 10) {
                            topPosition = 10;
                        }

                        // Ajustar si se sale por abajo
                        if (topPosition + dropdownHeight > windowHeight - 20) {
                            topPosition = Math.max(10, windowHeight - dropdownHeight - 20);
                        }

                        // Ajustar si se sale por la izquierda
                        if (leftPosition < 10) {
                            // Si no cabe a la izquierda, ponerlo a la derecha del botón
                            leftPosition = buttonRect.left + buttonWidth + 5;
                        }

                        // Aplicar posición fija inmediatamente
                        $dropdown.addClass("fixed-position").css({
                            "position": "fixed",
                            "top": topPosition + "px",
                            "left": leftPosition + "px",
                            "right": "auto",
                            "bottom": "auto",
                            "z-index": "9999"
                        });

                        openDropdowns.push($dropdown[0]);
                    }
                }

                // Manejar clic en el botón
                $(document).on("click", ".btn-dropdown-acciones", function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleDropdown(this);
                });

                // Cerrar al hacer clic fuera
                $(document).on("click", function(e) {
                    if (!$(e.target).closest(".dropdown-wrapper").length) {
                        $(".custom-scrollable-dropdown.show").each(function() {
                            var $dropdown = $(this);
                            $dropdown.removeClass("show fixed-position").css({
                                "position": "",
                                "top": "",
                                "left": "",
                                "right": "",
                                "bottom": ""
                            });
                            $dropdown.closest(".dropdown-wrapper").find(".btn-dropdown-acciones").attr("aria-expanded", "false");
                        });
                        openDropdowns = [];
                    }
                });

                // Prevenir que el scroll del grid cierre el dropdown
                $(document).on("scroll", ".table-responsive, .grid-view", { passive: true }, function(e) {
                    if (openDropdowns.length > 0) {
                        e.stopPropagation();
                    }
                });
            }

            // Inicializar cuando el DOM esté listo
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initDropdownAcciones);
            } else {
                initDropdownAcciones();
            }
        })();
        </script>
        ';
    }
}
