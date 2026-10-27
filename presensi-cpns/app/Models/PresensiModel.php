<?php

namespace App\Models;

use CodeIgniter\Database\RawSql;
use CodeIgniter\Model;

class PresensiModel extends Model
{
  protected $table            = 'presensi';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $useSoftDeletes   = false;
  protected $protectFields    = true;
  protected $allowedFields    = ['peserta_id', 'waktu_presensi', 'status', 'validasi_oleh'];

  protected bool $allowEmptyInserts = false;
  protected bool $updateOnlyChanged = true;

  protected array $casts = [];
  protected array $castHandlers = [];

  // Dates
  protected $useTimestamps = false;
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  // Validation
  protected $validationRules      = [];
  protected $validationMessages   = [];
  protected $skipValidation       = false;
  protected $cleanValidationRules = true;

  // Callbacks
  protected $allowCallbacks = true;
  protected $beforeInsert   = [];
  protected $afterInsert    = [];
  protected $beforeUpdate   = [];
  protected $afterUpdate    = [];
  protected $beforeFind     = [];
  protected $afterFind      = [];
  protected $beforeDelete   = [];
  protected $afterDelete    = [];

  public function getDatatable()
  {
    $builder =  $this->select(
      new RawSql(
        'presensi.*, 
        peserta.nama AS nama_peserta, 
        users.fullname AS validator,
        CASE 
            WHEN lokasi.id IS NULL 
            THEN "Tidak Ditetapkan" 
            ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian'
      )
    )
      ->join('peserta', 'peserta.id = presensi.peserta_id', 'left')
      ->join('users', 'users.id = presensi.validasi_oleh', 'left')
      ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left');

    if (session('role') === 'petugas') {
      $builder->where('lokasi.id', session('lokasi_id'));
    }

    return $builder;
  }
}
