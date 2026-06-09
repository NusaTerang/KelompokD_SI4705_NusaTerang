Story Sebagai pengguna platform (Admin & Donatur), saya ingin menerima notifikasi otomatis terkait perubahan status proyek, pencapaian target dana, dan pembaruan progres agar saya selalu mendapat informasi terkini tanpa harus memantau secara aktif.

Precondition

Pengguna sudah login sesuai rolenya

RBAC aktif (PBI-06 selesai)

Event-event pemicu notifikasi sudah ada (proyek ditugaskan, detail diisi, donasi masuk, target tercapai, update progres)

Alur / Skenario

Trigger notifikasi per event:

Event

Penerima

Proyek ditugaskan ke vendor

Penyedia Energi

Vendor mengisi detail proyek

Admin

Target dana tercapai

Admin + semua Donatur proyek

Vendor submit update progres

Admin + semua Donatur proyek

Proyek selesai (laporan akhir disubmit)

Admin + semua Donatur proyek

Alur menerima & membaca notifikasi:

Pengguna login dan membuka platform

Badge angka merah muncul di navbar jika ada notifikasi belum dibaca

Pengguna klik icon notifikasi

Dropdown/halaman notifikasi muncul menampilkan daftar notifikasi urut terbaru

Setiap item notifikasi menampilkan: pesan, waktu, status (dibaca/belum)

Pengguna klik salah satu notifikasi → diarahkan ke halaman terkait + status berubah jadi dibaca

Pengguna dapat klik "Tandai semua dibaca"

Alternatif — tidak ada notifikasi:

Dropdown menampilkan "Tidak ada notifikasi"

Mockup → Referensi: 4.5.13 Notifikasi

Test Case

ID

Skenario

Input

Expected Output

Type

TC-01

Notifikasi terkirim ke vendor saat proyek ditugaskan

Admin submit proyek ke vendor

Vendor menerima notifikasi ProyekDitugaskan

Feature Test

TC-02

Notifikasi terkirim ke Admin saat vendor isi detail

Vendor submit detail proyek

Admin menerima notifikasi DetailProyekDiisi

Feature Test

TC-03

Notifikasi terkirim ke semua donatur saat target tercapai

Total donasi = target dana

Semua donatur proyek menerima notifikasi

Feature Test

TC-04

Notifikasi terkirim saat vendor update progres

Vendor submit update

Admin & donatur proyek menerima notifikasi

Feature Test

TC-05

Badge unread count tampil benar

3 notifikasi belum dibaca

Badge angka 3 di navbar

Feature Test

TC-06

Notifikasi berubah dibaca setelah diklik

Klik 1 notifikasi

read_at terisi, badge berkurang 1

Feature Test

TC-07

Tandai semua dibaca

Klik "Tandai semua dibaca"

Semua read_at terisi, badge = 0

Feature Test

TC-08

Pengguna hanya lihat notifikasi miliknya

Login sebagai Donatur A

Tidak tampil notifikasi milik Donatur B

Feature Test

TC-09

Notifikasi urut terbaru di atas

3 notifikasi berbeda waktu

Urut descending by created_at

Feature Test

TC-10

Tidak ada notifikasi

Akun baru tanpa aktivitas

Pesan "Tidak ada notifikasi"

Feature Test


Untuk di client dan admin belum ada icon notifikasinya, jadi silahkan dibuat di navbar