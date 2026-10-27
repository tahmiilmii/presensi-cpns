<?= $this->extend('_layouts/participant'); ?>

<?= $this->section('title'); ?>
Profil | Presensi CPNS/P3K
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
          <h6 class="m-0 font-weight-bold text-app-primary">Profil</h6>

          <button class="btn btn-sm btn-primary" id="btn-enable-edit">
            <i class="fas fa-edit"></i>
            &nbsp;Edit Profil
          </button>
        </div>
      </div>

      <div class="card-body">
        <?php
        if (session()->getFlashdata('error')) {
          echo view_cell('AlertCell', ['type' => 'danger', 'message' => session()->getFlashdata('error')]);
        }
        ?>
        <?= form_open(url_to('participant.profile.save'), ['class' => 'user', 'id' => 'form-profil']) ?>
        <div class="form-group">
          <label for="nama">Nama Lengkap</label>
          <input type="text" class="form-control" id="nama" name="nama" value="<?= esc($peserta['nama']) ?>" disabled>
        </div>

        <div class="form-group">
          <label for="nik">NIK</label>
          <input type="text" class="form-control" id="nik" name="nik" value="<?= esc($peserta['nik']) ?>" disabled>
        </div>

        <div class="form-group">
          <label for="tempat_lahir">Tempat Lahir</label>
          <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= esc($peserta['tempat_lahir']) ?>" disabled>
        </div>

        <div class="form-group">
          <label for="tanggal_lahir">Tanggal Lahir</label>
          <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= esc($peserta['tanggal_lahir']) ?>" disabled>
        </div>

        <div class="form-group">
          <label for="alamat">Alamat</label>
          <textarea class="form-control" id="alamat" name="alamat" disabled><?= esc($peserta['alamat']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success" id="btnSimpan" disabled>
          <i class="fas fa-save"></i>
          &nbsp;Simpan Perubahan
        </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
  $(document).ready(() => {
    $('#btn-enable-edit').click(function() {
      const form = $('#form-profil');
      const inputs = form.find('input, textarea, select, button');

      inputs.each(function() {
        if (this.id !== 'btn-enable-edit') {
          $(this).prop('disabled', false);
        }
      });

      $(this).prop('disabled', true);
      $(this).html('<i class="fas fa-edit"></i>s&nbsp;Edit Diaktifkan');
    })
  });
</script>
<?= $this->endSection(); ?>