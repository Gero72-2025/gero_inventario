<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatProveedores extends Migration
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
            'nit_proveedor' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'unique'     => true,
            ],
            'nombre_legal' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'nombre_comercial' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'direccion_fiscal' => [
                'type'   => 'TEXT',
                'null'   => false,
            ],
            'telefono_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'email_contacto' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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
        $this->forge->createTable('cat_proveedores', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('cat_proveedores', true);
    }
}
