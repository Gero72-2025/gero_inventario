<?php

namespace App\Controllers;

use App\Models\RenglonesModel;
use CodeIgniter\Controller;

class Renglones extends Controller
{
    protected $renglonesModel;
    protected $session;

    public function __construct()
    {
        $this->renglonesModel = new RenglonesModel();
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
     * Listar todos los renglones con paginación
     */
    public function index()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $builder = $this->renglonesModel
                ->where('deleted_at', null);

            if ($searchTerm !== '') {
                $builder->groupStart()
                    ->like('codigo_renglon', $searchTerm)
                    ->orLike('descripcion', $searchTerm)
                    ->groupEnd();
            }

            $renglones = $builder
                ->orderBy('id', 'DESC')
                ->paginate(10);

            return view('renglones/index', [
                'renglones'  => $renglones,
                'pager'      => $this->renglonesModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al cargar los renglones: ' . $e->getMessage());
            return redirect()->to('/dashboard');
        }
    }

    /**
     * Formulario para crear nuevo renglón
     */
    public function create()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $data['title']   = 'Nuevo Renglón';
            $data['heading'] = 'Crear Nuevo Renglón';

            return view('renglones/create', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error: ' . $e->getMessage());
            return redirect()->to('renglones');
        }
    }

    /**
     * Guardar nuevo renglón
     */
    public function store()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            // Validar datos
            $validation = $this->validate([
                'codigo_renglon' => 'required|string|max_length[100]|is_unique[renglones.codigo_renglon]',
                'descripcion'    => 'required|string',
                'status'         => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'codigo_renglon' => $this->request->getPost('codigo_renglon'),
                'descripcion'    => $this->request->getPost('descripcion'),
                'status'         => $this->request->getPost('status'),
                'id_usuario_creo' => $idUsuario,
            ];

            $inserted = $this->renglonesModel->insert($data);

            if ($inserted === false) {
                $this->session->setFlashdata('error', 'Error al crear el renglón.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Renglón creado exitosamente.');
            return redirect()->to('renglones');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al guardar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Mostrar detalles de un renglón
     */
    public function ver($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglon = $this->renglonesModel->find($id);

            if (!$renglon) {
                throw new \Exception('Renglón no encontrado.');
            }

            $renglonesConAuditoria = $this->renglonesModel->getRenglonesConAuditoria($id);

            $data['renglon']  = $renglonesConAuditoria;
            $data['title']   = 'Detalles de Renglón';
            $data['heading'] = 'Detalles: ' . $renglon['codigo_renglon'];

            if ($this->request->isAJAX()) {
                return view('renglones/show_modal', $data);
            }

            return view('renglones/show', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('renglones');
        }
    }

    /**
     * Formulario para editar renglón
     */
    public function editar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglon = $this->renglonesModel->find($id);

            if (!$renglon) {
                throw new \Exception('Renglón no encontrado.');
            }

            $data['renglon']  = $renglon;
            $data['title']   = 'Editar Renglón';
            $data['heading'] = 'Editar: ' . $renglon['codigo_renglon'];

            if ($this->request->isAJAX()) {
                return view('renglones/edit_modal', $data);
            }

            return view('renglones/edit', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('renglones');
        }
    }

    /**
     * Actualizar renglón
     */
    public function actualizar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglon = $this->renglonesModel->find($id);

            if (!$renglon) {
                throw new \Exception('Renglón no encontrado.');
            }

            // Validar datos
            $validation = $this->validate([
                'codigo_renglon' => 'required|max_length[100]|is_unique[renglones.codigo_renglon,id,' . $id . ']',
                'descripcion'    => 'required',
                'status'         => 'required|in_list[activo,inactivo]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'codigo_renglon'      => $this->request->getPost('codigo_renglon'),
                'descripcion'         => $this->request->getPost('descripcion'),
                'status'              => $this->request->getPost('status'),
                'id_usuario_actualizo' => $idUsuario,
            ];

            $updated = $this->renglonesModel->update($id, $data);

            if ($updated === false) {
                $this->session->setFlashdata('error', 'Error al actualizar el renglón.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Renglón actualizado exitosamente.');
            return redirect()->to('renglones');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Eliminar renglón (Soft Delete)
     */
    public function delete($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglon = $this->renglonesModel->find($id);

            if (!$renglon) {
                throw new \Exception('Renglón no encontrado.');
            }

            $idUsuario = session('user_id');

            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->renglonesModel->update($id, $data);

            if ($deleted === false) {
                if ($this->request->isAJAX()) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setJSON(['success' => false, 'message' => 'Error al eliminar el renglón.']);
                }
                $this->session->setFlashdata('error', 'Error al eliminar el renglón.');
                return redirect()->back();
            }

            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON(['success' => true, 'message' => 'Renglón eliminado exitosamente.']);
            }

            $this->session->setFlashdata('success', 'Renglón eliminado exitosamente.');
            return redirect()->to('renglones');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'message' => $e->getMessage()]);
            }
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Obtener renglón por ID (JSON para AJAX)
     */
    public function getById($id)
    {
        try {
            $renglon = $this->renglonesModel->find($id);

            if (!$renglon) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['success' => false, 'message' => 'Renglón no encontrado.']);
            }

            return $this->response
                ->setStatusCode(200)
                ->setJSON(['success' => true, 'data' => $renglon]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
