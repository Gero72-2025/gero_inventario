<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatRoles extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre_rol' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nombre_rol');
        $this->forge->createTable('cat_roles');

        $now = date('Y-m-d H:i:s');
        $this->db->table('cat_roles')->insertBatch([
            [
                'nombre_rol'  => 'Administrador',
                'descripcion' => 'Acceso total a la plataforma.',
                'created_at'  => $now,
                'updated_at'  => $now,
                'deleted_at'  => null,
                'id_usuario'  => null,
            ],
            [
                'nombre_rol'  => 'Jefatura',
                'descripcion' => 'Gestion de equipos y aprobaciones de alto nivel.',
                'created_at'  => $now,
                'updated_at'  => $now,
                'deleted_at'  => null,
                'id_usuario'  => null,
            ],
            [
                'nombre_rol'  => 'Supervisor',
                'descripcion' => 'Supervision operativa y seguimiento de procesos.',
                'created_at'  => $now,
                'updated_at'  => $now,
                'deleted_at'  => null,
                'id_usuario'  => null,
            ],
            [
                'nombre_rol'  => 'Personal',
                'descripcion' => 'Operacion diaria con permisos base.',
                'created_at'  => $now,
                'updated_at'  => $now,
                'deleted_at'  => null,
                'id_usuario'  => null,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('cat_roles');
    }
}
