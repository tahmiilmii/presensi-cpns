<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use CodeIgniter\I18n\Time;
use App\Models\LocationModel;
use CodeIgniter\Database\RawSql;

class PetugasController extends BaseController
{
  public function index()
  {
    $data = [
      'page' => 'petugas',
      'subpage' => 'petugas',
      'lokasiUjian' => (new LocationModel())->findAll(),
    ];

    return view('admin/petugas', $data);
  }

  public function list()
  {
    $request = $this->request;;
    $model = new UsersModel();

    $columns = ['fullname', 'username', 'created_at'];

    $order = $request->getGet('order');
    $search = $request->getGet('search')['value'];
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    if ($search) {
      $model->groupStart();
      foreach ($columns as $col) {
        $model->orLike($col, $search);
      }
      $model->groupEnd();
    }

    if (!empty($order)) {
      $orderColumn = $columns[$order[0]['column']];
      $orderDir = $order[0]['dir'];
      $model->orderBy($orderColumn, $orderDir);
    }

    $totalRecords = $model->countAllResults(false);
    $data = $model->select(
      new RawSql(
        'users.*, 
        CASE 
          WHEN lokasi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian'
      )
    )
      ->where('role', 'petugas')
      ->join('lokasi', 'lokasi.id = users.lokasi_id', 'left')
      ->findAll($length, $start);
    $totalFiltered = $search ? count($data) : $totalRecords;

    $result = [];
    $no = $start + 1;
    foreach ($data as $row) {
      $actionBtn = <<<EOD
        <div class="d-flex" style="gap: 6px;">
          <button class="btn btn-primary btn-sm" onclick="editPetugas($row[id])" title="Edit Peserta">
            <i class="fas fa-edit"></i>
          </button> 
          <button class="btn btn-danger btn-sm" onclick="deletePetugas($row[id])" title="Hapus Peserta">
            <i class="fas fa-trash-alt"></i>
          </button> 
        </div>
      EOD;

      $result[] = [
        $no++,
        esc($row['fullname']),
        esc($row['username']),
        esc($row['lokasi_ujian']),
        Time::parse($row['created_at'])->format('d M Y'),
        $actionBtn,
      ];
    }

    return $this->response->setJSON([
      'draw' => $request->getGet('draw'),
      'recordsTotal' => $totalRecords,
      'recordsFiltered' => $totalFiltered,
      'data' => $result,
    ]);
  }

  public function get($id)
  {
    $model = new UsersModel();
    $data = $model
      ->join('lokasi', 'lokasi.id = users.lokasi_id', 'left')
      ->find($id);

    if (!$data) {
      return $this
        ->response
        ->setStatusCode(404)
        ->setJSON([
          'error' => true,
          'message' => 'Petugas tidak ditemukan',
          'data' => null,
        ]);
    }

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message' => 'Data ditemukan',
        'data' => $data,
      ]);
  }

  public function save()
  {
    $request = $this->request;;
    $validation = \Config\Services::validation();
    $model = new UsersModel();

    $id = $request->getPost('id');

    $isUpdate = !empty($id);
    $user = $isUpdate ? $model->find($id) : null;

    if ($isUpdate && !$user) {
      return $this
        ->response
        ->setStatusCode(404)
        ->setJSON([
          'error' => true,
          'message' => 'Petugas tidak ditemukan',
          'data' => null
        ]);
    }

    $rules = [
      'fullname' => 'required',
      'username' => 'required|' . ($isUpdate ? 'is_unique[users.username,id,' . $id . ']' : 'is_unique[users.username]'),
      'nip' => 'required',
      'location' => 'required',
    ];

    if (!$isUpdate || $request->getPost('password')) {
      $rules['password'] = 'required|min_length[4]';
    }

    if (!$this->validate($rules)) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message' => join('<br>', $validation->getErrors()),
          'data' => null
        ]);
    }

    $data = [
      'fullname' => $request->getPost('fullname'),
      'username' => $request->getPost('username'),
      'nip' => $request->getPost('nip'),
      'lokasi_id' => $request->getPost('location'),
      'role' => 'petugas',
    ];

    if ($request->getPost('password')) {
      $data['password'] = password_hash($request->getPost('password'), PASSWORD_BCRYPT);
    }

    if ($isUpdate) {
      $model->update($id, $data);
      $message = 'Petugas berhasil diperbarui';
    } else {
      $model->insert($data);
      $message = 'Petugas berhasil ditambahkan';
    }

    return $this->response->setJSON([
      'error' => false,
      'message' => $message,
      'data' => null
    ]);
  }

  public function delete($id)
  {
    $model = new UsersModel();
    $user = $model->find($id);

    if (!$user) {
      return $this->response->setJSON([
        'error' => true,
        'message' => 'Petugas tidak ditemukan',
        'data' => null,
      ]);
    }

    $model->delete($id);

    return $this->response->setJSON([
      'error' => false,
      'message' => 'Petugas berhasil dihapus',
      'data' => null,
    ]);
  }
}
