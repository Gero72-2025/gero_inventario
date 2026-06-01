<?php

namespace App\Controllers;

use App\Models\EmpleadosModel;
use App\Models\DivisionesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Empleados extends BaseController
{
    protected EmpleadosModel $empleadosModel;
    protected DivisionesModel $divisionesModel;

    public function __construct()
    {
        $this->empleadosModel = new EmpleadosModel();
        $this->divisionesModel = new DivisionesModel();
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

            return view('empleados/create', [
                'divisiones' => $divisiones,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('empleados'))
                ->with('error', 'No fue posible cargar el formulario de empleados.');
        }
    }

    public function store()
    {
        $rules = [
            'codigo_empleado' => 'required|max_length[50]|is_unique[cat_empleados.codigo_empleado]',
            'nombre_completo' => 'required|max_length[150]',
            'tipo_contrato'   => 'required|in_list[011,012,contratista]',
            'id_division'     => 'required|integer|is_not_unique[cat_divisiones.id]',
            'status'          => 'permit_empty|in_list[activo,inactivo]',
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

            return view('empleados/edit', [
                'empleado'   => $empleado,
                'divisiones' => $divisiones,
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
                'codigo_empleado' => 'required|max_length[50]|is_unique[cat_empleados.codigo_empleado,id,' . $id . ']',
                'nombre_completo' => 'required|max_length[150]',
                'tipo_contrato'   => 'required|in_list[011,012,contratista]',
                'id_division'     => 'required|integer|is_not_unique[cat_divisiones.id]',
                'status'          => 'permit_empty|in_list[activo,inactivo]',
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

        return [
            'codigo_empleado'      => trim((string) $this->request->getPost('codigo_empleado')),
            'nombre_completo'      => trim((string) $this->request->getPost('nombre_completo')),
            'tipo_contrato'        => $this->request->getPost('tipo_contrato'),
            'id_division'          => (int) $this->request->getPost('id_division'),
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
