<?= $this->extend('_layouts/admin'); ?>

<?= $this->section('title'); ?>
Peserta | Admin Presensi CPNS/P3K
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Peserta</h1>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="card w-100 shadow mb-4">
      <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-app-primary">Daftar Master Peserta</h6>

          <div class="d-flex" style="gap: 8px">
            <button class="btn m-0 btn-sm btn-success" id="tambah-peserta-btn">
              <i class="fas fa-plus"></i>
              &nbsp;Tambah
            </button>
            <button class="btn m-0 btn-sm btn-primary" data-toggle="modal" data-target="#modalImport">
              <i class="fas fa-file-import"></i>
              &nbsp;Import
            </button>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i>
                &nbsp;Ekspor
              </button>
              <div class="dropdown-menu">
                <button class="dropdown-item" id="export-excel">
                  Excel
                </button>
                <button class=" dropdown-item" id="export-pdf">
                  PDF
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="row" id="filter-peserta">
          <div class="col-md">
            <label>Tipe Peserta</label>
            <select class="form-control" id="tipe-peserta">
              <option value="semua">SEMUA</option>
              <option value="cpns">CPNS</option>
              <option value="pppk">PPPK</option>
            </select>
          </div>
          <?php if (session()->get('role') == 'admin'): ?>
            <div class="col-md">
              <label>Lokasi</label>
              <select class="form-control" id="lokasi-peserta">
                <option value="semua">SEMUA</option>
                <?php foreach ($lokasiUjian as $lokasi) : ?>
                  <option value="<?= $lokasi['id'] ?>"><?= $lokasi['nama_lokasi'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          <?php endif ?>
          <div class="col-md">
            <label>Sesi</label>
            <select class="form-control" id="sesi-peserta">
              <option value="semua">SEMUA</option>
              <?php foreach ($sesiUjian as $sesi) : ?>
                <option value="<?= $sesi['id'] ?>"><?= $sesi['nama_sesi'] ?> </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <hr>

        <div class="table-responsive">
          <table class="table table-sm table-striped table-bordered w-100" id="table-peserta">
            <thead class="bg-primary text-white">
              <tr>
                <th>No</th>
                <th>No Peserta</th>
                <th>Nama/NIK</th>
                <th>Tempat, Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Alamat</th>
                <th>Pendidikan</th>
                <th>Jabatan</th>
                <th>Unit Kerja</th>
                <th>Tipe Peserta</th>
                <th>Lokasi Ujian</th>
                <th>Sesi Ujian</th>
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

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalPeserta" tabindex="-1" role="dialog" aria-labelledby="modalPesertaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="formPeserta">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPesertaLabel">Tambah Peserta</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">

          <div class="form-row">
            <div class="form-group col-md">
              <label>Nama</label>
              <input type="text" class="form-control" name="nama" id="nama" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>NIK</label>
              <input type="text" class="form-control" name="nik" id="nik" required>
            </div>
            <div class="form-group col-md-6">
              <label>No Peserta</label>
              <input type="text" class="form-control" name="no_peserta" id="no_peserta" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Tempat Lahir</label>
              <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" required>
            </div>
            <div class="form-group col-md-3">
              <label>Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
            </div>
            <div class="form-group col-md-3">
              <label>Jenis Kelamin</label>
              <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Alamat</label>
            <textarea class="form-control" name="alamat" id="alamat" required></textarea>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Pendidikan</label>
              <input type="text" class="form-control" name="pendidikan" id="pendidikan" required>
            </div>
            <div class="form-group col-md-6">
              <label>Jabatan</label>
              <textarea type="text" class="form-control" name="jabatan" id="jabatan" required></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Unit Kerja</label>
              <textarea class="form-control" name="unit_kerja" id="unit_kerja" required></textarea>
            </div>
            <div class="form-group col-md-6">
              <label>Tipe Peserta</label>
              <select class="form-control" name="tipe_peserta" id="tipe_peserta" required>
                <option value="CPNS">CPNS</option>
                <option value="PPPK">PPPK</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md">
              <label>Lokasi Ujian</label>
              <select class="form-control" name="lokasi_ujian" id="lokasi_ujian" required>
                <option value="" disabled selected>Pilih Lokasi Ujian</option>
                <?php

                use CodeIgniter\I18n\Time;

                foreach ($lokasiUjian as $lokasi) : ?>
                  <option value="<?= $lokasi['id'] ?>"><?= $lokasi['nama_lokasi'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group col-md">
              <label>Sesi Ujian</label>
              <select class="form-control" name="sesi_ujian" id="sesi_ujian" required>
                <option value="" disabled selected>Pilih Sesi Ujian</option>
                <?php foreach ($sesiUjian as $sesi) : ?>
                  <option value="<?= $sesi['id'] ?>"><?= $sesi['nama_sesi'] ?> (<?= Time::parse($sesi['waktu_mulai'])->format('d-m-Y') ?>) Jam <?= Time::parse($sesi['waktu_mulai'])->format('H:i') ?> - <?= Time::parse($sesi['waktu_selesai'])->format('H:i') ?> </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>


        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Import Peserta -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalPesertaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formImport" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Import Peserta dari Excel</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info" role="alert">
            <p class="m-0">
              Untuk menggunakan fitur import, harap gunakan format excel yang sudah disediakan.<br>
              <a href="<?= url_to('admin.peserta.get-import-template') ?>">
                <i class="fas fa-file-download"></i>
                Unduh disini
              </a>
            </p>
          </div>

          <div class="form-group">
            <label>Pilih File Excel (.xlsx)</label>
            <input type="file" name="file_excel" accept=".xlsx" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Import</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Qr Code Peserta -->
<div class="modal fade" id="modalQr" tabindex="-1" role="dialog" aria-labelledby="modalPesertaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="formImport" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Qr Code Peserta</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div>
            <img class="img-fluid rounded mx-auto d-block border" id="img-qrcode" style="width: 500px;">
          </div>
          <button class="btn btn-success mt-3 mx-auto d-block" id="download-qrcode">
            <i class="fas fa-download"></i>
            &nbsp;Unduh Qr Code
          </button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
  $(document).ready(() => {
    initTablePeserta();

    $('#filter-peserta')
      .find('input, select')
      .on('change', function() {
        initTablePeserta();
      });

    select2('#lokasi_ujian', {
      placeholder: 'Pilih Lokasi Ujian',
      dropdownParent: $('#modalPeserta'),
    });

    $('#tambah-peserta-btn').click(function() {
      $('#formPeserta')[0].reset();
      $('#id').val('');

      $('#modalPesertaLabel').text('Tambah Peserta');
      $('#modalPeserta').find('.form-control').removeClass('is-invalid');
      $('#modalPeserta').find('.invalid-feedback').remove();
      $('#modalPeserta').modal('show');
    });

    $('#formPeserta').submit(function(e) {
      e.preventDefault();
      $('#modalPeserta').find('.form-control').removeClass('is-invalid');
      $('#modalPeserta').find('.invalid-feedback').remove();

      const formData = $(this).serialize();

      $.ajax({
        url: "<?= url_to('admin.peserta.save') ?>",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(res) {
          if (!res.error) {
            $('#modalPeserta').modal('hide');
            initTablePeserta();
            showToast('success', 'Sukses', 'Data peserta berhasil disimpan.');
          } else {
            showToast('error', 'Gagal', 'Gagal menyimpan data.');
          }
        },
        error: function(xhr) {
          const res = xhr.responseJSON;
          const message = res.message;

          if ((typeof message) == 'string') {
            Swal.fire('Gagal!', message, 'error');
          } else {
            $.each(message, function(fieldName, errorMessage) {
              const input = $(`[name=${fieldName}]`);
              input.addClass('is-invalid');

              // Cek apakah sudah ada feedback
              if (input.next('.invalid-feedback').length === 0) {
                input.after('<div class="invalid-feedback">' + errorMessage + '</div>');
              } else {
                input.next('.invalid-feedback').text(errorMessage);
              }
            });
          }
        }
      });
    });

    $('#formImport').submit(function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      $.ajax({
        url: "<?= url_to('admin.peserta.import') ?>",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        beforeSend: () => {
          Swal.showLoading();
        },
        success: function(res) {
          Swal.close();
          if (!res.error) {
            $('#modalImport').modal('hide');
            $('#formImport')[0].reset();

            initTablePeserta();

            Swal.fire('Sukses!', res.message, 'success');
          } else {
            Swal.fire('Gagal!', res.message, 'error');
          }
        },
        error: function(xhr) {
          const response = xhr.responseJSON;
          Swal.fire('Gagal!', response.message ?? 'Terjadi kesalahan saat mengunggah.', 'error');
        }
      });
    });

    $('#export-excel').click(() => {
      let id = null;
      let start = 0;
      let limit = 100;
      let totalBatch = null;
      let batchDone = 0;

      const data = dataFilter();

      Swal.fire({
        title: 'Export sedang diproses',
        html: 'Sedang menjalankan batch export...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      runBatch();

      function runBatch() {
        $.ajax({
          url: "<?= url_to('admin.peserta.export-excel') ?>",
          method: 'GET',
          data: {
            id: id,
            start: start,
            limit: limit,
            ...data,
          },
          success: function(res) {
            id = res.data.id;
            totalBatch = res.data.totalBatch;
            batchDone = res.data.currentBatch;

            Swal.update({
              html: `<b>Export batch ${batchDone} dari ${totalBatch}...</b>`,
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            if (batchDone < totalBatch) {
              start = res.data.nextStart;

              runBatch();
            } else {
              zipAllExports(id, 'excel');
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Export Gagal!',
              text: `Gagal saat memproses batch ke ${batchDone + 1}`
            });
          }
        });
      }
    });

    $('#export-pdf').click(() => {
      let id = null;
      let start = 0;
      let limit = 100;
      let totalBatch = null;
      let batchDone = 0;

      const data = dataFilter();

      Swal.fire({
        title: 'Export sedang diproses',
        html: 'Sedang menjalankan batch export...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      runBatch();

      function runBatch() {
        $.ajax({
          url: "<?= url_to('admin.peserta.export-pdf') ?>",
          method: 'GET',
          data: {
            id: id,
            start: start,
            limit: limit,
            ...data,
          },
          success: function(res) {
            id = res.data.id;
            totalBatch = res.data.totalBatch;
            batchDone = res.data.currentBatch;

            Swal.update({
              html: `<b>Export batch ${batchDone} dari ${totalBatch}...</b>`,
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => {
                Swal.showLoading();
              }
            });

            if (batchDone < totalBatch) {
              start = res.data.nextStart;

              runBatch();
            } else {
              zipAllExports(id, 'pdf');
            }
          },
          error: function() {
            Swal.fire({
              icon: 'error',
              title: 'Export Gagal!',
              text: `Gagal saat memproses batch ke ${batchDone + 1}`
            });
          }
        });
      }
    });

  });

  function zipAllExports(id, format) {
    const data = dataFilter();

    $.ajax({
      url: '<?= url_to('admin.peserta.download-export-zip') ?>',
      data: {
        id: id,
        format: format,
        ...data,
      },
      success: function(res) {
        Swal.fire({
          icon: 'success',
          title: 'Export Selesai!',
          html: `Download akan dimulai otomatis...`,
          showConfirmButton: false,
          timer: 1000,
          didClose: () => {
            triggerDownload(res.data.zipUrl);
          }
        });
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'ZIP Gagal',
          text: res.message
        });
      }
    });
  }

  function dataFilter() {
    return {
      tipe: $('#tipe-peserta').val(),
      sesi: $('#sesi-peserta').val(),
      lokasi: $('#lokasi-peserta').val()
    }
  }

  function initTablePeserta() {
    const tbl = $('#table-peserta');

    if ($.fn.DataTable.isDataTable(tbl)) {
      tbl.DataTable().ajax.reload(false, null);
    } else {
      tbl.DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [
          [1, 'asc']
        ],
        ajax: {
          url: `<?= url_to('admin.peserta.list') ?>`,
          data: (d1) => {
            const d2 = dataFilter();

            return {
              ...d1,
              ...d2
            }
          },
          type: "GET",
        },
        columnDefs: [{
          width: "5%",
          orderable: false,
          targets: [0, -1]
        }],
      });
    }
  }

  function editPeserta(id) {
    $.ajax({
      url: "<?= base_url('admin/peserta/get') ?>/" + id,
      type: "GET",
      dataType: "json",
      success: function(response) {
        const data = response.data;

        // Isi data ke form
        $('#id').val(data.id);
        $('#nama').val(data.nama);
        $('#nik').val(data.nik);
        $('#no_peserta').val(data.no_peserta);
        $('#tempat_lahir').val(data.tempat_lahir);
        $('#tanggal_lahir').val(data.tanggal_lahir);
        $('#jenis_kelamin').val(data.jenis_kelamin).trigger('change');
        $('#alamat').val(data.alamat);
        $('#pendidikan').val(data.pendidikan);
        $('#jabatan').val(data.jabatan);
        $('#unit_kerja').val(data.unit_kerja);
        $('#tipe_peserta').val(data.tipe_peserta).trigger('change');
        $('#lokasi_ujian').val(data.lokasi_id).trigger('change');
        $('#sesi_ujian').val(data.sesi_id).trigger('change');

        // Ganti judul modal
        $('#modalTitle').text('Edit Peserta');

        // Tampilkan modal
        $('#modalPeserta').find('.form-control').removeClass('is-invalid');
        $('#modalPeserta').find('.invalid-feedback').remove();
        $('#modalPeserta').modal('show');
      },
      error: function(xhr) {
        const data = xhr.responseJSON;

        showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data peserta.');
      }
    });
  }

  function deletePeserta(id) {
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
          url: "<?= base_url('admin/peserta/delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          success: function(res) {
            if (!res.error) {
              initTablePeserta();
              showToast('success', 'Sukses', 'Peserta telah dihapus.');
            } else {
              showToast('error', 'Gagal', res.message ?? 'Terjadi kesalahan saat menghapus data.');
            }
          }
        });
      }
    });
  }

  function getQrPeserta(id) {
    $.ajax({
      url: "<?= base_url('admin/peserta/get-qrcode') ?>/" + id,
      type: "GET",
      dataType: "json",
      success: function(response) {
        const data = response.data;

        $('#img-qrcode').prop('src', data.qrcode);
        $('#download-qrcode').off('click').click((e) => {
          e.preventDefault();
          e.stopImmediatePropagation();

          base64ToBlobAndDownload(data.qrcode, data.filename, 'image/png');
        });

        // Tampilkan modal
        $('#modalQr').modal('show');
      },
      error: function(xhr) {
        const data = xhr.responseJSON;

        showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data peserta.');
      }
    });
  }
</script>
<?= $this->endSection(); ?>