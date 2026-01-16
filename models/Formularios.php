<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Formularios".
 *
 * @property int $formularioID
 * @property string $nombreFormulario
 * @property string $urlArchivo
 * @property bool $estadoFormulario
 * @property int $orden
 * @property string $icono
 * @property int $menuID
 * @property int $aplicacionID
 * @property int $catalogoID
 * @property int $textoID
 * @property int $tipoFormularioID
 * @property int $versionRegistro
 * @property bool $regEstado
 * @property string $regFechaUltimaModificacion
 * @property int $regUsuarioUltimaModificacion
 * @property int $regFormularioUltimaModificacion
 * @property int $regVersionUltimaModificacion
 *
 * @property Acciones[] $acciones
 * @property AccionesFormularios[] $accionesFormularios
 * @property AccionesFormularios[] $accionesFormularios0
 * @property Almacenes[] $almacenes
 * @property Asistencias[] $asistencias
 * @property Bitacora[] $bitacoras
 * @property Bombas[] $bombas
 * @property CalibracionMedidoresMangueras[] $calibracionMedidoresMangueras
 * @property ClavesProductosEnviosSAT[] $clavesProductosEnviosSATs
 * @property ClavesSubProductosEnviosSAT[] $clavesSubProductosEnviosSATs
 * @property ComponentesAlarmas[] $componentesAlarmas
 * @property ConfiguracionBombasComunicacion[] $configuracionBombasComunicacions
 * @property ConfiguracionManguerasComunicacion[] $configuracionManguerasComunicacions
 * @property ConfiguracionesGlobales[] $configuracionesGlobales
 * @property ConfiguracionesPantallasPOS[] $configuracionesPantallasPOSs
 * @property ConfiguracionesRED[] $configuracionesREDs
 * @property ConfiguracionesSeriales[] $configuracionesSeriales
 * @property Cortes[] $cortes
 * @property Despachos[] $despachos
 * @property DetalleCalibracionMedidoresMangueras[] $detalleCalibracionMedidoresMangueras
 * @property DireccionesEstablecimientos[] $direccionesEstablecimientos
 * @property Dispensarios[] $dispensarios
 * @property Distribuidores[] $distribuidores
 * @property Empleados[] $empleados
 * @property EntregasInventarios[] $entregasInventarios
 * @property Establecimientos[] $establecimientos
 * @property Eventos[] $eventos
 * @property ExistenciasTanques[] $existenciasTanques
 * @property FEClaveProdServ[] $fEClaveProdServs
 * @property FEClaveUnidad[] $fEClaveUnidads
 * @property Catalogos $catalogo
 * @property Textos $texto
 * @property TiposFormularios $tipoFormulario
 * @property Usuarios $regUsuarioUltimaModificacion0
 * @property Formularios $regFormularioUltimaModificacion0
 * @property Formularios[] $formularios
 * @property Versiones $regVersionUltimaModificacion0
 * @property GeneralesPos[] $generalesPos
 * @property Grupos[] $grupos
 * @property Idiomas[] $iomas
 * @property Interfaces[] $interfaces
 * @property Jornadas[] $jornadas
 * @property JornadasFijas[] $jornadasFijas
 * @property JornadasModosOperaciones[] $jornadasModosOperaciones
 * @property ListadoEquiposSeguros[] $listadoEquiposSeguros
 * @property LogAccesos[] $logAccesos
 * @property LogAccesosAPI[] $logAccesosAPIs
 * @property Mangueras[] $mangueras
 * @property Medidores[] $medidores
 * @property Menus[] $menuses
 * @property MenusPOS[] $menusPOSs
 * @property POS[] $pOSs
 * @property PantallasPOS[] $pantallasPOSs
 * @property Perfiles[] $perfiles
 * @property PerfilesCompuestos[] $perfilesCompuestos
 * @property PermisosAccionesFormulariosPerfiles[] $permisosAccionesFormulariosPerfiles
 * @property PermisosFormulariosPerfiles[] $permisosFormulariosPerfiles
 * @property PermisosFormulariosPerfiles[] $permisosFormulariosPerfiles0
 * @property Productos[] $productos
 * @property ProductosEstablecimientos[] $productosEstablecimientos
 * @property ProductosPreciosVenta[] $productosPreciosVentas
 * @property RegistroAuditoriaAplicaciones[] $registroAuditoriaAplicaciones
 * @property RegistroAuditoriaAplicaciones[] $registroAuditoriaAplicaciones0
 * @property ReglasPassw[] $reglasPassws
 * @property Roles[] $roles
 * @property RolesUsuarios[] $rolesUsuarios
 * @property Tanques[] $tanques
 * @property Textos[] $textos
 * @property TextosIdiomas[] $textosIdiomas
 * @property TiposEmpleados[] $tiposEmpleados
 * @property TiposEventos[] $tiposEventos
 * @property TiposFormularios[] $tiposFormularios
 * @property TiposInterfaz[] $tiposInterfazs
 * @property TiposPOS[] $tiposPOSs
 * @property TiposProductos[] $tiposProductos
 * @property TiposUsuarios[] $tiposUsuarios
 * @property Turnos[] $turnos
 * @property Usuarios[] $usuarios
 * @property Versiones[] $versiones
 * @property VigenciaCalibraciones[] $vigenciaCalibraciones
 */
class Formularios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Formularios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
			[['textoID', 'menuID', 'tipoFormularioID'], 'required'],
            [['estadoFormulario', 'regEstado'], 'boolean'],
            [['orden', 'menuID', 'aplicacionID', 'catalogoID', 'textoID', 'tipoFormularioID', 'versionRegistro', 'regUsuarioUltimaModificacion', 'regFormularioUltimaModificacion', 'regVersionUltimaModificacion', 'formID'], 'integer'],
            [['tipoMenu', 'regFechaUltimaModificacion', 'formID'], 'safe'],
            [['nombreFormulario', 'urlArchivo'], 'string', 'max' => 150],
            [['icono'], 'string', 'max' => 50],
           
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formularioID' => 'ID',
			'tipoMenu' => 'Tipo de menu',
			'formID' => 'Sub menu',
            'nombreFormulario' => 'Nombre',
            'urlArchivo' => 'Url',
            'estadoFormulario' => 'Estado',
            'orden' => 'Orden',
            'icono' => 'Icono',
            'menuID' => 'MenuID',
            'aplicacionID' => 'AplicacionID',
            'catalogoID' => 'CatalogoID',
            'textoID' => 'TextoID',
            'tipoFormularioID' => 'Tipo Formulario',
            'versionRegistro' => 'Version Registro',
            'regEstado' => 'Reg Estado',
            'regFechaUltimaModificacion' => 'Reg Fecha Ultima Modificacion',
            'regUsuarioUltimaModificacion' => 'Reg Usuario Ultima Modificacion',
            'regFormularioUltimaModificacion' => 'Reg Formulario Ultima Modificacion',
            'regVersionUltimaModificacion' => 'Reg Version Ultima Modificacion',
        ];
    }

   
}
