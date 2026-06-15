<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?= route('Dashboard', 'admin') ?>">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= route('dataPelapor', 'index') ?>">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Data Pelapor</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= route('laporanOPDAdmin', 'index') ?>">
        <i class="mdi mdi-file-document menu-icon"></i>
        <span class="menu-title">Laporan OPD</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= route('laporanCamatAdmin', 'index') ?>">
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
      <!-- Menu Wilayah -->
      <div class="collapse <?php echo (strpos($_SERVER['REQUEST_URI'], 'kecamatan') !== false || strpos($_SERVER['REQUEST_URI'], 'desa') !== false || strpos($_SERVER['REQUEST_URI'], 'opd') !== false || strpos($_SERVER['REQUEST_URI'], 'profile') !== false) ? 'show' : ''; ?>" id="wilayah-menu">
        <ul class="nav flex-column sub-menu">

          <!-- Menu Kecamatan -->
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'kecamatan' && isset($_GET['action']) && $_GET['action'] == 'index-kecamatan') ? 'active' : ''; ?>"
              href="<?= route('kecamatan', 'index-kecamatan') ?>">
              <i class="fa-solid fa-map-location-dot menu-icon"></i>
              Kecamatan
            </a>
          </li>

          <!-- Menu Desa -->
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'desa' && isset($_GET['action']) && $_GET['action'] == 'index-desa') ? 'active' : ''; ?>"
              href="<?= route('desa', 'index-desa') ?>">
              <i class="fa-solid fa-house-chimney menu-icon"></i>
              Desa
            </a>
          </li>

          <!-- Menu OPD -->
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'opd' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : ''; ?>"
              href="<?= route('opd', 'index') ?>">
              <i class="fa-solid fa-building menu-icon"></i>
              OPD
            </a>
          </li>

          <!-- Menu Profile -->
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'profile' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : ''; ?>"
              href="<?= route('profile', 'index') ?>">
              <i class="fa-solid fa-user-gear menu-icon"></i>
              Profile
            </a>
          </li>

        </ul>
      </div>

    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= route('laporan', 'index') ?>">
        <i class="mdi mdi-printer menu-icon"></i>
        <span class="menu-title">Cetak Laporan</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?= route('waGateway', 'index') ?>">
        <i class="mdi mdi-whatsapp menu-icon"></i>
        <span class="menu-title">Kirim Pesan</span>
      </a>
    </li>
  </ul>
</nav>