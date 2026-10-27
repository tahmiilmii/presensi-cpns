<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLocationTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type'           => 'INT',
        'auto_increment' => true,
        'unsigned'       => true
      ],
      'nama_lokasi' => [
        'type'       => 'VARCHAR',
        'constraint' => 100
      ],
      'created_at' => [
        'type'    => 'DATETIME',
        'null'    => true,
        'default' => null
      ],
      'updated_at' => [
        'type'    => 'DATETIME',
        'null'    => true,
        'default' => null
      ]
    ]);

    $this->forge->addKey('id', true); // Primary key
    $this->forge->createTable('lokasi');
  }

  public function down()
  {
    $this->forge->dropTable('lokasi', true);
  }
}
