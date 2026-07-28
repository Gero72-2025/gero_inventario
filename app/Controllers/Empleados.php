<?php

namespace App\Controllers;

use App\Models\EmpleadosModel;
use App\Models\DivisionesModel;
use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Empleados extends BaseController
{
    protected EmpleadosModel $empleadosModel;
    protected DivisionesModel $divisionesModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->empleadosModel = new EmpleadosModel();
        $this->divisionesModel = new DivisionesModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->empleadosModel
                ->where('cat_empleados.deleted_at', null)
                ->select('cat_empleados.*, cat_divisiones.nombre_division')
                ->join('cat_divisiones', 'cat_empleados.id_division = cat_divisiones.id', 'left');

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_empleados.codigo_empleado', $searchTerm)
                    ->orLike('cat_empleados.nombre_completo', $searchTerm)
                    ->orLike('cat_divisiones.nombre_division', $searchTerm)
                    ->groupEnd();
            }

            $empleados = $builder
                ->orderBy('cat_empleados.id', 'DESC')
                ->paginate(10);

            return view('empleados/index', [
                'empleados' => $empleados,
                'pager'     => $this->empleadosModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('empleados'))
                ->with('error', 'No fue posible cargar el listado de empleados.');
        }
    }

    public function create()
    {
        try {
            $divisiones = $this->divisionesModel
                ->where('status', 'activo')
                ->where('deleted_at', null)
                ->orderBy('nombre_division', 'ASC')
                ->findAll();

            // usuarios para el desplegable de asignación (no eliminados)
            $usuarios = $this->usuarioModel
                ->where('deleted_at', null)
                ->orderBy('alias', 'ASC')
                ->findAll();

            return view('empleados/create', [
                'divisiones' => $divisiones,
                'usuarios'   => $usuarios,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('empleados'))
                ->with('error', 'No fue posible cargar el formulario de empleados.');
        }
    }

    public function store()
    {
        $rules = [
            'codigo_empleado'   => 'required|max_length[50]|is_unique[cat_empleados.codigo_empleado]',
            'nombre_completo'   => 'required|max_length[150]',
            'tipo_contrato'     => 'required|in_list[011,012,contratista]',
            'id_division'       => 'required|integer|is_not_unique[cat_divisiones.id]',
            'status'            => 'permit_empty|in_list[activo,inactivo]',
            'correo_electronico'=> 'permit_empty|valid_email|max_length[150]',
            'numero_telefonico' => 'permit_empty|max_length[25]',
            'nit'               => 'permit_empty|max_length[50]',
            'dpi'               => 'permit_empty|max_length[50]',
            'fecha_nacimiento'  => 'permit_empty|valid_date',
            'direccion'         => 'permit_empty|max_length[250]',
            'es_jefe'           => 'permit_empty',
            'id_usuario_asignado'=> 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->empleadosModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el empleado.');
            }

            return redirect()->to(base_url('empleados'))
                ->with('success', 'Empleado creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el empleado.');
        }
    }

    public function edit(int $id)
    {
        try {
            $empleado = $this->empleadosModel->find($id);

            if (! $empleado) {
                throw PageNotFoundException::forPageNotFound('El empleado no existe.');
            }

            $divisiones = $this->divisionesModel
                ->where('status', 'activo')
                ->where('deleted_at', null)
                ->orderBy('nombre_division', 'ASC')
                ->findAll();

            $usuarios = $this->usuarioModel
                ->where('deleted_at', null)
                ->orderBy('alias', 'ASC')
                ->findAll();

            return view('empleados/edit', [
                'empleado'   => $empleado,
                'divisiones' => $divisiones,
                'usuarios'   => $usuarios,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('empleados'))
                ->with('error', 'No fue posible cargar el empleado.');
        }
    }

    public function update(int $id)
    {
        try {
            $empleado = $this->empleadosModel->find($id);

            if (! $empleado) {
                throw PageNotFoundException::forPageNotFound('El empleado no existe.');
            }

            $rules = [
                'codigo_empleado'   => 'required|max_length[50]|is_unique[cat_empleados.codigo_empleado,id,' . $id . ']',
                'nombre_completo'   => 'required|max_length[150]',
                'tipo_contrato'     => 'required|in_list[011,012,contratista]',
                'id_division'       => 'required|integer|is_not_unique[cat_divisiones.id]',
                'status'            => 'permit_empty|in_list[activo,inactivo]',
                'correo_electronico'=> 'permit_empty|valid_email|max_length[150]',
                'numero_telefonico' => 'permit_empty|max_length[25]',
                'nit'               => 'permit_empty|max_length[50]',
                'dpi'               => 'permit_empty|max_length[50]',
                'fecha_nacimiento'  => 'permit_empty|valid_date',
                'direccion'         => 'permit_empty|max_length[250]',
                'es_jefe'           => 'permit_empty',
                'id_usuario_asignado'=> 'permit_empty|integer',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->empleadosModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el empleado.');
            }

            return redirect()->to(base_url('empleados'))
                ->with('success', 'Empleado actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el empleado.');
        }
    }

    public function delete(int $id)
    {
        try {
            $empleado = $this->empleadosModel->find($id);

            if (! $empleado) {
                throw PageNotFoundException::forPageNotFound('El empleado no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->empleadosModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->empleadosModel->delete($id);

            return redirect()->to(base_url('empleados'))
                ->with('success', 'Empleado eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('empleados'))
                ->with('error', 'No fue posible eliminar el empleado.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        // checkbox 'es_jefe' may come as 'on' or '1' when checked
        $esJefe = $this->request->getPost('es_jefe');
        $esJefe = $esJefe ? 1 : 0;

        $idUsuarioAsignado = $this->request->getPost('id_usuario_asignado');
        $idUsuarioAsignado = is_numeric($idUsuarioAsignado) ? (int) $idUsuarioAsignado : null;

        $fechaNacimiento = trim((string) $this->request->getPost('fecha_nacimiento')) ?: null;
        // optional: normalize empty string to null
        $direccion = trim((string) $this->request->getPost('direccion')) ?: null;

        return [
            'codigo_empleado'       => trim((string) $this->request->getPost('codigo_empleado')),
            'nombre_completo'       => trim((string) $this->request->getPost('nombre_completo')),
            'tipo_contrato'         => $this->request->getPost('tipo_contrato'),
            'id_division'           => (int) $this->request->getPost('id_division'),
            'status'                => $this->request->getPost('status') ?? 'activo',
            // new fields
            'nit'                   => trim((string) $this->request->getPost('nit')) ?: null,
            'dpi'                   => trim((string) $this->request->getPost('dpi')) ?: null,
            'numero_telefonico'     => trim((string) $this->request->getPost('numero_telefonico')) ?: null,
            'correo_electronico'    => trim((string) $this->request->getPost('correo_electronico')) ?: null,
            'fecha_nacimiento'      => $fechaNacimiento,
            'direccion'             => $direccion,
            'es_jefe'               => $esJefe,
            'id_usuario_asignado'   => $idUsuarioAsignado,

            'id_usuario_creo'       => $idUsuario,
            'id_usuario_actualizo'  => $idUsuario,
        ];
    }
}
