<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

/* TAMBAH BOOKING */
if (isset($_POST['tambah'])) {

    $customer_id = $_POST['customer_id'];
    $koleksi_id = $_POST['koleksi_id'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    mysqli_query($conn, "INSERT INTO booking
        (customer_id, koleksi_id, tanggal, status)
        VALUES
        ('$customer_id', '$koleksi_id', '$tanggal', '$status')
    ");

    header("Location: booking.php");
    exit;
}

/* HAPUS BOOKING */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM booking WHERE id=$id");

    header("Location: booking.php");
    exit;
}

/* EDIT BOOKING */
if (isset($_POST['edit'])) {

    $id = $_POST['id'];
    $customer_id = $_POST['customer_id'];
    $koleksi_id = $_POST['koleksi_id'];
    $tanggal = $_POST['tanggal'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE booking SET
        customer_id='$customer_id',
        koleksi_id='$koleksi_id',
        tanggal='$tanggal',
        status='$status'
        WHERE id=$id
    ");

    header("Location: booking.php");
    exit;
}

/* DATA EDIT */
$edit_data = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $edit_data = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM booking WHERE id=$id")
    );
}

/* DATA CUSTOMER */
$customers = mysqli_query(
    $conn,
    "SELECT * FROM customer ORDER BY nama ASC"
);

/* DATA KOLEKSI */
$koleksi = mysqli_query(
    $conn,
    "SELECT * FROM koleksi ORDER BY nama ASC"
);

/* DATA BOOKING */
$data = mysqli_query($conn, "
    SELECT 
        booking.*,
        customer.nama AS nama_customer,
        koleksi.nama AS nama_koleksi
    FROM booking
    JOIN customer ON booking.customer_id = customer.id
    JOIN koleksi ON booking.koleksi_id = koleksi.id
    ORDER BY booking.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Booking - KebayaRent</title>

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

            <a href="booking.php" class="active">
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


    <main class="content booking-page">

        <div class="topbar">

            <div>

                <p class="small-title">
                    KEBAYARENT
                </p>

                <h1>
                    Data Booking
                </h1>

            </div>

            <div class="profile">
                ♙ <?= htmlspecialchars($_SESSION['username']); ?>
            </div>

        </div>


        <!-- FORM BOOKING -->

        <div class="form-box">

            <h2>
                <?= $edit_data ? "Edit Booking" : "Tambah Booking"; ?>
            </h2>

            <form method="POST">

                <?php if ($edit_data): ?>

                    <input type="hidden"
                           name="id"
                           value="<?= $edit_data['id']; ?>">

                <?php endif; ?>


                <label>Customer</label>

                <select name="customer_id" required>

                    <option value="">
                        Pilih Customer
                    </option>

                    <?php while ($customer = mysqli_fetch_assoc($customers)): ?>

                        <option
                            value="<?= $customer['id']; ?>"
                            <?= isset($edit_data) && $edit_data['customer_id'] == $customer['id'] ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($customer['nama']); ?>
                        </option>

                    <?php endwhile; ?>

                </select>


                <label>Koleksi</label>

                <select name="koleksi_id" required>

                    <option value="">
                        Pilih Koleksi
                    </option>

                    <?php while ($item = mysqli_fetch_assoc($koleksi)): ?>

                        <option
                            value="<?= $item['id']; ?>"
                            <?= isset($edit_data) && $edit_data['koleksi_id'] == $item['id'] ? 'selected' : ''; ?>
                        >
                            <?= htmlspecialchars($item['nama']); ?>
                        </option>

                    <?php endwhile; ?>

                </select>


                <label>Tanggal Booking</label>

                <input
                    type="date"
                    name="tanggal"
                    required
                    value="<?= $edit_data['tanggal'] ?? ''; ?>"
                >


                <label>Status</label>

                <select name="status" required>

                    <option value="Pending"
                        <?= isset($edit_data) && $edit_data['status'] == 'Pending' ? 'selected' : ''; ?>>
                        Pending
                    </option>

                    <option value="Diproses"
                        <?= isset($edit_data) && $edit_data['status'] == 'Diproses' ? 'selected' : ''; ?>>
                        Diproses
                    </option>

                    <option value="Selesai"
                        <?= isset($edit_data) && $edit_data['status'] == 'Selesai' ? 'selected' : ''; ?>>
                        Selesai
                    </option>

                    <option value="Dibatalkan"
                        <?= isset($edit_data) && $edit_data['status'] == 'Dibatalkan' ? 'selected' : ''; ?>>
                        Dibatalkan
                    </option>

                </select>


                <div class="form-buttons">

                    <?php if ($edit_data): ?>

                        <button type="submit" name="edit">
                            Update Booking
                        </button>

                        <a href="booking.php">
                            Batal
                        </a>

                    <?php else: ?>

                        <button type="submit" name="tambah">
                            + Tambah Booking
                        </button>

                    <?php endif; ?>

                </div>

            </form>

        </div>


        <!-- DATA BOOKING -->

        <div class="section-title">

            <h2>
                Semua Booking
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


                        <div class="booking-right">

                            <span class="status status-<?= strtolower($row['status']); ?>">
                                <?= htmlspecialchars($row['status']); ?>
                            </span>

                            <div class="action-buttons">

                                <a href="booking.php?edit=<?= $row['id']; ?>">
                                    Edit
                                </a>

                                <a
                                    href="booking.php?hapus=<?= $row['id']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus booking ini?')"
                                >
                                    Hapus
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <p class="empty">
                    Belum ada booking.
                </p>

            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>