<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBodegasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
                'unsigned'       => true,
            ],
            'nombre_bodega' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'ubicacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
                'null'       => false,
            ],
            'id_usuario_creo' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
            ],
            'id_usuario_actualizo' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'id_usuario_elimino' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
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
        $this->forge->addKey('id_usuario_creo');
        $this->forge->addKey('deleted_at');
        $this->forge->createTable('bodegas');
    }

    public function down()
    {
        $this->forge->dropTable('bodegas');
    }
}
