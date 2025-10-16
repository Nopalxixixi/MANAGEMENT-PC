<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_pc = mysqli_real_escape_string($conn, $_POST['nama_pc']);
    $nama_user = mysqli_real_escape_string($conn, $_POST['nama_user']);
    $nomor_asset = mysqli_real_escape_string($conn, $_POST['nomor_asset']);
    $nomor_ip = mysqli_real_escape_string($conn, $_POST['nomor_ip']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $tanggal_produksi = mysqli_real_escape_string($conn, $_POST['tanggal_produksi']);

    if (empty($nama_pc) || empty($nama_user) || empty($nomor_asset) || empty($nomor_ip) || empty($status) || empty($tanggal_produksi)) {
        $error = "Semua field harus diisi!";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM pc_list WHERE nomor_asset = '$nomor_asset'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Nomor Asset sudah digunakan!";
        } else {
            $query = "INSERT INTO pc_list (nama_pc, nama_user, nomor_asset, nomor_ip, status, tanggal_produksi) 
                      VALUES ('$nama_pc', '$nama_user', '$nomor_asset', '$nomor_ip', '$status', '$tanggal_produksi')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: index.php?msg=add_success");
                exit();
            } else {
                $error = "Gagal menambahkan data: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah PC Baru - PC Management System</title>
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
            --gradient-hero: linear-gradient(135deg, #00AEEF, #00E3A5);
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 10px 40px rgba(0, 0, 0, 0.08);
            --accent-color: #3B82F6;
            --accent-hover: #2563EB;
            --hover-bg: #F8FAFC;
            --border-radius-md: 12px;
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
            --accent-color: #60A5FA;
            --accent-hover: #3B82F6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-hero);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: var(--bg-secondary);
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
            transition: all 0.3s ease;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #3B82F6;
            gap: 12px;
        }

        .header {
            margin-bottom: 40px;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        h1 i {
            color: #3B82F6;
        }

        .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .form-grid {
            display: grid;
            gap: 25px;
        }

        .form-group {
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
        }

        .required {
            color: #EF4444;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg-secondary);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-primary);
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
            color-scheme: light dark;
        }

        body.dark-mode input[type="text"],
        body.dark-mode input[type="date"],
        body.dark-mode select {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        body.dark-mode input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        input[type="text"]::placeholder {
            color: var(--text-secondary);
        }

        body.dark-mode input[type="text"]::placeholder {
            color: #64748B;
        }

        select {
            cursor: pointer;
            background-position: right 16px center;
            padding-right: 40px;
        }

        select option {
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: 10px;
        }

        body.dark-mode select option {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            gap: 10px;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00AEEF, #00E3A5);
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(0, 174, 239, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #00AEEF, #00E3A5);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 174, 239, 0.4);
        }

        body.dark-mode .btn-primary {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 2px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        body.dark-mode .btn-primary:hover {
            background: var(--hover-bg);
            border-color: #3B82F6;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--hover-bg);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #EF4444;
            color: #EF4444;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        body.dark-mode .alert {
            background: rgba(239, 68, 68, 0.2);
            border-left-color: #EF4444;
            color: #FCA5A5;
        }

        .button-group {
            margin-top: 35px;
            display: flex;
            gap: 12px;
        }

        .dark-mode-toggle {
            position: fixed;
            top: 30px;
            right: 30px;
            background: var(--bg-secondary);
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
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }

            .dark-mode-toggle {
                top: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <button class="dark-mode-toggle" onclick="toggleDarkMode()" title="Toggle Dark Mode">
        <i class="fas fa-sun sun-icon"></i>
        <i class="fas fa-moon moon-icon"></i>
    </button>

    <div class="container" data-aos="zoom-in" data-aos-duration="800">
        <a href="index.php" class="back-link" data-aos="fade-right" data-aos-delay="200">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        
        <div class="header" data-aos="fade-up" data-aos-delay="300">
             <h1>
                 <i class="fas fa-plus-circle"></i>
                Tambah PC Baru
            </h1>
            <p class="subtitle">Isi formulir untuk menambah data PC ke sistem</p>
        </div>

        <?php if ($error): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" data-aos="fade-up" data-aos-delay="400">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama PC <span class="required">*</span></label>
                    <input type="text" name="nama_pc" placeholder="Contoh: PC-IT-001" required 
                           value="<?= isset($_POST['nama_pc']) ? htmlspecialchars($_POST['nama_pc']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Nama User <span class="required">*</span></label>
                    <input type="text" name="nama_user" placeholder="Contoh: Ahmad Budiman" required
                           value="<?= isset($_POST['nama_user']) ? htmlspecialchars($_POST['nama_user']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Nomor Asset <span class="required">*</span></label>
                    <input type="text" name="nomor_asset" placeholder="Contoh: AST-2024-001" required
                           value="<?= isset($_POST['nomor_asset']) ? htmlspecialchars($_POST['nomor_asset']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>IP Address <span class="required">*</span></label>
                    <input type="text" name="nomor_ip" placeholder="Contoh: 192.168.1.10" required
                           value="<?= isset($_POST['nomor_ip']) ? htmlspecialchars($_POST['nomor_ip']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Status/Kondisi <span class="required">*</span></label>
                    <select name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="Baik" <?= (isset($_POST['status']) && $_POST['status'] == 'Baik') ? 'selected' : ''; ?>>Baik</option>
                        <option value="Maintenance" <?= (isset($_POST['status']) && $_POST['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="Rusak" <?= (isset($_POST['status']) && $_POST['status'] == 'Rusak') ? 'selected' : ''; ?>>Rusak</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal Produksi <span class="required">*</span></label>
                    <input type="date" name="tanggal_produksi" required
                           value="<?= isset($_POST['tanggal_produksi']) ? htmlspecialchars($_POST['tanggal_produksi']) : ''; ?>">
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>

    <script>
        // Initialize AOS
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init();
            
            // Load saved theme
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        }
    </script>
</body>
</html>