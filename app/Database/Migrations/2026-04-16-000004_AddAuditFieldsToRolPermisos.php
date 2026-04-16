<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuditFieldsToRolPermisos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('rol_permisos', [
            'id_usuario_creo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_usuario',
            ],
            'id_usuario_actualizo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_usuario_creo',
            ],
            'id_usuario_elimino' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_usuario_actualizo',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('rol_permisos', [
            'id_usuario_creo',
            'id_usuario_actualizo',
            'id_usuario_elimino',
        ]);
    }
}
