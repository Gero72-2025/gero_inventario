<?php

namespace App\Models;

use CodeIgniter\Model;

class CatInsumosModel extends Model
{
    protected $table            = 'cat_insumos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields    = [
        'id_renglon',
        'codigo_pacc',
        'nombre_insumo',
        'precio_unitario_pacc',
        'caracteristicas',
        'presentacion',
        'unidad_medida',
        'tipo_insumo',
        'cuenta_sap',
        'status',
        'id_usuario_creo',
        'id_usuario_actualizo',
        'id_usuario_elimino',
        'deleted_at',
    ];

    /**
     * Obtener insumos con datos de renglón y paginados (10 por página)
     */
    public function getInsumosConRenglones(string $searchTerm = '')
    {
        $this->select('cat_insumos.*, r.codigo_renglon as renglon_codigo, r.descripcion as renglon_descripcion')
             ->join('renglones r', 'cat_insumos.id_renglon = r.id', 'left')
             ->where('cat_insumos.deleted_at', null);

        if ($searchTerm !== '') {
            $this->groupStart()
                 ->like('codigo_pacc', $searchTerm)
                 ->orLike('nombre_insumo', $searchTerm)
                 ->orLike('r.codigo_renglon', $searchTerm)
                 ->orLike('r.descripcion', $searchTerm)
                 ->groupEnd();
        }

        return $this->orderBy('cat_insumos.id', 'DESC')->paginate(10);
    }

    public function getInsumoConAuditoria($id)
    {
        return $this->select('ci.*, 
                              r.codigo_renglon as renglon_codigo, r.descripcion as renglon_descripcion,
                              u1.alias as usuario_creo, u2.alias as usuario_actualizo, u3.alias as usuario_elimino')
                    ->from('cat_insumos ci')
                    ->join('renglones r', 'ci.id_renglon = r.id', 'left')
                    ->join('usuarios u1', 'ci.id_usuario_creo = u1.id', 'left')
                    ->join('usuarios u2', 'ci.id_usuario_actualizo = u2.id', 'left')
                    ->join('usuarios u3', 'ci.id_usuario_elimino = u3.id', 'left')
                    ->where('ci.id', $id)
                    ->where('ci.deleted_at', null)
                    ->first();
    }
}
