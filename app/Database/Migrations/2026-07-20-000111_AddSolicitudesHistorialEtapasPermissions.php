<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSolicitudesHistorialEtapasPermissions extends Migration
{
    public function up(): void
    {
        $permisos = [
            [
                'nombre_permiso' => 'Ver Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'index',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Crear Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'create',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Guardar Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'store',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Ver Detalle Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'ver',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Editar Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'editar',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Actualizar Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'actualizar',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'nombre_permiso' => 'Eliminar Historial de Etapas',
                'controlador'    => 'SolicitudesHistorialEtapas',
                'accion'         => 'delete',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($permisos as $permiso) {
            $this->db->table('permisos')->ignore(true)->insert($permiso);
        }

        $permisoIds = $this->db->table('permisos')
            ->select('id')
            ->where('controlador', 'SolicitudesHistorialEtapas')
            ->get()
            ->getResultArray();

        foreach ($permisoIds as $permiso) {
            $this->db->table('rol_permisos')->ignore(true)->insert([
                'id_rol'     => 1,
                'id_permiso' => $permiso['id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down(): void
    {
        $this->db->table('rol_permisos')
            ->join('permisos', 'rol_permisos.id_permiso = permisos.id')
            ->where('permisos.controlador', 'SolicitudesHistorialEtapas')
            ->delete();

        $this->db->table('permisos')
            ->where('controlador', 'SolicitudesHistorialEtapas')
            ->delete();
    }
}
