<?php

namespace App\Models;

use CodeIgniter\Model;

class BodegaModel extends Model
{
    protected $table            = 'bodegas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $allowedFields    = [
        'nombre_bodega',
        'ubicacion',
        'status',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'deleted_at',
    ];

    // Validación
    protected $validationRules = [
        'nombre_bodega' => 'required|string|max_length[255]',
        'ubicacion'     => 'required|string|max_length[255]',
        'status'        => 'required|in_list[activo,inactivo]',
    ];

    protected $validationMessages = [
        'nombre_bodega' => [
            'required'   => 'El nombre de la bodega es obligatorio.',
            'string'     => 'El nombre debe ser texto.',
            'max_length' => 'El nombre no puede exceder 255 caracteres.',
        ],
        'ubicacion' => [
            'required'   => 'La ubicación es obligatoria.',
            'string'     => 'La ubicación debe ser texto.',
            'max_length' => 'La ubicación no puede exceder 255 caracteres.',
        ],
        'status' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'El estado debe ser: activo o inactivo.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Obtener todas las bodegas activas (sin soft deletes)
     */
    public function getBodegasActivas()
    {
        return $this->where('deleted_at', null)->findAll();
    }

    /**
     * Obtener bodega por ID con datos de auditoría
     */
    public function getBodegaConAuditoria($id)
    {
        return $this->select('b.*, 
                               u1.alias as usuario_creo,
                               u2.alias as usuario_actualizo,
                               u3.alias as usuario_elimino')
                    ->from('bodegas b')
                    ->join('usuarios u1', 'b.id_usuario_creo = u1.id', 'left')
                    ->join('usuarios u2', 'b.id_usuario_actualizo = u2.id', 'left')
                    ->join('usuarios u3', 'b.id_usuario_elimino = u3.id', 'left')
                    ->where('b.id', $id)
                    ->where('b.deleted_at', null)
                    ->first();
    }

    /**
     * Listar bodegas con paginación
     */
    public function getBodegasConPaginacion($perPage = 10, $page = 1)
    {
        return [
            'data'        => $this->paginate($perPage),
            'pager'       => $this->pager,
            'currentPage' => $page,
        ];
    }

    /**
     * Buscar bodegas por nombre o ubicación
     */
    public function buscar($termino)
    {
        return $this->where('deleted_at', null)
                    ->groupStart()
                    ->like('nombre_bodega', $termino)
                    ->orLike('ubicacion', $termino)
                    ->groupEnd()
                    ->findAll();
    }
}
