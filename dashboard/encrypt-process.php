<?php
session_start();
include "../config.php";   //memasukan koneksi
include "AES.php"; //memasukan file AES

  if (isset($_POST['encrypt_now'])) {
      // Mengambil inputan dari form isian file yaitu ada user,password fle,dan deskripsi
      $user 		 = $_SESSION['username'];
      $key		   = mysqli_escape_string($koneksi,substr(md5($_POST["pwdfile"]), 0,16));
      $deskripsi   = mysqli_escape_string($koneksi,$_POST['desc']);

      $file_tmpname   = $_FILES['file']['tmp_name'];
      //untuk nama file url
      $file           = rand(1000,100000)."-".$_FILES['file']['name'];
      $new_file_name  = strtolower($file);
      $final_file     = str_replace(' ','-',$new_file_name);
      //untuk nama file
      $filename       = rand(1000,100000)."-".pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
      $new_filename  = strtolower($filename);
      $finalfile     = str_replace(' ','-',$new_filename);
      $size           = filesize($file_tmpname);
      $size2          = (filesize($file_tmpname))/1024;
      $info           = pathinfo($final_file );
      $file_source		= fopen($file_tmpname, 'rb');
      $ext            = $info["extension"];

      if($ext=="jpg"|| $ext=="jpeg" || $ext=="png" || $ext=="jfif"){
      }else{
          echo("<script language='javascript'>
          window.location.href='encrypt.php';
          window.alert('Maaf, file yang bisa dienkrip hanya jpg,jpeg,png, dan jfif');
          </script>");
          exit();
      }

      if($size2>3084){
          echo("<script language='javascript'>
          window.location.href='home.php?encrypt';
          window.alert('Maaf, file tidak bisa lebih besar dari 3MB.');
          </script>");
          exit();
      }
      // Query untuk memasukan inputan file yang akan dienrkipsi
      $query1  = mysqli_query($koneksi,"INSERT INTO file VALUES ('', '$user', '$final_file', '$finalfile.rda', '', '$size2', '$key', now(), '1', '$deskripsi')");

      $query2  = mysqli_query($koneksi,"select * from file where file_url =''");

      // file yang masuk akan dienkripsi dan diubah ekstensinya menjadi .rda
      $url   = $finalfile.".rda";
      $file_url = "file_encrypt/$url";

      // diupadte nama file yang udah di enrkripsi tadi, kedalam database 
      $query3  = mysqli_query($koneksi,"UPDATE file SET file_url ='$file_url' WHERE file_url=''");

      $file_output = fopen($file_url, 'wb');

      // Proses enkripsi gambar
      $mod    = $size%16;
      if($mod==0){
          $banyak = $size / 16;
      }else{
          $banyak = ($size - $mod) / 16;
          $banyak = $banyak+1;
      }

      if(is_uploaded_file($file_tmpname)){
          ini_set('max_execution_time', -1);
          ini_set('memory_limit', -1);
          $aes = new AES($key);

         for($bawah=0;$bawah<$banyak;$bawah++){
             $data    = fread($file_source, 16);
             $cipher  = $aes->encrypt($data);
             fwrite($file_output, $cipher);
         }
         fclose($file_source);
         fclose($file_output);

         // Notifikasi jika enkripsi berhasil dilakukan
         echo("<script language='javascript'>
          window.location.href='encrypt.php';
          window.alert('Enkripsi Berhasil..');
          </script>");
      }else{
        // Notifikasi jika enkripsi gagal dilakukan
         echo("<script language='javascript'>
          window.location.href='encrypt.php';
          window.alert('Encrypt file mengalami masalah..');
          </script>");
      }
  }
