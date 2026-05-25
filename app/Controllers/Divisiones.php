<?php

namespace App\Controllers;

use App\Models\DivisionesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Divisiones extends BaseController
{
    protected DivisionesModel $divisionesModel;

    public function __construct()
    {
        $this->divisionesModel = new DivisionesModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->divisionesModel
                ->where('cat_divisiones.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_divisiones.nombre_division', $searchTerm)
                    ->groupEnd();
            }

            $divisiones = $builder
                ->orderBy('cat_divisiones.id', 'DESC')
                ->paginate(10);

            return view('divisiones/index', [
                'divisiones' => $divisiones,
                'pager'      => $this->divisionesModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('divisiones'))
                ->with('error', 'No fue posible cargar el listado de divisiones.');
        }
    }

    public function create()
    {
        return view('divisiones/create');
    }

    public function store()
    {
        $rules = [
            'nombre_division' => 'required|max_length[100]|is_unique[cat_divisiones.nombre_division]',
            'status'          => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->divisionesModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar la división.');
            }

            return redirect()->to(base_url('divisiones'))
                ->with('success', 'División creada correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar la división.');
        }
    }

    public function edit(int $id)
    {
        try {
            $division = $this->divisionesModel->find($id);

            if (! $division) {
                throw PageNotFoundException::forPageNotFound('La división no existe.');
            }

            return view('divisiones/edit', [
                'division' => $division,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('divisiones'))
                ->with('error', 'No fue posible cargar la división.');
        }
    }

    public function update(int $id)
    {
        try {
            $division = $this->divisionesModel->find($id);

            if (! $division) {
                throw PageNotFoundException::forPageNotFound('La división no existe.');
            }

            $rules = [
                'nombre_division' => 'required|max_length[100]|is_unique[cat_divisiones.nombre_division,id,' . $id . ']',
                'status'          => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->divisionesModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar la división.');
            }

            return redirect()->to(base_url('divisiones'))
                ->with('success', 'División actualizada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar la división.');
        }
    }

    public function delete(int $id)
    {
        try {
            $division = $this->divisionesModel->find($id);

            if (! $division) {
                throw PageNotFoundException::forPageNotFound('La división no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->divisionesModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->divisionesModel->delete($id);

            return redirect()->to(base_url('divisiones'))
                ->with('success', 'División eliminada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('divisiones'))
                ->with('error', 'No fue posible eliminar la división.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'nombre_division'      => trim((string) $this->request->getPost('nombre_division')),
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
