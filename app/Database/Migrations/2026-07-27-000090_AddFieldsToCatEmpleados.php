<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCatEmpleados extends Migration
{
    public function up(): void
    {
        $fields = [
            'nit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'dpi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'numero_telefonico' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => true,
            ],
            'correo_electronico' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'es_jefe' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'id_usuario_asignado' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('cat_empleados', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cat_empleados', [
            'nit',
            'dpi',
            'numero_telefonico',
            'correo_electronico',
            'es_jefe',
            'id_usuario_asignado',
        ]);
    }
}
