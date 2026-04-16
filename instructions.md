# GERO - Inventario & Presupuestos | IA Instructions

## 1. Contexto del Proyecto
GERO es una plataforma diseñada para centralizar los procesos administrativos, enfocada en la trazabilidad de inventarios (suministros de oficina) y control presupuestario entre divisiones.

## 2. Stack Tecnológico & Arquitectura
- **Framework:** CodeIgniter 4 (MVC).
- **Base de Datos:** MySQL (Motores InnoDB).
- **Frontend:** Bootstrap 5 + Vanilla JS / jQuery (AJAX).
- **Patrón:** Repository Pattern para la lógica de base de datos.

## 3. Reglas Obligatorias de Codificación
- **Seguridad:** Usar siempre el Query Builder de CI4 o Prepared Statements. No concatenar variables en SQL.
- **Rutas:** Usar `base_url()` para assets y rutas.
- **Validación:** Toda entrada debe validarse en el Controlador usando `$this->validate()`.
- **Base de Datos:** No se permiten cambios manuales en SQL. Todo cambio debe realizarse mediante **Migrations** de CodeIgniter.
- **Layout Global (UI):** Toda nueva pantalla debe respetar el layout base de la plataforma: encabezado (header), navegación lateral izquierda (nav) y pie de página (footer). El footer debe conservar el texto: "Creado por AGPT - GERO - INDE © año".
- **Extensión de Vistas:** Toda vista nueva debe extender el layout compartido `app/Views/layouts/app.php` para garantizar consistencia visual y funcional.
- **Tablas en Index:** Todo `index` que muestre tablas debe incluir un input de filtrado y paginación. La paginación estándar obligatoria es de **10 registros por tabla**.
- **Campos de Auditoría en Formularios:** Los campos de auditoría (por ejemplo `id_usuario`) no deben mostrarse ni crearse visualmente en formularios de alta/edición, salvo que el usuario lo solicite explícitamente.

## 4. Instrucciones para la IA (@workspace)
- Al sugerir código, prioriza la compatibilidad con PHP 8.x.
- Si el usuario pide un nuevo módulo, verifica los modelos existentes en `app/Models` para mantener la consistencia de nombres (snake_case para tablas, PascalCase para clases).
- Generar siempre bloques try-catch para operaciones de base de datos.

## 5. Estándares de Base de Datos (Obligatorio)
- **Nomenclatura:** Nombres de tablas y campos siempre en `lowercase_snake_case` (minúsculas y guiones bajos).
- **Campos de Auditoría Detallada:** Toda tabla debe incluir obligatoriamente para trazabilidad total:
    - `id`: INT AUTO_INCREMENT PRIMARY KEY.
    - `id_usuario_creo`: INT (ID del usuario que realizó la inserción inicial).
    - `id_usuario_actualizo`: INT (ID del usuario que realizó la última modificación).
    - `id_usuario_elimino`: INT (ID del usuario que realizó el borrado lógico).
    - `created_at`: DATETIME (Fecha de creación).
    - `updated_at`: DATETIME (Fecha de última modificación).
    - `deleted_at`: DATETIME (Nulo por defecto, usado para Borrado Lógico).
- **Borrado Lógico (Soft Delete):** Prohibido usar la sentencia `DELETE`. Para "eliminar" registros, se debe llenar el campo `deleted_at` y registrar el ID del responsable en `id_usuario_elimino`.
- **Filtrado:** Todas las consultas (SELECT) deben incluir el filtro por defecto `deleted_at IS NULL` para asegurar que solo se visualice la data activa.
- **Precisión Financiera:** Todos los campos de moneda (monto, saldo, precio) deben usar el tipo de dato `DECIMAL(15,2)` en las migraciones para evitar errores de redondeo de punto flotante.

## 6. Protocolo de Creación de Módulos (Flujo de IA)
Cuando se solicite crear un nuevo módulo (ej: "Control de ingresos y egresos"):
1. **Generación de Migración:** Lo primero que debe proponer la IA es el código de la Migración (`app/Database/Migrations/`) siguiendo los estándares de auditoría (id, created_at, updated_at, deleted_at, id_usuario).
2. **Generación de Modelo:** Crear el Modelo en `app/Models/` activando `$useSoftDeletes` y `$useTimestamps`.
3. **Persistencia de Cambios:** La IA debe indicar explícitamente el comando de ejecución: `C:\xampp\php\php.exe spark migrate`.
4. **Actualizaciones de Tablas:** Si se solicita añadir un campo a una tabla existente, **PROHIBIDO** modificar la migración original. La IA debe crear una NUEVA migración tipo `AddColumnTo[Table]`.

## 7. Seguridad y Control de Acceso (RBAC)
- **Vinculación:** Usuario -> tiene un Rol (cat_roles) -> tiene muchos Permisos (permisos).
- **Asignación Automática:** Al crear un nuevo controlador, se debe asumir que sus métodos públicos son "acciones" protegibles. 
- **Middleware:** Cada controlador debe verificar en su `initController` o mediante un Filter de CI4 si el `id_rol` del usuario tiene permiso para la `acción` (método) y el `controlador` actual.

## 9. Sistema de Modales Global (GeroModal)

Toda confirmación, alerta o mensaje interactivo al usuario **debe** usar el sistema `GeroModal`. Está implementado en `public/assets/js/app.js` y el modal HTML vive en `app/Views/layouts/app.php`. Está disponible en todas las pantallas automáticamente.

### Tipos disponibles
| `type`    | Uso recomendado                                 |
|-----------|-------------------------------------------------|
| `confirm` | Confirmaciones neutras (acción genérica)        |
| `danger`  | Borrados, acciones irreversibles o destructivas |
| `warning` | Advertencias con consecuencias reversibles      |
| `info`    | Información relevante antes de continuar        |
| `success` | Confirmación de acción positiva                 |

### Uso declarativo (obligatorio para botones de formulario)
Agrega `data-gero-confirm` al `<button type="submit">`. **Prohibido usar `onclick="return confirm(...)"` o `alert()`.**

```html
<button type="submit"
        class="btn btn-sm btn-outline-danger"
        data-gero-confirm
        data-title="Eliminar registro"
        data-message="¿Deseas eliminar este registro? Esta acción no se puede deshacer."
        data-type="danger"
        data-confirm-label="Eliminar">Eliminar</button>
```

### Uso programático (para JS personalizado)
```js
GeroModal.show({
    title: 'Advertencia',
    message: 'Este proceso puede tardar varios minutos.',
    type: 'warning',
    confirmLabel: 'Continuar',
    showCancel: true,
    onConfirm: function () {
        // lógica al confirmar
    },
});
```

### Atributos `data-*` disponibles
- `data-title` — Título del modal.
- `data-message` — Mensaje del cuerpo del modal.
- `data-type` — Tipo visual (`confirm`, `danger`, `warning`, `info`, `success`).
- `data-confirm-label` — Texto del botón de confirmación (default: `Confirmar`).
- `data-cancel-label` — Texto del botón cancelar (default: `Cancelar`).

## 10. Trazabilidad y Bitácoras
- **Acciones Críticas:** Todo cambio en `presupuestos_renglon`, `facturas_liquidacion` y `asignaciones_responsabilidad` debe generar obligatoriamente un registro en `bitacora_cambios_datos`.
- **Formato de Datos:** Los cambios se almacenarán en formato JSON capturando el estado previo y posterior del registro para fines de auditoría de la Contraloría.