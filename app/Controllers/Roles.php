<?php

namespace App\Controllers;

use App\Models\RolModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Roles extends BaseController
{
    protected RolModel $rolModel;

    public function __construct()
    {
        $this->rolModel = new RolModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->rolModel
                ->select('cat_roles.id, cat_roles.nombre_rol, cat_roles.descripcion, cat_roles.updated_at, COUNT(usuarios.id) AS total_usuarios')
                ->join('usuarios', 'usuarios.id_rol = cat_roles.id AND usuarios.deleted_at IS NULL', 'left')
                ->where('cat_roles.deleted_at', null)
                ->groupBy('cat_roles.id, cat_roles.nombre_rol, cat_roles.descripcion, cat_roles.updated_at');

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_roles.nombre_rol', $searchTerm)
                    ->orLike('cat_roles.descripcion', $searchTerm)
                    ->groupEnd();
            }

            $roles = $builder
                ->orderBy('cat_roles.id', 'DESC')
                ->paginate(10);

            return view('roles/index', [
                'roles'      => $roles,
                'pager'      => $this->rolModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('usuarios'))
                ->with('error', 'No fue posible cargar el listado de roles.');
        }
    }

    public function create()
    {
        return view('roles/create');
    }

    public function store()
    {
        $rules = [
            'nombre_rol' => 'required|max_length[50]|is_unique[cat_roles.nombre_rol]',
            'descripcion' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->rolModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el rol.');
            }

            return redirect()->to(base_url('roles'))
                ->with('success', 'Rol creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el rol.');
        }
    }

    public function edit(int $id)
    {
        try {
            $rol = $this->rolModel->find($id);

            if (! $rol) {
                throw PageNotFoundException::forPageNotFound('El rol no existe.');
            }

            return view('roles/edit', [
                'rol' => $rol,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('roles'))
                ->with('error', 'No fue posible cargar el rol.');
        }
    }

    public function update(int $id)
    {
        try {
            $rol = $this->rolModel->find($id);

            if (! $rol) {
                throw PageNotFoundException::forPageNotFound('El rol no existe.');
            }

            $rules = [
                'nombre_rol' => 'required|max_length[50]|is_unique[cat_roles.nombre_rol,id,' . $id . ']',
                'descripcion' => 'permit_empty',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->rolModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el rol.');
            }

            return redirect()->to(base_url('roles'))
                ->with('success', 'Rol actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el rol.');
        }
    }

    public function delete(int $id)
    {
        try {
            $rol = $this->rolModel->find($id);

            if (! $rol) {
                throw PageNotFoundException::forPageNotFound('El rol no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->rolModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->rolModel->delete($id);

            return redirect()->to(base_url('roles'))
                ->with('success', 'Rol eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('roles'))
                ->with('error', 'No fue posible eliminar el rol.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'nombre_rol'           => trim((string) $this->request->getPost('nombre_rol')),
            'descripcion'          => $this->nullableText($this->request->getPost('descripcion')),
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }

    private function nullableText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }
}
