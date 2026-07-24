<?php

namespace App\Controllers;

if (!class_exists('App\\Models\\SolicitudesHistorialEtapasModel', false)) {
    require_once APPPATH . 'Models/SolicitudesHistorialEtapasModel.php';
}

use App\Models\EtapaModel;
use App\Models\SolicitudesEncabezadoModel;
use App\Models\SolicitudesHistorialEtapasModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Throwable;

class SolicitudesHistorialEtapas extends BaseController
{
    protected $historialModel;
    protected $solicitudesModel;
    protected $etapasModel;

    public function __construct()
    {
        $this->historialModel = new SolicitudesHistorialEtapasModel();
        $this->solicitudesModel = new SolicitudesEncabezadoModel();
        $this->etapasModel = new EtapaModel();
    }

    public function index()
    {
        $searchTerm = trim((string) $this->request->getGet('q'));
        $registros = $this->historialModel->getHistorialConRelaciones($searchTerm);

        return view('solicitudes-historial-etapas/index', [
            'registros'   => $registros,
            'pager'       => $this->historialModel->pager,
            'searchTerm'  => $searchTerm,
            'title'       => 'Historial de etapas',
            'heading'     => 'Historial de etapas',
        ]);
    }

    public function create()
    {
        $solicitudes = $this->solicitudesModel->where('deleted_at', null)->findAll();
        $etapas = $this->etapasModel->where('deleted_at', null)->findAll();

        return view('solicitudes-historial-etapas/create', [
            'solicitudes' => $solicitudes,
            'etapas'      => $etapas,
            'title'       => 'Nuevo historial de etapa',
            'heading'     => 'Crear nuevo registro',
            'submitLabel' => 'Crear',
        ]);
    }

    public function store()
    {
        try {
            $validation = $this->validate([
                'id_solicitud'         => 'required|integer|greater_than[0]',
                'id_etapa_anterior'    => 'required|integer|greater_than[0]',
                'id_etapa_nueva'       => 'required|integer|greater_than[0]',
                'comentario_transicion' => 'permit_empty|string|max_length[65535]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');
            $data = [
                'id_solicitud'         => (int) $this->request->getPost('id_solicitud'),
                'id_etapa_anterior'   => (int) $this->request->getPost('id_etapa_anterior'),
                'id_etapa_nueva'      => (int) $this->request->getPost('id_etapa_nueva'),
                'comentario_transicion' => trim((string) $this->request->getPost('comentario_transicion')),
                'id_usuario_creo'      => $idUsuario,
            ];

            $inserted = $this->historialModel->insert($data);
            if ($inserted === false) {
                return redirect()->back()->withInput()->with('error', 'Error al crear el registro.');
            }

            return redirect()->to('solicitudes-historial-etapas')->with('success', 'Registro guardado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function ver($id)
    {
        try {
            $registro = $this->historialModel->find($id);
            if (!$registro) {
                throw PageNotFoundException::forPageNotFound();
            }

            $registro = $this->historialModel->getHistorialById($id);

            return view('solicitudes-historial-etapas/show', [
                'registro' => $registro,
                'title'    => 'Detalle del historial',
                'heading'  => 'Detalle del historial',
            ]);
        } catch (Throwable $e) {
            return redirect()->to('solicitudes-historial-etapas')->with('error', 'Registro no encontrado.');
        }
    }

    public function editar($id)
    {
        try {
            $registro = $this->historialModel->find($id);
            if (!$registro) {
                throw PageNotFoundException::forPageNotFound();
            }

            $solicitudes = $this->solicitudesModel->where('deleted_at', null)->findAll();
            $etapas = $this->etapasModel->where('deleted_at', null)->findAll();

            return view('solicitudes-historial-etapas/edit', [
                'registro'     => $registro,
                'solicitudes'  => $solicitudes,
                'etapas'       => $etapas,
                'title'        => 'Editar historial',
                'heading'      => 'Editar historial',
                'submitLabel'  => 'Actualizar',
            ]);
        } catch (Throwable $e) {
            return redirect()->to('solicitudes-historial-etapas')->with('error', 'Registro no encontrado.');
        }
    }

    public function actualizar($id)
    {
        try {
            $registro = $this->historialModel->find($id);
            if (!$registro) {
                throw PageNotFoundException::forPageNotFound();
            }

            $validation = $this->validate([
                'id_solicitud'         => 'required|integer|greater_than[0]',
                'id_etapa_anterior'    => 'required|integer|greater_than[0]',
                'id_etapa_nueva'       => 'required|integer|greater_than[0]',
                'comentario_transicion' => 'permit_empty|string|max_length[65535]',
            ]);

            if (!$validation) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $idUsuario = session('user_id');
            $data = [
                'id_solicitud'         => (int) $this->request->getPost('id_solicitud'),
                'id_etapa_anterior'   => (int) $this->request->getPost('id_etapa_anterior'),
                'id_etapa_nueva'      => (int) $this->request->getPost('id_etapa_nueva'),
                'comentario_transicion' => trim((string) $this->request->getPost('comentario_transicion')),
                'id_usuario_actualizo' => $idUsuario,
            ];

            $updated = $this->historialModel->update($id, $data);
            if ($updated === false) {
                return redirect()->back()->withInput()->with('error', 'Error al actualizar el registro.');
            }

            return redirect()->to('solicitudes-historial-etapas')->with('success', 'Registro actualizado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $registro = $this->historialModel->find($id);
            if (!$registro) {
                throw PageNotFoundException::forPageNotFound();
            }

            $idUsuario = session('user_id');
            $data = [
                'id_usuario_elimino' => $idUsuario,
                'deleted_at'         => date('Y-m-d H:i:s'),
            ];

            $deleted = $this->historialModel->update($id, $data);
            if ($deleted === false) {
                return redirect()->back()->with('error', 'Error al eliminar el registro.');
            }

            return redirect()->to('solicitudes-historial-etapas')->with('success', 'Registro eliminado correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
