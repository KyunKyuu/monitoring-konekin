<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\CashAccount;
use App\Models\CashTransaction;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\ContributionPayment;
use App\Models\DevelopmentTarget;
use App\Models\IdealPosition;
use App\Models\Member;
use App\Models\MemberNote;
use App\Models\PositionCandidate;
use App\Models\ProgressUpdate;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate([
            'email' => 'admin@komunitas.local',
        ], [
            'name' => 'Super Admin',
            'username' => 'admin',
            'role' => 'super_admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::query()->updateOrCreate([
            'email' => 'mentor@komunitas.local',
        ], [
            'name' => 'Pembina Materi',
            'username' => 'mentor',
            'role' => 'mentor',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::query()->updateOrCreate([
            'email' => 'keuangan@komunitas.local',
        ], [
            'name' => 'Pengurus Keuangan',
            'username' => 'keuangan',
            'role' => 'pengurus_keuangan',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        if (Member::query()->doesntExist()) {
            $kelasCategory = Category::create([
                'name' => 'Kelas',
                'description' => 'Agenda pembelajaran dan materi',
            ]);

            $olahragaCategory = Category::create([
                'name' => 'Olahraga',
                'description' => 'Agenda fisik dan kebugaran',
            ]);

            $boundingCategory = Category::create([
                'name' => 'Bounding',
                'description' => 'Agenda kedekatan dan penguatan tim',
            ]);

            $materiSholat = Subcategory::create([
                'category_id' => $kelasCategory->id,
                'name' => 'Materi Sholat',
                'description' => 'Materi ibadah sholat dan evaluasi praktik',
            ]);

            Subcategory::create([
                'category_id' => $olahragaCategory->id,
                'name' => 'Agenda Fisik Pagi',
                'description' => 'Aktivitas fisik pagi untuk disiplin dan stamina',
            ]);

            $members = collect([
                ['code' => 'MBR-001', 'name' => 'Bintang A.', 'target_role' => 'Calon Pengurus Olahraga', 'target_function' => 'Olahraga', 'note_priority' => 'high'],
                ['code' => 'MBR-002', 'name' => 'Rasyid N.', 'target_role' => 'Calon Bendahara', 'target_function' => 'Keuangan', 'note_priority' => 'urgent'],
                ['code' => 'MBR-003', 'name' => 'Mira K.', 'target_role' => 'Walas Angkatan', 'target_function' => 'Kaderisasi', 'note_priority' => 'high'],
                ['code' => 'MBR-004', 'name' => 'Fadli S.', 'target_role' => 'Calon Pengurus Materi', 'target_function' => 'Materi', 'note_priority' => 'normal'],
            ])->map(fn ($member) => Member::create([
                ...$member,
                'status' => 'active',
            ]));

            $members[1]->update(['kaka_tingkat_id' => $members[2]->id]);
            $members[3]->update(['kaka_tingkat_id' => $members[2]->id]);
            $members[2]->update(['kaka_tingkat_id' => $members[0]->id]);

            $admin = User::where('username', 'admin')->firstOrFail();

            $activity = Activity::create([
                'created_by' => $admin->id,
                'category_id' => $kelasCategory->id,
                'subcategory_id' => $materiSholat->id,
                'category' => 'Kelas',
                'sub_category' => 'Materi Sholat',
                'title' => 'Kelas Malam Sholat',
                'theme' => 'Materi sholat dan evaluasi praktik',
                'scheduled_at' => now()->setTime(22, 0),
                'location' => 'Aula utama',
                'status' => 'scheduled',
                'summary_note' => 'Agenda contoh untuk memulai dashboard.',
            ]);

            $activity->members()->sync($members->take(3)->mapWithKeys(fn ($member) => [
                $member->id => ['role_in_activity' => 'participant', 'attendance_status' => 'planned'],
            ])->all());

            MemberNote::create([
                'member_id' => $members[1]->id,
                'author_id' => $admin->id,
                'activity_id' => $activity->id,
                'tag' => 'keuangan',
                'level' => 'attention',
                'follow_up_status' => 'open',
                'content' => 'Perlu dilibatkan pada pencatatan iuran agar terlihat ketelitian dan konsistensinya.',
                'follow_up_action' => 'Dampingi input kas sederhana pekan ini.',
            ]);

            MemberNote::create([
                'member_id' => $members[0]->id,
                'author_id' => $admin->id,
                'activity_id' => $activity->id,
                'tag' => 'fisik',
                'level' => 'info',
                'follow_up_status' => 'monitoring',
                'content' => 'Cocok diprioritaskan pada agenda fisik pagi untuk melihat disiplin dan stamina.',
                'follow_up_action' => 'Tambahkan ke agenda olahraga berikutnya.',
            ]);

            $targetA = DevelopmentTarget::create([
                'member_id' => $members[1]->id,
                'assigned_by' => $admin->id,
                'function_name' => 'Keuangan',
                'role_name' => 'Calon Bendahara',
                'status' => 'active',
                'priority' => 'high',
                'goal' => 'Siap memegang pencatatan iuran dan kas sederhana.',
                'next_action' => 'Libatkan dalam rekap iuran dan latihan laporan mingguan.',
            ]);

            $targetB = DevelopmentTarget::create([
                'member_id' => $members[0]->id,
                'assigned_by' => $admin->id,
                'function_name' => 'Olahraga',
                'role_name' => 'Calon Pengurus Olahraga',
                'status' => 'active',
                'priority' => 'high',
                'goal' => 'Menunjukkan disiplin, stamina, dan inisiatif koordinasi lapangan.',
                'next_action' => 'Prioritaskan agenda fisik pagi dan tugas koordinasi kecil.',
            ]);

            ProgressUpdate::create([
                'member_id' => $members[1]->id,
                'development_target_id' => $targetA->id,
                'activity_id' => $activity->id,
                'recorded_by' => $admin->id,
                'area' => 'Administrasi keuangan',
                'stage' => 'Observasi awal',
                'status' => 'on_track',
                'summary' => 'Mulai menunjukkan ketelitian saat diminta bantu cek daftar iuran.',
            ]);

            ProgressUpdate::create([
                'member_id' => $members[0]->id,
                'development_target_id' => $targetB->id,
                'activity_id' => $activity->id,
                'recorded_by' => $admin->id,
                'area' => 'Disiplin fisik',
                'stage' => 'Prioritas agenda',
                'status' => 'needs_attention',
                'summary' => 'Perlu diuji konsistensi hadir pada agenda fisik pagi selama dua pekan.',
            ]);

            $cash = CashAccount::create([
                'code' => 'KAS-UMUM',
                'name' => 'Kas Umum',
                'description' => 'Kas utama untuk operasional komunitas',
            ]);

            $contribution = Contribution::create([
                'member_id' => $members[1]->id,
                'period_month' => now()->month,
                'period_year' => now()->year,
                'amount_due' => 50000,
                'amount_paid' => 25000,
                'status' => 'partial',
                'due_date' => now()->addDays(7)->toDateString(),
            ]);

            ContributionPayment::create([
                'contribution_id' => $contribution->id,
                'recorded_by' => $admin->id,
                'amount' => 25000,
                'paid_on' => now()->toDateString(),
                'payment_method' => 'Tunai',
            ]);

            CashTransaction::create([
                'cash_account_id' => $cash->id,
                'recorded_by' => $admin->id,
                'type' => 'income',
                'amount' => 25000,
                'transaction_date' => now()->toDateString(),
                'category' => 'Iuran',
                'description' => 'Pembayaran iuran awal dari '.$members[1]->name,
            ]);

            CashTransaction::create([
                'cash_account_id' => $cash->id,
                'activity_id' => $activity->id,
                'recorded_by' => $admin->id,
                'type' => 'expense',
                'amount' => 50000,
                'transaction_date' => now()->toDateString(),
                'category' => 'Operasional kegiatan',
                'description' => 'Pengeluaran kas untuk konsumsi kegiatan kelas malam.',
            ]);

            $bendahara = IdealPosition::create([
                'function_name' => 'Keuangan',
                'position_name' => 'Bendahara',
                'goal' => 'Menjaga iuran, kas, dan laporan sederhana tetap tertib.',
                'responsibilities' => 'Mengelola iuran, mencatat pemasukan-pengeluaran, dan menyiapkan rekap rutin.',
                'required_count' => 1,
                'status' => 'partial',
            ]);

            $koorOlahraga = IdealPosition::create([
                'function_name' => 'Olahraga',
                'position_name' => 'Koordinator Olahraga',
                'goal' => 'Menjaga ritme agenda fisik dan kedisiplinan tim olahraga.',
                'responsibilities' => 'Menyusun agenda fisik, koordinasi lapangan, dan evaluasi kehadiran.',
                'required_count' => 1,
                'status' => 'open',
            ]);

            PositionCandidate::create([
                'ideal_position_id' => $bendahara->id,
                'member_id' => $members[1]->id,
                'assigned_by' => $admin->id,
                'status' => 'candidate',
                'notes' => 'Sudah mulai dilibatkan pada pencatatan iuran dan observasi ketelitian.',
            ]);

            PositionCandidate::create([
                'ideal_position_id' => $koorOlahraga->id,
                'member_id' => $members[0]->id,
                'assigned_by' => $admin->id,
                'status' => 'candidate',
                'notes' => 'Prioritaskan agenda fisik dan cek kemampuan koordinasi kelompok kecil.',
            ]);
        }
    }
}
