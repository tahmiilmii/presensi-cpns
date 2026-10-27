<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePesertaTable extends Migration
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
      'nama' => [
        'type'       => 'VARCHAR',
        'constraint' => 100,
      ],
      'nik' => [
        'type'       => 'VARCHAR',
        'constraint' => 20,
      ],
      'no_peserta' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
      ],
      'tempat_lahir' => [
        'type'       => 'VARCHAR',
        'constraint' => 50,
      ],
      'tanggal_lahir' => [
        'type' => 'DATE',
      ],
      'jenis_kelamin' => [
        'type'    => 'ENUM',
        'constraint' => ['L', 'P'],
        'default' => 'L',
      ],
      'alamat' => [
        'type' => 'TEXT',
      ],
      'pendidikan' => [
        'type'       => 'VARCHAR',
        'constraint' => 255,
      ],
      'jabatan' => [
        'type'       => 'TEXT',
      ],
      'unit_kerja' => [
        'type'       => 'TEXT',
      ],
      'tipe_peserta' => [
        'type'    => 'ENUM',
        'constraint' => ['CPNS', 'PPPK'],
      ],
      'lokasi_id' => [
        'type'    => 'INT',
        'constraint' => 11,
      ],
      'created_at' => [
        'type'    => 'DATETIME',
        'null'    => true,
        'default' => new RawSql('CURRENT_TIMESTAMP()'),
      ],
      'updated_at' => [
        'type'    => 'DATETIME',
        'null'    => true,
        'default' => new RawSql('CURRENT_TIMESTAMP()'),
      ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('peserta');
  }

  public function down()
  {
    $this->forge->dropTable('peserta');
  }
}
