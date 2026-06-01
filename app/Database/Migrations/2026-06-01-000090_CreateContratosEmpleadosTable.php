<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContratosEmpleadosTable extends Migration
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
            'numero_contrato' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'unique'     => true,
            ],
            'fecha_inicio' => [
                'type'   => 'DATE',
                'null'   => false,
            ],
            'fecha_fin' => [
                'type'   => 'DATE',
                'null'   => false,
            ],
            'monto_contrato' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => false,
            ],
            'estado_contrato' => [
                'type'       => 'ENUM',
                'constraint' => ['vigente', 'vencido', 'rescindido'],
                'default'    => 'vigente',
                'null'       => false,
            ],
            'pdf_contrato_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['activo', 'inactivo'],
                'default'    => 'activo',
                'null'       => false,
            ],
            'id_empleado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addForeignKey('id_empleado', 'cat_empleados', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('contratos_empleados', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('contratos_empleados', true);
    }
}
