<?php

namespace App\Controllers;

use App\Models\CatInsumosModel;
use App\Models\RenglonesModel;
use CodeIgniter\Controller;

class CatInsumos extends Controller
{
    protected $helpers = ['form'];
    protected $insumosModel;
    protected $renglonesModel;
    protected $session;

    public function __construct()
    {
        $this->insumosModel   = new CatInsumosModel();
        $this->renglonesModel = new RenglonesModel();
        $this->session        = session();
    }

    private function checkLogin()
    {
        if (! session('is_logged_in')) {
            return redirect()->to('/login')->with('error', 'Debe iniciar sesión para acceder a esta página.');
        }
        return null;
    }

    public function index()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $insumos = $this->insumosModel->getInsumosConRenglones($searchTerm);

            return view('cat_insumos/index', [
                'insumos'    => $insumos,
                'pager'      => $this->insumosModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al cargar insumos: ' . $e->getMessage());
            return redirect()->to('/dashboard');
        }
    }

    public function create()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $renglones = $this->renglonesModel->where('deleted_at', null)->findAll();

            return view('cat_insumos/create', [
                'renglones' => $renglones,
            ]);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('cat_insumos');
        }
    }

    public function store()
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $validation = $this->validate([
                'id_renglon'      => 'required|integer',
                'codigo_pacc'     => 'required|max_length[100]|is_unique[cat_insumos.codigo_pacc]',
                'nombre_insumo'   => 'required|max_length[255]',
                'precio_sugerido' => 'required|decimal',
                'tipo_insumo'     => 'required|in_list[activo,material]',
                'cuenta_sap'      => 'permit_empty|max_length[100]',
                'status'          => 'required|in_list[activo,anulado]',
            ]);

            if (! $validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'id_renglon'         => $this->request->getPost('id_renglon'),
                'codigo_pacc'        => $this->request->getPost('codigo_pacc'),
                'nombre_insumo'      => $this->request->getPost('nombre_insumo'),
                'precio_sugerido'    => $this->request->getPost('precio_sugerido'),
                'tipo_insumo'        => $this->request->getPost('tipo_insumo'),
                'cuenta_sap'         => $this->request->getPost('cuenta_sap'),
                'status'             => $this->request->getPost('status') ?? 'activo',
                'id_usuario_creo'    => $idUsuario,
            ];

            $inserted = $this->insumosModel->insert($data);

            if ($inserted === false) {
                $this->session->setFlashdata('error', 'Error al crear el insumo.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Insumo creado exitosamente.');
            return redirect()->to('cat_insumos');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al guardar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function ver($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $insumo = $this->insumosModel->find($id);

            if (! $insumo) {
                throw new \Exception('Insumo no encontrado.');
            }

            $insumoConAud = $this->insumosModel->getInsumoConAuditoria($id);

            $data = [
                'insumo' => $insumoConAud,
            ];

            if ($this->request->isAJAX()) {
                return view('cat_insumos/show_modal', $data);
            }

            return view('cat_insumos/show', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('cat_insumos');
        }
    }

    public function editar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $insumo = $this->insumosModel->find($id);
            if (! $insumo) {
                throw new \Exception('Insumo no encontrado.');
            }

            $renglones = $this->renglonesModel->where('deleted_at', null)->findAll();

            $data = [
                'insumo'    => $insumo,
                'renglones' => $renglones,
            ];

            if ($this->request->isAJAX()) {
                return view('cat_insumos/edit_modal', $data);
            }

            return view('cat_insumos/edit', $data);
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->to('cat_insumos');
        }
    }

    public function actualizar($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $insumo = $this->insumosModel->find($id);
            if (! $insumo) {
                throw new \Exception('Insumo no encontrado.');
            }

            $validation = $this->validate([
                'id_renglon'      => 'required|integer',
                'codigo_pacc'     => 'required|max_length[100]|is_unique[cat_insumos.codigo_pacc,id,' . $id . ']',
                'nombre_insumo'   => 'required|max_length[255]',
                'precio_sugerido' => 'required|decimal',
                'tipo_insumo'     => 'required|in_list[activo,material]',
                'cuenta_sap'      => 'permit_empty|max_length[100]',
                'status'          => 'required|in_list[activo,anulado]',
            ]);

            if (! $validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');

            $data = [
                'id_renglon'            => $this->request->getPost('id_renglon'),
                'codigo_pacc'           => $this->request->getPost('codigo_pacc'),
                'nombre_insumo'         => $this->request->getPost('nombre_insumo'),
                'precio_sugerido'       => $this->request->getPost('precio_sugerido'),
                'tipo_insumo'           => $this->request->getPost('tipo_insumo'),
                'cuenta_sap'            => $this->request->getPost('cuenta_sap'),
                'status'                => $this->request->getPost('status'),
                'id_usuario_actualizo'  => $idUsuario,
            ];

            $updated = $this->insumosModel->update($id, $data);

            if ($updated === false) {
                $this->session->setFlashdata('error', 'Error al actualizar el insumo.');
                return redirect()->back()->withInput();
            }

            $this->session->setFlashdata('success', 'Insumo actualizado exitosamente.');
            return redirect()->to('cat_insumos');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', 'Error al actualizar: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        $loginCheck = $this->checkLogin();
        if ($loginCheck) return $loginCheck;

        try {
            $insumo = $this->insumosModel->find($id);
            if (! $insumo) {
                throw new \Exception('Insumo no encontrado.');
            }

            $idUsuario = session('user_id');

            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->insumosModel->update($id, $data);

            if ($deleted === false) {
                $this->session->setFlashdata('error', 'Error al eliminar el insumo.');
                return redirect()->back();
            }

            $this->session->setFlashdata('success', 'Insumo eliminado exitosamente.');
            return redirect()->to('cat_insumos');
        } catch (\Exception $e) {
            $this->session->setFlashdata('error', $e->getMessage());
            return redirect()->back();
        }
    }

    public function getById($id)
    {
        try {
            $insumo = $this->insumosModel->find($id);
            if (! $insumo) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Insumo no encontrado.']);
            }
            return $this->response->setStatusCode(200)->setJSON(['success' => true, 'data' => $insumo]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
