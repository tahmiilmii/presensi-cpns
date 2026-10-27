<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LocationModel;
use App\Models\PesertaModel;
use App\Models\PresensiModel;
use App\Models\UsersModel;
use CodeIgniter\Database\RawSql;
use CodeIgniter\I18n\Time;
use Mpdf\Mpdf;

class PresensiController extends BaseController
{
  public function index()
  {
    $data = [
      'page' => 'presensi',
      'subpage' => 'presensi',
    ];

    if (session()->get('role') == 'admin') {
      $data['lokasiUjian'] = (new LocationModel())->findAll();
    }

    return view('admin/presensi', $data);
  }

  public function list()
  {
    $request = $this->request;

    $columns = ['peserta.nama', 'waktu_presensi', 'status', 'users.fullname'];

    $status = $request->getGet('status');
    $tipe = $request->getGet('tipe');
    $tgl = $request->getGet('tgl_presensi');
    $jam_mulai = $request->getGet('jam_presensi_mulai');
    $jam_akhir = $request->getGet('jam_presensi_akhir');

    $builder = null;
    if ($status == 'hadir') {
      $model = new PresensiModel();
      $builder = $model->getDatatable()
        ->where(new RawSql("CAST(presensi.waktu_presensi AS DATE) = '$tgl'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i')  >= '$jam_mulai'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i') <= '$jam_akhir'"));
    } else {
      $model = new PesertaModel();
      $builder = $model->select("presensi.*, peserta.nama AS nama_peserta, '-' AS validator")
        ->join('presensi', 'peserta.id = presensi.peserta_id', 'left')
        ->where('presensi.id', null);
    }

    $search = $request->getGet('search')['value'] ?? '';
    $order = $request->getGet('order');
    $start = (int) $request->getGet('start') ?? 0;
    $length = (int) $request->getGet('length') ?? 10;

    if (!empty($search)) {
      $builder->groupStart()
        ->like('peserta.nama', $search)
        ->orLike('status', $search);

      if ($status == 'hadir') {
        $builder->orLike('users.username', $search);
      }

      $builder->groupEnd();
    }

    if (!empty($order)) {
      $orderColumn = $columns[$order[0]['column']];
      $orderDir = $order[0]['dir'];
      $builder->orderBy($orderColumn, $orderDir);
    }

    if ($tipe != "semua") {
      $builder->where('peserta.tipe_peserta', $tipe);
    }

    $total = $builder->countAllResults(false);
    $builder->limit($length)->offset($start);

    $data = $builder->get()->getResult();

    $result = [];
    $no = $start + 1;
    foreach ($data as $row) {
      $id = $row->id;

      $actionBtn = <<<EOD
        <div class="d-flex" style="gap: 6px;">
          <button class="btn btn-danger btn-sm" onclick="deletePresensi($id)" title="Hapus Presensi">
            <i class="fas fa-trash-alt"></i>
          </button> 
        </div>
      EOD;

      $result[] = [
        $no++,
        $row->nama_peserta,
        $row->waktu_presensi ? Time::parse($row->waktu_presensi)->format('d M Y - H:i') : '-',
        $row->status == 'hadir' ? esc(ucfirst($row->status)) : 'Tidak Hadir',
        esc($row->validator ?? '-'),
        $row->status == 'hadir' ? $actionBtn : '-'
      ];
    }

    return $this->response->setJSON([
      'draw' => intval($request->getGet('draw')),
      'recordsTotal' => $total,
      'recordsFiltered' => $search ? count($data) : $total,
      'data' => $result,
    ]);
  }

  public function save()
  {
    $request = $this->request;

    $validation = \Config\Services::validation();

    $rules = [
      'id'     => 'required|integer|is_not_unique[peserta.id]',
    ];

    if (session()->get('role') == 'admin') {
      $rules['lokasiUjian'] = 'required';
    }

    if (!$this->validate($rules)) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => false,
          'message' => join("<br>", $validation->getErrors()),
          'data' => null,
        ]);
    }

    // cek lokasi peserta
    $lokasiUjian = (session()->get('role') == 'admin') ? $request->getPost('lokasiUjian') : session()->get('lokasi_ujian');
    $peserta = (new PesertaModel())->where('lokasi_id', $lokasiUjian)->first();
    if (!$peserta) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "Lokasi peserta ujian salah",
          'data' => null,
        ]);
    }

    $peserta = (new PesertaModel())
      ->join('sesi', 'sesi.id = peserta.sesi_id', 'inner')
      ->where('peserta.id', $request->getPost('id'))->first();
    if (!$peserta) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "Cek lagi sesi ujian peserta",
          'data' => null,
        ]);
    }

    $now = Time::now()->format('Y-m-d H:i');
    $waktuMulai = Time::parse($peserta['waktu_mulai'])->format('Y-m-d H:i');
    $waktuSelesai = Time::parse($peserta['waktu_selesai'])->format('Y-m-d H:i');

    if ($now < $waktuMulai) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "Belum waktunya masuk presensi peserta",
          'data' => null,
        ]);
    }

    if ($now > $waktuSelesai) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "Sudah melebihi waktu masuk presensi peserta",
          'data' => null,
        ]);
    }

    $data = [
      'peserta_id'     => $request->getPost('id'),
      'waktu_presensi' => Time::now()->toDateTimeString(),
      'status'         => 'hadir',
      'validasi_oleh'  => session()->get('id'),
    ];

    $model = new PresensiModel();
    $model->insert($data);

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message'  => "Sukses menyimpan presensi",
        'data' => null,
      ]);
  }

  public function exportExcel()
  {
    $request = $this->request;

    $status = $request->getGet('status');
    $tipe = $request->getGet('tipe');
    $tgl = $request->getGet('tgl_presensi');
    $jam_mulai = $request->getGet('jam_presensi_mulai');
    $jam_akhir = $request->getGet('jam_presensi_akhir');

    $builder = null;
    if ($status == 'hadir') {
      $model = new PresensiModel();
      $builder = $model->getDatatable()
        ->where(new RawSql("CAST(presensi.waktu_presensi AS DATE) = '$tgl'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i')  >= '$jam_mulai'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i') <= '$jam_akhir'"));
    } else {
      $model = new PesertaModel();
      $builder = $model->select("presensi.*, peserta.nama AS nama_peserta, peserta.tipe_peserta, '-' AS validator")
        ->join('presensi', 'peserta.id = presensi.peserta_id', 'left')
        ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
        ->where('presensi.id', null);
    }

    if (!empty($tipe) && $tipe != "semua") {
      $builder->where('peserta.tipe_peserta', $tipe);
    }

    if (session('role') === 'petugas') {
      $builder->where('lokasi.id', session('lokasi_id'));
    }

    $data = $builder
      ->findAll();

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama Peserta');
    $sheet->setCellValue('C1', 'Jenis Peserta');
    $sheet->setCellValue('D1', 'Waktu Presensi');
    $sheet->setCellValue('E1', 'Status');
    $sheet->setCellValue('F1', 'Lokasi Ujian');
    $sheet->setCellValue('G1', 'Validator');

    // Data
    $no = 1;
    $row = 2;
    foreach ($data as $item) {
      $sheet->setCellValue("A{$row}", $no++);
      $sheet->setCellValue("B{$row}", $item['nama_peserta']);
      $sheet->setCellValue("C{$row}", $item['tipe_peserta']);
      $sheet->setCellValue("D{$row}", $item['waktu_presensi'] ? $item['waktu_presensi'] : '-');
      $sheet->setCellValue("E{$row}", $item['status'] == 'hadir' ? 'Hadir' : 'Tidak Hadir');
      $sheet->setCellValue("F{$row}", $item['nama_lokasi'] ?? 'Tidak Ditetapkan');
      $sheet->setCellValue("G{$row}",  $item['status'] == 'hadir' ? $item['validator'] : '-');
      $row++;
    }

    // Export
    $filename = 'presensi_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
  }

  public function exportPdf()
  {
    $request = $this->request;

    $status = $request->getGet('status');
    $tipe = $request->getGet('tipe');
    $tgl = $request->getGet('tgl_presensi');
    $jam_mulai = $request->getGet('jam_presensi_mulai');
    $jam_akhir = $request->getGet('jam_presensi_akhir');

    $builder = null;
    if ($status == 'hadir') {
      $model = new PresensiModel();
      $builder = $model->getDatatable()
        ->where(new RawSql("CAST(presensi.waktu_presensi AS DATE) = '$tgl'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i')  >= '$jam_mulai'"))
        ->where(new RawSql("DATE_FORMAT(presensi.waktu_presensi, '%H:%i') <= '$jam_akhir'"));
    } else {
      $model = new PesertaModel();
      $builder = $model->select("presensi.*, peserta.nama AS nama_peserta, peserta.tipe_peserta, '-' AS validator")
        ->join('presensi', 'peserta.id = presensi.peserta_id', 'left')
        ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
        ->where('presensi.id', null);
    }

    if (!empty($tipe) && $tipe != "semua") {
      $builder->where('peserta.tipe_peserta', $tipe);
    }

    if (session('role') === 'petugas') {
      $builder->where('lokasi.id', session('lokasi_id'));
    }

    $result = $builder
      ->findAll();

    $data = [];
    foreach ($result as $row) {
      $data[] = [
        'nama_peserta' => $row['nama_peserta'],
        'tipe_peserta' => $row['tipe_peserta'],
        'waktu_presensi' => $row['waktu_presensi'] ? Time::parse($row['waktu_presensi'])->format('d M Y - H:i') : '-',
        'status' => $row['status'] == 'hadir' ? esc(ucfirst($row['status'])) : 'Tidak Hadir',
        'lokasi' => $row['nama_lokasi'] ?? 'Tidak Ditetapkan',
        'validator' => esc($row['validator'] ?? '-'),
      ];
    }

    $html = view('admin/pdf/presensi', [
      'data' => $data,
      'petugas' => (new UsersModel())->where('id', session()->id)->where('role', 'petugas')->first(),
    ]);

    $mpdf = new Mpdf(['format' => 'A4']);
    $mpdf->WriteHTML($html);

    return $this
      ->response
      ->setHeader('Content-Type', 'application/pdf')
      ->setHeader('Content-Disposition', 'attachment; filename="daftar_presensi.pdf"')
      ->setBody($mpdf->Output('', 'S'));
  }

  public function delete($id = null)
  {
    $model = new PresensiModel();

    if (!$id || !$model->find($id)) {
      return $this->response->setJSON([
        'error' => true,
        'message'  => "Presensi tidak ditemukan",
        'data' => null,
      ]);
    }

    $model->delete($id);

    return $this->response->setJSON([
      'error' => false,
      'message'  => "Sukses menghapus peserta",
      'data' => null,
    ]);
  }
}
