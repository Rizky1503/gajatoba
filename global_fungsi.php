<?php session_start();

	/**
	*	FUNGSI GLOBAL SISTEM ALUMNI
	*
	*/
	require_once(__DIR__ . '/lib/db.class.php');

	/**
	*	Cek apakah user sudah melakukan login
	*	Jika session sudah ada, maka kembalikan status true, selain itu false
	*	
	*	@return 	boolean
	*	
	*/
	function sudah_login()
	{
		if (isset($_SESSION["a2_username"])) {
			$a2_username      = $_SESSION["a2_username"];
			$a2_nama_depan    = $_SESSION["a2_nama_depan"];
			$a2_nama_belakang = $_SESSION["a2_nama_belakang"];
			$a2_level         = $_SESSION["a2_level"];

			if ($a2_username!="" && $a2_nama_depan!="" && $a2_level!="") {
				return TRUE;
			}
		}
		else {
			return FALSE;
		}
	}


	/**
	*	Ambil data sistem saat ini
	*	Set jadi array dan kembalikan
	*	
	*	@return 	array 		$settingan
	*	
	*/
	function setting_sistem()
	{
		$db    = new DB();
		$query = "SELECT * FROM aluni_pengaturan";
		$datas = $db->query($query);
		foreach ($datas as $data) {
			$settingan[$data["nama_pengaturan"]] = $data["nilai_pengaturan"];
		}
		return $settingan;
	}


	/**
	*	Tampilkan menu berdasarkan modul umum
	*	
	*	@param 		string 		$tipe (system / common)
	*	@param 		string 		$hak_akses
	*	@return 	array 		$datas
	*	
	*/
	function tampil_menu($tipe, $hak_akses = "")
	{
		$db = new DB();
		if ($hak_akses!="") {
			if (strpos($hak_akses, "|")!==FALSE) {
				$mod_array = explode("|", $hak_akses);
				foreach ($mod_array as $ma => $value) {
					$mod_array2[] = "'".$value."'";
				}
				$mod_ids = implode(",", $mod_array2);
				$query   = "SELECT * FROM aluni_m_modul WHERE tipe_modul = '$tipe' AND id_modul IN ($mod_ids)";
			}
			else if ($hak_akses == "*") {
				$query = "SELECT * FROM aluni_m_modul WHERE tipe_modul = '$tipe'";
			}
			else {
				$query   = "SELECT * FROM aluni_m_modul WHERE tipe_modul = '$tipe' AND id_modul = '$hak_akses'";
			}
		}
		$datas = $db->query($query);
		return $datas;
	}


	/**
	*	Ambil data dari database
	*	
	*	@param 		string 		$tabel
	*	@param 		string 		$kolom
	*	@param 		string 		$kriteria
	*	@param 		string 		$urutan
	*	@param 		string 		$tambahan
	*	@return 	array 		$datas
	*	
	*/
	function ambil_data_global($tabel, $kolom, $kriteria="", $urutan="", $tambahan="")
	{
		$db    = new DB();
		$query = "SELECT $kolom FROM $tabel ";
		if ($kriteria!="") {
			$query .= " WHERE $kriteria ";
		}
		if ($urutan!="") {
			$query .= " ORDER BY $urutan ";
		}
		if ($tambahan!="") {
			$query .= $tambahan;
		}

		$datas = $db->query($query);
		return $datas;
	}


	/**
	*	Ambil Data Anggota Lengkap
	*	
	*	@param 		string 		$kolom
	*	@param 		string 		$kriteria
	*	@param 		string 		$urutan
	*	@param 		string 		$tambahan
	*	@return 	array 		$datas
	*	
	*/
	function data_anggota_lengkap($kolom="", $kriteria="", $urutan="", $tambahan="")
	{
		$db = new DB();
		if ($kolom=="") {
			$kolom = "aluni_anggota_dasar.id_anggota, 
				aluni_anggota_dasar.nama_lengkap, 
				aluni_anggota_dasar.nama_panggilan, 
				aluni_anggota_dasar.jenis_kelamin, 
				aluni_anggota_dasar.tempat_lahir, 
				aluni_anggota_dasar.tanggal_lahir, 
				aluni_anggota_dasar.agama AS id_agama, 
				aluni_m_agama.nama_agama AS agama, 
				aluni_anggota_dasar.foto, 
				aluni_anggota_dasar.id_provinsi, 
				pro1.nama_provinsi AS provinsi, 
				aluni_anggota_dasar.id_kota, 
				kot1.nama_kota AS kota, 
				aluni_anggota_dasar.alamat, 
				aluni_anggota_dasar.aktif, 
				aluni_anggota_akademik.angkatan, 
				aluni_anggota_akademik.tahun_masuk, 
				aluni_anggota_akademik.tahun_keluar, 
				aluni_anggota_akademik.kelas_terakhir, 
				aluni_anggota_akademik.catatan, 
				aluni_anggota_keluarga.nama_pasangan, 
				aluni_anggota_keluarga.nama_anak, 
				aluni_anggota_orang_tua.nama_ayah, 
				aluni_anggota_orang_tua.nama_ibu, 
				aluni_anggota_orang_tua.nama_wali, 
				aluni_anggota_orang_tua.id_provinsi AS id_provinsi_ot, 
				pro2.nama_provinsi AS provinsi_orang_tua, 
				aluni_anggota_orang_tua.id_kota AS id_kota_ot, 
				kot2.nama_kota AS kota_orang_tua, 
				aluni_anggota_orang_tua.alamat_orang_tua, 
				aluni_anggota_kontak.no_rumah, 
				aluni_anggota_kontak.no_handphone, 
				aluni_anggota_kontak.no_handphone2, 
				aluni_anggota_kontak.pin_blackberry, 
				aluni_anggota_kontak.alamat_email, 
				aluni_anggota_kontak.alamat_website, 
				aluni_anggota_kontak.facebook, 
				aluni_anggota_kontak.twitter, 
				aluni_pengguna.username, 
				aluni_pengguna.nama_depan, 
				aluni_pengguna.nama_belakang 
				";
		}
		$query = "SELECT $kolom 
			FROM aluni_anggota_dasar 
			LEFT JOIN aluni_anggota_keluarga ON aluni_anggota_dasar.id_anggota = aluni_anggota_keluarga.id_anggota 
			LEFT JOIN aluni_anggota_akademik ON aluni_anggota_dasar.id_anggota = aluni_anggota_akademik.id_anggota 
			LEFT JOIN aluni_anggota_orang_tua ON aluni_anggota_dasar.id_anggota = aluni_anggota_orang_tua.id_anggota 
			LEFT JOIN aluni_anggota_kontak ON aluni_anggota_dasar.id_anggota = aluni_anggota_kontak.id_anggota 
			LEFT JOIN aluni_m_agama ON aluni_anggota_dasar.agama = aluni_m_agama.id_agama 
			LEFT JOIN aluni_m_provinsi pro1 ON aluni_anggota_dasar.id_provinsi = pro1.id_provinsi 
			LEFT JOIN aluni_m_kota kot1 ON aluni_anggota_dasar.id_kota = kot1.id_kota
			LEFT JOIN aluni_m_provinsi pro2 ON aluni_anggota_orang_tua.id_provinsi = pro2.id_provinsi 
			LEFT JOIN aluni_m_kota kot2 ON aluni_anggota_orang_tua.id_kota = kot2.id_kota 
			LEFT JOIN aluni_pengguna ON aluni_anggota_dasar.id_anggota = aluni_pengguna.id_anggota
		";
		if ($kriteria!="") {
			$query .= " WHERE $kriteria ";
		}
		if ($urutan!="") {
			$query .= " ORDER BY $urutan ";
		}
		if ($tambahan!="") {
			$query .= $tambahan;
		}
		$datas = $db->query($query);
		return $datas;
	}


	/**
	*	Ambil Data anggota pengguna
	*	
	*	@param 		string 		$kriteria
	*	@param 		string 		$urutan
	*	@param 		string 		$tambahan
	*	@return 	array 		$datas
	*	
	*/
	function data_pengguna_lengkap($kriteria="", $urutan="", $tambahan="")
	{
		$db = new DB();
		$query = "SELECT 
				a.username, 
				a.level, 
				a.nama_depan, 
				a.nama_belakang, 
				a.aktif, 
				a.foto, 
				a.id_anggota, 
				b.hak_akses, 
				c.password, 
				c.status 
			FROM aluni_pengguna a 
			LEFT JOIN aluni_pengguna_hak_akses b ON a.username = b.username 
			LEFT JOIN aluni_pengguna_status_password c ON a.username = c.username 
			";
		if ($kriteria!="") {
			$query .= " WHERE $kriteria ";
		}
		if ($urutan!="") {
			$query .= " ORDER BY $urutan ";
		}
		if ($tambahan!="") {
			$query .= $tambahan;
		}
		$datas = $db->query($query);
		return $datas;
	}


?>