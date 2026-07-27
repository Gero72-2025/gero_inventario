<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSolicitudesDetalle extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_solicitud' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_insumo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cantidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'precio_unitario_solicitado' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'status' => [
                'type'    => "ENUM('activo','anulado')",
                'null'    => false,
                'default' => 'activo',
            ],
            // Auditoría obligatoria
            'id_usuario_creo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_usuario_actualizo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_usuario_elimino' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_solicitud');
        $this->forge->addKey('id_insumo');

        // Crear tabla
        $this->forge->createTable('solicitudes_detalle', true);

        // Añadir llaves foráneas si existen las tablas referenciadas
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('solicitudes_detalle');

        // Agregar foreign key si la base de datos lo permite sin errores
        try {
            $this->db->query('ALTER TABLE `solicitudes_detalle` ADD CONSTRAINT `fk_solicitudes_detalle_solicitud` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes_encabezado` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION');
            $this->db->query('ALTER TABLE `solicitudes_detalle` ADD CONSTRAINT `fk_solicitudes_detalle_insumo` FOREIGN KEY (`id_insumo`) REFERENCES `cat_insumos` (`id`) ON DELETE RESTRICT ON UPDATE NO ACTION');
        } catch (\Throwable $e) {
            // Si no se puede agregar la FK (por ejemplo porque la tabla referenciada no existe aún), no interrumpir la migración
            log_message('error', 'No se pudo crear FK en solicitudes_detalle: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // Eliminar FK si existen
        try {
            $this->db->query('ALTER TABLE `solicitudes_detalle` DROP FOREIGN KEY `fk_solicitudes_detalle_solicitud`');
            $this->db->query('ALTER TABLE `solicitudes_detalle` DROP FOREIGN KEY `fk_solicitudes_detalle_insumo`');
        } catch (\Throwable $e) {
            // ignorar
        }

        $this->forge->dropTable('solicitudes_detalle', true);
    }
}
