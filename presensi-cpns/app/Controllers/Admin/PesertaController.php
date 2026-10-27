<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PesertaModel;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use CodeIgniter\I18n\Time;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;
use chillerlan\QRCode\QROptions;
use App\Models\LocationModel;
use App\Models\SesiModel;
use CodeIgniter\Database\RawSql;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use ZipArchive;

class PesertaController extends BaseController
{
  private $qrOptions;

  function __construct()
  {
    $this->qrOptions = new QROptions([
      'outputType'      => QROutputInterface::GDIMAGE_PNG,
      'eccLevel'        => QRCode::ECC_L,
      'scale'           => 20,
      'quietzoneSize'   => 2,
    ]);
  }

  public function index()
  {
    $data = [
      'page' => 'peserta',
      'subpage' => 'peserta',
      'lokasiUjian' => (new LocationModel())->findAll(),
      'sesiUjian' => (new SesiModel())->findAll(),
    ];

    return view('admin/peserta', $data);
  }

  public function list()
  {
    $request = $this->request;
    $pesertaModel = new PesertaModel();

    $columns = [
      'nama',
      'nik',
      'no_peserta',
      'tempat_lahir',
      'tanggal_lahir',
      'jenis_kelamin',
      'alamat',
      'pendidikan',
      'jabatan',
      'unit_kerja',
      'tipe_peserta'
    ];

    $tipe = $request->getGet('tipe');
    $lokasi = $request->getGet('lokasi');
    $sesi = $request->getGet('sesi');

    $search = $request->getGet('search')['value'] ?? '';
    $order = $request->getGet('order');
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('length') ?? 10;

    $pesertaModel->select(
      new RawSql(
        'peserta.*, 
        CASE 
          WHEN lokasi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian,
        CASE 
          WHEN sesi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE sesi.nama_sesi
        END AS sesi_ujian'
      )
    )
      ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
      ->join('sesi', 'sesi.id = peserta.sesi_id', 'left');

    if ($tipe != "semua") {
      $pesertaModel->where('tipe_peserta', $tipe);
    }

    if (session('role') === 'admin' && $lokasi != "semua") {
      $pesertaModel->where('lokasi.id', $lokasi);
    }

    if ($sesi != "semua") {
      $pesertaModel->where('sesi.id', $sesi);
    }

    if (session('role') === 'petugas') {
      $pesertaModel->where('lokasi.id', session('lokasi_id'));
    }

    if (!empty($search)) {
      $pesertaModel->groupStart();
      foreach ($columns as $col) {
        $pesertaModel->orLike($col, $search);
      }
      $pesertaModel->groupEnd();
    }

    if (!empty($order)) {
      $orderColumn = $columns[$order[0]['column']];
      $orderDir = $order[0]['dir'];
      $pesertaModel->orderBy($orderColumn, $orderDir);
    }

    $totalRecords = $pesertaModel->countAllResults(false);
    $data = $pesertaModel->findAll($length, $start);
    $totalFiltered = $search ? count($data) : $totalRecords;

    $result = [];
    $no = $start + 1;
    foreach ($data as $row) {
      $action = <<<EOD
        <div class="d-flex flex-column" style="gap: 6px;">
          <button class="btn btn-warning btn-sm" onclick="getQrPeserta($row[id])" title="Tampilkan QR Code Peserta">
            <i class="fas fa-qrcode"></i>
          </button> 
          <button class="btn btn-primary btn-sm" onclick="editPeserta($row[id])" title="Edit Peserta">
            <i class="fas fa-edit"></i>
          </button> 
          <button class="btn btn-danger btn-sm" onclick="deletePeserta($row[id])" title="Hapus Peserta">
            <i class="fas fa-trash-alt"></i>
          </button> 
        </div>
      EOD;

      $result[] = [
        $no++,
        esc($row['no_peserta']),
        "<p>$row[nama])</p><p>NIK: $row[nik]</p>",
        "$row[tempat_lahir], $row[tanggal_lahir]",
        $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan',
        esc($row['alamat']),
        esc($row['pendidikan']),
        esc($row['jabatan']),
        esc($row['unit_kerja']),
        esc($row['tipe_peserta']),
        esc($row['lokasi_ujian']),
        esc($row['sesi_ujian']),
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
    $pesertaModel = new PesertaModel();
    $data = $pesertaModel
      ->select(
        new RawSql(
          'peserta.*, 
        CASE 
          WHEN lokasi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian'
        )
      )
      ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
      ->find($id);

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

  public function getQrcode($id)
  {
    $pesertaModel = new PesertaModel();
    $data = $pesertaModel->find($id);

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

    $options = $this->qrOptions;

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message'  => "Sukses mendapatkan peserta",
        'data' => [
          'qrcode' => (new QRCode($options))->render($data['no_peserta']),
          'filename' => $data['no_peserta'],
        ],
      ]);
  }

  public function getByNoPeserta($noPeserta)
  {
    if (!$noPeserta) {
      return $this->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => "No Peserta kosong",
          'data' => null,
        ]);
    }

    $model = new PesertaModel();
    $peserta = $model
      ->select(new RawSql(
        'peserta.*, 
        CASE 
          WHEN sesi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE sesi.nama_sesi
        END AS sesi_ujian'
      ))
      ->join('sesi', 'sesi.id = peserta.sesi_id', 'left')
      ->where('no_peserta', $noPeserta)->first();

    if (!$peserta) {
      return $this->response
        ->setStatusCode(404)
        ->setJSON([
          'error' => true,
          'message'  => "Peserta tidak ditemukan",
          'data' => null,
        ]);
    }

    $result = [
      'id' => $peserta['id'],
      'instansi' => $peserta['unit_kerja'],
      'nik' => $peserta['nik'],
      'no_peserta' => $peserta['no_peserta'],
      'nama' => $peserta['nama'],
      'jk' => $peserta['jenis_kelamin'] == 'L' ? 'Laki - Laki' : 'Perempuan',
      'ttl' => $peserta['tempat_lahir'] . ', ' . Time::parse($peserta['tanggal_lahir'])->format('d M Y'),
      'pendidikan' => $peserta['pendidikan'],
      'jabatan' => $peserta['jabatan'],
      'sesi_ujian' => $peserta['sesi_ujian'],
    ];

    return $this->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => true,
        'message'  => "Sukses mendapatkan peserta",
        'data' => $result,
      ]);
  }

  public function save()
  {
    $request = $this->request;
    $validation = \Config\Services::validation();
    $pesertaModel = new PesertaModel();

    $id = $request->getPost('id');

    $rules = [
      'nama' => [
        'rules' => 'required|max_length[100]',
        'errors' => [
          'required'   => 'Nama harus diisi.',
          'max_length' => 'Nama tidak boleh lebih dari 100 karakter.',
        ]
      ],
      'nik' => [
        'rules' => 'required|min_length[16]|max_length[16]|numeric',
        'errors' => [
          'required'   => 'NIK harus diisi.',
          'max_length' => 'NIK maksimal 16 digit.',
          'min_length' => 'NIK minimal 16 digit.',
          'numeric'    => 'NIK harus berupa angka.',
        ]
      ],
      'no_peserta' => [
        'rules' => 'required|max_length[50]',
        'errors' => [
          'required'   => 'No Peserta harus diisi.',
          'max_length' => 'No Peserta tidak boleh lebih dari 50 karakter.',
        ]
      ],
      'tempat_lahir' => [
        'rules' => 'required|max_length[50]',
        'errors' => [
          'required'   => 'Tempat Lahir harus diisi.',
          'max_length' => 'Tempat Lahir tidak boleh lebih dari 50 karakter.',
        ]
      ],
      'tanggal_lahir' => [
        'rules' => 'required|valid_date',
        'errors' => [
          'required'   => 'Tanggal Lahir harus diisi.',
          'valid_date' => 'Format Tanggal Lahir tidak valid.',
        ]
      ],
      'jenis_kelamin' => [
        'rules' => 'required|in_list[L,P]',
        'errors' => [
          'required' => 'Jenis Kelamin harus dipilih.',
          'in_list'  => 'Jenis Kelamin harus L atau P.',
        ]
      ],
      'alamat' => [
        'rules' => 'required',
        'errors' => [
          'required' => 'Alamat harus diisi.',
        ]
      ],
      'pendidikan' => [
        'rules' => 'required|max_length[100]',
        'errors' => [
          'required'   => 'Pendidikan harus diisi.',
          'max_length' => 'Pendidikan tidak boleh lebih dari 100 karakter.',
        ]
      ],
      'jabatan' => [
        'rules' => 'required|max_length[100]',
        'errors' => [
          'required'   => 'Jabatan harus diisi.',
          'max_length' => 'Jabatan tidak boleh lebih dari 100 karakter.',
        ]
      ],
      'unit_kerja' => [
        'rules' => 'required|max_length[100]',
        'errors' => [
          'required'   => 'Unit Kerja harus diisi.',
          'max_length' => 'Unit Kerja tidak boleh lebih dari 100 karakter.',
        ]
      ],
      'tipe_peserta' => [
        'rules' => 'required|in_list[CPNS,PPPK]',
        'errors' => [
          'required' => 'Tipe Peserta harus diisi.',
          'in_list'  => 'Tipe Peserta harus CPNS atau PPPK.',
        ]
      ],
      'lokasi_ujian' => [
        'rules' => 'required',
        'errors' => [
          'required' => 'Lokasi Ujian harus diisi.',
        ]
      ],
      'sesi_ujian' => [
        'rules' => 'required',
        'errors' => [
          'required' => 'Sesi Ujian harus diisi.',
        ]
      ],
    ];

    if (!$this->validate($rules)) {
      return $this
        ->response
        ->setStatusCode(400)
        ->setJSON([
          'error' => true,
          'message'  => $validation->getErrors(),
          'data' => null,
        ]);
    }

    $data = [
      'nama'           => $request->getPost('nama'),
      'nik'            => $request->getPost('nik'),
      'no_peserta'     => $request->getPost('no_peserta'),
      'tempat_lahir'   => $request->getPost('tempat_lahir'),
      'tanggal_lahir'  => $request->getPost('tanggal_lahir'),
      'jenis_kelamin'  => $request->getPost('jenis_kelamin'),
      'alamat'         => $request->getPost('alamat'),
      'pendidikan'     => $request->getPost('pendidikan'),
      'jabatan'        => $request->getPost('jabatan'),
      'unit_kerja'     => $request->getPost('unit_kerja'),
      'tipe_peserta'   => $request->getPost('tipe_peserta'),
      'lokasi_id'      => $request->getPost('lokasi_ujian'),
      'sesi_id'        => $request->getPost('sesi_ujian'),
    ];

    if ($id) {
      $pesertaModel->update($id, $data);
    } else {
      $checkNik = $pesertaModel->where('nik', $request->getPost('nik'))->first();

      if ($checkNik) {
        return $this
          ->response
          ->setStatusCode(400)
          ->setJSON([
            'error' => true,
            'message'  => 'Sudah ada peserta dengan NIK yang sama',
            'data' => null,
          ]);
      }

      $checkNoPeserta = $pesertaModel->where('no_peserta', $request->getPost('no_peserta'))->first();

      if ($checkNoPeserta) {
        return $this
          ->response
          ->setStatusCode(400)
          ->setJSON([
            'error' => true,
            'message'  => 'Sudah ada peserta dengan No Peserta yang sama',
            'data' => null,
          ]);
      }


      $pesertaModel->insert($data);
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

    $pesertaModel = new PesertaModel();

    try {
      $pesertaModel->delete($id);
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
        'message'  => "Sukses menghapus peserta",
        'data' => null,
      ]);
  }

  public function import()
  {
    $file = $this->request->getFile('file_excel');

    if (!$file->isValid() || $file->getClientExtension() !== 'xlsx') {
      return $this
        ->response
        ->setStatusCode(401)
        ->setJSON([
          'error' => true,
          'message'  => 'File tidak valid atau bukan .xlsx',
          'data' => null,
        ]);
    }

    $spreadsheet = IOFactory::load($file->getTempName());
    $sheet = $spreadsheet->getActiveSheet()->toArray();

    $pesertaModel = new PesertaModel();
    $jumlah = 0;

    $duplikat = [];

    foreach ($sheet as $index => $row) {
      if ($index == 0) continue; // Skip header

      if (!$row[0]) {
        break;
      }

      //cek duplikat
      $cekPeserta = $pesertaModel->where('nik', $row[1])->first();
      if ($cekPeserta) {
        $duplikat[] = $row[1];

        continue;
      }

      $ttl = explode(',', $row[3]);

      if (count($ttl) > 1) {
        $tmptLahir = $ttl[0];
        $tglLahirInt = indonesianDateToIntl($ttl[1]);

        $tglLahir = Time::parse($tglLahirInt)->toDateString();
      } else {
        return $this
          ->response
          ->setStatusCode(400)
          ->setJSON([
            'error' => false,
            'message'  => "Format TTL tidak sesuai: $row[3]",
            'data' => null,
          ]);
      }

      $data = [
        'nama'          => trim($row[0]) ?? '',
        'nik'           => trim($row[1]) ?? '',
        'no_peserta'    => trim($row[2]) ?? '',
        'tempat_lahir'  => trim($tmptLahir) ?? '',
        'tanggal_lahir' => trim($tglLahir) ?? '',
        'jenis_kelamin' => trim(substr($row[4], 0, 1)) ?? '',
        'alamat'        => trim($row[5]) ?? '',
        'pendidikan'    => trim($row[6]) ?? '',
        'jabatan'       => trim($row[7]) ?? '',
        'unit_kerja'    => trim($row[8]) ?? '',
        'tipe_peserta'  => trim($row[9]) ?? '',
        'lokasi_id'     => trim($row[10]) ?? '',
      ];

      $pesertaModel->insert($data);
      $jumlah++;
    }

    if (count($duplikat) == 0) {
      return $this
        ->response
        ->setStatusCode(200)
        ->setJSON([
          'error' => false,
          'message'  => 'Sukses mengimport peserta',
          'data' => null,
        ]);
    } else {
      return $this
        ->response
        ->setStatusCode(200)
        ->setJSON([
          'error' => false,
          'message'  => 'Sukses mengimport peserta. duplikasi peserta dengan NIK: ' . join(", ", $duplikat),
          'data' => null,
        ]);
    }
  }

  public function exportExcel()
  {
    $request = $this->request;

    $tipe = $request->getGet('tipe');
    $lokasi = $request->getGet('lokasi');
    $sesi = $request->getGet('sesi');

    $id = empty($request->getGet('id')) ? uniqid() : $request->getGet('id');
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('limit') ?? 100;

    $pesertaModel = new PesertaModel();
    $peserta = $pesertaModel->select(
      new RawSql(
        'peserta.*, 
        CASE 
          WHEN lokasi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian, 
        CASE 
          WHEN sesi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE sesi.nama_sesi
        END AS sesi_ujian'
      )
    )
      ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
      ->join('sesi', 'sesi.id = peserta.sesi_id', 'left');


    if ($tipe != "semua") {
      $peserta->where('peserta.tipe_peserta', $tipe);
    }

    if (session('role') === 'admin' && $lokasi != "semua") {
      $pesertaModel->where('lokasi.id', $lokasi);
    }

    if ($sesi != "semua") {
      $peserta->where('sesi.id', $sesi);
    }

    if (session('role') === 'petugas') {
      $peserta->where('lokasi_id', session('lokasi_id'));
    }

    $total = $peserta->countAllResults(false);
    $peserta = $peserta->findAll($length, $start);

    $currentBatch = intval($start / $length);
    $totalBatch = ceil($total / $length);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $qrOptions = $this->qrOptions;

    // Header
    $headers = [
      "No",
      "Nama",
      "NIK",
      "No Peserta",
      "Tempat Lahir",
      "Tanggal Lahir",
      "Jenis Kelamin",
      "Alamat",
      "Pendidikan",
      "Jabatan",
      "Unit Kerja",
      "Tipe Peserta",
      "Lokasi Ujian",
      "Sesi Ujian",
      "QR Code"
    ];

    $sheet->setTitle(substr($tipe ?? 'Semua', 0, 31));
    $sheet->fromArray($headers, null, 'A1');

    $i = $start + 1;
    $rowNum = 2;
    foreach ($peserta as $p) {
      $qrCode = new QRCode($qrOptions);
      $qrImg = $qrCode->render($p['no_peserta']);

      $base64Data = explode(',', $qrImg)[1];
      $imageBinary = base64_decode($base64Data);
      $image = imagecreatefromstring($imageBinary);

      // Drawing
      $drawing = new MemoryDrawing;
      $drawing->setName("Qr Code $p[nama]");
      $drawing->setDescription("Qr Code $p[nama]");
      $drawing->setImageResource($image);
      $drawing->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
      $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
      $drawing->setHeight(80);
      $drawing->setOffsetX(5);
      $drawing->setOffsetY(5);
      $drawing->setResizeProportional(true);

      $sheet->fromArray([
        $i++,
        $p['nama'],
        $p['nik'],
        $p['no_peserta'],
        $p['tempat_lahir'],
        $p['tanggal_lahir'],
        $p['jenis_kelamin'],
        $p['alamat'],
        $p['pendidikan'],
        $p['jabatan'],
        $p['unit_kerja'],
        $p['tipe_peserta'],
        $p['lokasi_ujian'],
        $p['sesi_ujian'],
      ], null, 'A' . $rowNum);

      $drawing->setCoordinates('O' . $rowNum);
      $drawing->setWorksheet($sheet);

      $sheet->getRowDimension($rowNum)->setRowHeight(65);
      $sheet->getColumnDimension('O')->setWidth(15);
      $sheet->getStyle('A' . $rowNum . ':O' . $rowNum)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

      $rowNum++;
    }

    $startIndex = $start + 1;
    $endIndex = $start + count($peserta);
    $filename = "daftar_peserta_{$tipe}_{$startIndex}-{$endIndex}.xlsx";
    $path = APPPATH . "Storage/Exports/$id";

    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($path . "/$filename");

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message' => "Batch " . ($currentBatch + 1) . " dari $totalBatch selesai.",
        'data' => [
          'id' => $id,
          'filename' => $filename,
          'currentBatch' => $currentBatch + 1,
          'totalBatch' => $totalBatch,
          'nextStart' => $start + $length
        ]
      ]);
  }

  public function exportPdf()
  {
    $request = $this->request;

    $tipe = $request->getGet('tipe');
    $lokasi = $request->getGet('lokasi');
    $sesi = $request->getGet('sesi');

    $id = empty($request->getGet('id')) ? uniqid() : $request->getGet('id');
    $start = $request->getGet('start') ?? 0;
    $length = $request->getGet('limit') ?? 100;

    $pesertaModel = new PesertaModel();
    $peserta = $pesertaModel->select(
      new RawSql(
        'peserta.*, 
        CASE 
          WHEN lokasi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE lokasi.nama_lokasi 
        END AS lokasi_ujian,
                CASE 
          WHEN sesi.id IS NULL 
          THEN "Tidak Ditetapkan" 
          ELSE sesi.nama_sesi
        END AS sesi_ujian'
      )
    )
      ->join('lokasi', 'lokasi.id = peserta.lokasi_id', 'left')
      ->join('sesi', 'sesi.id = peserta.sesi_id', 'left');


    if (session('role') === 'petugas') {
      $peserta->where('lokasi_id', session('lokasi_id'));
    }

    if ($tipe != "semua") {
      $peserta->where('peserta.tipe_peserta', $tipe);
    }

    if (session('role') === 'admin' && $lokasi != "semua") {
      $pesertaModel->where('lokasi.id', $lokasi);
    }

    if ($sesi != "semua") {
      $peserta->where('sesi.id', $sesi);
    }

    $total = $peserta->countAllResults(false);
    $peserta = $peserta->findAll($length, $start);

    $currentBatch = intval($start / $length);
    $totalBatch = ceil($total / $length);

    $i = $start + 1;
    $data = [];
    foreach ($peserta as $p) {
      $data[] = [
        'no'             => $i++,
        'nama'           => $p['nama'],
        'nik'            => $p['nik'],
        'no_peserta'     => $p['no_peserta'],
        'tempat_lahir'   => $p['tempat_lahir'],
        'tanggal_lahir'  => $p['tanggal_lahir'],
        'jenis_kelamin'  => $p['jenis_kelamin'],
        'alamat'         => $p['alamat'],
        'pendidikan'     => $p['pendidikan'],
        'jabatan'        => $p['jabatan'],
        'unit_kerja'     => $p['unit_kerja'],
        'tipe_peserta'   => $p['tipe_peserta'],
        'lokasi_ujian'   => $p['lokasi_ujian'],
        'sesi_ujian'     => $p['sesi_ujian']
      ];
    }

    $html = view('admin/pdf/peserta', ['peserta' => $data]);

    $mpdf = new Mpdf(['format' => 'A4-L']);
    $mpdf->WriteHTML($html);

    $startIndex = $start + 1;
    $endIndex = $start + count($peserta);

    $filename = "daftar_peserta_{$tipe}_{$startIndex}-{$endIndex}.pdf";
    $path = APPPATH . "Storage/Exports/$id";

    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }

    $mpdf->Output($path . "/$filename", 'F');

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message' => "Batch " . ($currentBatch + 1) . " dari $totalBatch selesai.",
        'data' => [
          'id' => $id,
          'filename' => $filename,
          'currentBatch' => $currentBatch + 1,
          'totalBatch' => $totalBatch,
          'nextStart' => $start + $length
        ]
      ]);
  }

  public function getImportTemplate()
  {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Template Peserta');

    $style = [
      'font' => [
        'bold' => true,
      ],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
          'rgb' => 'FFFF00',
        ],
      ],
      'borders' => [
        'allBorders' => [
          'borderStyle' => Border::BORDER_THIN,
          'color' => ['argb' => '000000'],
        ],
      ],
      'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
      ],
    ];

    // ============================
    // HEADER TABEL PESERTA
    // ============================
    $headers = [
      'nama',
      'nik',
      'no_peserta',
      'tempat_tgl_lahir',
      'jenis_kelamin',
      'alamat',
      'pendidikan',
      'jabatan',
      'unit_kerja',
      'tipe_peserta',
      'id_lokasi',
      'id_sesi'
    ];

    $sheet->fromArray($headers, null, 'A1');
    $sheet->getStyle('A1:L1')->applyFromArray($style);

    // ============================
    // CONTOH ISI
    // ============================
    $sheet->fromArray([
      [
        'BUDI SANTOSO',
        '3175091201890001',
        'PS001',
        'JAKARTA, 16 Mei 2006',
        'LAKI-LAKI',
        'Jl. Melati No. 1',
        'S1 Teknik Informatika',
        'Programmer',
        'Diskominfo',
        'CPNS',
        '2',
        '1'
      ],
      [
        'SITI AMINAH',
        '3175091201890002',
        'PS002',
        'BANDUNG, 16 Mei 2006',
        'PEREMPUAN',
        'Jl. Kenanga No. 5',
        'S1 Akuntansi',
        'Akuntan',
        'BPKAD',
        'PPPK',
        '3',
        '1'
      ],
    ], null, 'A2');

    // Catatan kolom tipe_peserta
    $sheet->setCellValue('M2', 'Contoh penulisan untuk CPNS');
    $sheet->setCellValue('M3', 'Contoh penulisan untuk PPPK');

    // ============================
    // TABEL LOKASI (ambil dari DB)
    // ============================
    $lokasi = (new LocationModel())->findAll();

    // Header
    $sheet->setCellValue('O1', 'Tabel Lokasi');
    $sheet->setCellValue('O2', 'Id');
    $sheet->setCellValue('P2', 'Nama Lokasi');

    $row = 3;
    foreach ($lokasi as $lok) {
      $sheet->setCellValue('O' . $row, $lok['id']);
      $sheet->setCellValue('P' . $row, $lok['nama_lokasi']);
      $row++;
    }

    $sheet->getStyle('O' . $row - 1 - count($lokasi) . ':P' . $row - 1  - count($lokasi))->applyFromArray($style);

    // ============================
    // TABEL SESI (ambil dari DB)
    // ============================
    $sesi = (new SesiModel())->findAll();

    // Header
    $row++;
    $sheet->setCellValue('O' . $row, 'Tabel Sesi');
    $row++;
    $sheet->setCellValue('O' . $row, 'Id');
    $sheet->setCellValue('P' . $row, 'Nama Sesi');
    $sheet->setCellValue('Q' . $row, 'Tanggal');
    $sheet->setCellValue('R' . $row, 'Jam Mulai');
    $sheet->setCellValue('S' . $row, 'Jam Selesai');

    foreach ($sesi as $s) {
      $row++;

      $sheet->setCellValue('O' . $row, $s['id']);
      $sheet->setCellValue('P' . $row, $s['nama_sesi']);
      $sheet->setCellValue('Q' . $row, Time::parse($s['waktu_mulai'])->format('d-m-Y'));
      $sheet->setCellValue('R' . $row, Time::parse($s['waktu_mulai'])->format('H:i'));
      $sheet->setCellValue('S' . $row, Time::parse($s['waktu_selesai'])->format('H:i'));
    }

    $sheet->getStyle('O' . $row - count($sesi) . ':S' . $row - count($sesi))->applyFromArray($style);

    // ============================
    // OUTPUT FILE
    // ============================
    $filename = 'template_import_peserta.xlsx';
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $writer->save('php://output');
    exit;
  }

  public function zipExport()
  {
    $request = $this->request;

    $id = $request->getGet('id');
    $format = $request->getGet('format');
    $tipe = $request->getGet('tipe');

    $zip = new ZipArchive();
    $zipFolder = FCPATH . "exports/$id";
    $zipName = "daftar_peserta_{$tipe}_{$format}.zip";
    $zipPath = "$zipFolder/$zipName";

    if (!is_dir($zipFolder)) {
      mkdir($zipFolder, 0777, true);
    }

    if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
      return $this
        ->response
        ->setStatusCode(500)
        ->setJSON([
          'error' => false,
          'message' => "Gagal membuat file ZIP",
          'data' => null
        ]);
    }

    $folder = APPPATH . "Storage/Exports/$id";

    if ($format == 'excel') {
      $files = glob($folder . '/*.xlsx');
    } else {
      $files = glob($folder . '/*.pdf');
    }

    foreach ($files as $file) {
      $basename = basename($file);
      $zip->addFile($file, $basename);
    }

    $zip->close();

    foreach ($files as $file) {
      unlink($file);
    }

    rmdir($folder);

    return $this
      ->response
      ->setStatusCode(200)
      ->setJSON([
        'error' => false,
        'message' => "Sukses",
        'data' => ['zipUrl' => base_url("exports/$id/$zipName")]
      ]);
  }
}
