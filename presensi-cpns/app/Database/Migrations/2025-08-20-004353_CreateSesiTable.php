<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSesiTable extends Migration
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
      'nama_sesi' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
      ],
      'waktu_mulai' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'waktu_selesai' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('sesi');
  }

  public function down()
  {
    $this->forge->dropTable('sesi');
  }
}
