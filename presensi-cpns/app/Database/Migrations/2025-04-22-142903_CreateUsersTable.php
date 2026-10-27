<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateUsersTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type'           => 'INT',
        'unsigned'       => true,
        'auto_increment' => true,
      ],
      'fullname' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
      ],
      'username' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'unique'     => true,
      ],
      'nip' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'unique'     => true,
      ],
      'password' => [
        'type'       => 'VARCHAR',
        'constraint' => '255',
      ],
      'role' => [
        'type'       => 'ENUM',
        'constraint' => ['admin', 'petugas'],
        'default'    => 'petugas',
      ],
      'lokasi_id' => [
        'type'       => 'INT',
        'constraint' => 11,
        'null'       => true,
      ],
      'sesi_id' => [
        'type'       => 'INT',
        'constraint' => 11,
        'null'       => true,
      ],
      'created_at' => [
        'type'    => 'TIMESTAMP',
        'default' => new RawSql('CURRENT_TIMESTAMP()'),
      ],
      'updated_at' => [
        'type'    => 'TIMESTAMP',
        'default' => new RawSql('CURRENT_TIMESTAMP()'),
      ],
    ]);

    $this->forge->addKey('id', true); // Primary key
    $this->forge->createTable('users');
  }

  public function down()
  {
    $this->forge->dropTable('users');
  }
}
