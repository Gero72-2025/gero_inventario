<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBodegasPermissions extends Migration
{
    public function up()
    {
        // Insertar permisos para Bodegas
        $permisos = [
            [
                'nombre_permiso' => 'Ver Bodegas',
                'controlador'    => 'Bodegas',
                'accion'         => 'index',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Crear Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'create',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Guardar Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'store',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Ver Detalle Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'show',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Editar Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'edit',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Actualizar Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'actualizar',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Eliminar Bodega',
                'controlador'    => 'Bodegas',
                'accion'         => 'delete',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($permisos as $permiso) {
            // Se agrega ignore(true) para omitir si el permiso ya existe
            $this->db->table('permisos')->ignore(true)->insert($permiso);
        }

        // Asignar permisos al rol Administrador (asumiendo id=1)
        $permisoIds = $this->db->table('permisos')
            ->select('id')
            ->where('controlador', 'Bodegas')
            ->get()
            ->getResultArray();

        foreach ($permisoIds as $permiso) {
            // Se agrega ignore(true) para omitir si la relación rol-permiso ya existe
            $this->db->table('rol_permisos')->ignore(true)->insert([
                'id_rol'      => 1, // Administrador
                'id_permiso'  => $permiso['id'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        // Eliminar permisos de Bodegas
        $this->db->table('rol_permisos')
            ->join('permisos', 'rol_permisos.id_permiso = permisos.id')
            ->where('permisos.controlador', 'Bodegas')
            ->delete();

        $this->db->table('permisos')
            ->where('controlador', 'Bodegas')
            ->delete();
    }
}
