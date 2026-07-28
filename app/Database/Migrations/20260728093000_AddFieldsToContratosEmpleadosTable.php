<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToContratosEmpleadosTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'expediente' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'codigo_contrato' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'fecha_aceptacion_contrato' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'monto_texto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cantidad_pagos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'puente_financiamiento' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'renglon' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'codigo_renglon' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('contratos_empleados', $fields);
        $this->db->query(
            'ALTER TABLE `contratos_empleados` ADD CONSTRAINT `fk_contratos_empleados_renglon` FOREIGN KEY (`renglon`) REFERENCES `renglones`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE'
        );
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `contratos_empleados` DROP FOREIGN KEY `fk_contratos_empleados_renglon`');
        $this->forge->dropColumn('contratos_empleados', [
            'expediente',
            'codigo_contrato',
            'fecha_aceptacion_contrato',
            'monto_texto',
            'cantidad_pagos',
            'puente_financiamiento',
            'renglon',
            'codigo_renglon',
        ]);
    }
}
