<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

/* TAMBAH DATA */
if (isset($_POST['tambah'])) {

    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $foto = $_FILES['foto']['name'];
    $tmp = $_FILES['foto']['tmp_name'];

    if ($foto != "") {
        move_uploaded_file($tmp, "images/" . $foto);
    }

    mysqli_query($conn, "INSERT INTO koleksi
        (nama, kategori, harga, stok, foto)
        VALUES
        ('$nama', '$kategori', '$harga', '$stok', '$foto')
    ");

    header("Location: koleksi.php");
    exit;
}


/* HAPUS DATA */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM koleksi WHERE id=$id")
    );

    if (!empty($data['foto']) && file_exists("images/" . $data['foto'])) {
        unlink("images/" . $data['foto']);
    }

    mysqli_query($conn, "DELETE FROM koleksi WHERE id=$id");

    header("Location: koleksi.php");
    exit;
}


/* EDIT DATA */
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $foto_baru = $_FILES['foto']['name'];

    if ($foto_baru != "") {

        $tmp = $_FILES['foto']['tmp_name'];

        $data_lama = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT foto FROM koleksi WHERE id=$id")
        );

        if (!empty($data_lama['foto']) &&
            file_exists("images/" . $data_lama['foto'])) {

            unlink("images/" . $data_lama['foto']);
        }

        move_uploaded_file($tmp, "images/" . $foto_baru);

        mysqli_query($conn, "UPDATE koleksi SET
            nama='$nama',
            kategori='$kategori',
            harga='$harga',
            stok='$stok',
            foto='$foto_baru'
            WHERE id=$id
        ");

    } else {

        mysqli_query($conn, "UPDATE koleksi SET
            nama='$nama',
            kategori='$kategori',
            harga='$harga',
            stok='$stok'
            WHERE id=$id
        ");
    }

    header("Location: koleksi.php");
    exit;
}


/* DATA UNTUK EDIT */
$edit_data = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $edit_data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM koleksi WHERE id=$id")
    );
}


/* TAMPILKAN DATA */
$data = mysqli_query($conn, "SELECT * FROM koleksi ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Koleksi - KebayaRent</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">
            KEBAYARENT
        </div>

        <nav>

            <a href="dashboard.php">
                ⌂ Dashboard
            </a>

            <a href="koleksi.php" class="active">
                ♧ Koleksi
            </a>

            <a href="customer.php">
                ♙ Customer
            </a>

            <a href="booking.php">
                ◷ Booking
            </a>

            <a href="transaksi.php">
                ◇ Transaksi
            </a>

        </nav>

        <a href="login.php" class="logout">
            ↪ Logout
        </a>

    </aside>


    <!-- CONTENT -->

    <main class="content koleksi-page">

        <div class="topbar">

            <div>

                <p class="small-title">
                    KEBAYARENT
                </p>

                <h1>
                    Koleksi Kebaya
                </h1>

            </div>

            <div class="profile">
                ♙ <?= htmlspecialchars($_SESSION['username']); ?>
            </div>

        </div>


        <!-- FORM TAMBAH / EDIT -->

        <div class="form-box">

            <h2>
                <?= $edit_data ? "Edit Koleksi" : "Tambah Koleksi"; ?>
            </h2>

            <form method="POST" enctype="multipart/form-data">

                <?php if ($edit_data): ?>

                    <input type="hidden"
                           name="id"
                           value="<?= $edit_data['id']; ?>">

                <?php endif; ?>


                <label>Nama Koleksi</label>

                <input type="text"
                       name="nama"
                       required
                       value="<?= $edit_data['nama'] ?? ''; ?>">


                <label>Kategori</label>

                <select name="kategori" required>

                    <option value="">Pilih Kategori</option>

                    <option value="Kebaya"
                        <?= isset($edit_data) && $edit_data['kategori'] == 'Kebaya' ? 'selected' : ''; ?>>
                        Kebaya
                    </option>

                    <option value="Dress"
                        <?= isset($edit_data) && $edit_data['kategori'] == 'Dress' ? 'selected' : ''; ?>>
                        Dress
                    </option>

                </select>


                <label>Harga Sewa</label>

                <input type="number"
                       name="harga"
                       required
                       value="<?= $edit_data['harga'] ?? ''; ?>">


                <label>Stok</label>

                <input type="number"
                       name="stok"
                       required
                       value="<?= $edit_data['stok'] ?? ''; ?>">


                <label>
                    <?= $edit_data ? "Ganti Foto" : "Foto"; ?>
                </label>

                <input type="file"
                       name="foto"
                       accept="image/*">


                <?php if ($edit_data && !empty($edit_data['foto'])): ?>

                    <img
                        src="images/<?= htmlspecialchars($edit_data['foto']); ?>"
                        class="preview-foto"
                    >

                <?php endif; ?>


                <div class="form-buttons">

                    <?php if ($edit_data): ?>

                        <button type="submit" name="edit">
                            Update Koleksi
                        </button>

                        <a href="koleksi.php">
                            Batal
                        </a>

                    <?php else: ?>

                        <button type="submit" name="tambah">
                            + Tambah Koleksi
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- DAFTAR KOLEKSI -->

        <div class="section-title">

            <h2>
                Semua Koleksi
            </h2>

        </div>


        <div class="collection-grid">

            <?php while ($row = mysqli_fetch_assoc($data)): ?>

                <div class="collection-card">

                    <?php if (!empty($row['foto'])): ?>

                        <img
                            src="images/<?= htmlspecialchars($row['foto']); ?>"
                        >

                    <?php else: ?>

                        <div class="no-image">
                            FOTO
                        </div>

                    <?php endif; ?>


                    <div class="collection-info">

                        <h3>
                            <?= htmlspecialchars($row['nama']); ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($row['kategori']); ?>
                        </p>

                        <strong>
                            Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                        </strong>

                        <p>
                            Stok: <?= $row['stok']; ?>
                        </p>


                        <div class="action-buttons">

                            <a href="koleksi.php?edit=<?= $row['id']; ?>">
                                Edit
                            </a>

                            <a
                                href="koleksi.php?hapus=<?= $row['id']; ?>"
                                onclick="return confirm('Yakin ingin menghapus koleksi ini?')"
                            >
                                Hapus
                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </main>

</div>

</body>
</html>