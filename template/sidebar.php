<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=Dashboard&action=admin">
        <i class="mdi mdi-view-dashboard menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=dataPelapor&action=index">
        <i class="mdi mdi-account-multiple menu-icon"></i>
        <span class="menu-title">Data Pelapor</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=laporanOPDAdmin&action=index">
        <i class="mdi mdi-file-document menu-icon"></i>
        <span class="menu-title">Laporan OPD</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=laporanCamatAdmin&action=index">
        <i class="mdi mdi-map-marker menu-icon"></i>
        <span class="menu-title">Laporan Camat</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#wilayah-menu" aria-expanded="false" aria-controls="wilayah-menu">
        <i class="mdi mdi-map-marker-multiple menu-icon"></i>
        <span class="menu-title">Manajemen Wilayah</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse <?php echo (strpos($_SERVER['REQUEST_URI'], 'kecamatan') !== false || strpos($_SERVER['REQUEST_URI'], 'desa') !== false) ? 'show' : ''; ?>" id="wilayah-menu">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'kecamatan' && isset($_GET['action']) && $_GET['action'] == 'index-kecamatan') ? 'active' : ''; ?>"
              href="index.php?controller=kecamatan&action=index-kecamatan">
              <i class="mdi mdi-map-marker-multiple menu-icon"></i>
              Kecamatan
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'wilayah' && isset($_GET['action']) && $_GET['action'] == 'index-desa') ? 'active' : ''; ?>"
              href="index.php?controller=desa&action=index-desa">
              <i class="mdi mdi-home-map-marker menu-icon"></i>
              Desa
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'opd' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : ''; ?>"
              href="index.php?controller=opd&action=index">
              <i class="mdi mdi-home-map-marker menu-icon"></i>
              OPD
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['controller']) && $_GET['controller'] == 'profile' && isset($_GET['action']) && $_GET['action'] == 'index') ? 'active' : ''; ?>"
              href="index.php?controller=profile&action=index">
              <i class="mdi mdi-home-map-marker menu-icon"></i>
              Profile
            </a>
          </li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="index.php?controller=laporan&action=index">
        <i class="mdi mdi-printer menu-icon"></i>
        <span class="menu-title">Cetak Laporan</span>
      </a>
    </li>


  </ul>
</nav>