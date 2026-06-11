<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Donasi;
use App\Models\LaporanAkhirProyekVendor;
use App\Models\PenugasanProyek;
use App\Models\PenyediaEnergi;
use App\Models\ProgressProyekVendor;
use App\Models\Proyek;
use App\Models\User;
use App\Notifications\DetailProyekDiisi;
use App\Notifications\ProgressProyekDikirim;
use App\Notifications\ProyekDitugaskan;
use App\Notifications\TargetDanaTercapai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlaywrightSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'nama' => 'Admin Playwright',
            'email' => 'admin.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        $vendor = PenyediaEnergi::create([
            'nama' => 'Vendor Playwright',
            'spesialisasi' => 'panel_surya',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        $vendorUser = User::factory()->create([
            'nama' => 'Vendor Playwright',
            'email' => 'vendor.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'penyedia',
            'status' => 'aktif',
            'penyedia_id' => $vendor->id,
        ]);

        $donor = User::factory()->create([
            'nama' => 'Donatur Playwright',
            'email' => 'donatur.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'donatur',
            'status' => 'aktif',
        ]);

        $donorB = User::factory()->create([
            'nama' => 'Donatur B Playwright',
            'email' => 'donatur-b.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'donatur',
            'status' => 'aktif',
        ]);

        User::factory()->create([
            'nama' => 'Donatur Empty Playwright',
            'email' => 'empty.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'donatur',
            'status' => 'aktif',
        ]);

        $desa = $this->createDesa($admin);

        $submitProject = $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI21 Submit',
            initialProgress: 25,
            initialDescription: 'Pondasi awal sudah disiapkan.'
        );

        $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI21 Invalid Percent'
        );

        $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI21 Complete'
        );

        $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI21 Draft'
        );

        $this->createPbi22MonitoringProjects($admin, $vendor, $desa);

        $this->createPbi23Notifications($admin, $vendorUser, $donor, $donorB, $submitProject);

        $this->createPbi25FinalReportProjects($admin, $vendor, $donor, $desa);

        $this->createPbi29ExpiredProjectExtensionProjects($admin, $vendor, $donor, $desa);
    }

    private function createDesa(User $admin): Desa
    {
        return Desa::create([
            'nama_desa' => 'Desa Playwright',
            'provinsi' => 'Jawa Barat',
            'kabupaten' => 'Bandung',
            'kondisi_desa' => 'off-grid',
            'sumber' => 'solar_panel',
            'id_admin' => $admin->id_donatur,
        ]);
    }

    private function createAssignedProject(
        User $admin,
        PenyediaEnergi $vendor,
        Desa $desa,
        string $title,
        ?int $initialProgress = null,
        ?string $initialDescription = null
    ): Proyek {
        $proyek = Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $vendor->id,
            'judul' => $title,
            'deskripsi' => 'Proyek untuk automated browser testing.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 150000000,
            'status' => 'eksekusi',
            'created_by' => $admin->id_donatur,
        ]);

        $penugasan = PenugasanProyek::create([
            'id_proyek' => $proyek->id,
            'id_penyedia' => $vendor->id,
            'status_penugasan' => 'diterima',
            'tanggal_respon' => now(),
        ]);

        if ($initialProgress !== null) {
            ProgressProyekVendor::create([
                'id_penugasan' => $penugasan->id_penugasan,
                'persentase' => $initialProgress,
                'deskripsi' => $initialDescription,
                'status_progress' => $initialProgress === 100 ? 'selesai' : 'berjalan',
                'status' => 'submitted',
                'submitted_at' => now()->subDay(),
            ]);
        }

        return $proyek;
    }

    private function createPbi22MonitoringProjects(User $admin, PenyediaEnergi $vendor, Desa $desa): void
    {
        $runningProject = $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI22 Sedang Berjalan'
        );
        $runningAssignment = $runningProject->penugasan()->first();

        ProgressProyekVendor::create([
            'id_penugasan' => $runningAssignment->id_penugasan,
            'persentase' => 40,
            'deskripsi' => 'Update lama PBI22.',
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDays(2),
        ]);

        ProgressProyekVendor::create([
            'id_penugasan' => $runningAssignment->id_penugasan,
            'persentase' => 75,
            'deskripsi' => 'Panel surya PBI22 hampir selesai.',
            'foto_paths' => ['progress/pbi22-lapangan.jpg'],
            'status_progress' => 'berjalan',
            'status' => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        $finishedProject = $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI22 Selesai'
        );
        $finishedProject->update(['status' => 'selesai']);
        $finishedAssignment = $finishedProject->penugasan()->first();

        ProgressProyekVendor::create([
            'id_penugasan' => $finishedAssignment->id_penugasan,
            'persentase' => 100,
            'deskripsi' => 'Progress PBI22 selesai 100%.',
            'foto_paths' => ['progress/pbi22-selesai.jpg'],
            'status_progress' => 'selesai',
            'status' => 'submitted',
            'submitted_at' => now()->subDay(),
        ]);

        LaporanAkhirProyekVendor::create([
            'id_penugasan' => $finishedAssignment->id_penugasan,
            'id_proyek' => $finishedProject->id,
            'id_penyedia' => $vendor->id,
            'deskripsi' => 'Instalasi PBI22 selesai dan sudah diuji bersama warga.',
            'kapasitas_terpasang' => 10,
            'satuan_kapasitas' => 'kWp',
            'foto_paths' => ['laporan/pbi22-selesai.jpg'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI22 Belum Ada Update'
        );
    }

    private function createPbi23Notifications(
        User $admin,
        User $vendorUser,
        User $donor,
        User $donorB,
        Proyek $submitProject
    ): void {
        $assignment = $submitProject->penugasan()->first();

        $vendorUser->notify(new ProyekDitugaskan($submitProject, $assignment));
        $admin->notify(new DetailProyekDiisi($submitProject));
        $donor->notify(new TargetDanaTercapai($submitProject));
        $donor->notify(new ProgressProyekDikirim($submitProject));

        $donorBProject = Proyek::create([
            'desa_id' => $submitProject->desa_id,
            'penyedia_id' => $submitProject->penyedia_id,
            'judul' => 'Proyek Rahasia Donatur B',
            'deskripsi' => 'Proyek untuk validasi isolasi data notifikasi.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subWeek()->toDateString(),
            'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
            'target_dana' => 150000000,
            'dana_terkumpul' => 150000000,
            'status' => 'eksekusi',
            'created_by' => $admin->id_donatur,
        ]);

        $donorB->notify(new ProgressProyekDikirim($donorBProject));

        DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->whereIn('notifiable_id', [$admin->id_donatur, $vendorUser->id_donatur, $donor->id_donatur, $donorB->id_donatur])
            ->update(['read_at' => now()]);

        $flowProjects = collect([
            'Proyek PBI23 Alur Pertama',
            'Proyek PBI23 Alur Kedua',
            'Proyek PBI23 Alur Ketiga',
        ])->map(function (string $title) use ($admin, $submitProject) {
            return Proyek::create([
                'desa_id' => $submitProject->desa_id,
                'penyedia_id' => $submitProject->penyedia_id,
                'judul' => $title,
                'deskripsi' => 'Proyek untuk validasi alur UI notifikasi.',
                'jenis_energi' => 'panel_surya',
                'estimasi_mulai' => now()->subWeek()->toDateString(),
                'estimasi_selesai' => now()->addWeeks(4)->toDateString(),
                'target_dana' => 150000000,
                'dana_terkumpul' => 150000000,
                'status' => 'eksekusi',
                'created_by' => $admin->id_donatur,
            ]);
        })->values();

        $flowProjects->each(fn (Proyek $project) => $donor->notify(new ProgressProyekDikirim($project)));

        $timestamps = [
            'Proyek PBI23 Alur Ketiga' => now()->subMinutes(1),
            'Proyek PBI23 Alur Kedua' => now()->subMinutes(2),
            'Proyek PBI23 Alur Pertama' => now()->subMinutes(3),
        ];

        foreach ($timestamps as $title => $createdAt) {
            DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $donor->id_donatur)
                ->whereNull('read_at')
                ->where('data', 'like', "%{$title}%")
                ->update([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
        }
    }

    private function createPbi25FinalReportProjects(
        User $admin,
        PenyediaEnergi $vendor,
        User $donor,
        Desa $desa
    ): void {
        $happyPathProject = $this->createCompletedPbi25Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI25 Happy Path'
        );

        Donasi::create([
            'id_proyek' => $happyPathProject->id,
            'id_donatur' => $donor->id_donatur,
            'nominal' => 250000,
            'status' => 'success',
        ]);

        $this->createCompletedPbi25Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI25 Draft'
        );

        $this->createCompletedPbi25Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI25 Validasi'
        );

        $duplicateProject = $this->createCompletedPbi25Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI25 Duplikasi'
        );
        $duplicateAssignment = $duplicateProject->penugasan()->first();

        LaporanAkhirProyekVendor::create([
            'id_penugasan' => $duplicateAssignment->id_penugasan,
            'id_proyek' => $duplicateProject->id,
            'id_penyedia' => $vendor->id,
            'deskripsi' => 'Laporan akhir PBI25 sudah pernah dikirim.',
            'kapasitas_terpasang' => 8,
            'satuan_kapasitas' => 'kWp',
            'foto_paths' => ['laporan/pbi25-duplikasi.jpg'],
            'status' => 'submitted',
            'submitted_at' => now()->subHour(),
        ]);

        $this->createCompletedPbi25Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI25 Akses'
        );

        $otherVendor = PenyediaEnergi::create([
            'nama' => 'Vendor Lain Playwright',
            'spesialisasi' => 'biogas',
            'provinsi_operasi' => 'Jawa Barat',
            'kisaran_harga_min' => 1000000,
            'kisaran_harga_max' => 10000000,
            'status' => 'aktif',
        ]);

        User::factory()->create([
            'nama' => 'Vendor Lain Playwright',
            'email' => 'vendor-lain.e2e@nusa.test',
            'password' => Hash::make('password'),
            'role' => 'penyedia',
            'status' => 'aktif',
            'penyedia_id' => $otherVendor->id,
        ]);
    }

    private function createCompletedPbi25Project(
        User $admin,
        PenyediaEnergi $vendor,
        Desa $desa,
        string $title
    ): Proyek {
        return $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: $title,
            initialProgress: 100,
            initialDescription: "Progress {$title} sudah selesai 100%."
        );
    }

    private function createPbi29ExpiredProjectExtensionProjects(
        User $admin,
        PenyediaEnergi $vendor,
        User $donor,
        Desa $desa
    ): void {
        Proyek::create([
            'desa_id' => $desa->id_desa,
            'penyedia_id' => $vendor->id,
            'judul' => 'Proyek PBI29 Cron Underfunded',
            'deskripsi' => 'Proyek expired underfunded untuk validasi cron.',
            'jenis_energi' => 'panel_surya',
            'estimasi_mulai' => now()->subMonth()->toDateString(),
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'target_dana' => 100000000,
            'dana_terkumpul' => 5000000,
            'status' => 'aktif_funding',
            'created_by' => $admin->id_donatur,
        ]);

        $fundedProject = $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI29 Cron Funded'
        );
        $fundedProject->update([
            'estimasi_selesai' => now()->subDay()->toDateString(),
            'target_dana' => 100000000,
            'dana_terkumpul' => 100000000,
            'status' => 'eksekusi',
        ]);

        $continueProject = $this->createPendingDecisionPbi29Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI29 Lanjutkan'
        );
        Donasi::create([
            'id_proyek' => $continueProject->id,
            'id_donatur' => $donor->id_donatur,
            'nominal' => 300000,
            'status' => 'success',
        ]);

        $refundProject = $this->createPendingDecisionPbi29Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI29 Refund'
        );
        Donasi::create([
            'id_proyek' => $refundProject->id,
            'id_donatur' => $donor->id_donatur,
            'nominal' => 200000,
            'status' => 'success',
        ]);

        $this->createPendingDecisionPbi29Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI29 Akses'
        );

        $this->createPendingDecisionPbi29Project(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: 'Proyek PBI29 Reminder',
            extendedAt: now()->subDays(3)
        );
    }

    private function createPendingDecisionPbi29Project(
        User $admin,
        PenyediaEnergi $vendor,
        Desa $desa,
        string $title,
        $extendedAt = null
    ): Proyek {
        $project = $this->createAssignedProject(
            admin: $admin,
            vendor: $vendor,
            desa: $desa,
            title: $title
        );

        $project->update([
            'estimasi_selesai' => now()->addDays(30)->toDateString(),
            'target_dana' => 100000000,
            'dana_terkumpul' => 7500000,
            'status' => 'menunggu_keputusan_vendor',
            'expired_extension_pending' => true,
            'expired_original_end_date' => now()->subDay()->toDateString(),
            'expired_extended_at' => $extendedAt ?? now(),
        ]);

        return $project;
    }
}
