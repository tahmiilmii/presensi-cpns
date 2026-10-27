<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateDefaultUser extends Seeder
{
  public function run()
  {
    $data = [
      [
        'fullname'  => 'John Doe',
        'username'   => 'admin',
        'password'   => password_hash('#admin123', PASSWORD_BCRYPT),
        'role'       => 'admin',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ],
      [
        'fullname'  => 'Jane Doe',
        'username'   => 'petugas',
        'password'   => password_hash('#petugas123', PASSWORD_BCRYPT),
        'role'       => 'petugas',
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ],
    ];

    $this->db->table('users')->insertBatch($data);
  }
}
