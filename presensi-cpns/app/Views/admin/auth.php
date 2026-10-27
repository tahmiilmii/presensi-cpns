<?= $this->extend('_layouts/auth'); ?>

<?= $this->section('title'); ?>
Admin Login
<?= $this->endSection(); ?>


<?= $this->section('content'); ?>
<div class="bg-image"></div>
<div class="container">
  <!-- Outer Row -->
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card border-0 shadow-lg my-5">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col">
              <div class="p-5">
                <div class="text-center">
                  <h1 class="h2 text-gray-900 mb-4 font-weight-bolder">Masuk</h1>
                </div>
                <?php
                if (session()->getFlashdata('error')) {
                  echo view_cell('AlertCell', ['type' => 'danger', 'message' => session()->getFlashdata('error')]);
                }
                ?>
                <?= form_open(url_to('admin.login'), ['class' => 'user']) ?>
                <div class="form-group">
                  <input type="text" name="username" class="form-control form-control-user" placeholder="Username" required>
                </div>
                <div class="form-group">
                  <input type="password" name="password" class="form-control form-control-user" placeholder="Password"
                    required>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                  Login
                </button>
                <?= form_close() ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>