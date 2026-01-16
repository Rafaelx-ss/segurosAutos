#!/bin/bash

# ================================
# Script: extraer_cambios.sh
# Objetivo: Copiar los archivos que
# cambiaron desde un commit específico
# hasta HEAD, dejando la versión final
# (sin revivir archivos eliminados).
# Versión mejorada: Interactivo y dinámico
# ================================

# Colores para mejor UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Función para leer entrada del usuario
# Uso: leer_entrada VARIABLE "prompt" "default"
# La respuesta se guarda en la variable especificada
leer_entrada() {
    local var_name="$1"
    local prompt="$2"
    local default="$3"
    local respuesta
    
    # Mostrar el prompt de forma clara y visible
    if [ -n "$default" ]; then
        echo -e "${CYAN}${prompt}${NC} ${YELLOW}[default: ${default}]${NC}"
    else
        echo -e "${CYAN}${prompt}${NC}"
    fi
    echo -n "> "
    
    # Leer la entrada del usuario
    read respuesta
    
    # Si no hay respuesta y hay default, usar el default
    if [ -z "$respuesta" ] && [ -n "$default" ]; then
        respuesta="$default"
    fi
    
    # Asignar a la variable especificada usando eval de forma segura
    printf -v "$var_name" '%s' "$respuesta"
}

# Función para validar commit SHA
validar_commit() {
    local commit="$1"
    if git rev-parse --verify "$commit" >/dev/null 2>&1; then
        return 0
    else
        return 1
    fi
}

# Función para generar mensaje de commit inteligente
generar_mensaje_commit() {
    local desde_commit="$1"
    local tipo_extraccion="$2"
    local version="$3"
    
    # Obtener estadísticas de cambios
    local archivos_modificados=$(git diff --name-status "$desde_commit" HEAD | grep -c "^M" || echo "0")
    local archivos_agregados=$(git diff --name-status "$desde_commit" HEAD | grep -c "^A" || echo "0")
    local archivos_eliminados=$(git diff --name-status "$desde_commit" HEAD | grep -c "^D" || echo "0")
    local archivos_renombrados=$(git diff --name-status "$desde_commit" HEAD | grep -c "^R" || echo "0")
    
    # Obtener tipos de archivos modificados
    local tipos_archivos=$(git diff --name-only "$desde_commit" HEAD | sed 's/.*\.//' | sort -u | tr '\n' ',' | sed 's/,$//')
    
    # Obtener lista de archivos principales (máximo 5)
    local archivos_principales=$(git diff --name-only "$desde_commit" HEAD | head -5 | sed 's/.*\///' | tr '\n' ',' | sed 's/,$//')
    
    # Construir mensaje
    local mensaje=""
    
    # Prefijo según tipo
    if [ "$tipo_extraccion" = "produccion" ]; then
        mensaje="🚀 [PRODUCCIÓN]"
        if [ -n "$version" ]; then
            mensaje="$mensaje $version -"
        fi
    else
        mensaje="🧪 [PRUEBAS]"
    fi
    
    # Obtener fecha y hora actual
    local fecha_hora=$(date +"%d/%m/%Y %H:%M:%S")
    
    mensaje="$mensaje - $fecha_hora"
    
    # Construir resumen de cambios de forma clara y legible
    local cambios_resumen_parts=()
    
    if [ "$archivos_modificados" -gt 0 ]; then
        if [ "$archivos_modificados" -eq 1 ]; then
            cambios_resumen_parts+=("${archivos_modificados} modificado")
        else
            cambios_resumen_parts+=("${archivos_modificados} modificados")
        fi
    fi
    
    if [ "$archivos_agregados" -gt 0 ]; then
        if [ "$archivos_agregados" -eq 1 ]; then
            cambios_resumen_parts+=("${archivos_agregados} agregado")
        else
            cambios_resumen_parts+=("${archivos_agregados} agregados")
        fi
    fi
    
    if [ "$archivos_eliminados" -gt 0 ]; then
        if [ "$archivos_eliminados" -eq 1 ]; then
            cambios_resumen_parts+=("${archivos_eliminados} eliminado")
        else
            cambios_resumen_parts+=("${archivos_eliminados} eliminados")
        fi
    fi
    
    if [ "$archivos_renombrados" -gt 0 ]; then
        if [ "$archivos_renombrados" -eq 1 ]; then
            cambios_resumen_parts+=("${archivos_renombrados} renombrado")
        else
            cambios_resumen_parts+=("${archivos_renombrados} renombrados")
        fi
    fi
    
    # Unir las partes con comas
    if [ ${#cambios_resumen_parts[@]} -gt 0 ]; then
        local cambios_texto=$(IFS=', '; echo "${cambios_resumen_parts[*]}")
        mensaje="$mensaje (${cambios_texto})"
    fi
    
    # Agregar tipos de archivos si hay información
    if [ -n "$tipos_archivos" ]; then
        mensaje="$mensaje - Tipos: $tipos_archivos"
    fi
    
    # Agregar archivos principales si hay
    if [ -n "$archivos_principales" ]; then
        mensaje="$mensaje - Archivos: $archivos_principales"
    fi
    
    # Agregar rango de commits
    local commit_corto=$(git rev-parse --short "$desde_commit")
    local head_corto=$(git rev-parse --short HEAD)
    mensaje="$mensaje [${commit_corto}..${head_corto}]"
    
    echo "$mensaje"
}

# ================================
# PASO 1: Solicitar SHA del commit
# ================================
echo -e "${BLUE}════════════════════════════════════════${NC}"
echo -e "${BLUE}      EXTRACCIÓN DE CAMBIOS - PAPION    ${NC}"
echo -e "${BLUE}════════════════════════════════════════${NC}"
echo ""

DESDE_COMMIT=""
while [ -z "$DESDE_COMMIT" ]; do
    leer_entrada "DESDE_COMMIT" "📌 Ingresa el SHA del commit base"
    
    if [ -z "$DESDE_COMMIT" ]; then
        echo -e "${RED}❌ Error: El SHA del commit no puede estar vacío.${NC}"
        continue
    fi
    
    if ! validar_commit "$DESDE_COMMIT"; then
        echo -e "${RED}❌ Error: El commit '$DESDE_COMMIT' no existe en el repositorio.${NC}"
        DESDE_COMMIT=""
    fi
done

echo -e "${GREEN}✅ Commit válido: $DESDE_COMMIT${NC}"
echo ""

# ================================
# PASO 2: Tipo de extracción
# ================================
echo -e "${YELLOW}¿Qué tipo de extracción deseas realizar?${NC}"
echo -e "  ${CYAN}1)${NC} Pruebas (carpeta: cambios_papion)"
echo -e "  ${CYAN}2)${NC} Producción (carpeta: papion_v{versión})"
echo ""

TIPO_EXTRACCION=""
while [ -z "$TIPO_EXTRACCION" ]; do
    opcion=""
    leer_entrada "opcion" "Selecciona una opción (1 o 2)" "1"
    
    case "$opcion" in
        1)
            TIPO_EXTRACCION="pruebas"
            CARPETA="../cambios_papion"
            ;;
        2)
            TIPO_EXTRACCION="produccion"
            # Solicitar número de versión
            NUMERO_VERSION=""
            while [ -z "$NUMERO_VERSION" ]; do
                leer_entrada "NUMERO_VERSION" "📦 Ingresa el número de versión (ej: 1074, 1075, 1076)"
                
                if [ -z "$NUMERO_VERSION" ]; then
                    echo -e "${RED}❌ Error: El número de versión no puede estar vacío.${NC}"
                    continue
                fi
                
                # Validar que sea numérico
                if ! [[ "$NUMERO_VERSION" =~ ^[0-9]+$ ]]; then
                    echo -e "${RED}❌ Error: El número de versión debe ser numérico.${NC}"
                    NUMERO_VERSION=""
                fi
            done
            
            CARPETA="../papion_v${NUMERO_VERSION}"
            VERSION="papion_v${NUMERO_VERSION}"
            ;;
        *)
            echo -e "${RED}❌ Opción inválida. Por favor selecciona 1 o 2.${NC}"
            ;;
    esac
done

echo -e "${GREEN}✅ Carpeta de destino: $CARPETA${NC}"
echo ""

# ================================
# Verificar si hay cambios
# ================================
echo -e "${BLUE}🔍 Obteniendo archivos modificados, agregados o eliminados...${NC}"
ARCHIVOS=$(git diff --name-status "$DESDE_COMMIT" HEAD | awk '{print $2}')

if [ -z "$ARCHIVOS" ]; then
    echo -e "${YELLOW}⚠️ No se encontraron cambios entre $DESDE_COMMIT y HEAD.${NC}"
    exit 0
fi

TOTAL_ARCHIVOS=$(echo "$ARCHIVOS" | wc -l | tr -d ' ')
echo -e "${GREEN}✅ Se encontraron $TOTAL_ARCHIVOS archivo(s) con cambios${NC}"
echo ""

# ================================
# Procesar archivos
# ================================
echo -e "${BLUE}🚀 Procesando archivos...${NC}"
mkdir -p "$CARPETA"

ARCHIVOS_COPIADOS=0
ARCHIVOS_OMITIDOS=0

for archivo in $ARCHIVOS; do
    DESTINO="$CARPETA/$archivo"
    
    # Crear carpeta destino si no existe
    mkdir -p "$(dirname "$DESTINO")"
    
    # Verificar si el archivo existe en HEAD
    if git ls-tree -r HEAD --name-only | grep -qx "$archivo"; then
        git show "HEAD:$archivo" > "$DESTINO"
        if [ $? -eq 0 ]; then
            echo -e "   ${GREEN}✅${NC} $archivo"
            ((ARCHIVOS_COPIADOS++))
        else
            echo -e "   ${RED}❌${NC} Error al copiar: $archivo"
            ((ARCHIVOS_OMITIDOS++))
        fi
    else
        echo -e "   ${YELLOW}⚠️${NC} Archivo eliminado, omitido: $archivo"
        ((ARCHIVOS_OMITIDOS++))
    fi
done

echo ""
echo -e "${GREEN}🎉 Proceso de extracción finalizado.${NC}"
echo -e "${GREEN}✅ Archivos copiados: $ARCHIVOS_COPIADOS${NC}"
if [ $ARCHIVOS_OMITIDOS -gt 0 ]; then
    echo -e "${YELLOW}⚠️ Archivos omitidos: $ARCHIVOS_OMITIDOS${NC}"
fi
echo -e "${GREEN}📂 Ubicación: $CARPETA${NC}"
echo ""

# ================================
# PASO 3: Preguntar si hacer commit
# ================================
echo -e "${YELLOW}¿Deseas crear un commit en git para registrar esta extracción?${NC}"
echo -e "  ${CYAN}1)${NC} Sí"
echo -e "  ${CYAN}2)${NC} No"
echo ""

hacer_commit=""
leer_entrada "hacer_commit" "Selecciona una opción (1 o 2)" "2"

if [ "$hacer_commit" = "1" ]; then
    echo ""
    echo -e "${BLUE}📝 Preparando commit...${NC}"
    
    # Generar mensaje de commit
    MENSAJE_COMMIT=$(generar_mensaje_commit "$DESDE_COMMIT" "$TIPO_EXTRACCION" "$VERSION")
    
    echo -e "${CYAN}Mensaje de commit generado:${NC}"
    echo -e "${GREEN}$MENSAJE_COMMIT${NC}"
    echo ""
    
    # Confirmar mensaje
    echo -e "${YELLOW}¿Qué deseas hacer con este mensaje?${NC}"
    echo -e "  ${CYAN}1)${NC} Usar este mensaje"
    echo -e "  ${CYAN}2)${NC} Personalizar el mensaje"
    echo ""
    
    confirmar_mensaje=""
    leer_entrada "confirmar_mensaje" "Selecciona una opción (1 o 2)" "1"
    
    if [ "$confirmar_mensaje" = "2" ]; then
        leer_entrada "MENSAJE_COMMIT" "Ingresa el mensaje del commit personalizado"
    fi
    
    # Preparar commit editando archivo de historial
    echo ""
    echo -e "${BLUE}📝 Registrando extracción en historial...${NC}"
    
    # Obtener información adicional para el historial
    FECHA_ACTUAL=$(date +"%d/%m/%Y %H:%M:%S")
    FECHA_SIMPLE=$(date +"%d/%m/%Y")
    COMMIT_CORTO=$(git rev-parse --short "$DESDE_COMMIT")
    HEAD_CORTO=$(git rev-parse --short HEAD)
    USUARIO_GIT=$(git config user.name 2>/dev/null || echo "Usuario")
    
    # Obtener estadísticas detalladas
    ARCHIVOS_MODIFICADOS=$(git diff --name-status "$DESDE_COMMIT" HEAD | grep -c "^M" || echo "0")
    ARCHIVOS_AGREGADOS=$(git diff --name-status "$DESDE_COMMIT" HEAD | grep -c "^A" || echo "0")
    ARCHIVOS_ELIMINADOS=$(git diff --name-status "$DESDE_COMMIT" HEAD | grep -c "^D" || echo "0")
    ARCHIVOS_RENOMBRADOS=$(git diff --name-status "$DESDE_COMMIT" HEAD | grep -c "^R" || echo "0")
    
    # Obtener lista completa de archivos
    LISTA_ARCHIVOS=$(git diff --name-only "$DESDE_COMMIT" HEAD | sed 's/^/	- /')
    
    # Construir encabezado según tipo
    if [ "$TIPO_EXTRACCION" = "produccion" ]; then
        ENCABEZADO="=== ${VERSION} por ${USUARIO_GIT} ==="
        TIPO_TEXTO="PRODUCCIÓN"
    else
        ENCABEZADO="=== Extracción de Pruebas - ${FECHA_SIMPLE} por ${USUARIO_GIT} ==="
        TIPO_TEXTO="PRUEBAS"
    fi
    
    # Archivo de historial
    ARCHIVO_HISTORIAL="extraccion_version_commits.txt"
    
    # Crear contenido del registro
    {
        echo ""
        echo "$ENCABEZADO"
        echo ""
        echo "Fecha: $FECHA_ACTUAL"
        echo "Tipo: $TIPO_TEXTO"
        if [ -n "$VERSION" ]; then
            echo "Versión: $VERSION"
        fi
        echo "Rango de commits: ${COMMIT_CORTO}..${HEAD_CORTO}"
        echo ""
        echo "*** Resumen de cambios ***"
        echo ""
        echo "	- Archivos modificados: $ARCHIVOS_MODIFICADOS"
        echo "	- Archivos agregados: $ARCHIVOS_AGREGADOS"
        echo "	- Archivos eliminados: $ARCHIVOS_ELIMINADOS"
        if [ "$ARCHIVOS_RENOMBRADOS" -gt 0 ]; then
            echo "	- Archivos renombrados: $ARCHIVOS_RENOMBRADOS"
        fi
        echo "	- Total de archivos: $TOTAL_ARCHIVOS"
        echo ""
        echo "*** Lista de archivos extraídos ***"
        echo ""
        echo "$LISTA_ARCHIVOS"
        echo ""
        echo "*** Mensaje del commit ***"
        echo ""
        echo "	$MENSAJE_COMMIT"
        echo ""
        echo "*** Carpeta de destino ***"
        echo ""
        echo "	$CARPETA"
        echo ""
        echo "════════════════════════════════════════════════════════════"
        echo ""
    } >> "$ARCHIVO_HISTORIAL"
    
    echo -e "${GREEN}✅ Información agregada a $ARCHIVO_HISTORIAL${NC}"
    echo ""
    
    # Agregar archivo al staging
    echo -e "${BLUE}📦 Agregando archivo al staging area...${NC}"
    if git add "$ARCHIVO_HISTORIAL" 2>&1; then
        echo -e "${GREEN}✅ Archivo agregado al staging area${NC}"
        echo ""
        echo -e "${YELLOW}📌 El commit está preparado. Ejecuta manualmente:${NC}"
        echo -e "${CYAN}   git commit -m \"$MENSAJE_COMMIT\"${NC}"
        echo ""
        echo -e "${BLUE}💡 Después puedes hacer push cuando estés listo:${NC}"
        echo -e "${CYAN}   git push${NC}"
        COMMIT_PREPARADO=true
    else
        echo -e "${RED}❌ Error al agregar archivo al staging area${NC}"
        echo -e "${YELLOW}⚠️ El archivo $ARCHIVO_HISTORIAL fue actualizado pero no se pudo agregar al staging${NC}"
        COMMIT_PREPARADO=false
    fi
else
    echo -e "${BLUE}ℹ️ Commit omitido.${NC}"
    COMMIT_PREPARADO=false
fi

# ================================
# RESUMEN FINAL
# ================================
echo ""
echo -e "${BLUE}════════════════════════════════════════${NC}"
echo -e "${BLUE}           RESUMEN FINAL${NC}"
echo -e "${BLUE}════════════════════════════════════════${NC}"
echo ""

# Resumen de extracción
echo -e "${CYAN}📂 Extracción:${NC}"
echo -e "   Tipo: ${YELLOW}$TIPO_EXTRACCION${NC}"
if [ -n "$VERSION" ]; then
    echo -e "   Versión: ${YELLOW}$VERSION${NC}"
fi
echo -e "   Carpeta destino: ${GREEN}$CARPETA${NC}"
echo -e "   Archivos copiados: ${GREEN}$ARCHIVOS_COPIADOS${NC}"
if [ $ARCHIVOS_OMITIDOS -gt 0 ]; then
    echo -e "   Archivos omitidos: ${YELLOW}$ARCHIVOS_OMITIDOS${NC}"
fi
echo ""

# Resumen de commit
if [ "$hacer_commit" = "1" ]; then
    echo -e "${CYAN}📝 Commit:${NC}"
    if [ "$COMMIT_PREPARADO" = true ]; then
        echo -e "   Estado: ${GREEN}✅ Preparado en staging${NC}"
        echo -e "   Archivo: ${GREEN}$ARCHIVO_HISTORIAL${NC}"
        echo -e "   Mensaje: ${YELLOW}$MENSAJE_COMMIT${NC}"
    else
        echo -e "   Estado: ${RED}❌ No se pudo preparar${NC}"
        echo -e "   Archivo: ${YELLOW}$ARCHIVO_HISTORIAL${NC} (actualizado pero no en staging)"
    fi
    echo ""
fi

# Mensaje final
echo -e "${GREEN}════════════════════════════════════════${NC}"
if [ "$hacer_commit" = "1" ] && [ "$COMMIT_PREPARADO" = true ]; then
    echo -e "${GREEN}  ✅ PROCESO COMPLETADO EXITOSAMENTE${NC}"
elif [ "$hacer_commit" = "1" ] && [ "$COMMIT_PREPARADO" = false ]; then
    echo -e "${YELLOW}  ⚠️ PROCESO COMPLETADO CON ADVERTENCIAS${NC}"
else
    echo -e "${GREEN}  ✅ PROCESO COMPLETADO EXITOSAMENTE${NC}"
fi
echo -e "${GREEN}════════════════════════════════════════${NC}"
echo ""

# Pausa final para que el usuario pueda ver todo
echo -e "${CYAN}Presiona Enter para salir...${NC}"
read -r
