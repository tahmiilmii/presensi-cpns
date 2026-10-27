<?php

use CodeIgniter\I18n\Time;
?>
<?= $this->extend('_layouts/admin'); ?>

<?= $this->section('title'); ?>
Presensi | Admin Presensi CPNS/P3K
<?= $this->endSection(); ?>

<?= $this->section('css'); ?>
<style>
  #modal-dialog {
    max-width: 100%;
  }

  @media (min-width: 992px) {
    #modal-dialog {
      max-width: 1200px;
    }
  }
</style>
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Presensi</h1>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="card w-100 shadow mb-4">
      <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-app-primary">Daftar Presensi Peserta</h6>

          <div class="d-flex" style="gap: 8px">
            <button class="btn m-0 btn-sm btn-primary" data-toggle="modal" data-target="#verifikasiModal">
              <i class="fas fa-camera"></i>
              &nbsp;Verifikasi
            </button>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i>
                &nbsp;Ekspor
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" target="_blank" id="export-excel" href="<?= url_to('admin.presensi.export-excel') ?>">
                  Excel
                </a>
                <a class="dropdown-item" target="_blank" id="export-pdf" href="<?= url_to('admin.presensi.export-pdf') ?>">
                  PDF
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row" id="filter-presensi">
          <div class="col-md">
            <label>Tipe Peserta</label>
            <select class="form-control" id="tipe-peserta">
              <option value="semua">SEMUA</option>
              <option value="cpns">CPNS</option>
              <option value="pppk">PPPK</option>
            </select>
          </div>
          <div class="col-md">
            <label>Status Peserta</label>
            <select class="form-control" id="status-peserta">
              <option value="hadir" selected>Hadir</option>
              <option value="tidak-hadir">Tidak Hadir</option>
            </select>
          </div>
          <div class="col-md">
            <label>Tanggal Presensi</label>
            <input class="form-control" type="date" id="tgl-presensi" value="<?= Time::now()->format('Y-m-d') ?>">
          </div>
          <div class="col-md">
            <div class="row">
              <div class="col-md">
                <label>Jam Presensi Mulai</label>
                <input class="form-control" type="time" id="jam-presensi-mulai" value="<?= Time::now()->subHours(1)->format('H:i') ?>">
              </div>
              <div class="col-md">
                <label>Jam Presensi Akhir</label>
                <input class="form-control" type="time" id="jam-presensi-akhir" value="<?= Time::now()->addHours(1)->format('H:i') ?>">
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="table-responsive">
          <table class="table table-sm table-striped table-bordered w-100" id="table-presensi">
            <thead class="bg-primary text-white">
              <tr>
                <th>No</th>
                <th>Peserta</th>
                <th>Waktu Presensi</th>
                <th>Status</th>
                <th>Validasi Oleh</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Verifikasi-->
<div class="modal fade" id="verifikasiModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" id="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Scan Qr Code</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row" style="gap: 12px;">
          <div class="col-md-6">
            <div id="reader" width="600px"></div>
          </div>
          <div class="col-md-5">
            <input id="id-verifikasi" type="hidden">
            <table class="w-100 table table-bordered">
              <tr>
                <td>Instansi</td>
                <td id="instansi-peserta">-</td>
              </tr>
              <tr>
                <td>NIK</td>
                <td id="nik-peserta">-</td>
              </tr>
              <tr>
                <td>Nomor Peserta</td>
                <td id="no-peserta"><strong>-</strong></td>
              </tr>
              <tr>
                <td>Nama</td>
                <td id="nama-peserta"><strong>-</strong></td>
              </tr>
              <tr>
                <td>Jenis Kelamin</td>
                <td id="jk-peserta">-</td>
              </tr>
              <tr>
                <td>Tempat / Tanggal Lahir</td>
                <td id="ttl-peserta">-</td>
              </tr>
              <tr>
                <td>Kualifikasi Pendidikan</td>
                <td id="pendidikan-peserta">-</td>
              </tr>
              <tr>
                <td>Formasi Jabatan</td>
                <td id="formasi-peserta">-</td>
              </tr>
              <tr>
                <td>Sesi Ujian</td>
                <td id="sesi-ujian">-</td>
              </tr>
            </table>
            <?php if (session()->get('role') == 'admin'): ?>
              <div class="form-group p-3 border">
                <label>Lokasi Ujian</label>
                <select class="form-control" id="lokasi_ujian" required>
                  <option value="" disabled selected>Pilih Lokasi Ujian</option>
                  <?php foreach ($lokasiUjian as $lokasi) : ?>
                    <option value="<?= $lokasi['id'] ?>"><?= $lokasi['nama_lokasi'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endif ?>
            <button id="verifikasi-peserta" class="btn btn-primary btn-block">
              <i class="fas fa-check"></i>
              Verifikasi Peserta
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
  let scanner = null;

  $(document).ready(() => {
    initTablePresensi();

    select2('#lokasi_ujian', {
      placeholder: 'Pilih Lokasi Ujian',
      dropdownParent: $('#verifikasiModal'),
    });

    $('#filter-presensi')
      .find('input, select')
      .on('change', function() {
        initTablePresensi();

        const data = dataFilter();
        const query = `?tipe=${data.tipe}&status=${data.status}` +
          `&tgl_presensi=${data.tgl_presensi}&jam_presensi_mulai=${data.jam_presensi_mulai}` +
          `&jam_presensi_akhir=${data.jam_presensi_akhir}`;

        const exportExcelUrl = "<?= url_to('admin.presensi.export-excel') ?>" + query;
        const exportPdfUrl = "<?= url_to('admin.presensi.export-pdf') ?>" + query;

        $('#export-excel').prop("href", exportExcelUrl);
        $('#export-pdf').prop("href", exportPdfUrl);
      });


    $('#verifikasiModal').on('hide.bs.modal', () => {
      scanner.clear().catch(error => {
        console.warn("Gagal menghentikan scanner:", error);
      });
    }).on('shown.bs.modal', () => {
      initScanner();
    });

    $('#verifikasi-peserta').click(function() {
      const btn = $(this);

      $.ajax({
        url: '<?= url_to('admin.presensi.save') ?>',
        method: "POST",
        data: {
          id: $('#id-verifikasi').val(),
          lokasiUjian: $('#lokasi_ujian').val(),
        },
        beforeSend: () => {
          btn.prop('disabled', true).html(`
          <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          &nbsp;Memverifikasi...
          `);
        },
        success: (res) => {
          Swal.fire({
            title: 'Sukses',
            text: res.message,
            icon: 'success',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ok',
            timer: 1500,
          });

          initTablePresensi();
        },
        error: (xhr) => {
          const res = xhr.responseJSON;

          Swal.fire({
            title: 'Gagal',
            text: res.message,
            icon: 'error',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ok',
            timer: 3000
          });
        }
      }).always(() => {
        btn.prop('disabled', false).html('<i class="fas fa-check"></i> &nbsp;Verifikasi Peserta');
      });
    });
  });

  function initScanner() {
    scanner = new Html5QrcodeScanner(
      "reader", {
        fps: 10,
        qrbox: {
          width: 200,
          height: 200
        }
      }, false);

    scanner.render((decodedText, decodedResult) => {
      $.ajax({
        url: `<?= base_url('admin/peserta/get-by-no/') ?>${decodedText}`,
        method: "GET",
        beforeSend: () => {
          Swal.showLoading();
        },
        success: function(res) {
          Swal.close();
          const data = res.data;

          $('#id-verifikasi').val(data.id);
          $('#instansi-peserta').text(data.instansi);
          $('#nik-peserta').text(data.nik);
          $('#no-peserta').text(data.no_peserta);
          $('#nama-peserta').text(data.nama);
          $('#jk-peserta').text(data.jk);
          $('#ttl-peserta').text(data.ttl);
          $('#pendidikan-peserta').text(data.pendidikan);
          $('#formasi-peserta').text(data.jabatan);
          $('#sesi-ujian').text(data.sesi_ujian);
        },
        error: function(xhr) {
          const response = xhr.responseJSON;
          Swal.fire('Gagal!', response.message ?? 'Terjadi kesalahan', 'error');
        }
      });
    });
  }

  function dataFilter() {
    return {
      tipe: $('#tipe-peserta').val(),
      status: $('#status-peserta').val(),
      tgl_presensi: $('#tgl-presensi').val(),
      jam_presensi_mulai: $('#jam-presensi-mulai').val(),
      jam_presensi_akhir: $('#jam-presensi-akhir').val(),
    }
  }

  function initTablePresensi() {
    const tbl = $('#table-presensi');

    if ($.fn.DataTable.isDataTable(tbl)) {
      tbl.DataTable().ajax.reload(false, null);
    } else {
      tbl.DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
          url: `<?= url_to('admin.presensi.list') ?>`,
          type: "GET",
          data: (d1) => {
            const d2 = dataFilter();

            return {
              ...d1,
              ...d2
            }
          }
        },
        columnDefs: [{
          width: "5%",
          orderable: false,
          targets: [0, -1]
        }],
      });
    }
  }

  function deletePresensi(id) {
    Swal.fire({
      title: 'Yakin mau hapus?',
      text: "Data yang dihapus tidak bisa dikembalikan!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= base_url('admin/presensi/delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          success: function(res) {
            if (!res.error) {
              initTablePresensi();
              showToast('success', 'Sukses', res.message);
            } else {
              showToast('error', 'Gagal', res.message ?? 'Terjadi kesalahan saat menghapus data.');
            }
          }
        });
      }
    });
  }
</script>
<?= $this->endSection(); ?>