<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatConfiguraciones extends Migration
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
            'llave' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'valor_1' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_2' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_3' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_4' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_5' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_6' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_7' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_8' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_9' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'valor_10' => [
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
        $this->forge->addUniqueKey('llave');
        $this->forge->createTable('cat_configuraciones');
    }

    public function down()
    {
        $this->forge->dropTable('cat_configuraciones');
    }
}
