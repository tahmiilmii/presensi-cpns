<?= $this->extend('_layouts/admin'); ?>

<?= $this->section('title'); ?>
Sesi | Admin Presensi CPNS/P3K
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sesi</h1>
  </div>

  <!-- Content Row -->
  <div class="row">
    <div class="card w-100 shadow mb-4">
      <div class="card-header py-3">
        <div class="d-flex align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-app-primary">Daftar Sesi</h6>

          <div class="d-flex" style="gap: 8px">
            <button class="btn m-0 btn-sm btn-success" id="tambah-sesi-btn">
              <i class="fas fa-plus"></i>
              &nbsp;Tambah
            </button>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped table-bordered w-100" id="table-sesi">
            <thead class="bg-primary text-white">
              <tr>
                <th>No</th>
                <th>Nama Sesi</th>
                <th>Tanggal</th>
                <th>Jam Mulai</th>
                <th>Jam Selesai</th>
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
<div class="modal fade" id="modalSesi" tabindex="-1" role="dialog" aria-labelledby="modalSesiLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formSesi">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSesiLabel">Tambah Sesi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="nama_sesi">Nama Sesi <span class="text-danger">*</span></label>
            <input type="text" name="nama_sesi" id="nama_sesi" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
            <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
            <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
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
    initTableSesi();

    $('#tambah-sesi-btn').click(() => {
      $('#formSesi')[0].reset();
      $('#id').val('');
      $('#modalSesiLabel').text('Tambah Sesi');
      $('#modalSesi').modal('show');
    });

    $('#formSesi').on('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);
      const id = $('#id').val();

      $.ajax({
        url: '<?= url_to('admin.sesi.save') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
          if (!res.error) {
            showToast('success', 'Sukses', res.message);
            $('#modalSesi').modal('hide');
            initTableSesi();
          } else {
            showToast('error', 'Gagal', res.message);
          }
        },
        error: function(xhr) {
          const data = xhr.responseJSON;

          showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data sesi.');
        }
      });
    });
  });

  function initTableSesi() {
    const tbl = $('#table-sesi');

    if ($.fn.DataTable.isDataTable(tbl)) {
      tbl.DataTable().ajax.reload(false, null);
    } else {
      tbl.DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
          url: `<?= url_to('admin.sesi.list') ?>`,
          type: "GET",
        },
        columnDefs: [{
          width: "5%",
          orderable: false,
          targets: [0]
        }, {
          width: "8%",
          orderable: false,
          targets: [-1]
        }],
      });
    }
  }

  function editSesi(id) {
    $.ajax({
      url: "<?= base_url('admin/sesi/get') ?>/" + id,
      type: "GET",
      dataType: "json",
      success: function(response) {
        const data = response.data;

        // Isi data ke form
        $('#id').val(data.id);
        $('#nama_sesi').val(data.nama_sesi);
        $('#tanggal').val(data.tanggal);
        $('#jam_mulai').val(data.jam_mulai);
        $('#jam_selesai').val(data.jam_selesai);

        // Ganti judul modal
        $('#modalSesiLabel').text('Edit Sesi');

        // Tampilkan modal
        $('#modalSesi').modal('show');
      },
      error: function(xhr) {
        const data = xhr.responseJSON;

        showToast('error', 'Gagal', data.message ?? 'Tidak bisa mengambil data sesi.');
      }
    });
  }

  function deleteSesi(id) {
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
          url: "<?= base_url('admin/sesi/delete') ?>/" + id,
          type: "DELETE",
          dataType: "json",
          success: function(res) {
            if (!res.error) {
              initTableSesi();
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