<?php

namespace App\Models;

use CodeIgniter\Model;

class PresupuestosDivisionModel extends Model
{
    protected $table            = 'presupuestos_division';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_ejercicio',
        'id_division',
        'monto_asignado',
        'saldo_actual',
        'status',
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

    public function getPresupuestosDivisionesConRelaciones(string $searchTerm = '')
    {
        $this->select('presupuestos_division.id, presupuestos_division.id_ejercicio, presupuestos_division.id_division, presupuestos_division.monto_asignado, presupuestos_division.saldo_actual, presupuestos_division.status, presupuestos_division.created_at, presupuestos_division.updated_at, COALESCE(cef.anio, 0) as anio, COALESCE(cd.nombre_division, "Sin división") as nombre_division')
            ->join('cat_ejercicios_fiscales cef', 'presupuestos_division.id_ejercicio = cef.id', 'left')
            ->join('cat_divisiones cd', 'presupuestos_division.id_division = cd.id', 'left')
            ->where('presupuestos_division.deleted_at', null);

        if ($searchTerm !== '') {
            $this->groupStart()
                ->like('cef.anio', $searchTerm)
                ->orLike('cd.nombre_division', $searchTerm)
                ->groupEnd();
        }

        return $this->orderBy('presupuestos_division.id', 'DESC')->paginate(10);
    }
}
