<?php

namespace App\Filament\Admin\Widgets;
use Carbon\Carbon;
use App\Models\Obat;
use App\Models\User;
use App\Models\Pesanan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsWdigetPerformance extends BaseWidget
{
    protected ?string $heading = 'Statistik Penjualan & Inventori';

    protected function getStats(): array
    {
        $today   = Carbon::today();
        $yday    = Carbon::yesterday();

        $ordersToday  = Pesanan::where('status', 'selesai')
                        ->whereDate('created_at', $today)
                        ->count();

        $ordersYesterday = Pesanan::where('status', 'selesai')
                            ->whereDate('created_at', $yday)
                            ->count();

        $produkTersedia = Obat::where('status', 'Tersedia')->sum('stok');

        $jumlahPelanggan = User::role('user')->count();
        $deltaOrders = $ordersYesterday === 0
            ? 0
            : (($ordersToday - $ordersYesterday) / $ordersYesterday) * 100;

        return [
            Stat::make('Pesanan Selesai Hari Ini', number_format($ordersToday))
                ->description(
                    ($deltaOrders >= 0 ? '+' : '') .
                    number_format($deltaOrders, 1) . '% vs kemarin'
                )
                ->descriptionIcon($deltaOrders >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($deltaOrders >= 0 ? 'success' : 'danger'),

            Stat::make('Produk Tersedia', number_format($produkTersedia))
                ->description('Status: '.($produkTersedia > 0 ? 'Siap Jual' : 'Habis'))
                ->descriptionIcon($produkTersedia > 0 ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($produkTersedia > 0 ? 'success' : 'danger'),

            Stat::make('Total Pelanggan', number_format($jumlahPelanggan))
                ->description('Pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}

class ChartYears extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Pendapatan Bulanan di tahun ' . now()->year;
    }

    protected function getData(): array
    {
        $year = Carbon::now()->year;

        $monthlyTotals = Pesanan::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', $year)
            ->where('status', 'selesai')
            ->groupBy('month')
            ->pluck('revenue', 'month');

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = (float) ($monthlyTotals[$m] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => "Pendapatan {$year} (Rp)",
                    'data'  => $data,
                    'fill'  => false,
                    'tension' => 0.3,
                    'pointRadius' => 4,
                ],
            ],
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

class ChartMonthly extends ChartWidget
{
    public function getHeading(): ?string
    {
        return 'Pendapatan Harian – ' . now()->translatedFormat('F Y');
    }

    protected function getData(): array
    {
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;
        $daysInMonth = $now->daysInMonth;

        $dailyTotals = Pesanan::selectRaw('DAY(created_at) as day, SUM(total) as revenue')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'selesai')
            ->groupBy('day')
            ->pluck('revenue', 'day');   

        $data   = [];
        $labels = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = str_pad($d, 2, '0', STR_PAD_LEFT);   
            $data[]   = (float) ($dailyTotals[$d] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data'  => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)', 
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';   
    }
}


class ChartProductPie extends ChartWidget
{
    /** Judul tampil dinamis di dashboard */
    public function getHeading(): ?string
    {
        return 'Distribusi Produk Terjual (Semua Pesanan Selesai)';
    }

    protected function getData(): array
    {
        $topProducts = DB::table('pesanan_items')
            ->join('pesanans', 'pesanan_items.pesanan_id', '=', 'pesanans.id')
            ->join('obats',    'pesanan_items.obat_id',   '=', 'obats.id')
            ->where('pesanans.status', 'selesai')
            ->selectRaw('obats.nama_obat AS nama, SUM(pesanan_items.qty) AS total_qty')
            ->groupBy('obats.id', 'obats.nama_obat')
            ->orderByDesc('total_qty')
            ->limit(50)                              
            ->pluck('total_qty', 'nama');            

        return [
            'datasets' => [
                [
                    'data'=> $topProducts->values(),  

                    'backgroundColor' => [
                        '#4dc9f6', '#f67019', '#f53794',
                        '#537bc4', '#acc236', '#166a8f',
                        '#00a950', '#58595b', '#8549ba', '#b8de6f',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels'   => $topProducts->keys(),     
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}

