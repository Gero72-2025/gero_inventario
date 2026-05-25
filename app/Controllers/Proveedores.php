<?php

namespace App\Controllers;

use App\Models\ProveedoresModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Proveedores extends BaseController
{
    protected ProveedoresModel $proveedoresModel;

    public function __construct()
    {
        $this->proveedoresModel = new ProveedoresModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->proveedoresModel
                ->where('cat_proveedores.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_proveedores.nit_proveedor', $searchTerm)
                    ->orLike('cat_proveedores.nombre_legal', $searchTerm)
                    ->orLike('cat_proveedores.nombre_comercial', $searchTerm)
                    ->groupEnd();
            }

            $proveedores = $builder
                ->orderBy('cat_proveedores.nombre_legal', 'ASC')
                ->paginate(10);

            return view('proveedores/index', [
                'proveedores' => $proveedores,
                'pager'       => $this->proveedoresModel->pager,
                'searchTerm'  => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('proveedores'))
                ->with('error', 'No fue posible cargar el listado de proveedores.');
        }
    }

    public function create()
    {
        return view('proveedores/create');
    }

    public function store()
    {
        $rules = [
            'nit_proveedor'      => 'required|max_length[50]|is_unique[cat_proveedores.nit_proveedor]',
            'nombre_legal'       => 'required|max_length[255]',
            'nombre_comercial'   => 'required|max_length[255]',
            'direccion_fiscal'   => 'required',
            'telefono_contacto'  => 'permit_empty|max_length[20]',
            'email_contacto'     => 'permit_empty|max_length[100]|valid_email',
            'status'             => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->proveedoresModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar el proveedor.');
            }

            return redirect()->to(base_url('proveedores'))
                ->with('success', 'Proveedor creado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar el proveedor.');
        }
    }

    public function edit(int $id)
    {
        try {
            $proveedor = $this->proveedoresModel->find($id);

            if (! $proveedor) {
                throw PageNotFoundException::forPageNotFound('El proveedor no existe.');
            }

            return view('proveedores/edit', [
                'proveedor' => $proveedor,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('proveedores'))
                ->with('error', 'No fue posible cargar el proveedor.');
        }
    }

    public function update(int $id)
    {
        try {
            $proveedor = $this->proveedoresModel->find($id);

            if (! $proveedor) {
                throw PageNotFoundException::forPageNotFound('El proveedor no existe.');
            }

            $rules = [
                'nit_proveedor'      => 'required|max_length[50]|is_unique[cat_proveedores.nit_proveedor,id,' . $id . ']',
                'nombre_legal'       => 'required|max_length[255]',
                'nombre_comercial'   => 'required|max_length[255]',
                'direccion_fiscal'   => 'required',
                'telefono_contacto'  => 'permit_empty|max_length[20]',
                'email_contacto'     => 'permit_empty|max_length[100]|valid_email',
                'status'             => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->proveedoresModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar el proveedor.');
            }

            return redirect()->to(base_url('proveedores'))
                ->with('success', 'Proveedor actualizado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar el proveedor.');
        }
    }

    public function delete(int $id)
    {
        try {
            $proveedor = $this->proveedoresModel->find($id);

            if (! $proveedor) {
                throw PageNotFoundException::forPageNotFound('El proveedor no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->proveedoresModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->proveedoresModel->delete($id);

            return redirect()->to(base_url('proveedores'))
                ->with('success', 'Proveedor eliminado correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('proveedores'))
                ->with('error', 'No fue posible eliminar el proveedor.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'nit_proveedor'        => trim((string) $this->request->getPost('nit_proveedor')),
            'nombre_legal'         => trim((string) $this->request->getPost('nombre_legal')),
            'nombre_comercial'     => trim((string) $this->request->getPost('nombre_comercial')),
            'direccion_fiscal'     => trim((string) $this->request->getPost('direccion_fiscal')),
            'telefono_contacto'    => trim((string) $this->request->getPost('telefono_contacto')),
            'email_contacto'       => trim((string) $this->request->getPost('email_contacto')),
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
