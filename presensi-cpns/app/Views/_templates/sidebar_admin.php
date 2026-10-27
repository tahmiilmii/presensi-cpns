<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="<?= url_to('admin.dashboard') ?>">
    <div class="sidebar-brand-text mx-3 logo-font">Presensi</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?= ($page == 'dashboard') ? 'active' : '' ?>">
    <a class="nav-link" href="<?= url_to('admin.dashboard') ?>">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <!-- Heading -->
  <div class="sidebar-heading">
    Menu Utama
  </div>

  <!-- Nav Item -->
  <li class="nav-item <?= ($page == 'peserta') ? 'active' : '' ?>">
    <a class="nav-link" href="<?= url_to('admin.peserta') ?>">
      <i class="fas fa-users"></i>
      <span>Peserta</span>
    </a>
  </li>

  <?php if (session()->get('role') == 'admin'): ?>
    <li class="nav-item <?= ($page == 'lokasi') ? 'active' : '' ?>">
      <a class="nav-link" href="<?= url_to('admin.lokasi') ?>">
        <i class="fas fa-map-marker-alt"></i>
        <span>Lokasi</span>
      </a>
    </li>
  <?php endif ?>

  <?php if (session()->get('role') == 'admin'): ?>
    <li class="nav-item <?= ($page == 'sesi') ? 'active' : '' ?>">
      <a class="nav-link" href="<?= url_to('admin.sesi') ?>">
        <i class="fa fa-user-secret"></i>
        <span>Sesi</span>
      </a>
    </li>
  <?php endif ?>

  <li class="nav-item <?= ($page == 'presensi') ? 'active' : '' ?>">
    <a class="nav-link" href="<?= url_to('admin.presensi') ?>">
      <i class="fas fa-list-alt"></i>
      <span>Presensi</span>
    </a>
  </li>

  <!-- Divider -->
  <hr class="sidebar-divider">

  <?php if (session()->get('role') == 'admin'): ?>
    <!-- Heading -->
    <div class="sidebar-heading">
      Admin
    </div>

    <!-- Nav Item -->
    <li class="nav-item <?= ($page == 'petugas') ? 'active' : '' ?>">
      <a class="nav-link" href="<?= url_to('admin.petugas') ?>">
        <i class="fas fa-users"></i>
        <span>Petugas</span>
      </a>
    </li>
  <?php endif ?>

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>
<!-- End of Sidebar -->