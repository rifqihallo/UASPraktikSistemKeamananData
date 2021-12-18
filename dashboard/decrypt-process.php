<?php
session_start();
include "../config.php";//memasukan koneksi
include "AES.php"; //memasukan file AES

// Mengambil hasil inputan file yang ingin di dekripsi dan dikelola lalu, dicocokan dengan password yang pernah kita buat
$idfile    = mysqli_escape_string($koneksi,$_POST['fileid']);
$pwdfile   = mysqli_escape_string($koneksi,substr(md5($_POST["pwdfile"]), 0,16));
$query     = "SELECT password FROM file WHERE id_file='$idfile' AND password='$pwdfile'";
$sql       = mysqli_query($koneksi,$query);
// Jika memang benar maka akan diproses dekripsi filenya
if(mysqli_num_rows($sql)>0){
    $sql1       = mysqli_query($koneksi,"SELECT * FROM file WHERE id_file='$idfile'");
    $data       = mysqli_fetch_assoc($sql1);

    $file_path  = $data["file_url"];
    $key        = $data["password"];
    $file_name  = $data["file_name_source"];
    $size       = $data["file_size"];

    $file_size  = filesize($file_path);
    // Query untuk mengupdate file yang statusnya menjadi 2 nilainya
    $query2     = "UPDATE file SET status='2' WHERE id_file='$idfile'";
    $sql2       = mysqli_query($koneksi,$query2);

    $mod        = $file_size%16;

    $aes        = new AES($key); //Memasukan password key yang sudah diberikan kedalam aes
    $fopen1     = fopen($file_path, "rb"); //menggunakan mode biner untuk membaca file_path
    $plain      = "";
    $cache      = "file_decrypt/$file_name";
    $fopen2     = fopen($cache, "wb"); //memBuat file biner untuk menulis

    if($mod==0){
    $banyak = $file_size / 16;
     }else{
    $banyak = ($file_size - $mod) / 16;
    $banyak = $banyak+1;
    }
    // Mengeset Maximum Waktu dalam proses eksekusi
    ini_set('max_execution_time', -1);
    ini_set('memory_limit', -1);
    // Proses dekripsi file data   
    for($bawah=0;$bawah<$banyak;$bawah++){
      
      $filedata    = fread($fopen1, 16);
      $plain       = $aes->decrypt($filedata);
      fwrite($fopen2, $plain);
   }
   $_SESSION["download"] = $cache;

   echo("<script language='javascript'>
       window.open('download.php', '_blank');
       window.location.href='decrypt.php';
       window.alert('Berhasil mendekripsi file.');
       </script>
       ");
}else{
 echo("<script language='javascript'>
    window.location.href='decrypt-file.php?id_file=$idfile';
    window.alert('Maaf, Password tidak sesuai.');
    </script>");
}
?>
