<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Desa;
use App\Models\User;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $period = $request->query('period', 'all');

            $startDate = null;
            $prevStartDate = null;
            $prevEndDate = null;
            $now = Carbon::now();

            if ($period === 'today') {
                $startDate = $now->copy()->startOfDay();
                $prevStartDate = $now->copy()->subDay()->startOfDay();
                $prevEndDate = $now->copy()->subDay()->endOfDay();
            } elseif ($period === '7days') {
                $startDate = $now->copy()->subDays(7)->startOfDay();
                $prevStartDate = $now->copy()->subDays(14)->startOfDay();
                $prevEndDate = $now->copy()->subDays(7)->endOfDay();
            } elseif ($period === '30days') {
                $startDate = $now->copy()->subDays(30)->startOfDay();
                $prevStartDate = $now->copy()->subDays(60)->startOfDay();
                $prevEndDate = $now->copy()->subDays(30)->endOfDay();
            }

            $applyPeriod = function ($query) use ($startDate) {
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
                return $query;
            };

            $applyPrevPeriod = function ($query) use ($prevStartDate, $prevEndDate) {
                if ($prevStartDate && $prevEndDate) {
                    $query->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
                } else {
                    $query->whereRaw('1 = 0');
                }
                return $query;
            };

            // Statistik
            $currentProyek = $applyPeriod(Proyek::query())->count();
            $prevProyek = $applyPrevPeriod(Proyek::query())->count();
            $proyekGrowth = $this->calculateGrowth($currentProyek, $prevProyek);

            $currentDana = $applyPeriod(Proyek::query())->sum('dana_terkumpul') ?? 0;
            $prevDana = $applyPrevPeriod(Proyek::query())->sum('dana_terkumpul') ?? 0;
            $danaGrowth = $this->calculateGrowth($currentDana, $prevDana);

            $currentDesa = $applyPeriod(Desa::query())->count();
            $prevDesa = $applyPrevPeriod(Desa::query())->count();
            $desaGrowth = $this->calculateGrowth($currentDesa, $prevDesa);

            $currentDonatur = $applyPeriod(User::where('role', 'donatur'))->count();
            $prevDonatur = $applyPrevPeriod(User::where('role', 'donatur'))->count();
            $donaturGrowth = $this->calculateGrowth($currentDonatur, $prevDonatur);

            // Proyek Terbaru
            $proyekTerbaruQuery = Proyek::with('desa')->orderByDesc('created_at')->limit(5);
            $proyekTerbaru = $applyPeriod($proyekTerbaruQuery)->get();

            // Aktivitas Donasi
            $donasiTerbaruQuery = Order::with(['proyek', 'user'])->where('payment_status', Order::STATUS_SUCCESS)->orderByDesc('created_at')->limit(5);
            $donasiTerbaru = $applyPeriod($donasiTerbaruQuery)->get();

            return view('admin.dashboard', compact(
                'currentProyek', 'proyekGrowth',
                'currentDana', 'danaGrowth',
                'currentDesa', 'desaGrowth',
                'currentDonatur', 'donaturGrowth',
                'proyekTerbaru', 'donasiTerbaru', 'period'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            return view('admin.dashboard', [
                'error' => 'Gagal memuat data dashboard. Silakan coba muat ulang halaman.'
            ]);
        }
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
