<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PesertaModel;
use App\Models\PresensiModel;
use CodeIgniter\Database\RawSql;
use CodeIgniter\I18n\Time;

class DashboardController extends BaseController
{
  public function index()
  {
    $pesertaModel = new PesertaModel();
    $presensiModel = new PresensiModel();

    $tgl = Time::now()->toDateString();

    $total = $pesertaModel->countAll();
    $cpns = $pesertaModel->where('tipe_peserta', 'CPNS')->countAllResults();
    $pppk = $pesertaModel->where('tipe_peserta', 'PPPK')->countAllResults();

    $cpnsHadir = $presensiModel->getDatatable()
      ->where(new RawSql("CAST(presensi.waktu_presensi AS DATE) = '$tgl'"))
      ->where('peserta.tipe_peserta', 'CPNS')
      ->countAllResults();

    $pppkHadir = $presensiModel->getDatatable()
      ->where(new RawSql("CAST(presensi.waktu_presensi AS DATE) = '$tgl'"))
      ->where('peserta.tipe_peserta', 'PPPK')
      ->countAllResults();

    $data = [
      'page' => 'dashboard',
      'subpage' => 'dashboard',

      'total' => $total,
      'cpns' => $cpns,
      'pppk' => $pppk,

      'cpnsHadir' => $cpnsHadir,
      'cpnsTidakHadir' => $cpns - $cpnsHadir,

      'pppkHadir' => $pppkHadir,
      'pppkTidakHadir' => $pppk - $pppkHadir,
    ];

    return view('admin/dashboard', $data);
  }
}
