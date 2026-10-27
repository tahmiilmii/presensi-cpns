<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SesiModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Exception;

class SesiController extends BaseController
{
  public function index()
  {
    $data = [
      'page' => 'sesi',
      'subpage' => 'sesi',
    ];

    return view('admin/sesi', $data);
  }

  public function list()
  {
    $request = $this->request;
    $sesiModel = new SesiModel();

    $columns = [
      'id',
      'nama_lokasi',
    ];

    $search = $request->getGet('search')['value'] ?? '';
    $order = $request->getGet('order');
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    $sesiModel->select('*');

    if (!empty($search)) {
      $sesiModel->groupStart();
      foreach ($columns as $col) {
        $sesiModel->orLike($col, $search);
      }
      $sesiModel->groupEnd();
    }

    if (!empty($order)) {
      $orderColumn = $columns[$order[0]['column']];
      $orderDir = $order[0]['dir'];
      $sesiModel->orderBy($orderColumn, $orderDir);
    }

    $totalRecords = $sesiModel->countAllResults(false);
    $data = $sesiModel->findAll($length, $start);
    $totalFiltered = $search ? count($data) : $totalRecords;

    $result = [];
    $no = $start + 1;
    foreach ($data as $row) {
      $action = <<<EOD
        <div class="d-flex" style="gap: 8px;">
          <button class="btn btn-primary btn-sm" onclick="editSesi($row[id])" title="Edit Lokasi">
            <i class="fas fa-edit"></i>
            &nbsp;Edit
          </button> 
          <button class="btn btn-danger btn-sm" onclick="deleteSesi($row[id])" title="Hapus Lokasi">
            <i class="fas fa-trash-alt"></i>
            &nbsp;Hapus
          </button> 
        </div>
      EOD;

      $result[] = [
        $no++,
        esc($row['nama_sesi']),
        Time::parse($row['waktu_mulai'])->format('d-m-Y'),
        Time::parse($row['waktu_mulai'])->format('H:i'),
        Time::parse($row['waktu_selesai'])->format('H:i'),
        $action
      ];
    }

    return $this->response->setJSON([
      'draw' => (int) $request->getGet('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFiltered,
      'data' => $result
    ]);
  }

  public function get($id)
  {
    $sesiModel = new SesiModel();
    $sesi = $sesiModel->find($id);

    if (!$sesi) {
      return $this
        ->response
        ->setStatusCode(404)
        ->setJSON([
          'error' => true,
          'message'  => "Peserta tidak ditemukan",
          'data' => null,
        ]);
    }

    $data = [
      'id' => $sesi['id'],
      'nama_sesi' => esc($sesi['nama_sesi']),
      'tanggal' => Time::parse($sesi['waktu_mulai'])->format('Y-m-d'),
      'jam_mulai' => Time::parse($sesi['waktu_mulai'])->format('H:i'),
      'jam_selesai' => Time::parse($sesi['waktu_selesai'])->format('H:i'),
    ];

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message'  => "Sukses mendapatkan peserta",
        'data' => $data,
      ]);
  }

  public function save()
  {
    $request = $this->request;
    $validation = \Config\Services::validation();
    $sesiModel = new SesiModel();

    $id = $request->getPost('id');

    $rules = [
      'nama_sesi' => 'required|max_length[100]',
      'tanggal' => 'required',
      'jam_mulai' => 'required',
      'jam_selesai' => 'required',
    ];

    if (!$this->validate($rules)) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => join("<br>", $validation->getErrors()),
          'data' => null,
        ]);
    }

    $tanggal = Time::parse($request->getPost('tanggal'))->toDateString();
    $jamMulai = Time::parse($request->getPost('jam_mulai'))->toTimeString();
    $jamSelesai = Time::parse($request->getPost('jam_selesai'))->toTimeString();

    $data = [
      'nama_sesi' => $request->getPost('nama_sesi'),
      'waktu_mulai' => Time::parse("$tanggal $jamMulai")->toDateTimeString(),
      'waktu_selesai' => Time::parse("$tanggal $jamSelesai")->toDateTimeString(),
    ];

    if ($id) {
      $sesiModel->update($id, $data);
    } else {
      $sesiModel->insert($data);
    }

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON(
        [
          'error' => false,
          'message'  => "Sukses menyimpan peserta",
          'data' => null,
        ]
      );
  }

  public function delete($id = null)
  {
    if (!$id) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "Id kosong",
          'data' => null,
        ]);
    }

    $sesiModel = new SesiModel();

    try {
      $sesiModel->delete($id);
    } catch (Exception $e) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => $e->getMessage(),
          'data' => null,
        ]);
    }

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message'  => "Sukses menghapus sesi",
        'data' => null,
      ]);
  }
}
