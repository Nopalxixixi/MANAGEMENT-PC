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
    <title>Tambah PC Baru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #00AEEF, #00E3A5);
            color: #1E293B;
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 700px;
            width: 100%;
            background: #FFFFFF;
            border-radius: 24px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            margin: 20px auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #00AEEF;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 30px;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: #00E3A5;
            gap: 12px;
        }

        .header {
            margin-bottom: 40px;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #64748B;
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
            color: #1E293B;
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
            background: #FFFFFF;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 14px;
            color: #1E293B;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        input[type="text"]::placeholder {
            color: #94A3B8;
        }

        select {
            cursor: pointer;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Inter', sans-serif;
            border-radius: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00AEEF, #00E3A5);
            color: #FFFFFF;
            box-shadow: 0 4px 15px rgba(0, 174, 239, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 174, 239, 0.4);
        }

        .btn-secondary {
            background: #F8FAFC;
            color: #64748B;
            border: 2px solid #E2E8F0;
        }

        .btn-secondary:hover {
            background: #E2E8F0;
            color: #1E293B;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            color: #991B1B;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .button-group {
            margin-top: 35px;
            display: flex;
            gap: 12px;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        
        <div class="header">
            <h1>Tambah PC Baru</h1>
            <p class="subtitle">Isi formulir untuk menambah data PC</p>
        </div>

        <?php if ($error): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama PC <span class="required">*</span></label>
                    <input type="text" name="nama_pc" placeholder="Contoh: PC-MU-TIK01" required 
                           value="<?= isset($_POST['nama_pc']) ? htmlspecialchars($_POST['nama_pc']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Nama User <span class="required">*</span></label>
                    <input type="text" name="nama_user" placeholder="Contoh: Naufal Bima Raditya" required
                           value="<?= isset($_POST['nama_user']) ? htmlspecialchars($_POST['nama_user']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Nomor Asset <span class="required">*</span></label>
                    <input type="text" name="nomor_asset" placeholder="Contoh: 19928" required
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
                        <option value="Perbaikan" <?= (isset($_POST['status']) && $_POST['status'] == 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
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
</body>
</html>