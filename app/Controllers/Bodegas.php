<?php

namespace App\Controllers;

use App\Models\BodegaModel;
use CodeIgniter\Controller;

class Bodegas extends Controller
{
    protected $bodegaModel;
    protected $session;

    public function __construct()
    {
        $this->bodegaModel = new BodegaModel();
        $this->session = session();
    }

    /**
     * Verificar si el usuario está logueado
     */
    private function checkLogin()
    {
        if (!session('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }
        return null;
    }

    /**
     * Listar todas las bodegas con paginación
     */
    public function index()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $searchTerm = trim((string) $this->request->getGet('q'));
            $deletedIdsParam = trim((string) $this->request->getGet('deletedIds'));
            $deletedIds = [];

            if ($deletedIdsParam !== '') {
                $deletedIds = array_filter(array_map('trim', explode(',', $deletedIdsParam)), 'is_numeric');
            }

            $builder = $this->bodegaModel
                ->where('deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('nombre_bodega', $searchTerm)
                    ->orLike('ubicacion', $searchTerm)
                    ->groupEnd();
            }

            if (!empty($deletedIds)) {
                $builder->whereNotIn('id', $deletedIds);
            }

            $bodegas = $builder
                ->orderBy('id', 'DESC')
                ->paginate(10);

            return view('bodegas/index', [
                'bodegas'    => $bodegas,
                'pager'      => $this->bodegaModel->pager,
                'searchTerm' => $searchTerm,
                'deletedIds' => implode(',', $deletedIds),
            ]);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al cargar las bodegas: ' . $e->getMessage());
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Formulario para crear nueva bodega
     */
    public function create()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $data['title']   = 'Nueva Bodega';
            $data['heading'] = 'Crear Nueva Bodega';

            return view('bodegas/create', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->to('bodegas');
        }
    }

    /**
     * Guardar nueva bodega
     */
    public function store()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            // Validar datos
            $validation = $this->validate([
                'nombre_bodega' => 'required|string|max_length[255]',
                'ubicacion'     => 'required|string|max_length[255]',
                'status'        => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'nombre_bodega'     => $this->request->getPost('nombre_bodega'),
                'ubicacion'         => $this->request->getPost('ubicacion'),
                'status'            => $this->request->getPost('status'),
                'id_usuario_creo'   => $idUsuario,
            ];

            $inserted = $this->bodegaModel->insert($data);

            if ($inserted === false) {
                $this->session->setFlashdata('error', 'Error al crear la bodega.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Bodega creada exitosamente.');
            return redirect()->to('bodegas');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al guardar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Mostrar detalles de una bodega
     */
    public function ver($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $bodega = $this->bodegaModel->find($id);

            if (!$bodega) {
                throw new \Exception('Bodega no encontrada.');
            }

            $bodegaConAuditoria = $this->bodegaModel->getBodegaConAuditoria($id);

            $data['bodega']  = $bodegaConAuditoria;
            $data['title']   = 'Detalles de Bodega';
            $data['heading'] = 'Detalles: ' . $bodega['nombre_bodega'];

            if ($this->request->isAJAX()) {
                return view('bodegas/show_modal', $data);
            }

            return view('bodegas/show', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('bodegas');
        }
    }

    /**
     * Formulario para editar bodega
     */
    public function editar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $bodega = $this->bodegaModel->find($id);

            if (!$bodega) {
                throw new \Exception('Bodega no encontrada.');
            }

            $data['bodega']  = $bodega;
            $data['title']   = 'Editar Bodega';
            $data['heading'] = 'Editar: ' . $bodega['nombre_bodega'];

            if ($this->request->isAJAX()) {
                return view('bodegas/edit_modal', $data);
            }

            return view('bodegas/edit', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('bodegas');
        }
    }

    /**
     * Actualizar bodega
     */
    public function actualizar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $bodega = $this->bodegaModel->find($id);

            if (!$bodega) {
                throw new \Exception('Bodega no encontrada.');
            }

            // Validar datos
            $validation = $this->validate([
                'nombre_bodega' => 'required|string|max_length[255]',
                'ubicacion'     => 'required|string|max_length[255]',
                'status'        => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'nombre_bodega'      => $this->request->getPost('nombre_bodega'),
                'ubicacion'          => $this->request->getPost('ubicacion'),
                'status'             => $this->request->getPost('status'),
                'id_usuario_actualizo' => $idUsuario,
            ];

            $updated = $this->bodegaModel->update($id, $data);

            if ($updated === false) {
                $this->session->setFlashdata('error', 'Error al actualizar la bodega.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Bodega actualizada exitosamente.');
            return redirect()->to('bodegas');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Eliminar bodega (Soft Delete)
     */
    public function delete($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $bodega = $this->bodegaModel->find($id);

            if (!$bodega) {
                throw new \Exception('Bodega no encontrada.');
            }

            $idUsuario = session('user_id');

            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->bodegaModel->update($id, $data);

            if ($deleted === false) {
                $this->session->setFlashdata('error', 'Error al eliminar la bodega.');
                return redirect()->back();
            }

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON(['success' => true, 'message' => 'Bodega eliminada exitosamente.']);
            }

            $this->session->setFlashdata('success', 'Bodega eliminada exitosamente.');
            return redirect()->to('bodegas');
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Obtener bodega por ID (JSON para AJAX)
     */
    public function getById($id)
    {
        try {
            $bodega = $this->bodegaModel->find($id);

            if (!$bodega) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Bodega no encontrada.']);
            }

            return $this->response
                ->setStatusCode(200)
                ->setJSON(['success' => true, 'data' => $bodega]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
