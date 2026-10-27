<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePresensiTable extends Migration
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
      'peserta_id' => [
        'type'       => 'INT',
        'unsigned'   => true,
      ],
      'waktu_presensi' => [
        'type' => 'DATETIME',
        'null' => false,
        'default' => new RawSql('CURRENT_TIMESTAMP()'),
      ],
      'status' => [
        'type'       => "ENUM('hadir','tidak hadir')",
        'default'    => 'hadir',
      ],
      'validasi_oleh' => [
        'type'       => 'INT',
        'unsigned'   => true,
        'null'       => true,
      ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->addForeignKey('peserta_id', 'peserta', 'id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('validasi_oleh', 'users', 'id', 'SET NULL', 'CASCADE');

    $this->forge->createTable('presensi');
  }

  public function down()
  {
    $this->forge->dropTable('presensi');
  }
}
