<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNombreContactoToCatProveedores extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();

        if (! $db->tableExists('cat_proveedores')) {
            return;
        }

        if ($db->query("SHOW COLUMNS FROM `cat_proveedores` LIKE 'nombre_contacto'")->getNumRows() === 0) {
            $db->query("ALTER TABLE `cat_proveedores` ADD COLUMN `nombre_contacto` VARCHAR(255) NULL AFTER `nombre_comercial`");
        }
    }

    public function down(): void
    {
        $db = \Config\Database::connect();

        if (! $db->tableExists('cat_proveedores')) {
            return;
        }

        if ($db->query("SHOW COLUMNS FROM `cat_proveedores` LIKE 'nombre_contacto'")->getNumRows() > 0) {
            $db->query("ALTER TABLE `cat_proveedores` DROP COLUMN `nombre_contacto`");
        }
    }
}
