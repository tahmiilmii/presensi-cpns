<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <!-- Sidebar - Brand -->
  <a class="sidebar-brand d-flex align-items-center justify-content-center"
    href="<?= url_to('admin.dashboard') ?>">
    <div class="sidebar-brand-text mx-3 logo-font">Presensi</div>
  </a>

  <!-- Divider -->
  <hr class="sidebar-divider my-0">

  <!-- Nav Item - Dashboard -->
  <li class="nav-item <?= ($page == 'profile') ? 'active' : '' ?>">
    <a class="nav-link" href="<?= url_to('participant.profile') ?>">
      <i class="fas fa-id-card"></i>
      <span>Profile</span>
    </a>
  </li>

  <!-- Sidebar Toggler (Sidebar) -->
  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>
<!-- End of Sidebar -->