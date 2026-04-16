<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolPermisos extends Migration
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
            'id_rol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_permiso' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_rol');
        $this->forge->addKey('id_permiso');
        $this->forge->addUniqueKey(['id_rol', 'id_permiso'], 'uq_rol_permisos_rol_permiso');
        $this->forge->addForeignKey('id_rol', 'cat_roles', 'id', 'CASCADE', 'RESTRICT', 'fk_rol_permisos_id_rol');
        $this->forge->addForeignKey('id_permiso', 'permisos', 'id', 'CASCADE', 'RESTRICT', 'fk_rol_permisos_id_permiso');
        $this->forge->createTable('rol_permisos', true);
    }

    public function down()
    {
        $this->forge->dropTable('rol_permisos', true);
    }
}
