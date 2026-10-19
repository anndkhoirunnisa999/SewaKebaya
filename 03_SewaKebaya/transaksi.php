<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

/* TAMBAH TRANSAKSI */
if (isset($_POST['tambah'])) {

    $booking_id = $_POST['booking_id'];
    $total = $_POST['total'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    mysqli_query($conn, "INSERT INTO transaksi
        (booking_id, total, tanggal, status)
        VALUES
        ('$booking_id', '$total', '$tanggal', '$status')
    ");

    header("Location: transaksi.php");
    exit;
}

/* HAPUS TRANSAKSI */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM transaksi WHERE id=$id");

    header("Location: transaksi.php");
    exit;
}

/* EDIT TRANSAKSI */
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $booking_id = $_POST['booking_id'];
    $total = $_POST['total'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE transaksi SET
        booking_id='$booking_id',
        total='$total',
        tanggal='$tanggal',
        status='$status'
        WHERE id=$id
    ");

    header("Location: transaksi.php");
    exit;
}

/* DATA EDIT */
$edit_data = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $edit_data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM transaksi WHERE id=$id")
    );
}

/* DATA BOOKING */
$bookings = mysqli_query($conn, "
    SELECT 
        booking.id,
        customer.nama AS nama_customer,
        koleksi.nama AS nama_koleksi,
        booking.tanggal
    FROM booking
    JOIN customer ON booking.customer_id = customer.id
    JOIN koleksi ON booking.koleksi_id = koleksi.id
    ORDER BY booking.id DESC
");

/* DATA TRANSAKSI */
$data = mysqli_query($conn, "
    SELECT
        transaksi.*,
        customer.nama AS nama_customer,
        koleksi.nama AS nama_koleksi
    FROM transaksi
    JOIN booking ON transaksi.booking_id = booking.id
    JOIN customer ON booking.customer_id = customer.id
    JOIN koleksi ON booking.koleksi_id = koleksi.id
    ORDER BY transaksi.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Transaksi - KebayaRent</title>

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

            <a href="customer.php">
                ♙ Customer
            </a>

            <a href="booking.php">
                ◷ Booking
            </a>

            <a href="transaksi.php" class="active">
                ◇ Transaksi
            </a>

        </nav>

        <a href="logout.php" class="logout">
            ↪ Logout
        </a>

    </aside>


    <main class="content transaction-page">

        <div class="topbar">

            <div>

                <p class="small-title">
                    KEBAYARENT
                </p>

                <h1>
                    Data Transaksi
                </h1>

            </div>

            <div class="profile">
                ♙ <?= htmlspecialchars($_SESSION['username']); ?>
            </div>

        </div>


        <!-- FORM TRANSAKSI -->

        <div class="form-box">

            <h2>
                <?= $edit_data ? "Edit Transaksi" : "Tambah Transaksi"; ?>
            </h2>

            <form method="POST">

                <?php if ($edit_data): ?>

                    <input type="hidden"
                           name="id"
                           value="<?= $edit_data['id']; ?>">

                <?php endif; ?>


                <label>Booking</label>

                <select name="booking_id" required>

                    <option value="">
                        Pilih Booking
                    </option>

                    <?php while ($booking = mysqli_fetch_assoc($bookings)): ?>

                        <option
                            value="<?= $booking['id']; ?>"
                            <?= isset($edit_data) && $edit_data['booking_id'] == $booking['id'] ? 'selected' : ''; ?>
                        >

                            <?= htmlspecialchars($booking['nama_customer']); ?>
                            -
                            <?= htmlspecialchars($booking['nama_koleksi']); ?>
                            -
                            <?= date('d/m/Y', strtotime($booking['tanggal'])); ?>

                        </option>

                    <?php endwhile; ?>

                </select>


                <label>Total Pembayaran</label>

                <input
                    type="number"
                    name="total"
                    required
                    placeholder="Contoh: 250000"
                    value="<?= $edit_data['total'] ?? ''; ?>"
                >


                <label>Tanggal Transaksi</label>

                <input
                    type="date"
                    name="tanggal"
                    required
                    value="<?= $edit_data['tanggal'] ?? date('Y-m-d'); ?>"
                >


                <label>Status Pembayaran</label>

                <select name="status" required>

                    <option value="Belum Lunas"
                        <?= isset($edit_data) && $edit_data['status'] == 'Belum Lunas' ? 'selected' : ''; ?>>
                        Belum Lunas
                    </option>

                    <option value="Lunas"
                        <?= isset($edit_data) && $edit_data['status'] == 'Lunas' ? 'selected' : ''; ?>>
                        Lunas
                    </option>

                </select>


                <div class="form-buttons">

                    <?php if ($edit_data): ?>

                        <button type="submit" name="edit">
                            Update Transaksi
                        </button>

                        <a href="transaksi.php">
                            Batal
                        </a>

                    <?php else: ?>

                        <button type="submit" name="tambah">
                            + Tambah Transaksi
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- DATA TRANSAKSI -->

        <div class="section-title">

            <h2>
                Semua Transaksi
            </h2>

        </div>


        <div class="booking-list">

            <?php if (mysqli_num_rows($data) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($data)): ?>

                    <div class="booking-item">

                        <div>

                            <strong>
                                <?= htmlspecialchars($row['nama_customer']); ?>
                            </strong>

                            <span>
                                • <?= htmlspecialchars($row['nama_koleksi']); ?>
                            </span>

                            <br>

                            <small>
                                <?= date('d M Y', strtotime($row['tanggal'])); ?>
                            </small>

                        </div>


                        <div class="transaction-right">

                            <strong>
                                Rp <?= number_format($row['total'], 0, ',', '.'); ?>
                            </strong>

                            <span class="status
                                <?= $row['status'] == 'Lunas'
                                    ? 'status-selesai'
                                    : 'status-pending'; ?>">
                                <?= htmlspecialchars($row['status']); ?>
                            </span>

                            <div class="action-buttons">

                                <a href="transaksi.php?edit=<?= $row['id']; ?>">
                                    Edit
                                </a>

                                <a
                                    href="transaksi.php?hapus=<?= $row['id']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus transaksi ini?')"
                                >
                                    Hapus
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="empty">
                    Belum ada transaksi.
                </p>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>