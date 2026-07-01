-- PostgreSQL V1 schema for the community member monitoring dashboard.
-- This schema follows the scope defined in PROJECT_OVERVIEW_SDD.md.
-- Focus: auth, member/pengurus master data, kegiatan, note, pembinaan, iuran, kas.

begin;

create table access_roles (
    id bigserial primary key,
    code varchar(50) not null unique,
    name varchar(100) not null,
    description text,
    created_at timestamptz not null default now()
);

create table fungsi (
    id bigserial primary key,
    code varchar(50) not null unique,
    name varchar(100) not null unique,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table jabatan (
    id bigserial primary key,
    fungsi_id bigint not null references fungsi(id),
    code varchar(50) not null unique,
    name varchar(100) not null,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint uq_jabatan_fungsi_name unique (fungsi_id, name)
);

create table role_labels (
    id bigserial primary key,
    fungsi_id bigint references fungsi(id),
    code varchar(50) not null unique,
    name varchar(100) not null unique,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table persons (
    id bigserial primary key,
    person_type varchar(20) not null,
    code varchar(50) not null unique,
    name varchar(150) not null,
    gender varchar(20),
    phone varchar(30),
    email varchar(150),
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_person_type check (person_type in ('member', 'pengurus'))
);

create table members (
    id bigserial primary key,
    person_id bigint not null unique references persons(id),
    joined_at date,
    notes text
);

create table pengurus (
    id bigserial primary key,
    person_id bigint not null unique references persons(id),
    started_at date,
    ended_at date,
    notes text,
    constraint chk_pengurus_dates check (ended_at is null or started_at is null or ended_at >= started_at)
);

create table user_accounts (
    id bigserial primary key,
    pengurus_id bigint not null unique references pengurus(id),
    access_role_id bigint not null references access_roles(id),
    username varchar(100) not null unique,
    password_hash text not null,
    last_login_at timestamptz,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table person_role_labels (
    id bigserial primary key,
    person_id bigint not null references persons(id),
    role_label_id bigint not null references role_labels(id),
    start_date date,
    end_date date,
    status varchar(20) not null default 'active',
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_person_role_status check (status in ('active', 'inactive', 'candidate', 'completed')),
    constraint chk_person_role_dates check (end_date is null or start_date is null or end_date >= start_date)
);

create table kategori (
    id bigserial primary key,
    code varchar(50) not null unique,
    name varchar(100) not null unique,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table subkategori (
    id bigserial primary key,
    kategori_id bigint not null references kategori(id),
    code varchar(50) not null unique,
    name varchar(100) not null,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint uq_subkategori_kategori_name unique (kategori_id, name)
);

create table kegiatan (
    id bigserial primary key,
    kategori_id bigint not null references kategori(id),
    subkategori_id bigint references subkategori(id),
    created_by_pengurus_id bigint not null references pengurus(id),
    title varchar(200) not null,
    theme varchar(200),
    description text,
    scheduled_start_at timestamptz not null,
    scheduled_end_at timestamptz,
    actual_start_at timestamptz,
    actual_end_at timestamptz,
    status varchar(20) not null default 'draft',
    location varchar(200),
    is_global_calendar boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_kegiatan_status check (status in ('draft', 'scheduled', 'ongoing', 'completed', 'cancelled', 'postponed')),
    constraint chk_kegiatan_schedule check (scheduled_end_at is null or scheduled_end_at >= scheduled_start_at),
    constraint chk_kegiatan_actual check (actual_end_at is null or actual_start_at is null or actual_end_at >= actual_start_at)
);

create table kegiatan_partisipan (
    id bigserial primary key,
    kegiatan_id bigint not null references kegiatan(id) on delete cascade,
    person_id bigint not null references persons(id),
    participant_type varchar(20) not null,
    attendance_status varchar(20) not null default 'planned',
    activity_role varchar(50) not null,
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint uq_kegiatan_partisipan unique (kegiatan_id, person_id, activity_role),
    constraint chk_participant_type check (participant_type in ('member', 'pengurus')),
    constraint chk_attendance_status check (attendance_status in ('planned', 'present', 'absent', 'excused'))
);

create table note_kegiatan (
    id bigserial primary key,
    kegiatan_id bigint not null references kegiatan(id) on delete cascade,
    author_pengurus_id bigint not null references pengurus(id),
    content text not null,
    created_at timestamptz not null default now()
);

create table note_tags (
    id bigserial primary key,
    code varchar(50) not null unique,
    name varchar(100) not null unique,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table note_individu (
    id bigserial primary key,
    person_id bigint not null references persons(id),
    author_pengurus_id bigint not null references pengurus(id),
    kegiatan_id bigint references kegiatan(id) on delete set null,
    source_type varchar(30) not null default 'manual_observation',
    level varchar(20) not null default 'info',
    sentiment varchar(20) not null default 'neutral',
    follow_up_status varchar(20) not null default 'open',
    content text not null,
    follow_up_action text,
    created_at timestamptz not null default now(),
    constraint chk_note_source_type check (source_type in ('activity', 'profile_review', 'financial_review', 'academic_monitoring', 'manual_observation')),
    constraint chk_note_level check (level in ('info', 'attention', 'urgent')),
    constraint chk_note_sentiment check (sentiment in ('positive', 'neutral', 'negative')),
    constraint chk_note_follow_up_status check (follow_up_status in ('open', 'monitoring', 'followed_up', 'closed'))
);

create table note_individu_tags (
    id bigserial primary key,
    note_individu_id bigint not null references note_individu(id) on delete cascade,
    note_tag_id bigint not null references note_tags(id),
    constraint uq_note_individu_tag unique (note_individu_id, note_tag_id)
);

create table target_pembinaan (
    id bigserial primary key,
    person_id bigint not null references persons(id),
    jabatan_id bigint not null references jabatan(id),
    assigned_by_pengurus_id bigint not null references pengurus(id),
    priority_level varchar(20) not null default 'medium',
    status varchar(20) not null default 'active',
    reason text,
    development_plan text,
    start_date date not null default current_date,
    end_date date,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_target_priority check (priority_level in ('low', 'medium', 'high')),
    constraint chk_target_status check (status in ('active', 'paused', 'completed', 'cancelled')),
    constraint chk_target_dates check (end_date is null or end_date >= start_date)
);

create table progress_pembinaan (
    id bigserial primary key,
    person_id bigint not null references persons(id),
    target_pembinaan_id bigint references target_pembinaan(id) on delete set null,
    kegiatan_id bigint references kegiatan(id) on delete set null,
    recorded_by_pengurus_id bigint not null references pengurus(id),
    area_name varchar(100) not null,
    stage_name varchar(100),
    status varchar(20) not null default 'on_track',
    notes text,
    recorded_at timestamptz not null default now(),
    constraint chk_progress_status check (status in ('not_started', 'on_track', 'needs_attention', 'completed'))
);

create table iuran (
    id bigserial primary key,
    member_id bigint not null references members(id),
    period_month smallint not null,
    period_year integer not null,
    amount_due numeric(14,2) not null,
    amount_paid numeric(14,2) not null default 0,
    status varchar(20) not null default 'unpaid',
    due_date date,
    notes text,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_iuran_period_month check (period_month between 1 and 12),
    constraint chk_iuran_amount_due check (amount_due >= 0),
    constraint chk_iuran_amount_paid check (amount_paid >= 0),
    constraint chk_iuran_status check (status in ('unpaid', 'partial', 'paid')),
    constraint uq_iuran_member_period unique (member_id, period_month, period_year)
);

create table pembayaran_iuran (
    id bigserial primary key,
    iuran_id bigint not null references iuran(id) on delete cascade,
    recorded_by_pengurus_id bigint not null references pengurus(id),
    paid_at timestamptz not null,
    amount numeric(14,2) not null,
    payment_method varchar(30),
    notes text,
    created_at timestamptz not null default now(),
    constraint chk_pembayaran_iuran_amount check (amount > 0)
);

create table kas (
    id bigserial primary key,
    code varchar(50) not null unique,
    name varchar(100) not null unique,
    description text,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table transaksi_kas (
    id bigserial primary key,
    kas_id bigint not null references kas(id),
    kegiatan_id bigint references kegiatan(id) on delete set null,
    recorded_by_pengurus_id bigint not null references pengurus(id),
    transaction_type varchar(20) not null,
    reference_type varchar(30),
    reference_id bigint,
    amount numeric(14,2) not null,
    transaction_date timestamptz not null,
    description text not null,
    created_at timestamptz not null default now(),
    constraint chk_transaksi_kas_type check (transaction_type in ('income', 'expense')),
    constraint chk_transaksi_kas_amount check (amount > 0)
);

create table jabatan_ideal (
    id bigserial primary key,
    fungsi_id bigint not null references fungsi(id),
    jabatan_id bigint not null references jabatan(id),
    goal text,
    responsibilities text,
    required_count integer not null default 1,
    is_active boolean not null default true,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_jabatan_ideal_required_count check (required_count > 0),
    constraint uq_jabatan_ideal unique (fungsi_id, jabatan_id)
);

create table kandidat_jabatan (
    id bigserial primary key,
    jabatan_ideal_id bigint not null references jabatan_ideal(id) on delete cascade,
    person_id bigint not null references persons(id),
    candidate_status varchar(20) not null,
    notes text,
    assigned_at date not null default current_date,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    constraint chk_candidate_status check (candidate_status in ('candidate', 'assigned', 'rejected', 'completed')),
    constraint uq_kandidat_jabatan_person unique (jabatan_ideal_id, person_id)
);

create index idx_persons_type on persons(person_type);
create index idx_person_role_labels_person on person_role_labels(person_id);
create index idx_kegiatan_schedule on kegiatan(scheduled_start_at);
create index idx_kegiatan_status on kegiatan(status);
create index idx_kegiatan_partisipan_person on kegiatan_partisipan(person_id);
create index idx_note_individu_person on note_individu(person_id);
create index idx_note_individu_author on note_individu(author_pengurus_id);
create index idx_note_individu_kegiatan on note_individu(kegiatan_id);
create index idx_target_pembinaan_person on target_pembinaan(person_id);
create index idx_progress_pembinaan_person on progress_pembinaan(person_id);
create index idx_iuran_member_period on iuran(member_id, period_year, period_month);
create index idx_pembayaran_iuran_iuran on pembayaran_iuran(iuran_id);
create index idx_transaksi_kas_kas_date on transaksi_kas(kas_id, transaction_date);
create index idx_kandidat_jabatan_person on kandidat_jabatan(person_id);

insert into access_roles (code, name, description) values
    ('super_admin', 'Super Admin', 'Akses penuh seluruh sistem'),
    ('pembina_materi', 'Pembina Materi', 'Mengelola kegiatan dan monitoring materi'),
    ('pengurus_keuangan', 'Pengurus Keuangan', 'Mengelola iuran dan kas'),
    ('mentor', 'Mentor', 'Melakukan monitoring dan pembinaan anggota')
on conflict (code) do nothing;

insert into note_tags (code, name, description) values
    ('akademik', 'Akademik', 'Catatan terkait pendidikan atau IPK'),
    ('disiplin', 'Disiplin', 'Catatan terkait kedisiplinan'),
    ('keuangan', 'Keuangan', 'Catatan terkait iuran atau tanggung jawab keuangan'),
    ('kepemimpinan', 'Kepemimpinan', 'Catatan terkait potensi memimpin'),
    ('fisik', 'Fisik', 'Catatan terkait agenda atau evaluasi fisik'),
    ('ibadah', 'Ibadah', 'Catatan terkait pembinaan ibadah')
on conflict (code) do nothing;

commit;
