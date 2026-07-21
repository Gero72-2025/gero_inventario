<?php

namespace App\Models;

use CodeIgniter\Model;

class FacturasLiquidacionModel extends Model
{
    protected $table            = 'facturas_liquidacion';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_solicitud',
        'serie_factura',
        'numero_factura',
        'monto_real_pagado',
        'monto_vuelto_devuelto',
        'fecha_pago',
        'status',
        'id_proveedor',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    public function getFacturasConRelaciones(string $searchTerm = '')
    {
        $builder = $this->where('facturas_liquidacion.deleted_at', null);

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('facturas_liquidacion.serie_factura', $searchTerm)
                ->orLike('facturas_liquidacion.numero_factura', $searchTerm)
                ->orLike('solicitudes_encabezado.folio_fisico', $searchTerm)
                ->orLike('cat_proveedores.nombre_comercial', $searchTerm)
                ->groupEnd();
        }

        return $builder
            ->select('facturas_liquidacion.*, solicitudes_encabezado.folio_fisico as folio_solicitud, cat_proveedores.nombre_comercial as nombre_proveedor')
            ->join('solicitudes_encabezado', 'facturas_liquidacion.id_solicitud = solicitudes_encabezado.id', 'left')
            ->join('cat_proveedores', 'facturas_liquidacion.id_proveedor = cat_proveedores.id', 'left')
            ->orderBy('facturas_liquidacion.id', 'DESC')
            ->paginate(10);
    }
}
