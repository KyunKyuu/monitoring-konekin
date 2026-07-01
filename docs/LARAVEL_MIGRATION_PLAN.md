# Laravel Migration Plan

## 1. Tujuan

Dokumen ini menjabarkan urutan migration Laravel `V1` secara detail agar:

- foreign key aman
- struktur tabel konsisten dengan `DB_SCHEMA_V1.sql`
- implementasi migration tidak melebar

Dokumen acuan:

- `PROJECT_OVERVIEW_SDD.md`
- `DB_SCHEMA_V1.sql`
- `LARAVEL_IMPLEMENTATION_PLAN.md`

## 2. Prinsip Migration

- gunakan `id()` atau `bigIncrements()` secara konsisten
- gunakan `foreignId()` dan `constrained()` untuk relasi
- gunakan `timestamps()` untuk tabel utama
- gunakan `timestampTz()` atau `dateTimeTz()` bila ingin konsisten dengan PostgreSQL timezone
- check constraint opsional di Laravel migration; validasi utama tetap di `FormRequest` dan service
- tabel histori tidak memakai soft delete pada V1

## 3. Naming Convention

### 3.1 Nama File Migration

Gunakan pola:

- `xxxx_xx_xx_xxxxxx_create_<table_name>_table.php`

### 3.2 Nama Tabel

Gunakan tabel plural dan snake_case:

- `access_roles`
- `persons`
- `members`
- `pengurus`
- `users`
- `note_tags`

### 3.3 Catatan Khusus

Untuk Laravel, saya sarankan:

- gunakan tabel `users` menggantikan `user_accounts`
- simpan relasi `users.pengurus_id`

Alasannya:

- lebih idiomatik Laravel
- integrasi auth lebih mudah

## 4. Migration Order

### Phase 1: Auth and Core Master

#### 4.1 `create_access_roles_table`

Kolom:

- `id`
- `code`
- `name`
- `description`
- `created_at`

Index dan constraint:

- unique `code`

#### 4.2 `create_fungsi_table`

Kolom:

- `id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

Index dan constraint:

- unique `code`
- unique `name`

#### 4.3 `create_jabatan_table`

Kolom:

- `id`
- `fungsi_id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

Index dan constraint:

- foreign key ke `fungsi`
- unique `code`
- unique gabungan `fungsi_id, name`

#### 4.4 `create_role_labels_table`

Kolom:

- `id`
- `fungsi_id` nullable
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

Index dan constraint:

- foreign key ke `fungsi`
- unique `code`
- unique `name`

#### 4.5 `create_persons_table`

Kolom:

- `id`
- `person_type`
- `code`
- `name`
- `gender` nullable
- `phone` nullable
- `email` nullable
- `is_active`
- `created_at`
- `updated_at`

Index dan constraint:

- unique `code`
- index `person_type`

Catatan:

- `person_type` minimal `member` atau `pengurus`

#### 4.6 `create_members_table`

Kolom:

- `id`
- `person_id`
- `joined_at` nullable
- `notes` nullable

Index dan constraint:

- unique `person_id`
- foreign key ke `persons`

#### 4.7 `create_pengurus_table`

Kolom:

- `id`
- `person_id`
- `started_at` nullable
- `ended_at` nullable
- `notes` nullable
- `created_at`
- `updated_at`

Index dan constraint:

- unique `person_id`
- foreign key ke `persons`

#### 4.8 `create_users_table`

Kolom:

- `id`
- `pengurus_id`
- `access_role_id`
- `name`
- `username`
- `email` nullable
- `password`
- `remember_token`
- `last_login_at` nullable
- `is_active`
- `created_at`
- `updated_at`

Index dan constraint:

- unique `pengurus_id`
- unique `username`
- foreign key ke `pengurus`
- foreign key ke `access_roles`

Catatan:

- `name` di tabel `users` boleh diisi dari `persons.name` untuk kebutuhan auth UI

### Phase 2: Dynamic Master

#### 4.9 `create_kategori_table`

Kolom:

- `id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

#### 4.10 `create_subkategori_table`

Kolom:

- `id`
- `kategori_id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `kategori`
- unique `code`
- unique gabungan `kategori_id, name`

#### 4.11 `create_note_tags_table`

Kolom:

- `id`
- `code`
- `name`
- `description`
- `is_active`
- `created_at`
- `updated_at`

Constraint:

- unique `code`
- unique `name`

#### 4.12 `create_person_role_labels_table`

Kolom:

- `id`
- `person_id`
- `role_label_id`
- `start_date` nullable
- `end_date` nullable
- `status`
- `notes` nullable
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `persons`
- foreign key ke `role_labels`

### Phase 3: Kegiatan

#### 4.13 `create_kegiatan_table`

Kolom:

- `id`
- `kategori_id`
- `subkategori_id` nullable
- `created_by_pengurus_id`
- `title`
- `theme` nullable
- `description` nullable
- `scheduled_start_at`
- `scheduled_end_at` nullable
- `actual_start_at` nullable
- `actual_end_at` nullable
- `status`
- `location` nullable
- `is_global_calendar`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `kategori`
- foreign key ke `subkategori`
- foreign key ke `pengurus`

Index:

- index `scheduled_start_at`
- index `status`

#### 4.14 `create_kegiatan_partisipan_table`

Kolom:

- `id`
- `kegiatan_id`
- `person_id`
- `participant_type`
- `attendance_status`
- `activity_role`
- `notes` nullable
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `kegiatan`
- foreign key ke `persons`
- unique gabungan `kegiatan_id, person_id, activity_role`

Index:

- index `person_id`

#### 4.15 `create_note_kegiatan_table`

Kolom:

- `id`
- `kegiatan_id`
- `author_pengurus_id`
- `content`
- `created_at`

Constraint:

- foreign key ke `kegiatan`
- foreign key ke `pengurus`

### Phase 4: Monitoring

#### 4.16 `create_note_individu_table`

Kolom:

- `id`
- `person_id`
- `author_pengurus_id`
- `kegiatan_id` nullable
- `source_type`
- `level`
- `sentiment`
- `follow_up_status`
- `content`
- `follow_up_action` nullable
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `persons`
- foreign key ke `pengurus`
- foreign key nullable ke `kegiatan`

Index:

- index `person_id`
- index `author_pengurus_id`
- index `kegiatan_id`

#### 4.17 `create_note_individu_tags_table`

Kolom:

- `id`
- `note_individu_id`
- `note_tag_id`

Constraint:

- foreign key ke `note_individu`
- foreign key ke `note_tags`
- unique gabungan `note_individu_id, note_tag_id`

#### 4.18 `create_target_pembinaan_table`

Kolom:

- `id`
- `person_id`
- `jabatan_id`
- `assigned_by_pengurus_id`
- `priority_level`
- `status`
- `reason` nullable
- `development_plan` nullable
- `start_date`
- `end_date` nullable
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `persons`
- foreign key ke `jabatan`
- foreign key ke `pengurus`

Index:

- index `person_id`

#### 4.19 `create_progress_pembinaan_table`

Kolom:

- `id`
- `person_id`
- `target_pembinaan_id` nullable
- `kegiatan_id` nullable
- `recorded_by_pengurus_id`
- `area_name`
- `stage_name` nullable
- `status`
- `notes` nullable
- `recorded_at`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `persons`
- foreign key ke `target_pembinaan`
- foreign key ke `kegiatan`
- foreign key ke `pengurus`

Index:

- index `person_id`

### Phase 5: Keuangan

#### 4.20 `create_iuran_table`

Kolom:

- `id`
- `member_id`
- `period_month`
- `period_year`
- `amount_due`
- `amount_paid`
- `status`
- `due_date` nullable
- `notes` nullable
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `members`
- unique gabungan `member_id, period_month, period_year`

Index:

- index gabungan `member_id, period_year, period_month`

#### 4.21 `create_pembayaran_iuran_table`

Kolom:

- `id`
- `iuran_id`
- `recorded_by_pengurus_id`
- `paid_at`
- `amount`
- `payment_method` nullable
- `notes` nullable
- `created_at`

Constraint:

- foreign key ke `iuran`
- foreign key ke `pengurus`

Index:

- index `iuran_id`

#### 4.22 `create_kas_table`

Kolom:

- `id`
- `code`
- `name`
- `description` nullable
- `is_active`
- `created_at`
- `updated_at`

Constraint:

- unique `code`
- unique `name`

#### 4.23 `create_transaksi_kas_table`

Kolom:

- `id`
- `kas_id`
- `kegiatan_id` nullable
- `recorded_by_pengurus_id`
- `transaction_type`
- `reference_type` nullable
- `reference_id` nullable
- `amount`
- `transaction_date`
- `description`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `kas`
- foreign key ke `kegiatan`
- foreign key ke `pengurus`

Index:

- index gabungan `kas_id, transaction_date`

### Phase 6: Struktur Pengurus

#### 4.24 `create_jabatan_ideal_table`

Kolom:

- `id`
- `fungsi_id`
- `jabatan_id`
- `goal` nullable
- `responsibilities` nullable
- `required_count`
- `is_active`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `fungsi`
- foreign key ke `jabatan`
- unique gabungan `fungsi_id, jabatan_id`

#### 4.25 `create_kandidat_jabatan_table`

Kolom:

- `id`
- `jabatan_ideal_id`
- `person_id`
- `candidate_status`
- `notes` nullable
- `assigned_at`
- `created_at`
- `updated_at`

Constraint:

- foreign key ke `jabatan_ideal`
- foreign key ke `persons`
- unique gabungan `jabatan_ideal_id, person_id`

Index:

- index `person_id`

## 5. Seeder Order

Jalankan seeder minimal dengan urutan:

1. `AccessRoleSeeder`
2. `FungsiSeeder`
3. `JabatanSeeder`
4. `RoleLabelSeeder`
5. `KategoriSeeder`
6. `SubkategoriSeeder`
7. `NoteTagSeeder`
8. `SuperAdminSeeder`

## 6. Suggested Artisan Commands

Contoh urutan generate file migration:

```bash
php artisan make:migration create_access_roles_table
php artisan make:migration create_fungsi_table
php artisan make:migration create_jabatan_table
php artisan make:migration create_role_labels_table
php artisan make:migration create_persons_table
php artisan make:migration create_members_table
php artisan make:migration create_pengurus_table
php artisan make:migration create_users_table
php artisan make:migration create_kategori_table
php artisan make:migration create_subkategori_table
php artisan make:migration create_note_tags_table
php artisan make:migration create_person_role_labels_table
php artisan make:migration create_kegiatan_table
php artisan make:migration create_kegiatan_partisipan_table
php artisan make:migration create_note_kegiatan_table
php artisan make:migration create_note_individu_table
php artisan make:migration create_note_individu_tags_table
php artisan make:migration create_target_pembinaan_table
php artisan make:migration create_progress_pembinaan_table
php artisan make:migration create_iuran_table
php artisan make:migration create_pembayaran_iuran_table
php artisan make:migration create_kas_table
php artisan make:migration create_transaksi_kas_table
php artisan make:migration create_jabatan_ideal_table
php artisan make:migration create_kandidat_jabatan_table
```

## 7. Implementation Notes

- kalau memakai `spatie/laravel-permission`, tabel package itu bisa coexist dengan `access_roles`
- alternatifnya, `access_roles` bisa dihapus dan semua role akses dipindah ke Spatie
- untuk V1 yang paling sederhana, pilih salah satu, jangan dua-duanya aktif penuh

Rekomendasi saya:

- kalau pakai Spatie, jadikan `access_roles` sebagai domain reference atau hilangkan
- kalau tidak pakai Spatie, pertahankan `access_roles` dan gunakan policy manual

## 8. Decision Recommendation

Untuk mengurangi kerumitan:

1. gunakan tabel `users` standar Laravel
2. gunakan `spatie/laravel-permission` untuk RBAC
3. hapus `access_roles` dari implementasi fisik jika Spatie dipakai penuh
4. pertahankan semua tabel domain lain sesuai schema

Kalau ingin paling cepat build:

- `users` + `spatie roles/permissions`
- `pengurus` tetap jadi profil domain
- `users.pengurus_id` sebagai jembatan auth ke domain bisnis

