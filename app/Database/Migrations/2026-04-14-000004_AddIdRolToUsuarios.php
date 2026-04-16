<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdRolToUsuarios extends Migration
{
    public function up()
    {
        $this->forge->addColumn('usuarios', [
            'id_rol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'email',
            ],
        ]);

        $this->db->query('ALTER TABLE `usuarios` ADD CONSTRAINT `fk_usuarios_id_rol` FOREIGN KEY (`id_rol`) REFERENCES `cat_roles`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `usuarios` DROP FOREIGN KEY `fk_usuarios_id_rol`');
        $this->forge->dropColumn('usuarios', 'id_rol');
    }
}
