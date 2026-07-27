<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatInsumos extends Migration
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
            'id_renglon' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'codigo_pacc' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'nombre_insumo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'precio_sugerido' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'tipo_insumo' => [
                'type'       => "ENUM('activo','material')",
                'null'       => false,
                'default'    => 'material',
            ],
            'cuenta_sap' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'status' => [
                'type'       => "ENUM('activo','anulado')",
                'null'       => false,
                'default'    => 'activo',
            ],

            // Auditoría
            'id_usuario_creo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_usuario_actualizo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_usuario_elimino' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addForeignKey('id_renglon', 'renglones', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cat_insumos', true);
    }

    public function down()
    {
        $this->forge->dropTable('cat_insumos', true);
    }
}
