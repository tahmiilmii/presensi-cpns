<!DOCTYPE html>
<html>

<head>
  <style>
    body {
      font-family: sans-serif;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      font-size: 11px;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 5px;
      text-align: left;
    }

    th {
      background-color: #eee;
    }

    h2 {
      text-align: center;
    }
  </style>
</head>

<body>

  <h2>Daftar Peserta</h2>

  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama</th>
        <th>NIK</th>
        <th>No Peserta</th>
        <th>Tempat Lahir</th>
        <th>Tanggal Lahir</th>
        <th>JK</th>
        <th>Alamat</th>
        <th>Pendidikan</th>
        <th>Jabatan</th>
        <th>Unit Kerja</th>
        <th>Tipe Peserta</th>
        <th>Lokasi Ujian</th>
        <th>Sesi Ujian</th>
        <th>QR Code</th>
      </tr>
    </thead>
    <tbody>
      <?php

      use chillerlan\QRCode\QRCode;
      use chillerlan\QRCode\QROptions;
      use chillerlan\QRCode\Output\QROutputInterface;

      $options = new QROptions([
        'outputType'      => QROutputInterface::GDIMAGE_PNG,
        'eccLevel'        => QRCode::ECC_M,
        'scale'           => 20,
        'quietzoneSize'   => 2,
      ]);

      foreach ($peserta as $p): ?>
        <tr>
          <td><?= $p['no'] ?></td>
          <td><?= esc($p['nama']) ?></td>
          <td><?= esc($p['nik']) ?></td>
          <td><?= esc($p['no_peserta']) ?></td>
          <td><?= esc($p['tempat_lahir']) ?></td>
          <td><?= esc($p['tanggal_lahir']) ?></td>
          <td><?= esc($p['jenis_kelamin']) ?></td>
          <td><?= esc($p['alamat']) ?></td>
          <td><?= esc($p['pendidikan']) ?></td>
          <td><?= esc($p['jabatan']) ?></td>
          <td><?= esc($p['unit_kerja']) ?></td>
          <td><?= esc($p['tipe_peserta']) ?></td>
          <td><?= esc($p['lokasi_ujian']) ?></td>
          <td><?= esc($p['sesi_ujian']) ?></td>
          <td>
            <img src="<?= (new QRCode($options))->render($p['no_peserta']) ?>" style="width: 80px;">
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

</body>

</html>