<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $setting = Setting::query()->firstOrCreate([]);
            View::share('appSettings', $setting);

            if (! empty($setting->bahasa)) {
                app()->setLocale($setting->bahasa);
                Carbon::setLocale($setting->bahasa);
            }
        }

        View::composer(['layouts.notification', 'layouts.topbar'], function ($view) {
            $notifications = collect();
            if (Schema::hasTable('transactions') && Schema::hasTable('projects')) {
                // 1. Pending transactions (Menunggu Verifikasi)
                $pendingTransactions = \App\Models\Transaction::query()->where('status', 'Pending')->latest('tanggal')->limit(4)->get();
                foreach ($pendingTransactions as $pt) {
                    $notifications->push([
                        'id' => 'tx-' . $pt->id,
                        'type' => 'pending',
                        'title' => 'Menunggu Verifikasi',
                        'title_en' => 'Pending Verification',
                        'message' => ($pt->tipe === 'masuk' ? 'Pemasukan ' : 'Pengeluaran ') . format_currency($pt->nominal) . ' - ' . ($pt->deskripsi ?: ucfirst($pt->kategori)),
                        'time' => $pt->tanggal ? format_app_date($pt->tanggal) : 'Baru saja',
                        'icon' => 'warning',
                        'url' => route('transaksi', ['status' => 'Pending']),
                        'unread' => true,
                    ]);
                }

                // 2. Budget Alert (>= 80%)
                $projects = \App\Models\Project::query()->where('status', 'aktif')->where('pagu_anggaran', '>', 0)->get();
                foreach ($projects as $proj) {
                    $spent = \App\Models\Transaction::query()->where('proyek_id', $proj->id)->where('tipe', 'keluar')->where('status', 'Sukses')->sum('nominal');
                    $progress = round(($spent / $proj->pagu_anggaran) * 100);
                    if ($progress >= 80) {
                        $notifications->push([
                            'id' => 'proj-' . $proj->id,
                            'type' => 'budget_alert',
                            'title' => 'Peringatan Anggaran',
                            'title_en' => 'Budget Limit Alert',
                            'message' => 'Proyek ' . $proj->nama_proyek . ' telah mencapai ' . $progress . '% pagu anggaran.',
                            'time' => 'Pagu: ' . format_currency($proj->pagu_anggaran),
                            'icon' => 'alert',
                            'url' => route('proyek'),
                            'unread' => true,
                        ]);
                    }
                }

                // 3. Transaksi sukses terkini
                $recentSuccess = \App\Models\Transaction::query()->where('status', 'Sukses')->latest('tanggal')->latest('id')->limit(2)->get();
                foreach ($recentSuccess as $st) {
                    $notifications->push([
                        'id' => 'succ-' . $st->id,
                        'type' => 'success',
                        'title' => $st->tipe === 'masuk' ? 'Pemasukan Terverifikasi' : 'Pengeluaran Terverifikasi',
                        'title_en' => $st->tipe === 'masuk' ? 'Income Verified' : 'Expense Recorded',
                        'message' => ($st->deskripsi ?: ucfirst($st->kategori)) . ' (' . format_currency($st->nominal) . ')',
                        'time' => $st->tanggal ? format_app_date($st->tanggal) : 'Terkini',
                        'icon' => 'check',
                        'url' => route('transaksi'),
                        'unread' => false,
                    ]);
                }
            }

            $unreadCount = $notifications->where('unread', true)->count();
            $view->with([
                'appNotifications' => $notifications,
                'unreadNotificationCount' => $unreadCount,
            ]);
        });
    }
}

