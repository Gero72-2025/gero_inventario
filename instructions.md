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

## 11. Patrones Críticos de CodeIgniter 4 (PROHIBIDO Violar estos patrones)

### 11.1 Query Builder en Modelos - USO CORRECTO
- **✅ CORRECTO:** Usar `$this->select()`, `$this->where()`, `$this->paginate()` dentro del Modelo que hereda de `CodeIgniter\Model`
- **❌ INCORRECTO:** Usar `$this->db->table()` cuando necesites acceder a `paginate()` — eso devuelve un Query Builder genérico sin el método `paginate()`
- **❌ INCORRECTO:** Usar `$this->builder()->paginate()` — el método builder() devuelve el query builder genérico

**Patrón correcto en Modelos:**
```php
public function getConRelaciones(string $searchTerm = '')
{
    // Usa $this directamente, NO $this->db->table() o $this->builder()
    $this->select('tabla.*, relacion.campo')
        ->join('tabla_relacion', 'tabla.id = tabla_relacion.tabla_id', 'left')
        ->where('tabla.deleted_at', null);

    if ($searchTerm !== '') {
        $this->groupStart()
            ->like('campo1', $searchTerm)
            ->orLike('campo2', $searchTerm)
            ->groupEnd();
    }

    return $this->orderBy('tabla.id', 'DESC')->paginate(10);
}
```

### 11.2 Try-Catch Infinitos - PROHIBIDO
- **❌ PROHIBIDO:** `try { ... } catch(Throwable $e) { return redirect()->to(base_url('ruta-actual')) }`
- Esto causa **ERR_TOO_MANY_REDIRECTS** porque redirige a la misma ruta que causó el error
- Si el usuario accede a `/foo` y dentro hay un error capturado que redirige a `/foo`, entra en loop infinito

**Patrón correcto en Controladores:**
```php
public function index()
{
    // Sin try-catch genérico que redirige a la misma ruta
    // Deja que CodeIgniter muestre el error (en desarrollo) o usa log_message()
    $data = $this->model->getData();
    return view('index', ['data' => $data]);
}

// Solo usa try-catch si REDIRIGES A OTRA RUTA
public function store()
{
    try {
        $this->model->insert($data);
        return redirect()->to(base_url('ruta-diferente'))
            ->with('success', 'Guardado');
    } catch (Throwable $e) {
        // Ahora redirigir a otra ruta es seguro
        return redirect()->back()->with('error', 'Error al guardar');
    }
}
```

### 11.3 Configuración de Filtros (Config/Filters.php) - RUTAS COMO ARRAYS
- **Formato de rutas en filtros:** Las rutas deben estar como **strings en un array**, nunca anidadas con `'except' =>`
- **✅ CORRECTO:**
```php
'permission' => [
    'before' => [
        'usuarios*',
        'configuraciones*',
        'presupuestos-divisiones*',
    ],
],
```

- **❌ INCORRECTO:** `'except' => [...]` como sub-clave dentro del array `'before'`
- **❌ INCORRECTO:** Pasar un string suelto en lugar de un array

### 11.4 Sincronización de Permisos - PASO OBLIGATORIO AL CREAR CONTROLADORES
Cuando crees un nuevo Controlador con métodos públicos (index, create, store, edit, update, delete):
1. El usuario DEBE ir a `/permisos`
2. Hacer clic en **"Sincronizar permisos"** — descubre automáticamente nuevos métodos
3. Luego ir a `/permisos/roles` y asignar permisos al rol del usuario
4. **Sin esto, el PermissionFilter bloqueará el acceso** (Error 403 Access Denied)

### 11.5 Migrations - Ejecución y Verificación
- Comando correcto: `C:\xampp\php\php.exe spark migrate`
- **NO es:** `php -l app/Database/Migrations/archivo.php` (eso solo verifica sintaxis)
- Verifica que devuelva: `Running all new migrations... Migrations complete.`
- Si no migra nada, probablemente ya fue ejecutada o hay un error de sintaxis en la migración

### 11.6 Alias de Rutas - AMBAS VERSIONES REQUERIDAS
Cuando crees rutas nuevas, SIEMPRE crea dos versiones:
- Con guiones: `/presupuestos-divisiones`
- Sin guiones: `/presupuestosdivisiones`

Así se evitan confusiones y ambos patrones funcionan. Ejemplo:
```php
$routes->get('presupuestos-divisiones', 'PresupuestosDivisiones::index');
$routes->get('presupuestos-divisiones/nuevo', 'PresupuestosDivisiones::create');
// ... más rutas

// Rutas alternativas sin guiones
$routes->get('presupuestosdivisiones', 'PresupuestosDivisiones::index');
$routes->get('presupuestosdivisiones/nuevo', 'PresupuestosDivisiones::create');
// ... más rutas
```

### 11.7 Paginación - Solo en Modelos, NO en Controladores
- El método `paginate()` **SOLO existe en Modelos**, no en Query Builder genérico
- Si necesitas paginar en el Controlador desde un Query Builder ad-hoc, usa el patrón del Modelo
- **Recomendación:** SIEMPRE delega la lógica de consulta complejas al Modelo mediante métodos publicos

### 11.8 Debugging de Errores - ANTES DE HACER CAMBIOS MASIVOS
- Si algo no funciona, **PRIMERO quita los try-catch** para ver el error real
- Si hay loop de redirecciones, **PRIMERO verifica que no hay try-catch redirigiendo a la misma ruta**
- Usa `log_message('error', $exception->getMessage())` para registrar errores sin exponer la aplicación
- En desarrollo, deja que CodeIgniter muestre la pantalla de error completa — es más útil que un redirect ciego
