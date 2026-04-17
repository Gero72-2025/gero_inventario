<?php

namespace App\Controllers;

use App\Models\ConfiguracionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class Configuraciones extends BaseController
{
    protected ConfiguracionModel $configuracionModel;

    public function __construct()
    {
        $this->configuracionModel = new ConfiguracionModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->configuracionModel
                ->where('deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('llave', $searchTerm)
                    ->groupEnd();
            }

            $configuraciones = $builder
                ->orderBy('id', 'DESC')
                ->paginate(10);

            return view('configuraciones/index', [
                'configuraciones' => $configuraciones,
                'pager'           => $this->configuracionModel->pager,
                'searchTerm'      => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('configuraciones'))
                ->with('error', 'No fue posible cargar las configuraciones.');
        }
    }

    public function create()
    {
        return view('configuraciones/create');
    }

    public function store()
    {
        $rules = [
            'llave'      => 'required|max_length[100]|is_unique[cat_configuraciones.llave]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->configuracionModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar la configuracion.');
            }

            return redirect()->to(base_url('configuraciones'))
                ->with('success', 'Configuracion creada correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar la configuracion.');
        }
    }

    public function edit(int $id)
    {
        try {
            $configuracion = $this->configuracionModel->find($id);

            if (! $configuracion) {
                throw PageNotFoundException::forPageNotFound('La configuracion no existe.');
            }

            return view('configuraciones/edit', [
                'configuracion' => $configuracion,
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('configuraciones'))
                ->with('error', 'No fue posible cargar la configuracion.');
        }
    }

    public function update(int $id)
    {
        try {
            $configuracion = $this->configuracionModel->find($id);

            if (! $configuracion) {
                throw PageNotFoundException::forPageNotFound('La configuracion no existe.');
            }

            // La llave es inmutable: no se valida ni se permite modificar en update.

            $data = $this->extractRequestData();
            unset($data['llave'], $data['id_usuario_creo']);

            if (! $this->configuracionModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar la configuracion.');
            }

            return redirect()->to(base_url('configuraciones'))
                ->with('success', 'Configuracion actualizada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar la configuracion.');
        }
    }

    public function delete(int $id)
    {
        try {
            $configuracion = $this->configuracionModel->find($id);

            if (! $configuracion) {
                throw PageNotFoundException::forPageNotFound('La configuracion no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->configuracionModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->configuracionModel->delete($id);

            return redirect()->to(base_url('configuraciones'))
                ->with('success', 'Configuracion eliminada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('configuraciones'))
                ->with('error', 'No fue posible eliminar la configuracion.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'llave'               => trim((string) $this->request->getPost('llave')),
            'valor_1'             => $this->request->getPost('valor_1'),
            'valor_2'             => $this->request->getPost('valor_2'),
            'valor_3'             => $this->request->getPost('valor_3'),
            'valor_4'             => $this->request->getPost('valor_4'),
            'valor_5'             => $this->request->getPost('valor_5'),
            'valor_6'             => $this->request->getPost('valor_6'),
            'valor_7'             => $this->request->getPost('valor_7'),
            'valor_8'             => $this->request->getPost('valor_8'),
            'valor_9'             => $this->request->getPost('valor_9'),
            'valor_10'            => $this->request->getPost('valor_10'),
            'id_usuario_creo'     => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
