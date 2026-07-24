<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudesHistorialEtapasModel extends Model
{
    protected $table            = 'solicitudes_historial_etapas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_solicitud',
        'id_etapa_anterior',
        'id_etapa_nueva',
        'comentario_transicion',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    public function getHistorialConRelaciones(string $searchTerm = '')
    {
        $builder = $this->where('solicitudes_historial_etapas.deleted_at', null);

        if ($searchTerm !== '') {
            $builder->groupStart()
                ->like('solicitudes_historial_etapas.comentario_transicion', $searchTerm)
                ->orLike('solicitudes_encabezado.folio_fisico', $searchTerm)
                ->orLike('etapa_anterior.nombre_etapa', $searchTerm)
                ->orLike('etapa_nueva.nombre_etapa', $searchTerm)
                ->groupEnd();
        }

        return $builder
            ->select('solicitudes_historial_etapas.*, solicitudes_encabezado.folio_fisico as folio_solicitud, COALESCE(etapa_anterior.nombre_etapa, "Sin etapa anterior") as etapa_anterior, COALESCE(etapa_nueva.nombre_etapa, "Sin etapa nueva") as etapa_nueva')
            ->join('solicitudes_encabezado', 'solicitudes_historial_etapas.id_solicitud = solicitudes_encabezado.id', 'left')
            ->join('cat_etapas as etapa_anterior', 'solicitudes_historial_etapas.id_etapa_anterior = etapa_anterior.id', 'left')
            ->join('cat_etapas as etapa_nueva', 'solicitudes_historial_etapas.id_etapa_nueva = etapa_nueva.id', 'left')
            ->orderBy('solicitudes_historial_etapas.id', 'DESC')
            ->paginate(10);
    }

    public function getHistorialById(int $id)
    {
        return $this->select('solicitudes_historial_etapas.*, solicitudes_encabezado.folio_fisico as folio_solicitud, COALESCE(etapa_anterior.nombre_etapa, "Sin etapa anterior") as etapa_anterior, COALESCE(etapa_nueva.nombre_etapa, "Sin etapa nueva") as etapa_nueva')
            ->join('solicitudes_encabezado', 'solicitudes_historial_etapas.id_solicitud = solicitudes_encabezado.id', 'left')
            ->join('cat_etapas as etapa_anterior', 'solicitudes_historial_etapas.id_etapa_anterior = etapa_anterior.id', 'left')
            ->join('cat_etapas as etapa_nueva', 'solicitudes_historial_etapas.id_etapa_nueva = etapa_nueva.id', 'left')
            ->where('solicitudes_historial_etapas.id', $id)
            ->where('solicitudes_historial_etapas.deleted_at', null)
            ->first();
    }
}
