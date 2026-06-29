# Laravel Route and Controller Map

## 1. Tujuan

Dokumen ini memetakan route `web.php` dan controller Laravel untuk `V1`.

Tujuannya:

- memperjelas endpoint dan halaman yang dibutuhkan
- menentukan boundary controller per modul
- menjaga implementasi HTTP tetap fokus pada scope `V1`

Dokumen acuan:

- `PROJECT_OVERVIEW_SDD.md`
- `LARAVEL_IMPLEMENTATION_PLAN.md`
- `LARAVEL_MIGRATION_PLAN.md`
- `ELOQUENT_RELATIONSHIP_MAP.md`

## 2. Prinsip Routing

- gunakan `web routes` untuk dashboard internal
- gunakan `auth` middleware untuk semua halaman setelah login
- gunakan `role` atau `permission` middleware untuk modul sensitif
- gunakan resource controller untuk CRUD utama
- gunakan route tambahan terpisah untuk aksi domain yang bukan CRUD murni

## 3. Route Groups

Struktur grup route yang disarankan:

```php
Route::middleware('guest')->group(function () {
    // auth routes
});

Route::middleware(['auth'])->group(function () {
    // dashboard
    // profile
    // master data
    // kegiatan
    // monitoring
    // keuangan
    // struktur pengurus
});
```

Jika memakai `spatie/laravel-permission`, contoh middleware:

- `role:super_admin`
- `role:pengurus_keuangan`
- `role:mentor|pembina_materi|super_admin`

## 4. Controller List

### 4.1 Auth

- `Auth\AuthenticatedSessionController`
- `Auth\RegisteredUserController` opsional

### 4.2 Dashboard

- `DashboardController`

### 4.3 Master

- `Master\MemberController`
- `Master\PengurusController`
- `Master\FungsiController`
- `Master\JabatanController`
- `Master\RoleLabelController`
- `Master\KategoriController`
- `Master\SubkategoriController`
- `Master\NoteTagController`
- `Master\UserController`

### 4.4 Kegiatan

- `Kegiatan\KegiatanController`
- `Kegiatan\KegiatanPartisipanController`
- `Kegiatan\KegiatanNoteController`

### 4.5 Monitoring

- `Monitoring\NoteIndividuController`
- `Monitoring\TargetPembinaanController`
- `Monitoring\ProgressPembinaanController`

### 4.6 Keuangan

- `Keuangan\IuranController`
- `Keuangan\PembayaranIuranController`
- `Keuangan\KasController`
- `Keuangan\TransaksiKasController`

### 4.7 Struktur Pengurus

- `StrukturPengurus\JabatanIdealController`
- `StrukturPengurus\KandidatJabatanController`

## 5. Route Map

### 5.1 Auth Routes

#### Login

- `GET /login`
  - controller: `AuthenticatedSessionController@create`
  - name: `login`

- `POST /login`
  - controller: `AuthenticatedSessionController@store`
  - name: `login.store`

- `POST /logout`
  - controller: `AuthenticatedSessionController@destroy`
  - name: `logout`

Catatan:

- register publik tidak dibuka
- hanya super admin yang boleh membuat akun pengurus dari dalam sistem

### 5.2 Dashboard Routes

- `GET /`
  - redirect ke dashboard bila sudah login

- `GET /dashboard`
  - controller: `DashboardController@index`
  - name: `dashboard`

### 5.3 Member Routes

Prefix:

- `/members`

Routes:

- `GET /members`
  - `MemberController@index`
  - `members.index`

- `GET /members/create`
  - `MemberController@create`
  - `members.create`

- `POST /members`
  - `MemberController@store`
  - `members.store`

- `GET /members/{member}`
  - `MemberController@show`
  - `members.show`

- `GET /members/{member}/edit`
  - `MemberController@edit`
  - `members.edit`

- `PUT /members/{member}`
  - `MemberController@update`
  - `members.update`

- `POST /members/import`
  - `MemberController@import`
  - `members.import`

Tambahan halaman profil anggota:

- `GET /members/{member}/notes`
  - `MemberController@notes`
  - `members.notes`

- `GET /members/{member}/progress`
  - `MemberController@progress`
  - `members.progress`

- `GET /members/{member}/iuran`
  - `MemberController@iuran`
  - `members.iuran`

- `GET /members/{member}/activities`
  - `MemberController@activities`
  - `members.activities`

### 5.4 Pengurus Routes

Prefix:

- `/pengurus`

Routes:

- `GET /pengurus`
  - `PengurusController@index`
  - `pengurus.index`

- `GET /pengurus/create`
  - `PengurusController@create`
  - `pengurus.create`

- `POST /pengurus`
  - `PengurusController@store`
  - `pengurus.store`

- `GET /pengurus/{pengurus}`
  - `PengurusController@show`
  - `pengurus.show`

- `GET /pengurus/{pengurus}/edit`
  - `PengurusController@edit`
  - `pengurus.edit`

- `PUT /pengurus/{pengurus}`
  - `PengurusController@update`
  - `pengurus.update`

- `GET /pengurus/{pengurus}/activities`
  - `PengurusController@activities`
  - `pengurus.activities`

### 5.5 User Management Routes

Prefix:

- `/users`

Role recommendation:

- `super_admin` only

Routes:

- `GET /users`
  - `UserController@index`
  - `users.index`

- `GET /users/create`
  - `UserController@create`
  - `users.create`

- `POST /users`
  - `UserController@store`
  - `users.store`

- `GET /users/{user}/edit`
  - `UserController@edit`
  - `users.edit`

- `PUT /users/{user}`
  - `UserController@update`
  - `users.update`

- `PATCH /users/{user}/toggle-status`
  - `UserController@toggleStatus`
  - `users.toggle-status`

- `PATCH /users/{user}/reset-password`
  - `UserController@resetPassword`
  - `users.reset-password`

### 5.6 Fungsi Routes

- `Route::resource('fungsi', FungsiController::class)->except(['destroy'])`

Nama route:

- `fungsi.index`
- `fungsi.create`
- `fungsi.store`
- `fungsi.show`
- `fungsi.edit`
- `fungsi.update`

### 5.7 Jabatan Routes

- `Route::resource('jabatan', JabatanController::class)->except(['destroy'])`

Tambahan:

- `GET /jabatan/by-fungsi/{fungsi}`
  - `JabatanController@byFungsi`
  - `jabatan.by-fungsi`

### 5.8 Role Label Routes

- `Route::resource('role-labels', RoleLabelController::class)->except(['destroy'])`

Nama route:

- `role-labels.index`
- `role-labels.create`
- `role-labels.store`
- `role-labels.show`
- `role-labels.edit`
- `role-labels.update`

### 5.9 Kategori and Subkategori Routes

Kategori:

- `Route::resource('kategori', KategoriController::class)->except(['destroy'])`

Subkategori:

- `Route::resource('subkategori', SubkategoriController::class)->except(['destroy'])`

Tambahan:

- `GET /subkategori/by-kategori/{kategori}`
  - `SubkategoriController@byKategori`
  - `subkategori.by-kategori`

### 5.10 Note Tag Routes

- `Route::resource('note-tags', NoteTagController::class)->except(['destroy'])`

### 5.11 Kegiatan Routes

Prefix:

- `/kegiatan`

Routes:

- `GET /kegiatan`
  - `KegiatanController@index`
  - `kegiatan.index`

- `GET /kegiatan/create`
  - `KegiatanController@create`
  - `kegiatan.create`

- `POST /kegiatan`
  - `KegiatanController@store`
  - `kegiatan.store`

- `GET /kegiatan/{kegiatan}`
  - `KegiatanController@show`
  - `kegiatan.show`

- `GET /kegiatan/{kegiatan}/edit`
  - `KegiatanController@edit`
  - `kegiatan.edit`

- `PUT /kegiatan/{kegiatan}`
  - `KegiatanController@update`
  - `kegiatan.update`

- `PATCH /kegiatan/{kegiatan}/status`
  - `KegiatanController@updateStatus`
  - `kegiatan.update-status`

- `GET /kegiatan-calendar`
  - `KegiatanController@calendar`
  - `kegiatan.calendar`

### 5.12 Kegiatan Partisipan Routes

Nested routes:

- `POST /kegiatan/{kegiatan}/partisipan`
  - `KegiatanPartisipanController@store`
  - `kegiatan.partisipan.store`

- `PUT /kegiatan/{kegiatan}/partisipan/{partisipan}`
  - `KegiatanPartisipanController@update`
  - `kegiatan.partisipan.update`

- `DELETE /kegiatan/{kegiatan}/partisipan/{partisipan}`
  - `KegiatanPartisipanController@destroy`
  - `kegiatan.partisipan.destroy`

- `PATCH /kegiatan/{kegiatan}/partisipan/{partisipan}/attendance`
  - `KegiatanPartisipanController@updateAttendance`
  - `kegiatan.partisipan.attendance`

### 5.13 Kegiatan Note Routes

Nested routes:

- `POST /kegiatan/{kegiatan}/notes`
  - `KegiatanNoteController@store`
  - `kegiatan.notes.store`

- `PUT /kegiatan/{kegiatan}/notes/{note}`
  - `KegiatanNoteController@update`
  - `kegiatan.notes.update`

### 5.14 Note Individu Routes

Prefix:

- `/monitoring/notes`

Routes:

- `GET /monitoring/notes`
  - `NoteIndividuController@index`
  - `monitoring.notes.index`

- `GET /monitoring/notes/create`
  - `NoteIndividuController@create`
  - `monitoring.notes.create`

- `POST /monitoring/notes`
  - `NoteIndividuController@store`
  - `monitoring.notes.store`

- `GET /monitoring/notes/{noteIndividu}`
  - `NoteIndividuController@show`
  - `monitoring.notes.show`

- `GET /monitoring/notes/{noteIndividu}/edit`
  - `NoteIndividuController@edit`
  - `monitoring.notes.edit`

- `PUT /monitoring/notes/{noteIndividu}`
  - `NoteIndividuController@update`
  - `monitoring.notes.update`

- `PATCH /monitoring/notes/{noteIndividu}/follow-up-status`
  - `NoteIndividuController@updateFollowUpStatus`
  - `monitoring.notes.follow-up-status`

Catatan:

- note sebaiknya tidak dihapus pada V1

### 5.15 Target Pembinaan Routes

Prefix:

- `/monitoring/targets`

Routes:

- `GET /monitoring/targets`
  - `TargetPembinaanController@index`
  - `monitoring.targets.index`

- `GET /monitoring/targets/create`
  - `TargetPembinaanController@create`
  - `monitoring.targets.create`

- `POST /monitoring/targets`
  - `TargetPembinaanController@store`
  - `monitoring.targets.store`

- `GET /monitoring/targets/{targetPembinaan}`
  - `TargetPembinaanController@show`
  - `monitoring.targets.show`

- `GET /monitoring/targets/{targetPembinaan}/edit`
  - `TargetPembinaanController@edit`
  - `monitoring.targets.edit`

- `PUT /monitoring/targets/{targetPembinaan}`
  - `TargetPembinaanController@update`
  - `monitoring.targets.update`

- `PATCH /monitoring/targets/{targetPembinaan}/status`
  - `TargetPembinaanController@updateStatus`
  - `monitoring.targets.update-status`

### 5.16 Progress Pembinaan Routes

Prefix:

- `/monitoring/progress`

Routes:

- `GET /monitoring/progress`
  - `ProgressPembinaanController@index`
  - `monitoring.progress.index`

- `GET /monitoring/progress/create`
  - `ProgressPembinaanController@create`
  - `monitoring.progress.create`

- `POST /monitoring/progress`
  - `ProgressPembinaanController@store`
  - `monitoring.progress.store`

- `GET /monitoring/progress/{progressPembinaan}`
  - `ProgressPembinaanController@show`
  - `monitoring.progress.show`

- `GET /monitoring/progress/{progressPembinaan}/edit`
  - `ProgressPembinaanController@edit`
  - `monitoring.progress.edit`

- `PUT /monitoring/progress/{progressPembinaan}`
  - `ProgressPembinaanController@update`
  - `monitoring.progress.update`

### 5.17 Iuran Routes

Prefix:

- `/keuangan/iuran`

Routes:

- `GET /keuangan/iuran`
  - `IuranController@index`
  - `keuangan.iuran.index`

- `GET /keuangan/iuran/create`
  - `IuranController@create`
  - `keuangan.iuran.create`

- `POST /keuangan/iuran`
  - `IuranController@store`
  - `keuangan.iuran.store`

- `GET /keuangan/iuran/{iuran}`
  - `IuranController@show`
  - `keuangan.iuran.show`

- `GET /keuangan/iuran/{iuran}/edit`
  - `IuranController@edit`
  - `keuangan.iuran.edit`

- `PUT /keuangan/iuran/{iuran}`
  - `IuranController@update`
  - `keuangan.iuran.update`

- `POST /keuangan/iuran/generate-period`
  - `IuranController@generatePeriod`
  - `keuangan.iuran.generate-period`

### 5.18 Pembayaran Iuran Routes

Nested routes:

- `POST /keuangan/iuran/{iuran}/payments`
  - `PembayaranIuranController@store`
  - `keuangan.iuran.payments.store`

- `PUT /keuangan/iuran/{iuran}/payments/{payment}`
  - `PembayaranIuranController@update`
  - `keuangan.iuran.payments.update`

### 5.19 Kas Routes

Prefix:

- `/keuangan/kas`

Routes:

- `GET /keuangan/kas`
  - `KasController@index`
  - `keuangan.kas.index`

- `GET /keuangan/kas/create`
  - `KasController@create`
  - `keuangan.kas.create`

- `POST /keuangan/kas`
  - `KasController@store`
  - `keuangan.kas.store`

- `GET /keuangan/kas/{kas}`
  - `KasController@show`
  - `keuangan.kas.show`

- `GET /keuangan/kas/{kas}/edit`
  - `KasController@edit`
  - `keuangan.kas.edit`

- `PUT /keuangan/kas/{kas}`
  - `KasController@update`
  - `keuangan.kas.update`

### 5.20 Transaksi Kas Routes

Prefix:

- `/keuangan/transaksi-kas`

Routes:

- `GET /keuangan/transaksi-kas`
  - `TransaksiKasController@index`
  - `keuangan.transaksi-kas.index`

- `GET /keuangan/transaksi-kas/create`
  - `TransaksiKasController@create`
  - `keuangan.transaksi-kas.create`

- `POST /keuangan/transaksi-kas`
  - `TransaksiKasController@store`
  - `keuangan.transaksi-kas.store`

- `GET /keuangan/transaksi-kas/{transaksiKas}`
  - `TransaksiKasController@show`
  - `keuangan.transaksi-kas.show`

- `GET /keuangan/transaksi-kas/{transaksiKas}/edit`
  - `TransaksiKasController@edit`
  - `keuangan.transaksi-kas.edit`

- `PUT /keuangan/transaksi-kas/{transaksiKas}`
  - `TransaksiKasController@update`
  - `keuangan.transaksi-kas.update`

### 5.21 Jabatan Ideal Routes

Prefix:

- `/struktur-pengurus/jabatan-ideal`

Routes:

- `GET /struktur-pengurus/jabatan-ideal`
  - `JabatanIdealController@index`
  - `struktur-pengurus.jabatan-ideal.index`

- `GET /struktur-pengurus/jabatan-ideal/create`
  - `JabatanIdealController@create`
  - `struktur-pengurus.jabatan-ideal.create`

- `POST /struktur-pengurus/jabatan-ideal`
  - `JabatanIdealController@store`
  - `struktur-pengurus.jabatan-ideal.store`

- `GET /struktur-pengurus/jabatan-ideal/{jabatanIdeal}`
  - `JabatanIdealController@show`
  - `struktur-pengurus.jabatan-ideal.show`

- `GET /struktur-pengurus/jabatan-ideal/{jabatanIdeal}/edit`
  - `JabatanIdealController@edit`
  - `struktur-pengurus.jabatan-ideal.edit`

- `PUT /struktur-pengurus/jabatan-ideal/{jabatanIdeal}`
  - `JabatanIdealController@update`
  - `struktur-pengurus.jabatan-ideal.update`

### 5.22 Kandidat Jabatan Routes

Nested routes:

- `POST /struktur-pengurus/jabatan-ideal/{jabatanIdeal}/candidates`
  - `KandidatJabatanController@store`
  - `struktur-pengurus.jabatan-ideal.candidates.store`

- `PUT /struktur-pengurus/jabatan-ideal/{jabatanIdeal}/candidates/{candidate}`
  - `KandidatJabatanController@update`
  - `struktur-pengurus.jabatan-ideal.candidates.update`

- `PATCH /struktur-pengurus/jabatan-ideal/{jabatanIdeal}/candidates/{candidate}/status`
  - `KandidatJabatanController@updateStatus`
  - `struktur-pengurus.jabatan-ideal.candidates.update-status`

## 6. Suggested Route File Shape

Untuk `V1`, cukup satu file `routes/web.php`.

Kalau mulai besar, bisa dipecah menjadi:

- `routes/web.php`
- `routes/web/master.php`
- `routes/web/kegiatan.php`
- `routes/web/monitoring.php`
- `routes/web/keuangan.php`
- `routes/web/struktur-pengurus.php`

Tapi untuk awal, satu file masih aman.

## 7. Controller Responsibility Boundaries

### DashboardController

Tanggung jawab:

- menyiapkan widget dashboard
- memilih data ringkasan sesuai role login

### MemberController

Tanggung jawab:

- CRUD member
- import member
- tampilkan halaman profil member

Jangan taruh:

- logika pembayaran iuran
- logika note creation kompleks

### PengurusController

Tanggung jawab:

- CRUD pengurus
- tampilkan profil pengurus

### KegiatanController

Tanggung jawab:

- CRUD kegiatan
- kalender kegiatan
- ubah status kegiatan

### KegiatanPartisipanController

Tanggung jawab:

- tambah, ubah, hapus partisipan kegiatan
- update kehadiran

### KegiatanNoteController

Tanggung jawab:

- simpan note umum kegiatan

### NoteIndividuController

Tanggung jawab:

- CRUD note individu secara terbatas
- filter note
- update follow up status

### TargetPembinaanController

Tanggung jawab:

- CRUD target pembinaan
- ubah status target

### ProgressPembinaanController

Tanggung jawab:

- tambah dan lihat progress pembinaan

### IuranController

Tanggung jawab:

- CRUD iuran
- generate iuran per periode

### PembayaranIuranController

Tanggung jawab:

- input pembayaran iuran
- koreksi pembayaran bila diperlukan

### KasController

Tanggung jawab:

- CRUD kas
- tampilkan ringkasan kas

### TransaksiKasController

Tanggung jawab:

- CRUD transaksi kas secara terbatas
- rekap transaksi

### JabatanIdealController

Tanggung jawab:

- CRUD definisi jabatan ideal

### KandidatJabatanController

Tanggung jawab:

- tambah kandidat jabatan
- ubah status kandidat

## 8. Middleware Recommendation

### Global Auth

Gunakan untuk semua route internal:

- `auth`

### Role-Based Module Protection

Contoh pembatasan:

- dashboard: `auth`
- user management: `auth`, `role:super_admin`
- master data sensitif: `auth`, `role:super_admin`
- kegiatan: `auth`, `role:super_admin|mentor|pembina_materi`
- monitoring: `auth`, `role:super_admin|mentor|pembina_materi`
- keuangan: `auth`, `role:super_admin|pengurus_keuangan`
- struktur pengurus: `auth`, `role:super_admin`

## 9. Recommended First Controllers to Build

Urutan aman:

1. `DashboardController`
2. `MemberController`
3. `PengurusController`
4. `KegiatanController`
5. `KegiatanPartisipanController`
6. `NoteIndividuController`
7. `TargetPembinaanController`
8. `IuranController`
9. `TransaksiKasController`

## 10. Next Artifact

Setelah dokumen ini, artefak berikutnya yang paling berguna adalah:

1. `FormRequest map`
2. `Service map`
3. `Blade page map`

Rekomendasi urutan:

1. `FormRequest and validation map`
2. `Service and workflow map`
3. `Blade page map`

