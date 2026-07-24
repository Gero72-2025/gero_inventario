<?php

namespace App\Controllers;

use App\Models\FacturasLiquidacionModel;
use App\Models\ProveedoresModel;
use App\Models\SolicitudesEncabezadoModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
use Throwable;

class FacturasLiquidacion extends BaseController
{
    protected FacturasLiquidacionModel $facturasLiquidacionModel;
    protected SolicitudesEncabezadoModel $solicitudesEncabezadoModel;
    protected ProveedoresModel $proveedoresModel;

    public function __construct()
    {
        $this->facturasLiquidacionModel = new FacturasLiquidacionModel();
        $this->solicitudesEncabezadoModel = new SolicitudesEncabezadoModel();
        $this->proveedoresModel = new ProveedoresModel();
    }

    public function index()
    {
        try {
            $searchTerm = trim((string) $this->request->getGet('q'));

            $facturas = $this->facturasLiquidacionModel->getFacturasConRelaciones($searchTerm);

            return view('facturas-liquidacion/index', [
                'facturas'   => $facturas,
                'pager'      => $this->facturasLiquidacionModel->pager,
                'searchTerm' => $searchTerm,
            ]);
        } catch (Throwable $e) {
            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('error', 'No fue posible cargar el listado de facturas.');
        }
    }

    public function create()
    {
        return view('facturas-liquidacion/create', [
            'solicitudes' => $this->solicitudesEncabezadoModel
                ->select('id, folio_fisico')
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->findAll(),
            'proveedores' => $this->proveedoresModel
                ->select('id, nombre_comercial')
                ->where('deleted_at', null)
                ->orderBy('nombre_comercial', 'ASC')
                ->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'id_solicitud' => 'required|is_natural_no_zero',
            'id_proveedor' => 'required|is_natural_no_zero',
            'serie_factura' => 'required|max_length[100]',
            'numero_factura' => 'required|max_length[100]',
            'monto_real_pagado' => 'required|decimal',
            'monto_vuelto_devuelto' => 'permit_empty|decimal',
            'fecha_pago' => 'required|valid_date[Y-m-d]',
            'status' => 'required|in_list[pagado,anulado]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->extractRequestData();

        try {
            if (! $this->facturasLiquidacionModel->insert($data)) {
                throw new RuntimeException('No fue posible insertar la factura.');
            }

            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('success', 'Factura de liquidación creada correctamente.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()
                ->with('error', 'No fue posible guardar la factura de liquidación.');
        }
    }

    public function edit(int $id)
    {
        try {
            $factura = $this->facturasLiquidacionModel->find($id);

            if (! $factura) {
                throw PageNotFoundException::forPageNotFound('La factura no existe.');
            }

            return view('facturas-liquidacion/edit', [
                'factura' => $factura,
                'solicitudes' => $this->solicitudesEncabezadoModel
                    ->select('id, folio_fisico')
                    ->where('deleted_at', null)
                    ->orderBy('id', 'DESC')
                    ->findAll(),
                'proveedores' => $this->proveedoresModel
                    ->select('id, nombre_comercial')
                    ->where('deleted_at', null)
                    ->orderBy('nombre_comercial', 'ASC')
                    ->findAll(),
            ]);
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('error', 'No fue posible cargar la factura de liquidación.');
        }
    }

    public function update(int $id)
    {
        try {
            $factura = $this->facturasLiquidacionModel->find($id);

            if (! $factura) {
                throw PageNotFoundException::forPageNotFound('La factura no existe.');
            }

            $rules = [
                'id_solicitud' => 'required|is_natural_no_zero',
                'id_proveedor' => 'required|is_natural_no_zero',
                'serie_factura' => 'required|max_length[100]',
                'numero_factura' => 'required|max_length[100]',
                'monto_real_pagado' => 'required|decimal',
                'monto_vuelto_devuelto' => 'permit_empty|decimal',
                'fecha_pago' => 'required|valid_date[Y-m-d]',
                'status' => 'required|in_list[pagado,anulado]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $data = $this->extractRequestData();
            unset($data['id_usuario_creo']);

            if (! $this->facturasLiquidacionModel->update($id, $data)) {
                throw new RuntimeException('No fue posible actualizar la factura.');
            }

            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('success', 'Factura de liquidación actualizada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->back()->withInput()
                ->with('error', 'No fue posible actualizar la factura de liquidación.');
        }
    }

    public function delete(int $id)
    {
        try {
            $factura = $this->facturasLiquidacionModel->find($id);

            if (! $factura) {
                throw PageNotFoundException::forPageNotFound('La factura no existe.');
            }

            $idUsuario = (int) (session('user_id') ?? 0);
            $idUsuario = $idUsuario > 0 ? $idUsuario : null;

            $this->facturasLiquidacionModel->update($id, [
                'id_usuario_elimino' => $idUsuario,
            ]);
            $this->facturasLiquidacionModel->delete($id);

            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('success', 'Factura de liquidación eliminada correctamente.');
        } catch (Throwable $e) {
            if ($e instanceof PageNotFoundException) {
                throw $e;
            }

            return redirect()->to(base_url('facturas-liquidacion'))
                ->with('error', 'No fue posible eliminar la factura de liquidación.');
        }
    }

    private function extractRequestData(): array
    {
        $idUsuario = (int) (session('user_id') ?? 0);
        $idUsuario = $idUsuario > 0 ? $idUsuario : null;

        return [
            'id_solicitud' => (int) $this->request->getPost('id_solicitud'),
            'serie_factura' => trim((string) $this->request->getPost('serie_factura')),
            'numero_factura' => trim((string) $this->request->getPost('numero_factura')),
            'monto_real_pagado' => $this->request->getPost('monto_real_pagado') ?? '0.00',
            'monto_vuelto_devuelto' => $this->request->getPost('monto_vuelto_devuelto') ?? '0.00',
            'fecha_pago' => $this->request->getPost('fecha_pago'),
            'status' => $this->request->getPost('status') ?? 'pagado',
            'id_proveedor' => (int) $this->request->getPost('id_proveedor'),
            'id_usuario_creo' => $idUsuario,
            'id_usuario_actualizo' => $idUsuario,
        ];
    }
}
