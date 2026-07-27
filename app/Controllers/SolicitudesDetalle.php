<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SolicitudesDetalleModel;
use App\Models\CatInsumosModel;
use App\Models\SolicitudesEncabezadoModel;

class SolicitudesDetalle extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new SolicitudesDetalleModel();
    }

    public function index()
    {
        $search = $this->request->getGet('q');
        $data['solicitudes_detalle'] = $this->model->getWithRelations($search);
        $data['pager'] = $this->model->pager;
        $data['q'] = $search;
        return view('solicitudes_detalle/index', $data);
    }

    public function create()
    {
        $insumosModel = new CatInsumosModel();
        $solicitudesModel = new SolicitudesEncabezadoModel();

        $data['insumos'] = $insumosModel->where('deleted_at', null)->findAll();
        $data['solicitudes'] = $solicitudesModel->where('deleted_at', null)->findAll();

        return view('solicitudes_detalle/create', $data);
    }

    public function store()
    {
        $rules = [
            'id_solicitud' => 'required|integer',
            'id_insumo' => 'required|integer',
            'cantidad' => 'required|integer',
            'precio_unitario_solicitado' => 'required',
            'status' => 'required|in_list[activo,anulado]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $post = $this->request->getPost();
            $session = session();
            $userId = $session->get('id_usuario') ?? null;

            $save = [
                'id_solicitud' => $post['id_solicitud'],
                'id_insumo' => $post['id_insumo'],
                'cantidad' => $post['cantidad'],
                'precio_unitario_solicitado' => $post['precio_unitario_solicitado'],
                'status' => $post['status'],
                'id_usuario_creo' => $userId,
            ];

            $this->model->insert($save);

            return redirect()->to(base_url('solicitudes-detalle'))->with('success', 'Registro guardado');
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar el registro');
        }
    }

    public function edit($id = null)
    {
        $record = $this->model->find($id);
        if (empty($record) || $record['deleted_at'] !== null) {
            return redirect()->to(base_url('solicitudes-detalle'))->with('error', 'Registro no encontrado');
        }

        $insumosModel = new CatInsumosModel();
        $solicitudesModel = new SolicitudesEncabezadoModel();

        $data['insumos'] = $insumosModel->where('deleted_at', null)->findAll();
        $data['solicitudes'] = $solicitudesModel->where('deleted_at', null)->findAll();
        $data['record'] = $record;

        return view('solicitudes_detalle/edit', $data);
    }

    public function update($id = null)
    {
        $rules = [
            'id_solicitud' => 'required|integer',
            'id_insumo' => 'required|integer',
            'cantidad' => 'required|integer',
            'precio_unitario_solicitado' => 'required',
            'status' => 'required|in_list[activo,anulado]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $post = $this->request->getPost();
            $session = session();
            $userId = $session->get('id_usuario') ?? null;

            $save = [
                'id_solicitud' => $post['id_solicitud'],
                'id_insumo' => $post['id_insumo'],
                'cantidad' => $post['cantidad'],
                'precio_unitario_solicitado' => $post['precio_unitario_solicitado'],
                'status' => $post['status'],
                'id_usuario_actualizo' => $userId,
            ];

            $this->model->update($id, $save);

            return redirect()->to(base_url('solicitudes-detalle'))->with('success', 'Registro actualizado');
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el registro');
        }
    }

    public function delete($id = null)
    {
        try {
            $session = session();
            $userId = $session->get('id_usuario') ?? null;
            $this->model->update($id, [
                'deleted_at' => date('Y-m-d H:i:s'),
                'id_usuario_elimino' => $userId,
            ]);

            return redirect()->to(base_url('solicitudes-detalle'))->with('success', 'Registro eliminado');
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el registro');
        }
    }
}
