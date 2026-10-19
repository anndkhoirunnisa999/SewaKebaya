<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

/* TAMBAH CUSTOMER */
if (isset($_POST['tambah'])) {

    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    mysqli_query($conn, "INSERT INTO customer (nama, no_hp, alamat)
        VALUES ('$nama', '$no_hp', '$alamat')");

    header("Location: customer.php");
    exit;
}

/* HAPUS CUSTOMER */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM customer WHERE id=$id");

    header("Location: customer.php");
    exit;
}

/* EDIT CUSTOMER */
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];

    mysqli_query($conn, "UPDATE customer SET
        nama='$nama',
        no_hp='$no_hp',
        alamat='$alamat'
        WHERE id=$id
    ");

    header("Location: customer.php");
    exit;
}

/* DATA EDIT */
$edit_data = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $edit_data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM customer WHERE id=$id")
    );
}

/* TAMPIL DATA */
$data = mysqli_query($conn, "SELECT * FROM customer ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer - KebayaRent</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <div class="brand">
            KEBAYARENT
        </div>

        <nav>

            <a href="dashboard.php">
                ⌂ Dashboard
            </a>

            <a href="koleksi.php">
                ♧ Koleksi
            </a>

            <a href="customer.php" class="active">
                ♙ Customer
            </a>

            <a href="booking.php">
                ◷ Booking
            </a>

            <a href="transaksi.php">
                ◇ Transaksi
            </a>

        </nav>

        <a href="logout.php" class="logout">
            ↪ Logout
        </a>

    </aside>


    <main class="content customer-page">

        <div class="topbar">

            <div>

                <p class="small-title">
                    KEBAYARENT
                </p>

                <h1>
                    Data Customer
                </h1>

            </div>

            <div class="profile">
                ♙ <?= htmlspecialchars($_SESSION['username']); ?>
            </div>

        </div>


        <!-- FORM -->

        <div class="form-box">

            <h2>
                <?= $edit_data ? "Edit Customer" : "Tambah Customer"; ?>
            </h2>

            <form method="POST">

                <?php if ($edit_data): ?>

                    <input type="hidden"
                           name="id"
                           value="<?= $edit_data['id']; ?>">

                <?php endif; ?>


                <label>Nama Customer</label>

                <input type="text"
                       name="nama"
                       required
                       value="<?= $edit_data['nama'] ?? ''; ?>">


                <label>No. HP</label>

                <input type="text"
                       name="no_hp"
                       required
                       value="<?= $edit_data['no_hp'] ?? ''; ?>">


                <label>Alamat</label>

                <input type="text"
                       name="alamat"
                       value="<?= $edit_data['alamat'] ?? ''; ?>">


                <div class="form-buttons">

                    <?php if ($edit_data): ?>

                        <button type="submit" name="edit">
                            Update Customer
                        </button>

                        <a href="customer.php">
                            Batal
                        </a>

                    <?php else: ?>

                        <button type="submit" name="tambah">
                            + Tambah Customer
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- DATA CUSTOMER -->

        <div class="section-title">

            <h2>
                Semua Customer
            </h2>

        </div>


        <div class="customer-list">

            <?php if (mysqli_num_rows($data) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($data)): ?>

                    <div class="customer-item">

                        <div class="customer-info">

                            <h3>
                                <?= htmlspecialchars($row['nama']); ?>
                            </h3>

                            <p>
                                ☎ <?= htmlspecialchars($row['no_hp']); ?>
                            </p>

                            <p>
                                <?= htmlspecialchars($row['alamat']); ?>
                            </p>

                        </div>


                        <div class="action-buttons">

                            <a href="customer.php?edit=<?= $row['id']; ?>">
                                Edit
                            </a>

                            <a
                                href="customer.php?hapus=<?= $row['id']; ?>"
                                onclick="return confirm('Yakin ingin menghapus customer ini?')"
                            >
                                Hapus
                            </a>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="empty">
                    Belum ada customer.
                </p>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>