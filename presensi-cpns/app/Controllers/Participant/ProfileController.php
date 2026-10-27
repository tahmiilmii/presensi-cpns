<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\PesertaModel;
use CodeIgniter\HTTP\ResponseInterface;

class ProfileController extends BaseController
{
  public function index()
  {
    $participant = (new PesertaModel())->where('id', session()->get('id'))->first();

    $data = [
      'page' => 'profile',
      'subpage' => 'profile',
      'peserta' => $participant,
    ];

    return view('participant/profile', $data);
  }

  public function save()
  {
    $validation = \Config\Services::validation();

    $rules = [
      'nama' => [
        'label' => 'Nama Lengkap',
        'rules' => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong.',
        ]
      ],
      'nik' => [
        'label' => 'NIK',
        'rules' => 'required|numeric|min_length[16]|max_length[20]',
        'errors' => [
          'required' => '{field} wajib diisi.',
          'numeric' => '{field} harus berupa angka.',
          'min_length' => '{field} minimal 16 digit.',
          'max_length' => '{field} maksimal 20 digit.',
        ]
      ],
      'tempat_lahir' => [
        'label' => 'Tempat Lahir',
        'rules' => 'required',
        'errors' => [
          'required' => '{field} wajib diisi.',
        ]
      ],
      'tanggal_lahir' => [
        'label' => 'Tanggal Lahir',
        'rules' => 'required|valid_date',
        'errors' => [
          'required' => '{field} tidak boleh kosong.',
          'valid_date' => '{field} harus berupa tanggal yang valid.',
        ]
      ],
      'alamat' => [
        'label' => 'Alamat',
        'rules' => 'required',
        'errors' => [
          'required' => '{field} wajib diisi.',
        ]
      ],
    ];

    if (!$this->validate($rules)) {
      return redirect()->back()->withInput()->with('error', join("<br>", $validation->getErrors()));
    }

    $model = new PesertaModel();
    $model->update(session()->get('id'), [
      'nama' => $this->request->getPost('nama'),
      'nik' => $this->request->getPost('nik'),
      'tempat_lahir' => $this->request->getPost('tempat_lahir'),
      'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
      'alamat' => $this->request->getPost('alamat'),
    ]);

    return redirect()->to('participant/profile')->with('success', 'Profil berhasil diperbarui.');
  }
}
