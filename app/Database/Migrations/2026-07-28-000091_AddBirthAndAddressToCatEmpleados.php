<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBirthAndAddressToCatEmpleados extends Migration
{
    public function up(): void
    {
        $fields = [
            'fecha_nacimiento' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'direccion' => [
                'type'       => 'VARCHAR',
                'constraint' => 250,
                'null'       => true,
            ],
        ];

        $this->forge->addColumn('cat_empleados', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('cat_empleados', [
            'fecha_nacimiento',
            'direccion',
        ]);
    }
}
