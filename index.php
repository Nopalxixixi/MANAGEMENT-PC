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
        }

        body.dark-mode {
            --bg-primary: #1E1E1E;
            --bg-secondary: #2A2A2A;
            --text-primary: #E2E8F0;
            --text-secondary: #94A3B8;
            --border-color: #3A3A3A;
            --hover-bg: #2D2D2D;
            --gradient-hero: linear-gradient(135deg, #0F172A, #1E3A8A);
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
            padding: 20px 0;
            box-shadow: var(--shadow-light);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        .navbar-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
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

        /* Hero Section */
        .hero {
            background: var(--gradient-hero);
            padding: 120px 30px 70px;
            margin-top: 70px;
            margin-bottom: 40px;
            border-radius: 0 0 40px 40px;
            transition: all 0.3s ease;
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
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
            padding: 18px 24px;
            border-radius: 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #FFFFFF;
            border-left: 4px solid #00E3A5;
            color: var(--text-primary);
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 227, 165, 0.15);
            animation: slideIn 0.5s ease-out;
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

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px 80px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 20px;
            gap: 12px;
            flex: 1;
            max-width: 400px;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box input {
            border: none;
            outline: none;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            width: 100%;
            background: transparent;
            color: var(--text-primary);
        }

        .search-box input::placeholder {
            color: var(--text-secondary);
        }

        .search-box i {
            color: var(--text-secondary);
        }

        .btn-add {
            background: linear-gradient(135deg, #00AEEF, #00E3A5);
            color: #FFFFFF;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 15px rgba(0, 174, 239, 0.3);
            text-decoration: none;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 174, 239, 0.4);
        }

        /* Table */
        .table-container {
            background: var(--bg-secondary);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: var(--bg-primary);
        }

        th {
            padding: 22px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 22px 20px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--hover-bg);
        }

        .pc-name {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .pc-name i {
            color: #3B82F6;
        }

        .status-badge {
            padding: 7px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-baik {
            background: rgba(59, 130, 246, 0.15);
            color: #3B82F6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-maintenance {
            background: rgba(251, 191, 36, 0.15);
            color: #D97706;
            border: 1px solid rgba(251, 191, 36, 0.3);
        }

        .status-rusak {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .btn-edit:hover {
            background: #3B82F6;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn-delete:hover {
            background: #EF4444;
            color: #FFFFFF;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .ip-address {
            font-family: 'Courier New', monospace;
            background: var(--bg-primary);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .asset-number {
            font-family: 'Courier New', monospace;
            color: #3B82F6;
            font-weight: 600;
            font-size: 13px;
        }

        .no-data {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-secondary);
        }

        .no-data i {
            font-size: 56px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        /* Delete Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
            position: relative;
        }

        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(50px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .modal-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #EF4444;
        }

        .modal-header h3 {
            font-size: 24px;
            color: var(--text-primary);
            font-weight: 700;
        }

        .modal-body {
            margin-bottom: 30px;
        }

        .modal-body p {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .delete-info {
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
        }

        .delete-info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
        }

        .delete-info-item:last-child {
            border-bottom: none;
        }

        .delete-info-label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 13px;
        }

        .delete-info-value {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
            text-align: right;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-modal {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel {
            background: var(--bg-primary);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
        }

        .btn-cancel:hover {
            background: var(--border-color);
            color: var(--text-primary);
        }

        .btn-confirm-delete {
            background: #EF4444;
            color: #FFFFFF;
        }

        .btn-confirm-delete:hover {
            background: #DC2626;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
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
                    <li><a href="index.php">Data PC</a></li> 
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
            <h1>Kelola inventaris komputer dengan mudah dan efisien</h1>
            <div class="stats-grid">
                <a href="index.php?filter=all" class="stat-card <?= $filter == 'all' ? 'active' : ''; ?>">
                    <div class="stat-label">Total PC</div>
                    <div class="stat-value">
                        <?= $total; ?>
                        <i class="fas fa-desktop stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Baik" class="stat-card <?= $filter == 'Baik' ? 'active' : ''; ?>">
                    <div class="stat-label">Operasional</div>
                    <div class="stat-value">
                        <?= $baik; ?>
                        <i class="fas fa-check-circle stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Maintenance" class="stat-card <?= $filter == 'Maintenance' ? 'active' : ''; ?>">
                    <div class="stat-label">Maintenance</div>
                    <div class="stat-value">
                        <?= $maintenance; ?>
                        <i class="fas fa-tools stat-icon"></i>
                    </div>
                </a>
                <a href="index.php?filter=Rusak" class="stat-card <?= $filter == 'Rusak' ? 'active' : ''; ?>">
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
        <div class="alert">
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
        <div class="content-header">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama PC, user, atau asset..." onkeyup="searchTable()">
            </div>
            <a href="tambah.php" class="btn-add">
                <i class="fas fa-plus"></i>
                Tambah PC Baru
            </a>
        </div>

        <div class="table-container">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama PC</th>
                        <th>Nama User</th>
                        <th>Nomor Asset</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Tanggal Produksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <div class="pc-name">
                                        <i class="fas fa-microchip"></i>
                                        <?= htmlspecialchars($row['nama_pc']); ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['nama_user']); ?></td>
                                <td><span class="asset-number"><?= htmlspecialchars($row['nomor_asset']); ?></span></td>
                                <td><span class="ip-address"><?= htmlspecialchars($row['nomor_ip']); ?></span></td>
                                <td>
                                    <?php
                                    $statusClass = 'status-baik';
                                    $statusIcon = 'fa-check-circle';
                                    $status = strtolower($row['status']);
                                    if (strpos($status, 'maintenance') !== false) {
                                        $statusClass = 'status-maintenance';
                                        $statusIcon = 'fa-tools';
                                    } elseif (strpos($status, 'rusak') !== false) {
                                        $statusClass = 'status-rusak';
                                        $statusIcon = 'fa-exclamation-triangle';
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass; ?>">
                                        <i class="fas <?= $statusIcon; ?>"></i>
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_produksi'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit.php?id=<?= $row['id']; ?>" class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="showDeleteModal(<?= $row['id']; ?>, '<?= addslashes($row['nama_pc']); ?>', '<?= addslashes($row['nama_user']); ?>', '<?= addslashes($row['nomor_asset']); ?>')" 
                                           class="btn-icon btn-delete" 
                                           title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-data">
                                <i class="fas fa-inbox"></i>
                                <div>Belum ada data PC. Silakan tambah data baru.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 PC Management System | Made by Nopal Siegma</p>
    </footer>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Konfirmasi Hapus</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data PC berikut? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="delete-info" id="deleteInfo">
                    <!-- Will be filled by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="btn-modal btn-confirm-delete" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>

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
    </script>
</body>
</html>