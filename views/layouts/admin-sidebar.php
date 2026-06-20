<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('admin/dashboard') ? 'active' : ''; ?>" href="<?= route('Dashboard', 'admin') ?>">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('data-pelapor') ? 'active' : ''; ?>" href="<?= route('dataPelapor', 'index') ?>">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Data Pelapor</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('admin/laporan-opd') ? 'active' : ''; ?>" href="<?= route('laporanOPDAdmin', 'index') ?>">
        <i class="mdi mdi-file-document menu-icon"></i>
        <span class="menu-title">Laporan OPD</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('admin/laporan-camat') ? 'active' : ''; ?>" href="<?= route('laporanCamatAdmin', 'index') ?>">
        <i class="mdi mdi-map-marker menu-icon"></i>
        <span class="menu-title">Laporan Camat</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#wilayah-menu" aria-expanded="false" aria-controls="wilayah-menu">
        <i class="fa-solid fa-gear menu-icon"></i>
        <span class="menu-title">Pengaturan</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse <?php echo (route_starts_with('kecamatan') || route_starts_with('desa') || route_starts_with('opd') || route_starts_with('profiles')) ? 'show' : ''; ?>" id="wilayah-menu">
        <ul class="nav flex-column sub-menu">

          <li class="nav-item">
            <a class="nav-link <?php echo route_starts_with('kecamatan') ? 'active' : ''; ?>"
              href="<?= route('kecamatan', 'index') ?>">
              <i class="fa-solid fa-map-location-dot menu-icon"></i>
              Kecamatan
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php echo route_starts_with('desa') ? 'active' : ''; ?>"
              href="<?= route('desa', 'index') ?>">
              <i class="fa-solid fa-house-chimney menu-icon"></i>
              Desa
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?php echo route_starts_with('opd') ? 'active' : ''; ?>"
              href="<?= route('opd', 'index') ?>">
              <i class="fa-solid fa-building menu-icon"></i>
              OPD
            </a>
          </li>

          
          <li class="nav-item">
            <a class="nav-link <?php echo route_starts_with('profiles') ? 'active' : ''; ?>"
              href="<?= route('profile', 'index') ?>">
              <i class="fa-solid fa-user-gear menu-icon"></i>
              Profile
            </a>
          </li>

        </ul>
      </div>

    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('laporan') ? 'active' : ''; ?>" href="<?= route('laporan', 'index') ?>">
        <i class="mdi mdi-printer menu-icon"></i>
        <span class="menu-title">Cetak Laporan</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo route_starts_with('wa-messages') ? 'active' : ''; ?>" href="<?= route('waGateway', 'index') ?>">
        <i class="mdi mdi-whatsapp menu-icon"></i>
        <span class="menu-title">Kirim Pesan</span>
      </a>
    </li>
  </ul>
</nav>
