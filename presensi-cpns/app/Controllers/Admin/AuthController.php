<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use App\Models\LocationModel;

class AuthController extends BaseController
{
  public function index()
  {
    $session = session();

    if ($session->get('logged_in')) {
      return redirect()->to('/admin/dashboard');
    }

    $data = [
      'lokasiUjian' => (new LocationModel())->findAll(),
    ];

    return view('admin/auth', $data);
  }

  public function login()
  {
    $rules = [
      'username' => 'required',
      'password' => 'required',
    ];

    $messages = [
      'username' => [
        'required' => 'Username harus diisi.',
      ],
      'password' => [
        'required' => 'Password harus diisi.',
      ],
    ];

    if (!$this->validate($rules, $messages)) {
      return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
    }

    $session = session();
    $model = new UsersModel();

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    $admin = $model->where('username', $username)->first();

    if ($admin) {
      if (password_verify($password, $admin['password'])) {
        $sessionData = [
          'id'       => $admin['id'],
          'fullname' => $admin['fullname'],
          'username' => $admin['username'],
          'role'     => $admin['role'],
          'logged_in' => true,
        ];

        if ($admin['role'] === 'petugas') {
          $lokasi = (new LocationModel())->find($admin['lokasi_id']);

          $sessionData['lokasi_id'] = $lokasi['id'];
          $sessionData['lokasi_ujian'] = $lokasi['nama_lokasi'];
        } else {
          $sessionData['lokasi_ujian'] = 'Admin';
        }

        $session->set($sessionData);

        return redirect()->to('/admin/dashboard');
      } else {
        return redirect()->back()->with('error', 'Password salah.');
      }
    } else {
      return redirect()->back()->with('error', 'Username tidak ditemukan.');
    }
  }

  public function logout()
  {
    $session = session();
    $session->destroy();

    return redirect()->to('/admin');
  }
}
