<?php

namespace Database\Seeders;

use App\Models\Donasi;
use App\Models\Order;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FakeDonationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'aditya.pratama@example.com')->first();
        $proyek = Proyek::first();

        if (! $user || ! $proyek) {
            return;
        }

        $amount = 50000;
        $amountBig = 50000000;
        $createdAt = Carbon::parse('2024-01-15 10:00:00');
        $createdAtBig = Carbon::parse('2024-01-16 10:00:00');

        DB::transaction(function () use ($user, $proyek, $amount, $amountBig, $createdAt, $createdAtBig) {
            $donasi = Donasi::firstOrNew([
                'id_proyek' => $proyek->id,
                'id_donatur' => $user->id_donatur,
                'created_at' => $createdAt,
            ]);

            if (! $donasi->exists) {
                $donasi->nominal = $amount;
                $donasi->status = 'success';
                $donasi->created_at = $createdAt;
                $donasi->updated_at = $createdAt;
                $donasi->save();

                $proyek->increment('dana_terkumpul', $amount);
            }

            $donasiBig = Donasi::firstOrNew([
                'id_proyek' => $proyek->id,
                'id_donatur' => $user->id_donatur,
                'created_at' => $createdAtBig,
            ]);

            if (! $donasiBig->exists) {
                $donasiBig->nominal = $amountBig;
                $donasiBig->status = 'success';
                $donasiBig->created_at = $createdAtBig;
                $donasiBig->updated_at = $createdAtBig;
                $donasiBig->save();

                $proyek->increment('dana_terkumpul', $amountBig);
            }

            Order::firstOrCreate(
                ['number' => 'DONASI-FAKE-TEST-001'],
                [
                    'user_id' => $user->id_donatur,
                    'total_price' => $amount,
                    'payment_status' => Order::STATUS_SUCCESS,
                    'proyek_id' => $proyek->id,
                ]
            );

            Order::firstOrCreate(
                ['number' => 'DONASI-FAKE-TEST-002'],
                [
                    'user_id' => $user->id_donatur,
                    'total_price' => $amountBig,
                    'payment_status' => Order::STATUS_SUCCESS,
                    'proyek_id' => $proyek->id,
                ]
            );

                Order::firstOrCreate(
                ['number' => 'DONASI-FAKE-TEST-003'],
                [
                    'user_id' => $user->id_donatur,
                    'total_price' => $amountBig,
                    'payment_status' => Order::STATUS_SUCCESS,
                    'proyek_id' => $proyek->id,
                ]
            );
        });
    }
}
