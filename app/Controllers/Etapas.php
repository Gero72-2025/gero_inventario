<?php

namespace App\Controllers;

use App\Models\EtapaModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Etapas extends BaseController
{
    protected EtapaModel $etapaModel;

    public function __construct()
    {
        $this->etapaModel = new EtapaModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->etapaModel
                ->where('cat_etapas.deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('cat_etapas.nombre_etapa', $searchTerm)
                    ->orLike('cat_etapas.color_hex', $searchTerm)
                    ->groupEnd();
            }

            $etapas = $builder
                ->orderBy('cat_etapas.id', 'DESC')
                ->paginate(10);

            return view('etapas/index', [
                'etapas'     => $etapas,
                'pager'      => $this->etapaModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('etapas'))
                ->with('error', 'No fue posible cargar el listado de etapas.');
        }
    }

    public function create()
    {
        return view('etapas/create');
    }

    public function store()
    {
        $rules = [
            'nombre_etapa' => 'required|max_length[100]|is_unique[cat_etapas.nombre_etapa]',
            'color_hex'    => 'required|max_length[7]',
            'status'       => 'permit_empty|in_list[activo,inactivo]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->etapaModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar la etapa.');
            }

            return redirect()->to(base_url('etapas'))
                ->with('success', 'Etapa creada correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar la etapa.');
        }
    }

    public function edit(int $id)
    {
        try {
            $etapa = $this->etapaModel->find($id);

            if (! $etapa) {
                throw PageNotFoundException::forPageNotFound('La etapa no existe.');
            }

            return view('etapas/edit', [
                'etapa' => $etapa,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('etapas'))
                ->with('error', 'No fue posible cargar la etapa.');
        }
    }

    public function update(int $id)
    {
        try {
            $etapa = $this->etapaModel->find($id);

            if (! $etapa) {
                throw PageNotFoundException::forPageNotFound('La etapa no existe.');
            }

            $rules = [
                'nombre_etapa' => 'required|max_length[100]|is_unique[cat_etapas.nombre_etapa,id,' . $id . ']',
                'color_hex'    => 'required|max_length[7]',
                'status'       => 'permit_empty|in_list[activo,inactivo]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->etapaModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar la etapa.');
            }

            return redirect()->to(base_url('etapas'))
                ->with('success', 'Etapa actualizada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar la etapa.');
        }
    }

    public function delete(int $id)
    {
        try {
            $etapa = $this->etapaModel->find($id);

            if (! $etapa) {
                throw PageNotFoundException::forPageNotFound('La etapa no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->etapaModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->etapaModel->delete($id);

            return redirect()->to(base_url('etapas'))
                ->with('success', 'Etapa eliminada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('etapas'))
                ->with('error', 'No fue posible eliminar la etapa.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'nombre_etapa'         => trim((string) $this->request->getPost('nombre_etapa')),
            'color_hex'            => trim((string) $this->request->getPost('color_hex')),
            'status'               => $this->request->getPost('status') ?? 'activo',
            'id_usuario_creo'      => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
