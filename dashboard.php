<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Admin Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style/style.css">
</head>
<body class="dashboard-body">

  <div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="sidebar d-flex flex-column p-3">
      <h4 class="text-center text-white mb-4"><i class="fas fa-user-shield"></i> Admin</h4>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a>
        </li>
        <li>
          <a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i> Users</a>
        </li>
        <li>
          <a href="settings.php" class="nav-link"><i class="fas fa-cogs me-2"></i> Settings</a>
        </li>
        <li>
          <a href="reports.php" class="nav-link"><i class="fas fa-chart-bar me-2"></i> Reports</a>
        </li>
      </ul>
    </nav>

    <!-- Overlay for mobile -->
    <div id="overlay" class="overlay"></div>

    <!-- Content -->
    <div class="content">
      <!-- Header -->
      <div class="header">
        <div class="d-flex align-items-center">
          <!-- Toggle button only visible on small screens -->
          <button class="btn btn-outline-secondary d-lg-none me-2" id="sidebarToggle">
            <i class="fas fa-bars"></i>
          </button>
          <h5 class="mb-0">Admin Dashboard</h5>
        </div>
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle fa-2x me-2"></i>
            <span>Admin</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>

      <!-- Main content -->
      <div class="main">
        <h4>Welcome, Admin!</h4>
        <p>This is your responsive dashboard main content area. Resize your browser to see sidebar behavior.</p>
      </div>

      <!-- Footer -->
      <div class="footer">
        <small>&copy; 2025 Admin Panel. All rights reserved.</small>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const sidebar = document.getElementById('sidebarMenu');
    const toggleBtn = document.getElementById('sidebarToggle');
    const overlay = document.getElementById('overlay');

    function toggleSidebar() {
      sidebar.classList.toggle('show');
      overlay.classList.toggle('show');
    }

    toggleBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', toggleSidebar);
  </script>
</body>
</html>
