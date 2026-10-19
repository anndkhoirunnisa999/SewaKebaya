<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$username = $_SESSION['username'];

$koleksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM koleksi"));
$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM customer"));
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking"));

$pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total) AS total FROM transaksi"));
$total_pendapatan = $pendapatan['total'] ?? 0;

$data_koleksi = mysqli_query($conn, "SELECT * FROM koleksi ORDER BY id DESC LIMIT 3");

$data_booking = mysqli_query($conn, "
    SELECT booking.*, customer.nama AS nama_customer, koleksi.nama AS nama_koleksi
    FROM booking
    JOIN customer ON booking.customer_id = customer.id
    JOIN koleksi ON booking.koleksi_id = koleksi.id
    ORDER BY booking.id DESC
    LIMIT 3
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - KebayaRent</title>
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
            <a href="dashboard.php" class="active">⌂ Dashboard</a>
            <a href="koleksi.php">♧ Koleksi</a>
            <a href="customer.php">♙ Customer</a>
            <a href="booking.php">◷ Booking</a>
            <a href="transaksi.php">◇ Transaksi</a>
        </nav>

        <a href="logout.php" class="logout">
            ↪ Logout
        </a>

    </aside>


    <!-- CONTENT -->
    <main class="content dashboard-page">

        <div class="topbar">
            <div>
                <p class="small-title">DASHBOARD</p>
                <h1>Selamat datang, <?= htmlspecialchars($username); ?> ✦</h1>
            </div>

            <div class="profile">
                ♙ <?= htmlspecialchars($username); ?>
            </div>
        </div>


        <!-- STATISTIK -->
        <div class="cards">

            <div class="card">
                <span>♡</span>
                <h2><?= $koleksi['total']; ?></h2>
                <p>Koleksi</p>
            </div>

            <div class="card">
                <span>♡</span>
                <h2><?= $customer['total']; ?></h2>
                <p>Customer</p>
            </div>

            <div class="card">
                <span>♡</span>
                <h2><?= $booking['total']; ?></h2>
                <p>Booking</p>
            </div>

        </div>


        <!-- PENDAPATAN -->
        <div class="income">
            <p>Total Pendapatan</p>
            <h2>Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h2>
        </div>


        <!-- KOLEKSI -->
        <div class="section-title">
            <h2>Koleksi Terbaru</h2>
            <a href="koleksi.php">Lihat semua →</a>
        </div>

        <div class="collection-grid">

            <?php if (mysqli_num_rows($data_koleksi) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($data_koleksi)): ?>

                    <div class="collection-card">

                        <?php if (!empty($row['foto'])): ?>
                            <img src="images/<?= htmlspecialchars($row['foto']); ?>">
                        <?php else: ?>
                            <div class="no-image">
                                FOTO
                            </div>
                        <?php endif; ?>

                        <div class="collection-info">
                            <h3><?= htmlspecialchars($row['nama']); ?></h3>
                            <p><?= htmlspecialchars($row['kategori']); ?></p>
                            <strong>
                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                            </strong>
                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="empty">Belum ada koleksi.</p>

            <?php endif; ?>

        </div>


        <!-- BOOKING -->
        <div class="section-title">
            <h2>Booking Terbaru</h2>
            <a href="booking.php">Lihat semua →</a>
        </div>

        <div class="booking-list">

            <?php if (mysqli_num_rows($data_booking) > 0): ?>

                <?php while ($row = mysqli_fetch_assoc($data_booking)): ?>

                    <div class="booking-item">

                        <div>
                            <strong><?= htmlspecialchars($row['nama_customer']); ?></strong>
                            <span>• <?= htmlspecialchars($row['nama_koleksi']); ?></span>
                        </div>

                        <div>
                            <?= date('d M Y', strtotime($row['tanggal'])); ?>
                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="empty">Belum ada booking.</p>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>