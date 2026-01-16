<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Versiones".
 *
 * @property int $versionID
 * @property string $version
 * @property string $fechaLiberacionVersion
 * @property string $aliasVersion
 * @property string $urlVersion
 * @property string $urlDocumentacionVersion
 * @property bool $versionActual
 * @property int $aplicacionID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Acciones[] $acciones
 * @property AccionesFormularios[] $accionesFormularios
 * @property Apis[] $apis
 * @property AplicacionesConexionApi[] $aplicacionesConexionApis
 * @property AplicacionesGrupos[] $aplicacionesGrupos
 * @property AplicacionessGruposEstablecimientos[] $aplicacionessGruposEstablecimientos
 * @property Bitacora[] $bitacoras
 * @property BitacoraEstablecimientosVersionesModuloSoftwareBrentec[] $bitacoraEstablecimientosVersionesModuloSoftwareBrentecs
 * @property Campos[] $campos
 * @property CamposGrid[] $camposGrs
 * @property CartasResponsivas[] $cartasResponsivas
 * @property Catalogos[] $catalogos
 * @property ClavesProductosEnviosSAT[] $clavesProductosEnviosSATs
 * @property ClavesSubProductosEnviosSAT[] $clavesSubProductosEnviosSATs
 * @property ClavesUnidadesSAT[] $clavesUnidadesSATs
 * @property ComponentesAlarmas[] $componentesAlarmas
 * @property ConexionesApis[] $conexionesApis
 * @property ConexionesApisEstablecimientos[] $conexionesApisEstablecimientos
 * @property ConexionesBasesDatosSQLServer[] $conexionesBasesDatosSQLServers
 * @property ConfiguracionesSistema[] $configuracionesSistemas
 * @property ConfiguracionesSlider[] $configuracionesSliders
 * @property ConsumosFoliosPAC[] $consumosFoliosPACs
 * @property ContactosEstablecimientos[] $contactosEstablecimientos
 * @property DetalleScripts[] $detalleScripts
 * @property DireccionesEstablecimientos[] $direccionesEstablecimientos
 * @property Distribuidores[] $distribuidores
 * @property EjecucionScripts[] $ejecucionScripts
 * @property EquiposKairos[] $equiposKairos
 * @property Establecimientos[] $establecimientos
 * @property EstablecimientosConexionesBD[] $establecimientosConexionesBDs
 * @property EstablecimientosVersionesModuloSoftwareBrentec[] $establecimientosVersionesModuloSoftwareBrentecs
 * @property Estados[] $estados
 * @property Eventos[] $eventos
 * @property FEClaveProdServ[] $fEClaveProdServs
 * @property FEClaveUnidad[] $fEClaveUnidads
 * @property FEFormaPago[] $fEFormaPagos
 * @property Formularios[] $formularios
 * @property GrupoPoliza[] $grupoPolizas
 * @property GruposEstablecimientos[] $gruposEstablecimientos
 * @property HistoricosPassword[] $historicosPasswords
 * @property Idiomas[] $iomas
 * @property IntentosMaximosReplica[] $intentosMaximosReplicas
 * @property InventariosInstalacion[] $inventariosInstalacions
 * @property ListadoEquiposSeguros[] $listadoEquiposSeguros
 * @property LogAccesos[] $logAccesos
 * @property LogEjecucionScripts[] $logEjecucionScripts
 * @property MensajesBitacora[] $mensajesBitacoras
 * @property MensajesBitacoraIdiomas[] $mensajesBitacoraIdiomas
 * @property Menus[] $menuses
 * @property MenusFormularios[] $menusFormularios
 * @property MenusPOS[] $menusPOSs
 * @property MetodosApis[] $metodosApis
 * @property MetodosUsuariosApis[] $metodosUsuariosApis
 * @property MigracionApis[] $migracionApis
 * @property MigracionCatalogos[] $migracionCatalogos
 * @property MigracionFormularios[] $migracionFormularios
 * @property MigracionMenus[] $migracionMenuses
 * @property MigracionReportes[] $migracionReportes
 * @property ModuloSoftwareBrentec[] $moduloSoftwareBrentecs
 * @property Paises[] $paises
 * @property PantallasPOS[] $pantallasPOSs
 * @property PaquetesKairos[] $paquetesKairos
 * @property ParidadSerial[] $paridadSerials
 * @property PerfilAccionFormulario[] $perfilAccionFormularios
 * @property Perfiles[] $perfiles
 * @property PerfilesCompuestos[] $perfilesCompuestos
 * @property PerfilesEstablecimientos[] $perfilesEstablecimientos
 * @property PeriodosFoliosPAC[] $periodosFoliosPACs
 * @property PermisosFormulariosPerfiles[] $permisosFormulariosPerfiles
 * @property PermisosMenus[] $permisosMenuses
 * @property ProductosCongo[] $productosCongos
 * @property ProveedoresCongo[] $proveedoresCongos
 * @property RegistroCambiosPassword[] $registroCambiosPasswords
 * @property ReglasPassw[] $reglasPassws
 * @property ReglasSistemaBase[] $reglasSistemaBases
 * @property RelacionPaquetesSotfware[] $relacionPaquetesSotfwares
 * @property ReportesCampos[] $reportesCampos
 * @property ReportesConfiguraciones[] $reportesConfiguraciones
 * @property Roles[] $roles
 * @property RolesUsuarios[] $rolesUsuarios
 * @property Scripts[] $scripts
 * @property ServidoresApis[] $servidoresApis
 * @property SoftwareKairos[] $softwareKairos
 * @property SoftwareKairosInstaladoEquipos[] $softwareKairosInstaladoEquipos
 * @property TemplatesReportes[] $templatesReportes
 * @property Textos[] $textos
 * @property TextosIdiomas[] $textosIdiomas
 * @property TiposContactosEstablecimientos[] $tiposContactosEstablecimientos
 * @property TiposDespachos[] $tiposDespachos
 * @property TiposEmpleados[] $tiposEmpleados
 * @property TiposEventos[] $tiposEventos
 * @property TiposFormularios[] $tiposFormularios
 * @property TiposImpuestos[] $tiposImpuestos
 * @property TiposInterfaz[] $tiposInterfazs
 * @property TiposMovimientosAlmacenes[] $tiposMovimientosAlmacenes
 * @property TiposMovimientosVentas[] $tiposMovimientosVentas
 * @property TiposPOS[] $tiposPOSs
 * @property TiposProductos[] $tiposProductos
 * @property TiposProductosCongo[] $tiposProductosCongos
 * @property TiposProveedoresCongo[] $tiposProveedoresCongos
 * @property TiposUsuarios[] $tiposUsuarios
 * @property Usuarios[] $usuarios
 * @property UsuariosEstablecimientos[] $usuariosEstablecimientos
 * @property VelocidadBaudios[] $velocidadBaudios
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property VersionesModuloSoftwareBrentec[] $versionesModuloSoftwareBrentecs
 */
class Versiones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Versiones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fechaLiberacionVersion', 'regFechaUltimaModificacion'], 'safe'],
            [['urlVersion', 'urlDocumentacionVersion'], 'string'],
            [['versionActual', 'regEstado'], 'boolean'],
            [['aplicacionID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion'], 'integer'],
            [['version'], 'string', 'max' => 50],
            [['aliasVersion'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'versionID' => 'Version ID',
            'version' => 'Version',
            'fechaLiberacionVersion' => 'Fecha Liberacion Version',
            'aliasVersion' => 'Alias Version',
            'urlVersion' => 'Url Version',
            'urlDocumentacionVersion' => 'Url Documentacion Version',
            'versionActual' => 'Version Actual',
            'aplicacionID' => 'Aplicacion ID',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }


 /**
     * funciones relaciones
     * relaciones con tablas
     */
 public function getIdAplicaciones()
    {
       return $this->hasOne(Aplicaciones::className(), ['aplicacionID' => 'aplicacionID']);
    }


}
