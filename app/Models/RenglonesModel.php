<?php

namespace App\Models;

use CodeIgniter\Model;

class RenglonesModel extends Model
{
    protected $table            = 'renglones';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $allowedFields    = [
        'codigo_renglon',
        'descripcion',
        'status',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'deleted_at',
    ];


    /* SECCION COMENTADA POR POSIBLE FALLOS CON LA ACTUALIZACION DE RENGLONES
    // Validación
    protected $validationRules = [
        'codigo_renglon' => 'required|max_length[100]|is_unique[renglones.codigo_renglon]',
        'descripcion'    => 'required',
        'status'         => 'required|in_list[activo,inactivo]',
    ];

    protected $validationMessages = [
        'codigo_renglon' => [
            'required'   => 'El código de renglón es obligatorio.',
            'max_length' => 'El código no puede exceder 100 caracteres.',
            'is_unique'  => 'Este código de renglón ya existe.',
        ],
        'descripcion' => [
            'required' => 'La descripción es obligatoria.',
        ],
        'status' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'El estado debe ser: activo o inactivo.',
        ],
    ];

    protected $skipValidation = false;

    */

    /**
     * Obtener todos los renglones activos (sin soft deletes)
     */
    public function getRenglonesActivos()
    {
        return $this->where('deleted_at', null)->findAll();
    }

    /**
     * Obtener renglón por ID con datos de auditoría
     */
    public function getRenglonesConAuditoria($id)
    {
        return $this->select('r.*, 
                               u1.alias as usuario_creo,
                               u2.alias as usuario_actualizo,
                               u3.alias as usuario_elimino')
                    ->from('renglones r')
                    ->join('usuarios u1', 'r.id_usuario_creo = u1.id', 'left')
                    ->join('usuarios u2', 'r.id_usuario_actualizo = u2.id', 'left')
                    ->join('usuarios u3', 'r.id_usuario_elimino = u3.id', 'left')
                    ->where('r.id', $id)
                    ->where('r.deleted_at', null)
                    ->first();
    }

    /**
     * Buscar renglones por código o descripción
     */
    public function buscar($termino)
    {
        return $this->where('deleted_at', null)
                    ->groupStart()
                    ->like('codigo_renglon', $termino)
                    ->orLike('descripcion', $termino)
                    ->groupEnd()
                    ->findAll();
    }
}
