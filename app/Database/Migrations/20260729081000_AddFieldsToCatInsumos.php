<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCatInsumos extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (! $db->tableExists('cat_insumos')) {
            return;
        }

        $hasPrecioSugerido = $db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'precio_sugerido'")->getNumRows() > 0;
        $hasPrecioUnitario = $db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'precio_unitario_pacc'")->getNumRows() > 0;

        if ($hasPrecioSugerido && ! $hasPrecioUnitario) {
            $db->query("ALTER TABLE `cat_insumos` CHANGE `precio_sugerido` `precio_unitario_pacc` DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'caracteristicas'")->getNumRows() === 0) {
            $db->query("ALTER TABLE `cat_insumos` ADD COLUMN `caracteristicas` VARCHAR(2000) NULL AFTER `precio_unitario_pacc`");
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'presentacion'")->getNumRows() === 0) {
            $db->query("ALTER TABLE `cat_insumos` ADD COLUMN `presentacion` VARCHAR(255) NULL AFTER `caracteristicas`");
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'unidad_medida'")->getNumRows() === 0) {
            $db->query("ALTER TABLE `cat_insumos` ADD COLUMN `unidad_medida` VARCHAR(255) NULL AFTER `presentacion`");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if (! $db->tableExists('cat_insumos')) {
            return;
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'unidad_medida'")->getNumRows() > 0) {
            $db->query("ALTER TABLE `cat_insumos` DROP COLUMN `unidad_medida`");
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'presentacion'")->getNumRows() > 0) {
            $db->query("ALTER TABLE `cat_insumos` DROP COLUMN `presentacion`");
        }

        if ($db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'caracteristicas'")->getNumRows() > 0) {
            $db->query("ALTER TABLE `cat_insumos` DROP COLUMN `caracteristicas`");
        }

        $hasPrecioUnitario = $db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'precio_unitario_pacc'")->getNumRows() > 0;
        $hasPrecioSugerido = $db->query("SHOW COLUMNS FROM `cat_insumos` LIKE 'precio_sugerido'")->getNumRows() > 0;

        if ($hasPrecioUnitario && ! $hasPrecioSugerido) {
            $db->query("ALTER TABLE `cat_insumos` CHANGE `precio_unitario_pacc` `precio_sugerido` DECIMAL(15,2) NOT NULL DEFAULT '0.00'");
        }
    }
}
