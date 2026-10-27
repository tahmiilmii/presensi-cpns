<!DOCTYPE html>
<html>

<head>
  <title>Data Presensi</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 12px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      border: 1px solid #333;
      padding: 6px;
      text-align: left;
    }
  </style>
</head>

<body>
  <h2 style="text-align: center;">Data Presensi Tahun <?= date('Y') ?></h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Peserta</th>
        <th>Jenis Peserta</th>
        <th>Waktu Presensi</th>
        <th>Status</th>
        <th>Lokasi</th>
        <th>Validator</th>
      </tr>
    </thead>
    <tbody>
      <?php

      use CodeIgniter\I18n\Time;

      $no = 1;
      foreach ($data as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $row['nama_peserta'] ?></td>
          <td><?= $row['tipe_peserta'] ?></td>
          <td><?= esc($row['waktu_presensi']) ?></td>
          <td><?= esc($row['status']) ?></td>
          <td><?= esc($row['lokasi']) ?></td>
          <td><?= esc($row['validator'] ?? '-') ?></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <table width="100%" style="margin-top: 50px; text-align: center;">
    <tr>
      <td width="33%">
        <p>Mengetahui,</p>
        <p><strong>Ketua Panitia</strong></p>
        <br><br><br><br>
        <p><u>........................................</u></p>
        <p>NIP. ....................................</p>
      </td>
      <td width="33%"></td>
      <td width="33%">
        <p>......................., <?= Time::now()->toLocalizedString('dd MMMM YYYY') ?></p>
        <p><strong>Petugas</strong></p>
        <br><br><br><br>
        <?php if ($petugas): ?>
          <p><u><?= $petugas['fullname'] ?></u></p>
          <p>NIP. <?= $petugas['nip'] ?></p>
        <?php else: ?>
          <p><u>........................................</u></p>
          <p>NIP. ....................................</p>
        <?php endif ?>
      </td>
    </tr>
  </table>

</body>

</html>