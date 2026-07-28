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

    public function recalcularSaldosPorGrupo(int $idPresupuestoDivision, ?int $idUsuario = null): void
    {
        $presupuesto = $this->find($idPresupuestoDivision);

        if (! $presupuesto) {
            return;
        }

        $this->recalcularGrupo((int) $presupuesto['id_ejercicio'], (int) $presupuesto['id_division'], $idUsuario);
    }

    public function recalcularGrupo(int $idEjercicio, int $idDivision, ?int $idUsuario = null): void
    {
        $presupuestos = $this->where('id_ejercicio', $idEjercicio)
            ->where('id_division', $idDivision)
            ->where('deleted_at', null)
            ->findAll();

        if (empty($presupuestos)) {
            return;
        }

        $db = db_connect();
        $saldoBasePorPresupuesto = [];

        foreach ($presupuestos as $presupuesto) {
            $asignadoEnRenglones = (float) $db->table('presupuesto_renglones')
                ->selectSum('monto_asignado')
                ->where('id_presupuesto_division', $presupuesto['id'])
                ->where('deleted_at', null)
                ->get()
                ->getRow()
                ->monto_asignado ?? 0;

            $saldoBasePorPresupuesto[(int) $presupuesto['id']] = (float) $presupuesto['monto_asignado'] - $asignadoEnRenglones;
        }

        $saldoGrupo = array_sum($saldoBasePorPresupuesto);

        foreach ($presupuestos as $presupuesto) {
            $data = ['saldo_actual' => $saldoGrupo];

            if ($idUsuario !== null) {
                $data['id_usuario_actualizo'] = $idUsuario;
            }

            $this->update((int) $presupuesto['id'], $data);
        }
    }
}
