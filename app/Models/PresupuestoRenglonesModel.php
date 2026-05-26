<?php

namespace App\Models;

use CodeIgniter\Model;

class PresupuestoRenglonesModel extends Model
{
    protected $table            = 'presupuesto_renglones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_presupuesto_division',
        'id_renglon',
        'monto_asignado',
        'saldo_actual',
        'status',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'created_at',
        'updated_at',
        'deleted_at',
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

    public function getPresupuestoRenglonesConRelaciones(string $searchTerm = '')
    {
        $this->distinct()
            ->select('pr.id, pr.id_presupuesto_division, pr.id_renglon, pr.monto_asignado, pr.saldo_actual, pr.status, pr.created_at, pr.updated_at, COALESCE(r.codigo_renglon, "Sin código") as codigo_renglon, COALESCE(r.descripcion, "Sin descripción") as descripcion, COALESCE(cd.nombre_division, "Sin división") as nombre_division, COALESCE(cef.anio, 0) as anio, COALESCE(pd.monto_asignado, 0) as presupuesto_monto, COALESCE(pd.saldo_actual, 0) as presupuesto_saldo')
            ->from('presupuesto_renglones pr')
            ->join('renglones r', 'pr.id_renglon = r.id', 'left')
            ->join('presupuestos_division pd', 'pr.id_presupuesto_division = pd.id', 'left')
            ->join('cat_divisiones cd', 'pd.id_division = cd.id', 'left')
            ->join('cat_ejercicios_fiscales cef', 'pd.id_ejercicio = cef.id', 'left')
            ->where('pr.deleted_at', null);

        if ($searchTerm !== '') {
            $this->groupStart()
                ->like('r.codigo_renglon', $searchTerm)
                ->orLike('r.descripcion', $searchTerm)
                ->orLike('cd.nombre_division', $searchTerm)
                ->groupEnd();
        }

        return $this->orderBy('pr.id', 'DESC')->paginate(10);
    }

    public function obtenerDisponiblePorPresupuesto($idPresupuestoDivision)
    {
        $presupuesto = new PresupuestosDivisionModel();
        $pd = $presupuesto->find($idPresupuestoDivision);

        if (!$pd) {
            return 0;
        }

        $asignado = $this->selectSum('monto_asignado')
            ->where('id_presupuesto_division', $idPresupuestoDivision)
            ->where('deleted_at', null)
            ->get()
            ->getRow();

        $totalAsignado = $asignado->monto_asignado ?? 0;
        return (float)$pd['monto_asignado'] - (float)$totalAsignado;
    }
}
