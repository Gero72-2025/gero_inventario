<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresupuestosDivisionTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_ejercicio' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_division' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'monto_asignado' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0,
            ],
            'saldo_actual' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
                'null'       => false,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'id_usuario_creo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_usuario_actualizo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_usuario_elimino' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_ejercicio', 'cat_ejercicios_fiscales', 'id', '', 'RESTRICT');
        $this->forge->addForeignKey('id_division', 'cat_divisiones', 'id', '', 'RESTRICT');
        $this->forge->createTable('presupuestos_division', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('presupuestos_division', true);
    }
}
