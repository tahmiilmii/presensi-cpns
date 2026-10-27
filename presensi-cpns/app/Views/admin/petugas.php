<?= $this->extend('_layouts/admin'); ?>

<?= $this->section('title'); ?>
Petugas | Admin Presensi CPNS/P3K
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Petugas</h1>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="card w-100 shadow mb-4">
      <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-app-primary">Daftar Petugas</h6>

          <div class="d-flex" style="gap: 8px">
            <button class="btn m-0 btn-sm btn-success" id="tambah-petugas-btn">
              <i class="fas fa-plus"></i>
              &nbsp;Tambah
            </button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped table-bordered w-100" id="table-petugas">
            <thead class="bg-primary text-white">
              <tr>
                <th>No</th>
                <th>Nama Petugas</th>
                <th>Nama Pengguna</th>
                <th>Lokasi Pengawasan</th>
                <th>Ditambahkan Tanggal</th>
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
<div class="modal fade" id="modalPetugas" tabindex="-1" role="dialog" aria-labelledby="modalPetugasLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formPetugas">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalPetugasLabel">Tambah Petugas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="username">Nama Petugas <span class="text-danger">*</span></label>
            <input type="text" name="fullname" id="fullname" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="username">Nama Pengguna / Username <span class="text-danger">*</span></label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="nip">NIP <span class="text-danger">*</span></label>
            <input type="text" name="nip" id="nip" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="password">Password<span class="text-danger">*</span></label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
          <div class="form-group">
            <label for="location">Lokasi Pengawasan</label>
            <select name="location" id="location" class="form-control">
              <option value="" selected disabled>Pilih Lokasi</option>
              <?php foreach ($lokasiUjian as $lokasi): ?>
                <option value="<?= $lokasi['id'] ?>"><?= esc($lokasi['nama_lokasi']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
    </form>
  </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
  $(document).ready(() => {
    initTablePetugas();

    select2('#location', {
      dropdownParent: $('#modalPetugas'),
    });

    $('#tambah-petugas-btn').click(() => {
      $('#formPetugas')[0].reset();
      $('#id').val('');
      $('#modalPetugasLabel').text('Tambah Petugas');
      $('#modalPetugas').modal('show');
    });

    $('#formPetugas').on('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const id = $('#id').val();

      $.ajax({
        url: '<?= url_to('admin.petugas.save') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
          if (!res.error) {
            showToast('success', 'Sukses', res.message);
            $('#modalPetugas').modal('hide');
            initTablePetugas();
          } else {
            showToast('error', 'Gagal', res.message);
          }
        },
        error: function(xhr) {
          const data = xhr.responseJSON;

          showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data petugas.');
        }
      });
    });
  });

  function initTablePetugas() {
    const tbl = $('#table-petugas');

    if ($.fn.DataTable.isDataTable(tbl)) {
      tbl.DataTable().ajax.reload(false, null);
    } else {
      tbl.DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
          url: `<?= url_to('admin.petugas.list') ?>`,
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

  function editPetugas(id) {
    $.ajax({
      url: "<?= base_url('admin/petugas/get') ?>/" + id,
      type: "GET",
      dataType: "json",
      success: function(response) {
        const data = response.data;

        // Isi data ke form
        $('#id').val(data.id);
        $('#fullname').val(data.fullname);
        $('#username').val(data.username);
        $('#nip').val(data.nip);
        $('#role').val(data.role);
        $('#password').val('');
        $('#location').val(data.lokasi_id).change();

        // Ganti judul modal
        $('#modalPetugasLabel').text('Edit Petugas');

        // Tampilkan modal
        $('#modalPetugas').modal('show');
      },
      error: function(xhr) {
        const data = xhr.responseJSON;

        showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data peserta.');
      }
    });
  }

  function deletePetugas(id) {
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
          url: "<?= base_url('admin/petugas/delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          success: function(res) {
            if (!res.error) {
              initTablePetugas();
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