<?php 
session_start();
require_once '../dbcontroller.php';
$db = new DBConnection();

$jumlahdataperhalaman = 10;
$totaldata = $db->rowCOUNT("SELECT * FROM t_peminjaman");
$jumlahhalaman = ceil($totaldata / $jumlahdataperhalaman);
if (isset($_GET['halaman'])) {
	$halamanberapa = $_GET['halaman'];
} else {
	$halamanberapa = 1;
}

$halaman = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;

// deskripsikan bisa pakai keduanya. (halaman dan tidak ada)
$awaldata = $jumlahdataperhalaman * $halamanberapa - $jumlahdataperhalaman;

$Previous = $halaman - 1;
$Next = $halaman + 1;

$halamanawal =
$halaman > 1 ? $halaman * $jumlahdataperhalaman - $jumlahdataperhalaman : 0;

$query = "SELECT t_peminjaman.f_id AS idpeminjaman, t_peminjaman.f_tanggalpeminjaman, t_detailpeminjaman.f_tanggalkembali, t_anggota.f_nama AS namaanggota, t_anggota.f_kelas, t_anggota.f_jurusan, t_admin.f_nama AS namaadmin, t_detailbuku.f_id AS iddetailbuku, t_buku.f_judul, t_kategori.f_kategori
FROM t_peminjaman
LEFT JOIN t_anggota ON t_peminjaman.f_idanggota = t_anggota.f_id
LEFT JOIN t_admin ON t_peminjaman.f_idadmin = t_admin.f_id
LEFT JOIN t_detailpeminjaman ON t_peminjaman.f_id = t_detailpeminjaman.f_idpeminjaman
LEFT JOIN t_detailbuku ON t_detailpeminjaman.f_iddetailbuku = t_detailbuku.f_id
LEFT JOIN t_buku ON t_detailbuku.f_idbuku = t_buku.f_id
LEFT JOIN t_kategori ON t_buku.f_idkategori = t_kategori.f_id
WHERE t_peminjaman.f_id IS NOT NULL AND t_buku.f_judul IS NOT NULL AND t_anggota.f_nama = '$_SESSION[namaAnggota]'
ORDER BY t_peminjaman.f_tanggalpeminjaman DESC LIMIT $awaldata, $jumlahdataperhalaman";
$peminjaman = $db->getALL($query);

	// tombol cari ditekan
if(isset($_POST["cari"])) {
	// $peminjaman = cari($_POST["keyword"]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Halaman Data Buku</title>

	<!-- Icon -->
	<link rel="shortcut icon" href="../../assets/icon/favicon.png" type="image/x-icon">

	<!-- Link CSS -->
	<link rel="stylesheet" href="../../assets/style/style.css">

	<!-- Link Bootstrap -->
	<link rel="stylesheet" href="../../assets/bootstrap-5.3.0/css/bootstrap.min.css">


	<!-- Link Font Awesome -->
	<link rel="stylesheet" href="../../assets/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="../../assets/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="../../assets/fontawesome/css/solid.min.css">

	<!-- Sweetalert -->
	<link rel="stylesheet" href="../../node_modules/sweetalert2/dist/sweetalert2.min.css">

	<style>
		.username {
			border: none;
			color: rgb(255, 255, 255);
			padding: 8px 12px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			margin: 8px 2px;
			border-radius: 0.375rem;
		}

		.header-m {
			margin-bottom: 5px;
		}

		.kiri-form {
			float: right;
		}

		table {
			border: none;
		}

		table tr td {
			padding: 1rem;
		}

		table thead tr th {
			background-color: #000;
			color: #fff;
			padding: 1rem;
		}

		table tr:nth-child(even) {
			background-color: #212529;
		}

		table tr:nth-child(odd) {
			background-color: #2c3034;
		}

		h1 {
			font-size: 2.25rem;
			margin: .5rem 0 2rem 0;
		}

		.btn {
			padding: .35rem .375rem;
			font-size: .8rem;
		}
	</style>
</head>
<body data-bs-theme="dark">
	<div class="container-fluid">
		<header class="d-flex flex-wrap align-items-center justify-content-center justify-content-around py-3 px-5">
			<a href="../home/index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
				<span class="fs-4">Perpustakaan</span>
			</a>

			<ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
				<li class="nav-item">
					<a href="../home/index.php" class="nav-link text-white" aria-current="page">
						Home
					</a>
				</li>
				<li>
					<a href="../buku/buku.php" class="nav-link text-white">
						Buku
					</a>
				</li>
				<?php if($_SESSION['level'] == 'Anggota Perpustakaan') {
					echo '
					<li>
					<a href="../peminjaman/riwayatPeminjaman.php" class="nav-link active">
					Riwayat Peminjaman
					</a>
					</li>
					<li>';
				} ?>
			</ul>

			<div class="d-flex col-md-3 text-end align-items-center justify-content-center">
				<!-- <button type="button" class="btn btn-outline-primary me-2">Login</button>
					<button type="button" class="btn btn-primary">Sign-up</button> -->
					<div class="dropdown text-end">
						<a href="#" class="text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							<img src="../../assets/profil/default-profil.png" alt="" width="32" height="32" class="rounded-circle me-2">
							<strong><?= $_SESSION['username']; ?></strong>
						</a>
						<ul class="dropdown-menu dropdown-menu-dark text-small shadow">
							<li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
						</ul>
					</div>
				</div>

			</header>
		</div>


		<!-- Content -->
		<div class="content">


			<center><h1>Daftar Peminjaman Buku Perpustakaan</h1></center>

			<form action="" method="post">
				<a class="btn btn-outline-success" href="../laporan/exportRiwayatPeminjaman.php?cetak=xls" role="button" target="_blank">Export XLS</a>
				<a class="btn btn-outline-danger" href="../laporan/exportRiwayatPeminjaman.php?cetak=pdf" role="button" target="_blank">Export PDF</a>
				<input type="text" name="keyword" id="keyword" size="40" autofocus placeholder="masukkan keyword pencarian.." autocomplete="off">
				<button type="submit" name="cari" class="tbl-cari">Cari!</button>

				<?php if (isset($_SESSION['username'])) { ?>
					<a href="../logout.php" class="tbl-hapus kiri-form" role="button" onclick="return confirm('Yakin?');" title="Logout"><i class="fa-sharp fa-solid fa-right-from-bracket"></i></a>
					<div class="username kiri-form">
						<?= $_SESSION['username'] ?>
					</div>
				<?php } ?>
			</form>	
			<br>

			<table border="0" cellpadding="10" cellspacing="0" class="mb-4">
				<thead>
					<tr>
						<th>#</th>
						<th>Nama</th>
						<th>Nama Admin</th>
						<th>Judul Buku</th>
						<th>Kategori</th>
						<th class="text-center">Kelas</th>
						<th class="text-center">Jurusan</th>
						<th class="text-center">Tanggal Pinjam</th>
						<th class="text-center">Tanggal Kembali</th>
						<th class="text-center">Status</th>

						<?php if($_SESSION['level'] == 'Admin') { ?>
							<th><center>Aksi</center></th>
						<?php } ?>
					</tr>
				</thead>
				<?php $i = $halamanawal + 1;
				?>
				<?php if ($peminjaman) {foreach($peminjaman as $data) : ?>
					<tr>
						<td><?= $i; ?></td>
						<td><?php if($data['namaanggota'] == '') {echo 'Anggota tidak terdaftar';} else {echo $data['namaanggota'];} ?></td>
						<td><?= $data["namaadmin"]; ?></td>
						<td><?= $data["f_judul"]; ?></td>
						<td><?= $data["f_kategori"]; ?></td>
						<td class="text-center"><?= $data['f_kelas']; ?></td>
						<td class="text-center"><?= $data['f_jurusan']; ?></td>
						<td class="text-center"><?= $data["f_tanggalpeminjaman"]; ?></td>
						<td class="text-center"><?= $data["f_tanggalkembali"]; ?></td>
						<td class="text-center"><?php if($data['f_tanggalkembali'] == '0000-00-00'){ echo 'Belum Kembali';} else {echo 'Sudah Kembali';}?></td>
						<?php if($_SESSION['level'] == 'Admin') { ?>
							<td>
								<center>
									<a href="ubahPeminjaman.php?id=<?= $data["idpeminjaman"]; ?>"><i class="fa-sharp fa-solid fa-edit tbl-edit"></i></a>
								</center>
							</td>
						<?php } ?>	
					</tr>
					<?php $i++; ?>
				<?php endforeach; } else {?>
					<div class="alert alert-danger">
						Anda Belum Meminjam Buku!
					</div>
				<?php } ?>
			</table>

			<div class="pagination-flex">
				<div class="pagination">
					<ul class="page-list">
						<li class="page-item">
							<a class="page-link" <?php if ($halaman > 1) {
								echo "href='?halaman=$Previous'";
							} ?>>Previous</a>
						</li>
						<?php for ($i = 1; $i <= $jumlahhalaman; $i++): ?>
							<li class="page-item"><a class="page-link" href="?halaman=<?= $i ?>"><?= $i ?></a></li>
						<?php endfor; ?>
						<li class="page-item">
							<a  class="page-link" <?php if (
								$halaman < $jumlahhalaman
							) {
								echo "href='?halaman=$Next'";
							} ?>>Next</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</main>

	
	<script src="../../assets/bootstrap-5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>