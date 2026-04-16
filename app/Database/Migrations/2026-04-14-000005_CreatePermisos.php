<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePermisos extends Migration
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
            'nombre_permiso' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'controlador' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'accion' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addUniqueKey('nombre_permiso', 'uq_permisos_nombre_permiso');
        $this->forge->addKey('controlador');
        $this->forge->addKey('accion');
        $this->forge->createTable('permisos', true);
    }

    public function down()
    {
        $this->forge->dropTable('permisos', true);
    }
}
