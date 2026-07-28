<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratosEmpleadosModel extends Model
{
    protected $table            = 'contratos_empleados';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero_contrato',
        'expediente',
        'codigo_contrato',
        'fecha_aceptacion_contrato',
        'fecha_inicio',
        'fecha_fin',
        'monto_contrato',
        'monto_texto',
        'cantidad_pagos',
        'estado_contrato',
        'puente_financiamiento',
        'renglon',
        'codigo_renglon',
        'pdf_contrato_path',
        'status',
        'id_empleado',
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
}
