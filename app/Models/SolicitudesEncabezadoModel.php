<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudesEncabezadoModel extends Model
{
    protected $table            = 'solicitudes_encabezado';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_division',
        'id_etapa',
        'folio_fisico',
        'monto_estimado_total',
        'pdf_firmado_path',
        'status',
        'id_usuario_solicita',
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

    public function getSolicitudesConRelaciones(string $searchTerm = '')
    {
        $builder = $this->where('solicitudes_encabezado.deleted_at', null);

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('solicitudes_encabezado.folio_fisico', $searchTerm)
                ->orLike('cat_divisiones.nombre_division', $searchTerm)
                ->orLike('cat_etapas.nombre_etapa', $searchTerm)
                ->orLike('usuarios.alias', $searchTerm)
                ->groupEnd();
        }

        return $builder
            ->select('solicitudes_encabezado.*, COALESCE(cat_divisiones.nombre_division, "Sin división") as nombre_division, COALESCE(cat_etapas.nombre_etapa, "Sin etapa") as nombre_etapa, COALESCE(usuarios.alias, "Sin usuario") as nombre_usuario')
            ->join('cat_divisiones', 'solicitudes_encabezado.id_division = cat_divisiones.id', 'left')
            ->join('cat_etapas', 'solicitudes_encabezado.id_etapa = cat_etapas.id', 'left')
            ->join('usuarios', 'solicitudes_encabezado.id_usuario_solicita = usuarios.id', 'left')
            ->orderBy('solicitudes_encabezado.id', 'DESC')
            ->paginate(10);
    }
}
