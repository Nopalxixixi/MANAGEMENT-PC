<?php
include 'config.php';

// Proses Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM pc_list WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $affected = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            if ($affected > 0) {
                header("Location: data_pc.php?msg=delete_success");
                exit();
            } else {
                header("Location: data_pc.php?msg=delete_error");
                exit();
            }
        } else {
            header("Location: data_pc.php?msg=delete_error");
            exit();
        }
    } else {
        header("Location: data_pc.php?msg=delete_error");
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
    <title>Data PC - PC Management System</title>
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

        /* Page Header */
        .page-header {
            background: var(--gradient-hero);
            padding: 120px 30px 50px;
            margin-top: 70px;
            margin-bottom: 40px;
            transition: all 0.3s ease;
        }

        .page-header-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header h1 {
            color: #FFFFFF;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        /* Filter Tabs */
        .filter-tabs {
            max-width: 1400px;
            margin: -20px auto 30px;
            padding: 0 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-tab {
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-tab:hover {
            border-color: #3B82F6;
            transform: translateY(-2px);
        }

        .filter-tab.active {
            background: #3B82F6;
            border-color: #3B82F6;
            color: #FFFFFF;
        }

        .filter-count {
            background: rgba(59, 130, 246, 0.1);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
        }

        .filter-tab.active .filter-count {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }

        /* Alert */
        .alert {
            max-width: 1400px;
            margin: 0 auto 30px;
            padding: 0 30px;
        }

        .alert-box {
            padding: 18px 24px;
            border-radius: 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg-secondary);
            border-left: 4px solid #00E3A5;
            color: var(--text-primary);
            font-weight: 500;
            box-shadow: var(--shadow-light);
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

        body.dark-mode .btn-add {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        body.dark-mode .btn-add:hover {
            background: var(--hover-bg);
            border-color: #3B82F6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
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
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transform: scale(1.1);
        transition: visibility 0s linear 0.25s, opacity 0.25s 0s, transform 0.25s;
    }
    
    .modal.show {
        display: block;
        opacity: 1;
        visibility: visible;
        transform: scale(1.0);
        transition: visibility 0s linear 0s, opacity 0.25s 0s, transform 0.25s;
    }
    
    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: var(--bg-secondary);
        padding: 30px;
        width: 500px;
        max-width: 90%;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border-color);
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .modal-icon {
        width: 48px;
        height: 48px;
        background-color: #FEF2F2;
        color: #EF4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 20px;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    
    .modal h3 {
        color: var(--text-primary);
        margin: 0;
        font-size: 22px;
        font-weight: 600;
    }
    
    .modal-body {
        margin-bottom: 25px;
    }
    
    .modal-body p {
        color: var(--text-secondary);
        margin-bottom: 20px;
        font-size: 15px;
        line-height: 1.5;
    }
    
    .delete-info {
        background-color: var(--bg-primary);
        border-radius: 10px;
        padding: 18px;
        margin-top: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
    }
    
    .delete-info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
    }
    
    .delete-info-item:last-child {
        border-bottom: none;
    }
    
    .delete-info-label {
        font-weight: 500;
        color: var(--text-secondary);
        flex: 1;
        font-size: 14px;
    }
    
    .delete-info-value {
        color: var(--text-primary);
        font-weight: 600;
        flex: 2;
        text-align: right;
        font-size: 15px;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }
    
    .btn-modal {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        font-size: 15px;
    }
    
    .btn-cancel {
        background-color: var(--bg-primary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }
    
    .btn-cancel:hover {
        background-color: var(--hover-bg);
        transform: translateY(-2px);
    }
    
    .btn-confirm-delete {
        background-color: #EF4444;
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    
    .btn-confirm-delete:hover {
        background-color: #DC2626;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(239, 68, 68, 0.25);
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

            .page-header {
                padding: 100px 20px 40px;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .filter-tabs {
                padding: 0 20px;
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
                    <li><a href="index.php">Dashboard</a></li>
                    <li><a href="data_pc.php" class="active">Data PC</a></li>
                </ul>
                <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="fas fa-sun sun-icon"></i>
                    <i class="fas fa-moon moon-icon"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-container">
            <h1><i class="fas fa-database"></i> Data PC</h1>
            <p>Kelola semua data komputer perusahaan</p>
        </div>
    </section>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="data_pc.php?filter=all" class="filter-tab <?= $filter == 'all' ? 'active' : ''; ?>">
            <i class="fas fa-th"></i>
            Semua
            <span class="filter-count"><?= $total; ?></span>
        </a>
        <a href="data_pc.php?filter=Baik" class="filter-tab <?= $filter == 'Baik' ? 'active' : ''; ?>">
            <i class="fas fa-check-circle"></i>
            Operasional
            <span class="filter-count"><?= $baik; ?></span>
        </a>
        <a href="data_pc.php?filter=Maintenance" class="filter-tab <?= $filter == 'Maintenance' ? 'active' : ''; ?>">
            <i class="fas fa-tools"></i>
            Maintenance
            <span class="filter-count"><?= $maintenance; ?></span>
        </a>
        <a href="data_pc.php?filter=Rusak" class="filter-tab <?= $filter == 'Rusak' ? 'active' : ''; ?>">
            <i class="fas fa-exclamation-triangle"></i>
            Rusak
            <span class="filter-count"><?= $rusak; ?></span>
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert">
            <div class="alert-box">
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
                    case 'delete_error':
                        echo "Gagal menghapus data PC.";
                        break;
                }
                ?>
            </div>
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

        <div class="table-responsive" data-aos="fade-up" data-aos-delay="200">
                <table id="dataTable">
                    <thead>
                        <tr data-aos="fade-right" data-aos-delay="300">
                            <th>No</th>
                            <th>Nama PC</th>
                            <th>Nama User</th>
                            <th>Nomor Asset</th>
                            <th>Nomor IP</th>
                            <th>Status</th>
                            <th>Tanggal Produksi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr data-aos="fade-up" data-aos-delay="<?= ($no * 50) + 300 ?>">
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
                                        <button onclick="showDeleteModal(<?= $row['id']; ?>, '<?= addslashes($row['nama_pc']); ?>', '<?= addslashes($row['nama_user']); ?>', '<?= addslashes($row['nomor_asset']); ?>', '<?= addslashes($row['nomor_ip']); ?>', '<?= addslashes($row['status']); ?>', '<?= addslashes($row['tanggal_produksi']); ?>')" 
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
        <p>© 2025 PC Management System | Made by Nopal</p>
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
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
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
            const alert = document.querySelector('.alert-box');
            if (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.parentElement.remove(), 500);
            }
        }, 3000);

        // Delete Modal Functions
        let deleteId = null;

        function showDeleteModal(id, namaPc, namaUser, nomorAsset, nomorIp, status, tanggalProduksi) {
            deleteId = id;
            const deleteInfo = document.getElementById('deleteInfo');
            const modal = document.getElementById('deleteModal');
            
            // Format tanggal untuk tampilan yang lebih baik
            const formattedDate = new Date(tanggalProduksi).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            
            // Tentukan kelas status untuk warna
            let statusClass = 'status-baik';
            let statusIcon = 'fa-check-circle';
            
            if (status.toLowerCase().includes('maintenance')) {
                statusClass = 'status-maintenance';
                statusIcon = 'fa-tools';
            } else if (status.toLowerCase().includes('rusak')) {
                statusClass = 'status-rusak';
                statusIcon = 'fa-exclamation-triangle';
            }
            
            // Tampilkan modal
            modal.classList.add('show');
            
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
                <div class="delete-info-item">
                    <span class="delete-info-label">Nomor IP:</span>
                    <span class="delete-info-value">${nomorIp}</span>
                </div>
                <div class="delete-info-item">
                    <span class="delete-info-label">Status:</span>
                    <span class="delete-info-value">
                        <span class="status-badge ${statusClass}">
                            <i class="fas ${statusIcon}"></i>
                            ${status}
                        </span>
                    </span>
                </div>
                <div class="delete-info-item">
                    <span class="delete-info-label">Tanggal Produksi:</span>
                    <span class="delete-info-value">${formattedDate}</span>
                </div>
            `;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                window.location.href = `data_pc.php?delete=${deleteId}`;
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