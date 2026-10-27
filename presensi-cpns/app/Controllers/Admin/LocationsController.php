<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LocationModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class LocationsController extends BaseController
{
  public function index()
  {
    $data = [
      'page' => 'lokasi',
      'subpage' => 'lokasi',
    ];

    return view('admin/lokasi', $data);
  }

  public function list()
  {
    $request = $this->request;
    $locationModel = new LocationModel();

    $columns = [
      'id',
      'nama_lokasi',
    ];

    $search = $request->getGet('search')['value'] ?? '';
    $order = $request->getGet('order');
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    $locationModel->select('*');

    if (!empty($search)) {
      $locationModel->groupStart();
      foreach ($columns as $col) {
        $locationModel->orLike($col, $search);
      }
      $locationModel->groupEnd();
    }

    if (!empty($order)) {
      $orderColumn = $columns[$order[0]['column']];
      $orderDir = $order[0]['dir'];
      $locationModel->orderBy($orderColumn, $orderDir);
    }

    $totalRecords = $locationModel->countAllResults(false);
    $data = $locationModel->findAll($length, $start);
    $totalFiltered = $search ? count($data) : $totalRecords;

    $result = [];
    $no = $start + 1;
    foreach ($data as $row) {
      $action = <<<EOD
        <div class="d-flex" style="gap: 8px;">
          <button class="btn btn-primary btn-sm" onclick="editLokasi($row[id])" title="Edit Lokasi">
            <i class="fas fa-edit"></i>
            &nbsp;Edit
          </button> 
          <button class="btn btn-danger btn-sm" onclick="deleteLokasi($row[id])" title="Hapus Lokasi">
            <i class="fas fa-trash-alt"></i>
            &nbsp;Hapus
          </button> 
        </div>
      EOD;

      $result[] = [
        $no++,
        esc($row['id']),
        esc($row['nama_lokasi']),
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
    $locationModel = new LocationModel();
    $data = $locationModel->find($id);

    if (!$data) {
      return $this
        ->response
        ->setStatusCode(404)
        ->setJSON([
          'error' => true,
          'message'  => "Peserta tidak ditemukan",
          'data' => null,
        ]);
    }

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
    $locationModel = new LocationModel();

    $id = $request->getPost('id');

    $rules = [
      'nama_lokasi'          => 'required|max_length[100]',
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

    $data = [
      'nama_lokasi'           => $request->getPost('nama_lokasi'),
    ];

    if ($id) {
      $locationModel->update($id, $data);
    } else {
      $locationModel->insert($data);
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

    $locationModel = new LocationModel();

    try {
      $locationModel->delete($id);
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
        'message'  => "Sukses menghapus lokasi",
        'data' => null,
      ]);
  }
}
