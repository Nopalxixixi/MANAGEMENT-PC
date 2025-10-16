<?php
include 'config.php';

// Proses Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete = mysqli_query($conn, "DELETE FROM pc_list WHERE id = $id");
    if ($delete) {
        header("Location: index.php?msg=delete_success");
        exit();
    }
}

// Filter berdasarkan status
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$query = "SELECT * FROM pc_list";
if ($filter != 'all') {
    $filter_escaped = mysqli_real_escape_string($conn, $filter);
    $query .= " WHERE status = '$filter_escaped'";
}
$query .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query);

// Hitung statistik
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pc_list"))['count'];
$baik = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pc_list WHERE status = 'Baik'"))['count'];
$maintenance = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pc_list WHERE status = 'Maintenance'"))['count'];
$rusak = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM pc_list WHERE status = 'Rusak'"))['count'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Management System - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-primary: #F5F7FA;
            --bg-secondary: #FFFFFF;
            --text-primary: #1E293B;
            --text-secondary: #64748B;
            --border-color: #E2E8F0;
            --hover-bg: #F8FAFC;
            --gradient-hero: linear-gradient(135deg, #00AEEF, #00E3A5);
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 10px 40px rgba(0, 0, 0, 0.08);
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --border-radius-lg: 16px;
        }
        
        /* Table */
        .data-table-container {
            background: var(--bg-secondary);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-light);
            overflow: hidden;
            margin-top: 24px;
            border: 1px solid var(--border-color);
        }

        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--bg-primary);
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: var(--hover-bg);
        }
        
        tbody tr {
            transition: all 0.2s ease;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: var(--border-radius-md);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            font-size: 14px;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--hover-bg);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #DC2626;
            transform: translateY(-1px);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        body.dark-mode {
            --bg-primary: #111827;
            --bg-secondary: #1F2937;
            --text-primary: #F9FAFB;
            --text-secondary: #D1D5DB;
            --border-color: #374151;
            --hover-bg: #2D3748;
            --accent-color: #60A5FA;
            --accent-hover: #3B82F6;
            --success-color: #34D399;
            --warning-color: #FBBF24;
            --danger-color: #F87171;
            --gradient-hero: linear-gradient(135deg, #4F46E5, #3B82F6);
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.3);
            --shadow-medium: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        /* Navbar */
        .navbar {
            background: var(--bg-secondary);
            padding: 16px 0;
            box-shadow: var(--shadow-light);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logo i {
            color: #3B82F6;
            font-size: 28px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 35px;
        }

        .nav-menu {
            display: flex;
            gap: 35px;
            list-style: none;
            align-items: center;
        }

        .nav-menu a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: #3B82F6;
        }

        .dark-mode-toggle {
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .dark-mode-toggle:hover {
            transform: scale(1.1);
            border-color: #3B82F6;
        }

        .sun-icon {
            color: #FCD34D;
            display: none;
        }

        .moon-icon {
            color: #64748B;
            display: block;
        }

        body.dark-mode .sun-icon {
            display: block;
        }

        body.dark-mode .moon-icon {
            display: none;
        }

        /* Animasi */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .fade-in.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .scale-in {
            transform: scale(0.8);
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
        
        .scale-in.active {
            transform: scale(1);
            opacity: 1;
        }
        
        .slide-in-right {
            transform: translateX(50px);
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
        
        .slide-in-right.active {
            transform: translateX(0);
            opacity: 1;
        }
        
        .slide-in-left {
            transform: translateX(-50px);
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
        
        .slide-in-left.active {
            transform: translateX(0);
            opacity: 1;
        }
        
        /* Hero Section */
        .hero {
            background: var(--gradient-hero);
            padding: 120px 30px 70px;
            margin-top: 70px;
            margin-bottom: 40px;
            border-radius: 0 0 40px 40px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.5;
            z-index: 0;
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            color: #FFFFFF;
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 50px;
            text-align: center;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            position: relative;
            z-index: 2;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            padding: 35px;
            transition: all 0.3s ease;
            cursor: pointer;
            color: #FFFFFF;
            text-decoration: none;
            display: block;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-card.active {
            background: rgba(255, 255, 255, 0.35);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .stat-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 15px;
            font-weight: 600;
            opacity: 0.95;
        }

        .stat-value {
            font-size: 48px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .stat-icon {
            font-size: 32px;
            opacity: 0.9;
        }

        /* Alert */
        .alert {
            max-width: 1400px;
            margin: -20px auto 30px;
            padding: 16px 20px;
            border-radius: var(--border-radius-md);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--bg-secondary);
            border-left: 4px solid var(--success-color);
            color: var(--text-primary);
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            animation: slideIn 0.5s ease-out;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.2);
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border-color: rgba(239, 68, 68, 0.2);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dashboard Info */
        .dashboard-info {
            text-align: center;
            padding: 60px 20px;
        }

        .dashboard-info h2 {
            font-size: 36px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .dashboard-info p {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 50px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .action-card {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 20px;
            padding: 40px 30px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            border-color: #3B82F6;
        }

        .action-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
        }

        .action-card h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .action-card p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px 80px;
        }

        /* Footer */
        .footer {
            background: var(--bg-secondary);
            padding: 30px 0;
            text-align: center;
            margin-top: 60px;
            border-top: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .footer p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-primary);
            cursor: pointer;
            padding: 8px;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
        }
        
        .mobile-menu-btn:hover {
            background-color: var(--hover-bg);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }

            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--bg-secondary);
                flex-direction: column;
                padding: 20px;
                box-shadow: var(--shadow-light);
            }

            .nav-menu.active {
                display: flex;
            }

            .hero {
                padding: 100px 20px 50px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 36px;
            }

            .content-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                max-width: 100%;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 900px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="index.php" class="logo">
                <i class="fas fa-server"></i>
                PC Management System
            </a>
            <button class="mobile-menu-btn" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="nav-right">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="index.php" class="active">Dashboard</a></li>
                    <li><a href="data_pc.php">Data PC</a></li>
                </ul>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="fas fa-sun sun-icon"></i>
                    <i class="fas fa-moon moon-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1 data-aos="fade-down">Kelola inventaris komputer dengan mudah dan efisien</h1>
            <div class="stats-grid">
                <a href="index.php?filter=all" class="stat-card <?= $filter == 'all' ? 'active' : ''; ?>" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-label">Total PC</div>
                    <div class="stat-value">
                        <?= $total; ?>
                        <i class="fas fa-desktop stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Baik" class="stat-card <?= $filter == 'Baik' ? 'active' : ''; ?>" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-label">Operasional</div>
                    <div class="stat-value">
                        <?= $baik; ?>
                        <i class="fas fa-check-circle stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Maintenance" class="stat-card <?= $filter == 'Maintenance' ? 'active' : ''; ?>" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-label">Maintenance</div>
                    <div class="stat-value">
                        <?= $maintenance; ?>
                        <i class="fas fa-tools stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Rusak" class="stat-card <?= $filter == 'Rusak' ? 'active' : ''; ?>" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-label">Rusak</div>
                    <div class="stat-value">
                        <?= $rusak; ?>
                        <i class="fas fa-exclamation-triangle stat-icon"></i>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert" data-aos="fade-in">
            <i class="fas fa-check-circle"></i>
            <?php
            switch ($_GET['msg']) {
                case 'add_success':
                    echo "Data PC berhasil ditambahkan!";
                    break;
                case 'update_success':
                    echo "Data PC berhasil diperbarui!";
                    break;
                case 'delete_success':
                    echo "Data PC berhasil dihapus!";
                    break;
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="dashboard-info" data-aos="fade-up">
            <h2 data-aos="fade-up" data-aos-delay="100">Selamat Datang di Dashboard</h2>
            <p data-aos="fade-up" data-aos-delay="200">Kelola dan monitor semua inventaris komputer perusahaan Anda dengan mudah dan efisien.</p>
            

            
            <div class="quick-actions" data-aos="fade-up" data-aos-delay="300">
                <a href="data_pc.php" class="action-card">
                    <div class="action-icon" style="background: rgba(59, 130, 246, 0.1); color: #3B82F6;">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3>Lihat Semua Data PC</h3>
                    <p>Kelola dan lihat detail semua komputer</p>
                </a>
                
                <a href="tambah.php" class="action-card">
                    <div class="action-icon" style="background: rgba(0, 227, 165, 0.1); color: #00E3A5;">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3>Tambah PC Baru</h3>
                    <p>Daftarkan komputer baru ke sistem</p>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 PC Management System | Made by Nopal</p>
    </footer>
    <script>
        // Load saved theme on page load
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            
            // Save preference to localStorage
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        }

        function toggleMenu() {
            document.getElementById('navMenu').classList.toggle('active');
        }

        // Search function
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('dataTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const td = tr[i].getElementsByTagName('td');
                
                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = found ? '' : 'none';
            }
        }

        // Auto hide alert
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 3000);

        // Delete Modal Functions
        let deleteId = null;

        function showDeleteModal(id, namaPc, namaUser, nomorAsset) {
            deleteId = id;
            const deleteInfo = document.getElementById('deleteInfo');
            deleteInfo.innerHTML = `
                <div class="delete-info-item">
                    <span class="delete-info-label">Nama PC:</span>
                    <span class="delete-info-value">${namaPc}</span>
                </div>
                <div class="delete-info-item">
                    <span class="delete-info-label">Nama User:</span>
                    <span class="delete-info-value">${namaUser}</span>
                </div>
                <div class="delete-info-item">
                    <span class="delete-info-label">Nomor Asset:</span>
                    <span class="delete-info-value">${nomorAsset}</span>
                </div>
            `;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                window.location.href = `index.php?delete=${deleteId}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
        // Inisialisasi AOS
        AOS.init({
            duration: 400,
            easing: 'ease-in-out',
            once: true
        });
        
        // Animasi custom untuk elemen-elemen
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi untuk elemen dengan class fade-in
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach(el => {
                setTimeout(() => {
                    el.classList.add('active');
                }, 300);
            });
            
            // Animasi untuk elemen dengan class scale-in
            const scaleElements = document.querySelectorAll('.scale-in');
            scaleElements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('active');
                }, 300 + (index * 100));
            });
            
            // Animasi untuk elemen dengan class slide-in-right dan slide-in-left
            const slideRightElements = document.querySelectorAll('.slide-in-right');
            const slideLeftElements = document.querySelectorAll('.slide-in-left');
            
            slideRightElements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('active');
                }, 300 + (index * 100));
            });
            
            slideLeftElements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('active');
                }, 300 + (index * 100));
            });
        });
    </script>
</body>
</html>