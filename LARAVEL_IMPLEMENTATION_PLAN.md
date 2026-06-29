# Laravel Implementation Plan

## 1. Tujuan Dokumen

Dokumen ini menjadi panduan implementasi teknis Laravel untuk `V1` aplikasi monitoring anggota komunitas.

Tujuannya:

- menjaga implementasi tetap sesuai `PROJECT_OVERVIEW_SDD.md`
- menerjemahkan desain bisnis dan schema SQL ke struktur Laravel yang rapi
- menentukan urutan pengerjaan backend agar tidak melebar

## 2. Scope V1 Laravel

Implementasi Laravel `V1` hanya mencakup:

- login pengurus
- RBAC dasar
- master member dan pengurus
- master fungsi, jabatan, role label
- master kategori, subkategori, tag note
- kegiatan dan partisipan kegiatan
- note kegiatan dan note individu
- target pembinaan dan progress pembinaan
- iuran anggota
- kas dan transaksi kas
- dashboard ringkas

Belum masuk:

- scoring otomatis calon jabatan
- approval workflow bertingkat
- notifikasi WhatsApp atau email
- akuntansi lengkap
- rekomendasi AI

## 3. Rekomendasi Stack

### 3.1 Framework

- `Laravel 12` atau versi stabil Laravel terbaru yang dipakai tim

### 3.2 Database

- `PostgreSQL`

Alasan:

- cocok dengan schema yang sudah dibuat
- constraint dan integritas data lebih kuat
- baik untuk data historis dan relasi yang cukup banyak

### 3.3 Frontend V1

- `Blade`
- `Tailwind CSS`

Catatan:

- untuk V1, gunakan Blade biasa agar fokus pada alur bisnis
- jangan langsung masuk ke SPA jika target utama adalah cepat jadi dan stabil

### 3.4 Authentication

- gunakan auth Laravel bawaan
- akun login hanya untuk pengurus

Pilihan aman:

- `Laravel Breeze` untuk bootstrap auth sederhana

### 3.5 Authorization

Rekomendasi:

- `spatie/laravel-permission`

Alasan:

- role dan permission lebih rapi
- mudah dipadukan dengan policy dan middleware
- cocok untuk `super_admin`, `mentor`, `pembina_materi`, `pengurus_keuangan`

## 4. Prinsip Implementasi Laravel

### 4.1 Pisahkan Auth dari Domain Orang

Jangan jadikan `users` sebagai pusat semua data bisnis.

Gunakan pola:

- `users` untuk akun login
- `pengurus` untuk data pengurus
- `persons` sebagai data induk orang
- `members` untuk member non-login

Relasi:

- `persons` 1:1 `members`
- `persons` 1:1 `pengurus`
- `pengurus` 1:1 `users`

### 4.2 Histori Bersifat Append-Only

Tabel berikut diperlakukan sebagai histori:

- `note_individu`
- `note_kegiatan`
- `progress_pembinaan`
- `pembayaran_iuran`
- `transaksi_kas`

Artinya:

- update hanya untuk koreksi terbatas
- jangan overwrite makna histori
- data baru ditambahkan sebagai record baru

### 4.3 Dynamic Master Data

Data berikut harus master-driven, bukan hardcoded:

- kategori
- subkategori
- fungsi
- jabatan
- role label
- note tag

### 4.4 Saldo Kas Tidak Disimpan Manual

Saldo kas dihitung dari `transaksi_kas`.

Jika nanti butuh performa:

- bisa tambahkan layer summary
- jangan ubah prinsip dasar histori transaksi

## 5. Struktur Modul Laravel

Disarankan module boundary secara konseptual seperti ini:

### 5.1 Auth and Access

- login
- logout
- manajemen user
- role akses
- permission

### 5.2 Master Data

- persons
- members
- pengurus
- fungsi
- jabatan
- role labels
- kategori
- subkategori
- note tags

### 5.3 Kegiatan

- kegiatan
- kegiatan_partisipan
- note_kegiatan

### 5.4 Monitoring

- note_individu
- note_individu_tags
- target_pembinaan
- progress_pembinaan

### 5.5 Struktur Pengurus

- jabatan_ideal
- kandidat_jabatan

### 5.6 Keuangan

- iuran
- pembayaran_iuran
- kas
- transaksi_kas

## 6. Struktur Folder yang Disarankan

Struktur ini tetap idiomatik Laravel, tanpa over-engineering.

```text
app/
  Actions/
  Enums/
  Http/
    Controllers/
      Auth/
      Dashboard/
      Master/
      Kegiatan/
      Monitoring/
      Keuangan/
      StrukturPengurus/
    Requests/
      Master/
      Kegiatan/
      Monitoring/
      Keuangan/
      StrukturPengurus/
  Models/
  Policies/
  Services/
    Kegiatan/
    Monitoring/
    Keuangan/
    StrukturPengurus/
database/
  factories/
  migrations/
  seeders/
resources/
  views/
    auth/
    dashboard/
    master/
    kegiatan/
    monitoring/
    keuangan/
    struktur-pengurus/
routes/
  web.php
```

Catatan:

- `Controllers` fokus pada HTTP flow
- `FormRequest` untuk validasi
- `Services` untuk business workflow yang melibatkan banyak model
- `Policies` untuk pembatasan akses data

## 7. Mapping Tabel ke Model Laravel

### 7.1 Model Inti

- `User`
- `Person`
- `Member`
- `Pengurus`
- `Fungsi`
- `Jabatan`
- `RoleLabel`
- `Kategori`
- `Subkategori`
- `Kegiatan`
- `KegiatanPartisipan`
- `NoteKegiatan`
- `NoteIndividu`
- `NoteTag`
- `TargetPembinaan`
- `ProgressPembinaan`
- `Iuran`
- `PembayaranIuran`
- `Kas`
- `TransaksiKas`
- `JabatanIdeal`
- `KandidatJabatan`

### 7.2 Pivot atau Mapping Model

Disarankan tetap buat model untuk pivot yang punya metadata:

- `PersonRoleLabel`
- `NoteIndividuTag`

Alasan:

- pivot ini punya atribut bisnis
- lebih mudah di-query dan di-audit

## 8. Urutan Migration Laravel

Supaya foreign key aman, migration dibuat bertahap.

### Batch 1: Auth and Master Base

1. `create_access_roles_table`
2. `create_fungsi_table`
3. `create_jabatan_table`
4. `create_role_labels_table`
5. `create_persons_table`
6. `create_members_table`
7. `create_pengurus_table`
8. `create_users_table`

### Batch 2: Master Dynamic

9. `create_kategori_table`
10. `create_subkategori_table`
11. `create_note_tags_table`
12. `create_person_role_labels_table`

### Batch 3: Kegiatan

13. `create_kegiatan_table`
14. `create_kegiatan_partisipan_table`
15. `create_note_kegiatan_table`

### Batch 4: Monitoring

16. `create_note_individu_table`
17. `create_note_individu_tags_table`
18. `create_target_pembinaan_table`
19. `create_progress_pembinaan_table`

### Batch 5: Keuangan

20. `create_iuran_table`
21. `create_pembayaran_iuran_table`
22. `create_kas_table`
23. `create_transaksi_kas_table`

### Batch 6: Struktur Pengurus

24. `create_jabatan_ideal_table`
25. `create_kandidat_jabatan_table`

## 9. Seeder Strategy

Seeder awal sebaiknya mencakup data master minimum agar development cepat.

### 9.1 Required Seeders

- `AccessRoleSeeder`
- `NoteTagSeeder`
- `FungsiSeeder`
- `JabatanSeeder`
- `KategoriSeeder`
- `SuperAdminSeeder`

### 9.2 Seed Minimal Awal

`Access roles`:

- `super_admin`
- `mentor`
- `pembina_materi`
- `pengurus_keuangan`

`Note tags`:

- `akademik`
- `disiplin`
- `keuangan`
- `kepemimpinan`
- `fisik`
- `ibadah`

`Fungsi` contoh:

- `keuangan`
- `olahraga`
- `materi`
- `kaderisasi`

## 10. RBAC Strategy

### 10.1 Level Akses

Pisahkan tegas:

- `access role`: untuk login dan hak akses aplikasi
- `role label`: untuk peran atau status orang di organisasi

### 10.2 Implementasi Laravel

Gunakan:

- middleware role
- policy untuk akses per resource
- query scoping jika pengurus hanya boleh melihat data tertentu

Contoh:

- `super_admin` bisa akses semua
- `pembina_materi` bisa buat kegiatan kategori tertentu
- `pengurus_keuangan` bisa akses iuran dan kas

### 10.3 Policy yang Perlu Ada

- `MemberPolicy`
- `PengurusPolicy`
- `KegiatanPolicy`
- `NoteIndividuPolicy`
- `TargetPembinaanPolicy`
- `IuranPolicy`
- `TransaksiKasPolicy`

## 11. Form and Validation Strategy

Untuk V1, semua input penting sebaiknya lewat `FormRequest`.

### 11.1 Contoh Request Class

- `StoreMemberRequest`
- `StorePengurusRequest`
- `StoreKegiatanRequest`
- `StoreKegiatanPartisipanRequest`
- `StoreNoteIndividuRequest`
- `StoreTargetPembinaanRequest`
- `StoreIuranRequest`
- `StorePembayaranIuranRequest`
- `StoreTransaksiKasRequest`

### 11.2 Validasi Penting

- kode person unik
- username unik
- periode iuran unik per member
- satu kandidat tidak boleh dobel dalam jabatan ideal yang sama
- note wajib punya isi
- transaksi kas wajib nominal positif

## 12. Business Workflow Placement

Workflow yang sederhana bisa di controller.

Workflow yang melibatkan banyak tabel sebaiknya dipindah ke service.

### 12.1 Service yang Direkomendasikan

- `CreateKegiatanService`
- `RecordKegiatanOutcomeService`
- `CreateNoteIndividuService`
- `AssignTargetPembinaanService`
- `RecordPembayaranIuranService`
- `RecordTransaksiKasService`

### 12.2 Contoh Kasus Service

Saat `RecordPembayaranIuranService` dijalankan:

1. simpan pembayaran iuran
2. update agregat `amount_paid` pada `iuran`
3. tentukan status `unpaid`, `partial`, atau `paid`
4. opsional buat `transaksi_kas` masuk

Saat `RecordKegiatanOutcomeService` dijalankan:

1. simpan note kegiatan
2. simpan note individu bila ada
3. update kehadiran partisipan
4. simpan progress pembinaan jika relevan

## 13. Dashboard V1

### 13.1 Super Admin Dashboard

- total member aktif
- total pengurus aktif
- kegiatan hari ini
- kegiatan minggu ini
- target pembinaan aktif
- total tunggakan iuran
- saldo kas
- posisi pengurus ideal yang kosong

### 13.2 Pengurus Dashboard

- kegiatan yang dibuatnya
- kegiatan hari ini
- note follow up yang masih open
- member yang sedang dia bina
- agenda minggu ini

## 14. Fase Implementasi

### Fase 1: Bootstrap Project

- buat project Laravel
- setup PostgreSQL
- setup auth
- setup role and permission
- setup layout dashboard dasar

### Fase 2: Master Data

- implement model dan migration person, member, pengurus
- implement master fungsi, jabatan, role label
- implement master kategori, subkategori, note tag

### Fase 3: Kegiatan

- CRUD kegiatan
- peserta kegiatan
- kalender dasar
- note kegiatan

### Fase 4: Monitoring

- note individu bertag
- target pembinaan
- progress pembinaan
- timeline profil anggota

### Fase 5: Keuangan

- iuran
- pembayaran iuran
- kas
- transaksi kas

### Fase 6: Struktur Pengurus

- jabatan ideal
- kandidat jabatan
- status calon dan ditetapkan

### Fase 7: Dashboard Refinement

- ringkasan dashboard
- filter monitoring
- summary per fungsi dan per kategori

## 15. Prioritas Build Backend

Urutan backend yang paling aman:

1. auth dan role
2. person, member, pengurus
3. kegiatan dan partisipan
4. note individu dan note tag
5. target pembinaan dan progress
6. iuran dan transaksi kas
7. jabatan ideal dan kandidat

## 16. Batasan Teknis V1

Untuk menjaga fokus, V1 sebaiknya:

- tidak memakai event-driven architecture berlebihan
- tidak memakai microservice
- tidak memakai CQRS
- tidak memakai dashboard realtime kompleks
- tidak membuat engine scoring dulu

## 17. Output Teknis Berikutnya

Setelah dokumen ini, artefak yang paling tepat dibuat adalah:

1. migration Laravel per tabel
2. daftar model dan relasi Eloquent
3. daftar route web dan controller
4. wireframe halaman V1

Rekomendasi urutan:

1. `migration plan detail`
2. `Eloquent relationship map`
3. `route and controller map`

Artefak turunan yang sudah tersedia:

- `LARAVEL_MIGRATION_PLAN.md`
- `ELOQUENT_RELATIONSHIP_MAP.md`
- `LARAVEL_ROUTE_CONTROLLER_MAP.md`
