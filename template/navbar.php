<?php
  date_default_timezone_set("Asia/Jakarta");
  $hour = date("H");
  if ($hour >= 5 && $hour < 12) {
    $greeting = "Selamat Pagi";
  } elseif ($hour >= 12 && $hour < 15) {
    $greeting = "Selamat Siang";
  } elseif ($hour >= 15 && $hour < 18) {
    $greeting = "Selamat Sore";
  } else {
    $greeting = "Selamat Malam";
  }

  $nama = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : (isset($_SESSION['username']) ? $_SESSION['username'] : 'Pengguna');
  $email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@example.com';
  $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'masyarakat';
  $foto_profile = isset($_SESSION['profile_photo']) && !empty($_SESSION['profile_photo']) ? $_SESSION['profile_photo'] : 'favicon.png';
?>

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-top">
    <ul class="navbar-nav">
      <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
        <h1 class="welcome-text"><?= $greeting ?>, <span class="text-black fw-bold"><?= $nama ?></span></h1>
      </li>
    </ul>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item dropdown d-none d-lg-block user-dropdown">
        <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
          <img class="img-md rounded-circle" src="uploads/user.png" style="width: 35px;" alt="Profile image" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
          <div class="dropdown-header text-center">
            <p class="mb-1 mt-3 font-weight-semibold"><?= $nama ?></p>
            <p class="mb-1 text-muted"><?= $email ?></p>
            <p class="mb-0 text-muted text-small">Role: <span class="text-primary text-capitalize"><?= $role ?></span></p>
          </div>
        
          <a class="dropdown-item" href="index.php?controller=Auth&action=logout">
            <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
            Sign Out
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>
