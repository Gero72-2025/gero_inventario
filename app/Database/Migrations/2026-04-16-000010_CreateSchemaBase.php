<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración base unificada del proyecto.
 *
 * Consolida todas las migraciones anteriores en un único punto de partida.
 * Usa IF NOT EXISTS para que sea idempotente en instalaciones ya existentes.
 *
 * Tablas creadas:
 *   - cat_configuraciones
 *   - cat_roles            (incluye datos semilla de roles base)
 *   - usuarios
 *   - permisos
 *   - rol_permisos
 */
class CreateSchemaBase extends Migration
{
    // -------------------------------------------------------------------------
    // UP
    // -------------------------------------------------------------------------

    public function up(): void
    {
        $this->upCatConfiguraciones();
        $this->upCatRoles();
        $this->upUsuarios();
        $this->upPermisos();
        $this->upRolPermisos();
    }

    // -------------------------------------------------------------------------
    // DOWN
    // -------------------------------------------------------------------------

    public function down(): void
    {
        // Orden inverso respetando claves foráneas
        $this->forge->dropTable('rol_permisos', true);
        $this->forge->dropTable('usuarios', true);
        $this->forge->dropTable('permisos', true);
        $this->forge->dropTable('cat_roles', true);
        $this->forge->dropTable('cat_configuraciones', true);
    }

    // =========================================================================
    // Tablas
    // =========================================================================

    private function upCatConfiguraciones(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'llave' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'valor_1'  => ['type' => 'TEXT', 'null' => true],
            'valor_2'  => ['type' => 'TEXT', 'null' => true],
            'valor_3'  => ['type' => 'TEXT', 'null' => true],
            'valor_4'  => ['type' => 'TEXT', 'null' => true],
            'valor_5'  => ['type' => 'TEXT', 'null' => true],
            'valor_6'  => ['type' => 'TEXT', 'null' => true],
            'valor_7'  => ['type' => 'TEXT', 'null' => true],
            'valor_8'  => ['type' => 'TEXT', 'null' => true],
            'valor_9'  => ['type' => 'TEXT', 'null' => true],
            'valor_10' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
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
        $this->forge->addUniqueKey('llave');
        $this->forge->createTable('cat_configuraciones', true);
    }

    private function upCatRoles(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre_rol' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'descripcion' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
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
        $this->forge->addUniqueKey('nombre_rol');
        $this->forge->createTable('cat_roles', true);

        // Datos semilla: sólo inserta si el rol no existe (idempotente)
        $now = date('Y-m-d H:i:s');
        $roles = [
            ['nombre_rol' => 'Administrador', 'descripcion' => 'Acceso total a la plataforma.'],
            ['nombre_rol' => 'Jefatura',      'descripcion' => 'Gestion de equipos y aprobaciones de alto nivel.'],
            ['nombre_rol' => 'Supervisor',    'descripcion' => 'Supervision operativa y seguimiento de procesos.'],
            ['nombre_rol' => 'Personal',      'descripcion' => 'Operacion diaria con permisos base.'],
        ];

        foreach ($roles as $rol) {
            $this->db->query(
                "INSERT IGNORE INTO `cat_roles` (`nombre_rol`, `descripcion`, `created_at`, `updated_at`, `deleted_at`, `id_usuario`) VALUES (?, ?, ?, ?, NULL, NULL)",
                [$rol['nombre_rol'], $rol['descripcion'], $now, $now]
            );
        }
    }

    private function upUsuarios(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'alias' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'id_rol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'token_recuperacion' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'esta_conectado' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ],
            'ultimo_login' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
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
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('usuarios', true);

        // Clave foránea id_rol → cat_roles(id) (solo si la tabla acaba de crearse)
        if ($this->db->tableExists('usuarios') && $this->db->tableExists('cat_roles')) {
            // Verificar que la FK no exista antes de crearla
            $fkExists = $this->db->query(
                "SELECT COUNT(*) AS cnt
                   FROM information_schema.TABLE_CONSTRAINTS
                  WHERE CONSTRAINT_SCHEMA = DATABASE()
                    AND TABLE_NAME        = 'usuarios'
                    AND CONSTRAINT_NAME   = 'fk_usuarios_id_rol'
                    AND CONSTRAINT_TYPE   = 'FOREIGN KEY'"
            );
            $row = $fkExists ? $fkExists->getRowArray() : null;
            if (empty($row['cnt'])) {
                $this->db->query(
                    'ALTER TABLE `usuarios`
                     ADD CONSTRAINT `fk_usuarios_id_rol`
                     FOREIGN KEY (`id_rol`) REFERENCES `cat_roles`(`id`)
                     ON UPDATE CASCADE ON DELETE RESTRICT'
                );
            }
        }
    }

    private function upPermisos(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nombre_permiso' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'controlador' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'accion' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
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
        $this->forge->addUniqueKey('nombre_permiso', 'uq_permisos_nombre_permiso');
        $this->forge->addKey('controlador');
        $this->forge->addKey('accion');
        $this->forge->createTable('permisos', true);
    }

    private function upRolPermisos(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_rol' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_permiso' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
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
        $this->forge->addKey('id_rol');
        $this->forge->addKey('id_permiso');
        $this->forge->addUniqueKey(['id_rol', 'id_permiso'], 'uq_rol_permisos_rol_permiso');
        $this->forge->addForeignKey('id_rol',     'cat_roles', 'id', 'CASCADE', 'RESTRICT', 'fk_rol_permisos_id_rol');
        $this->forge->addForeignKey('id_permiso', 'permisos',  'id', 'CASCADE', 'RESTRICT', 'fk_rol_permisos_id_permiso');
        $this->forge->createTable('rol_permisos', true);
    }
}
