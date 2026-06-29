# Eloquent Relationship Map

## 1. Tujuan

Dokumen ini memetakan relasi antar model Laravel untuk `V1`.

Tujuannya:

- memperjelas struktur model Eloquent
- menyamakan nama method relasi
- mengurangi salah tafsir saat implementasi controller, policy, dan service

## 2. Konvensi Nama Relasi

Gunakan nama method relasi yang deskriptif dan konsisten:

- singular untuk `belongsTo` dan `hasOne`
- plural untuk `hasMany` dan `belongsToMany`

Contoh:

- `person()`
- `members()`
- `noteTags()`

## 3. Relationship Map

### 3.1 User

Model: `App\Models\User`

Relasi:

- `pengurus()`: `belongsTo(Pengurus::class)`

Catatan:

- jika pakai Spatie, model ini juga memakai trait `HasRoles`

### 3.2 Person

Model: `App\Models\Person`

Relasi:

- `member()`: `hasOne(Member::class)`
- `pengurus()`: `hasOne(Pengurus::class)`
- `roleLabels()`: `hasMany(PersonRoleLabel::class)`
- `kegiatanPartisipan()`: `hasMany(KegiatanPartisipan::class)`
- `receivedNotes()`: `hasMany(NoteIndividu::class)`
- `targetPembinaan()`: `hasMany(TargetPembinaan::class)`
- `progressPembinaan()`: `hasMany(ProgressPembinaan::class)`
- `kandidatJabatan()`: `hasMany(KandidatJabatan::class)`

Accessor atau helper yang disarankan:

- `isMember()`
- `isPengurus()`

### 3.3 Member

Model: `App\Models\Member`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `iuran()`: `hasMany(Iuran::class)`

Helper yang disarankan:

- `payments()` melalui `hasManyThrough` opsional ke `PembayaranIuran`

### 3.4 Pengurus

Model: `App\Models\Pengurus`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `user()`: `hasOne(User::class)`
- `createdKegiatan()`: `hasMany(Kegiatan::class, 'created_by_pengurus_id')`
- `activityNotes()`: `hasMany(NoteKegiatan::class, 'author_pengurus_id')`
- `writtenIndividualNotes()`: `hasMany(NoteIndividu::class, 'author_pengurus_id')`
- `assignedTargets()`: `hasMany(TargetPembinaan::class, 'assigned_by_pengurus_id')`
- `recordedProgress()`: `hasMany(ProgressPembinaan::class, 'recorded_by_pengurus_id')`
- `recordedIuranPayments()`: `hasMany(PembayaranIuran::class, 'recorded_by_pengurus_id')`
- `recordedCashTransactions()`: `hasMany(TransaksiKas::class, 'recorded_by_pengurus_id')`

### 3.5 Fungsi

Model: `App\Models\Fungsi`

Relasi:

- `jabatan()`: `hasMany(Jabatan::class)`
- `roleLabels()`: `hasMany(RoleLabel::class)`
- `jabatanIdeal()`: `hasMany(JabatanIdeal::class)`

### 3.6 Jabatan

Model: `App\Models\Jabatan`

Relasi:

- `fungsi()`: `belongsTo(Fungsi::class)`
- `targetPembinaan()`: `hasMany(TargetPembinaan::class)`
- `jabatanIdeal()`: `hasMany(JabatanIdeal::class)`

### 3.7 RoleLabel

Model: `App\Models\RoleLabel`

Relasi:

- `fungsi()`: `belongsTo(Fungsi::class)`
- `personRoleLabels()`: `hasMany(PersonRoleLabel::class)`

### 3.8 PersonRoleLabel

Model: `App\Models\PersonRoleLabel`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `roleLabel()`: `belongsTo(RoleLabel::class)`

### 3.9 Kategori

Model: `App\Models\Kategori`

Relasi:

- `subkategori()`: `hasMany(Subkategori::class)`
- `kegiatan()`: `hasMany(Kegiatan::class)`

### 3.10 Subkategori

Model: `App\Models\Subkategori`

Relasi:

- `kategori()`: `belongsTo(Kategori::class)`
- `kegiatan()`: `hasMany(Kegiatan::class)`

### 3.11 Kegiatan

Model: `App\Models\Kegiatan`

Relasi:

- `kategori()`: `belongsTo(Kategori::class)`
- `subkategori()`: `belongsTo(Subkategori::class)`
- `creator()`: `belongsTo(Pengurus::class, 'created_by_pengurus_id')`
- `partisipan()`: `hasMany(KegiatanPartisipan::class)`
- `notes()`: `hasMany(NoteKegiatan::class)`
- `individualNotes()`: `hasMany(NoteIndividu::class)`
- `progressPembinaan()`: `hasMany(ProgressPembinaan::class)`
- `cashTransactions()`: `hasMany(TransaksiKas::class)`

Helper scope yang disarankan:

- `scopeScheduled()`
- `scopeCompleted()`
- `scopeToday()`
- `scopeGlobalCalendar()`

### 3.12 KegiatanPartisipan

Model: `App\Models\KegiatanPartisipan`

Relasi:

- `kegiatan()`: `belongsTo(Kegiatan::class)`
- `person()`: `belongsTo(Person::class)`

### 3.13 NoteKegiatan

Model: `App\Models\NoteKegiatan`

Relasi:

- `kegiatan()`: `belongsTo(Kegiatan::class)`
- `author()`: `belongsTo(Pengurus::class, 'author_pengurus_id')`

### 3.14 NoteTag

Model: `App\Models\NoteTag`

Relasi:

- `noteMaps()`: `hasMany(NoteIndividuTag::class)`
- `individualNotes()`: `belongsToMany(NoteIndividu::class, 'note_individu_tags')`

### 3.15 NoteIndividu

Model: `App\Models\NoteIndividu`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `author()`: `belongsTo(Pengurus::class, 'author_pengurus_id')`
- `kegiatan()`: `belongsTo(Kegiatan::class)`
- `tagMaps()`: `hasMany(NoteIndividuTag::class)`
- `noteTags()`: `belongsToMany(NoteTag::class, 'note_individu_tags')`

Scope yang disarankan:

- `scopeOpenFollowUp()`
- `scopeByTag($query, $tagCode)`
- `scopeForPerson($query, $personId)`

### 3.16 NoteIndividuTag

Model: `App\Models\NoteIndividuTag`

Relasi:

- `noteIndividu()`: `belongsTo(NoteIndividu::class)`
- `noteTag()`: `belongsTo(NoteTag::class)`

### 3.17 TargetPembinaan

Model: `App\Models\TargetPembinaan`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `jabatan()`: `belongsTo(Jabatan::class)`
- `assigner()`: `belongsTo(Pengurus::class, 'assigned_by_pengurus_id')`
- `progressRecords()`: `hasMany(ProgressPembinaan::class)`

Scope yang disarankan:

- `scopeActive()`
- `scopeHighPriority()`

### 3.18 ProgressPembinaan

Model: `App\Models\ProgressPembinaan`

Relasi:

- `person()`: `belongsTo(Person::class)`
- `targetPembinaan()`: `belongsTo(TargetPembinaan::class)`
- `kegiatan()`: `belongsTo(Kegiatan::class)`
- `recorder()`: `belongsTo(Pengurus::class, 'recorded_by_pengurus_id')`

### 3.19 Iuran

Model: `App\Models\Iuran`

Relasi:

- `member()`: `belongsTo(Member::class)`
- `payments()`: `hasMany(PembayaranIuran::class)`

Helper yang disarankan:

- `isPaid()`
- `remainingAmount()`

### 3.20 PembayaranIuran

Model: `App\Models\PembayaranIuran`

Relasi:

- `iuran()`: `belongsTo(Iuran::class)`
- `recordedBy()`: `belongsTo(Pengurus::class, 'recorded_by_pengurus_id')`

### 3.21 Kas

Model: `App\Models\Kas`

Relasi:

- `transactions()`: `hasMany(TransaksiKas::class)`

Helper yang disarankan:

- `currentBalance()`

### 3.22 TransaksiKas

Model: `App\Models\TransaksiKas`

Relasi:

- `kas()`: `belongsTo(Kas::class)`
- `kegiatan()`: `belongsTo(Kegiatan::class)`
- `recordedBy()`: `belongsTo(Pengurus::class, 'recorded_by_pengurus_id')`

### 3.23 JabatanIdeal

Model: `App\Models\JabatanIdeal`

Relasi:

- `fungsi()`: `belongsTo(Fungsi::class)`
- `jabatan()`: `belongsTo(Jabatan::class)`
- `candidates()`: `hasMany(KandidatJabatan::class)`

### 3.24 KandidatJabatan

Model: `App\Models\KandidatJabatan`

Relasi:

- `jabatanIdeal()`: `belongsTo(JabatanIdeal::class)`
- `person()`: `belongsTo(Person::class)`

## 4. Relationship Summary by Domain

### Auth

- `User -> Pengurus -> Person`

### Master Orang

- `Person -> Member`
- `Person -> Pengurus`
- `Person -> PersonRoleLabel -> RoleLabel`

### Kegiatan

- `Kegiatan -> KegiatanPartisipan -> Person`
- `Kegiatan -> NoteKegiatan`
- `Kegiatan -> NoteIndividu`

### Monitoring

- `Person -> NoteIndividu -> NoteTag`
- `Person -> TargetPembinaan -> Jabatan`
- `Person -> ProgressPembinaan`

### Keuangan

- `Member -> Iuran -> PembayaranIuran`
- `Kas -> TransaksiKas`
- `Kegiatan -> TransaksiKas`

### Struktur Pengurus

- `Fungsi -> Jabatan`
- `Fungsi + Jabatan -> JabatanIdeal -> KandidatJabatan -> Person`

## 5. Eager Loading Recommendation

Untuk mengurangi N+1 query, eager load pada halaman utama:

### Profil Member

- `person`
- `iuran.payments`
- `person.receivedNotes.noteTags`
- `person.targetPembinaan.jabatan`
- `person.progressPembinaan`

### Detail Kegiatan

- `kategori`
- `subkategori`
- `creator.person`
- `partisipan.person`
- `notes.author.person`
- `individualNotes.noteTags`

### Dashboard

- `targetPembinaan.jabatan`
- `cashTransactions`
- `iuran.payments`

## 6. Model Trait Recommendation

Trait opsional yang berguna:

- `HasStatusLabel`
- `HasCode`
- `HasActiveScope`

Gunakan seperlunya, jangan abstractions berlebihan pada V1.

## 7. Implementation Recommendation

Untuk mulai implementasi:

1. buat semua model inti
2. isi relasi dasar dulu
3. baru tambahkan scope helper
4. baru setelah itu buat service dan policy

Fokus awal:

- `User`
- `Person`
- `Member`
- `Pengurus`
- `Kegiatan`
- `NoteIndividu`
- `TargetPembinaan`
- `Iuran`
- `Kas`

