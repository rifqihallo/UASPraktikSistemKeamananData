<?php
//Host yang digunakan Localhost atau 127.0.0.1 Username dari Host yakni root
//User root tidak menggunakan password
//Nama Database Aplikasi Enkirpsi dan Dekripsi
//Mencoba terhubung apabila Host, User, dan Pass Benar. Kalau tidak MySQL memberikan Notif Error
//Jika benar maka akan memilih Database sesuai pada variable $dbname
$koneksi = new mysqli("localhost","root","","uasprakskd");
?>