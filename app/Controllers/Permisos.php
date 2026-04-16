<?php

namespace App\Controllers;

use App\Models\PermisoModel;
use App\Models\RolModel;
use App\Models\RolPermisoModel;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class Permisos extends BaseController
{
    protected PermisoModel $permisoModel;
    protected RolModel $rolModel;
    protected RolPermisoModel $rolPermisoModel;

    public function __construct()
    {
        $this->permisoModel = new PermisoModel();
        $this->rolModel = new RolModel();
        $this->rolPermisoModel = new RolPermisoModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->permisoModel
                ->where('deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('nombre_permiso', $searchTerm)
                    ->orLike('controlador', $searchTerm)
                    ->orLike('accion', $searchTerm)
                    ->groupEnd();
            }

            $permisos = $builder
                ->orderBy('id', 'DESC')
                ->paginate(10);

            return view('permisos/index', [
                'permisos'   => $permisos,
                'pager'      => $this->permisoModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('usuarios'))
                ->with('error', 'No fue posible cargar el listado de permisos.');
        }
    }

    public function sincronizar()
    {
        try {
            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $accionesSistema = $this->discoverControllerActions();
            $creados = 0;
            $restaurados = 0;

            foreach ($accionesSistema as $accionSistema) {
                $nombrePermiso = $accionSistema['nombre_permiso'];
                $controlador = $accionSistema['controlador'];
                $accion = $accionSistema['accion'];

                $permisoExistente = $this->permisoModel
                    ->withDeleted()
                    ->where('nombre_permiso', $nombrePermiso)
                    ->first();

                if ($permisoExistente === null) {
                    $inserted = $this->permisoModel->insert([
                        'nombre_permiso'  => $nombrePermiso,
                        'controlador'     => $controlador,
                        'accion'          => $accion,
                        'id_usuario_creo' => $idUsuario,
                    ]);

                    if (! $inserted) {
                        throw new RuntimeException('No fue posible registrar el permiso ' . $nombrePermiso . '.');
                    }

                    $creados++;
                    continue;
                }

                if (! empty($permisoExistente['deleted_at'])) {
                    $updated = $this->permisoModel->update((int) $permisoExistente['id'], [
                        'controlador'          => $controlador,
                        'accion'               => $accion,
                        'deleted_at'           => null,
                        'id_usuario_actualizo' => $idUsuario,
                    ]);

                    if (! $updated) {
                        throw new RuntimeException('No fue posible restaurar el permiso ' . $nombrePermiso . '.');
                    }

                    $restaurados++;
                }
            }

            return redirect()->to(base_url('permisos'))
                ->with('success', 'Sincronizacion completada. Acciones detectadas: ' . count($accionesSistema) . '. Nuevos: ' . $creados . '. Restaurados: ' . $restaurados . '.');
        } catch (Throwable $e) {
            return redirect()->to(base_url('permisos'))
                ->with('error', 'No fue posible sincronizar permisos: ' . $e->getMessage());
        }
    }

    public function gestionarPorRol()
    {
        try {
            $idRol = (int) ($this->request->getGet('id_rol') ?? 0);

            $roles = $this->rolModel
                ->where('deleted_at', null)
                ->orderBy('nombre_rol', 'ASC')
                ->findAll();

            $permisos = $this->permisoModel
                ->where('deleted_at', null)
                ->orderBy('nombre_permiso', 'ASC')
                ->findAll();

            $permisosPorControlador = [];
            foreach ($permisos as $permiso) {
                $controlador = (string) ($permiso['controlador'] ?? 'SinControlador');
                if (!isset($permisosPorControlador[$controlador])) {
                    $permisosPorControlador[$controlador] = [];
                }

                $permisosPorControlador[$controlador][] = $permiso;
            }

            ksort($permisosPorControlador);

            $asignados = [];
            if ($idRol > 0) {
                $rows = $this->rolPermisoModel
                    ->select('id_permiso')
                    ->where('id_rol', $idRol)
                    ->where('deleted_at', null)
                    ->findAll();

                $asignados = array_map(
                    static fn(array $row): int => (int) $row['id_permiso'],
                    $rows
                );
            }

            return view('permisos/roles', [
                'roles'     => $roles,
                'idRol'     => $idRol,
                'permisos'  => $permisos,
                'permisosPorControlador' => $permisosPorControlador,
                'asignados' => $asignados,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('permisos'))
                ->with('error', 'No fue posible cargar la gestion de permisos por rol.');
        }
    }

    public function guardarPermisosPorRol()
    {
        $rules = [
            'id_rol' => 'required|integer|is_not_unique[cat_roles.id]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $idRol = (int) $this->request->getPost('id_rol');
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        $postedIds = $this->request->getPost('id_permisos');
        $idsPermisos = [];
        if (is_array($postedIds)) {
            $idsPermisos = array_values(array_unique(array_map('intval', $postedIds)));
            $idsPermisos = array_values(array_filter($idsPermisos, static fn(int $id): bool => $id > 0));
        }

        $db = db_connect();
        $db->transStart();

        try {
            $now = date('Y-m-d H:i:s');

            $this->rolPermisoModel
                ->where('id_rol', $idRol)
                ->where('deleted_at', null)
                ->set([
                    'deleted_at'         => $now,
                    'updated_at'         => $now,
                    'id_usuario_elimino' => $idUsuario,
                ])
                ->update();

            if ($idsPermisos !== []) {
                $permisosValidos = $this->permisoModel
                    ->select('id')
                    ->whereIn('id', $idsPermisos)
                    ->where('deleted_at', null)
                    ->findAll();

                $idsValidos = array_map(
                    static fn(array $row): int => (int) $row['id'],
                    $permisosValidos
                );

                foreach ($idsValidos as $idPermiso) {
                    $registro = $this->rolPermisoModel
                        ->withDeleted()
                        ->where('id_rol', $idRol)
                        ->where('id_permiso', $idPermiso)
                        ->first();

                    if ($registro !== null) {
                        $updated = $this->rolPermisoModel->update((int) $registro['id'], [
                            'deleted_at'           => null,
                            'id_usuario_actualizo' => $idUsuario,
                        ]);

                        if (! $updated) {
                            throw new RuntimeException('No fue posible actualizar la asignacion del permiso ID ' . $idPermiso . '.');
                        }

                        continue;
                    }

                    $inserted = $this->rolPermisoModel->insert([
                        'id_rol'          => $idRol,
                        'id_permiso'      => $idPermiso,
                        'id_usuario_creo' => $idUsuario,
                    ]);

                    if (!$inserted) {
                        throw new RuntimeException('No fue posible asignar el permiso ID ' . $idPermiso . '.');
                    }
                }
            }

            $db->transComplete();
            if (! $db->transStatus()) {
                throw new RuntimeException('No fue posible guardar la relacion rol-permisos.');
            }

            return redirect()->to(base_url('permisos/roles?id_rol=' . $idRol))
                ->with('success', 'Permisos del rol actualizados correctamente.');
        } catch (Throwable $e) {
            $db->transRollback();

            return redirect()->to(base_url('permisos/roles?id_rol=' . $idRol))
                ->with('error', 'No fue posible guardar permisos por rol: ' . $e->getMessage());
        }
    }

    public function delete(int $id)
    {
        try {
            $permiso = $this->permisoModel->find($id);

            if (! $permiso) {
                return redirect()->to(base_url('permisos'))
                    ->with('error', 'El permiso no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->permisoModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->permisoModel->delete($id);

            return redirect()->to(base_url('permisos'))
                ->with('success', 'Permiso eliminado correctamente.');
        } catch (Throwable $e) {
            return redirect()->to(base_url('permisos'))
                ->with('error', 'No fue posible eliminar el permiso.');
        }
    }

    private function discoverControllerActions(): array
    {
        $controllersPath = APPPATH . 'Controllers';
        $baseMethods = $this->getBaseControllerMethods();
        $acciones = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($controllersPath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (! $fileInfo instanceof \SplFileInfo) {
                continue;
            }

            if ($fileInfo->getExtension() !== 'php') {
                continue;
            }

            $realPath = (string) $fileInfo->getRealPath();
            if ($realPath === '' || str_ends_with($realPath, DIRECTORY_SEPARATOR . 'BaseController.php')) {
                continue;
            }

            $relative = substr($realPath, strlen(realpath($controllersPath)) + 1);
            $relativeClass = str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relative);
            $className = 'App\\Controllers\\' . $relativeClass;

            if (! class_exists($className)) {
                require_once $realPath;
            }

            if (! class_exists($className)) {
                continue;
            }

            $reflection = new ReflectionClass($className);
            if ($reflection->isAbstract()) {
                continue;
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->isConstructor() || $method->isDestructor() || $method->isStatic()) {
                    continue;
                }

                if ($method->getDeclaringClass()->getName() !== $className) {
                    continue;
                }

                $methodName = $method->getName();
                if (in_array($methodName, $baseMethods, true)) {
                    continue;
                }

                $shortController = $reflection->getShortName();
                $nombrePermiso = $shortController . '::' . $methodName;

                $acciones[$nombrePermiso] = [
                    'nombre_permiso' => $nombrePermiso,
                    'controlador'    => $shortController,
                    'accion'         => $methodName,
                ];
            }
        }

        ksort($acciones);
        return array_values($acciones);
    }

    private function getBaseControllerMethods(): array
    {
        $reflection = new ReflectionClass(BaseController::class);

        return array_map(
            static fn(ReflectionMethod $method): string => $method->getName(),
            $reflection->getMethods(ReflectionMethod::IS_PUBLIC)
        );
    }
}
