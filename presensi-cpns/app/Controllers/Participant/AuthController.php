<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\PesertaModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
  public function index()
  {
    $session = session();

    if ($session->get('logged_in')) {
      return redirect()->to('/profile');
    }

    return view('/participant/auth');
  }

  public function login()
  {
    $session = session();
    $model = new PesertaModel();

    $username = $this->request->getPost('username');
    $password = $this->request->getPost('password');

    $participant = $model->where('no_peserta', $username)->first();

    if ($participant) {
      if ($password  == Time::parse($participant['tanggal_lahir'])->format('dmY')) {
        $sessionData = [
          'id'       => $participant['id'],
          'fullname' => $participant['nama'],
          'role'      => 'participant',
          'logged_in' => true,
        ];
        $session->set($sessionData);

        return redirect()->to('participant/profile');
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

    return redirect()->to('/');
  }
}
