<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudesDetalleModel extends Model
{
    protected $table = 'solicitudes_detalle';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_solicitud',
        'id_insumo',
        'cantidad',
        'precio_unitario_solicitado',
        'status',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    protected $returnType = 'array';

    /**
     * Obtener registros con relación a insumo y aplicar búsqueda/paginación (10 por página)
     */
    public function getWithRelations(?string $search = null)
    {
        // Normalizar a cadena para evitar TypeError cuando se pasa null
        $search = $search ?? '';

        $this->select('solicitudes_detalle.*, cat_insumos.nombre_insumo AS insumo_nombre')
            ->join('cat_insumos', 'cat_insumos.id = solicitudes_detalle.id_insumo', 'left')
            ->where('solicitudes_detalle.deleted_at', null);

        if ($search !== '') {
            $this->groupStart()
                         ->like('cat_insumos.nombre_insumo', $search)
                 ->orLike('solicitudes_detalle.cantidad', $search)
                 ->orLike('solicitudes_detalle.precio_unitario_solicitado', $search)
                 ->groupEnd();
        }

        return $this->orderBy('solicitudes_detalle.id', 'DESC')->paginate(10);
    }
}
