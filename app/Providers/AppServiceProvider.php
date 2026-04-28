<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Karyawan;
use App\Models\Shift;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('kasir.*', function ($view) {

            $user = Auth::user();

            $karyawan = null;

            if ($user && $user->id_karyawan) {
                $karyawan = Karyawan::find($user->id_karyawan);
            }

            // ================= SHIFT =================
            $now = Carbon::now();

            $shift = Shift::where('status', 1)->get()->first(function ($s) use ($now) {

                $mulai = Carbon::parse($s->waktu_mulai);
                $selesai = Carbon::parse($s->waktu_selesai);

                // shift normal
                if ($mulai <= $selesai) {
                    return $now->between($mulai, $selesai);
                }

                // shift malam
                return $now->gte($mulai) || $now->lte($selesai);
            });

            // 🔥 INI LETAKNYA DI SINI (PENTING)
            if (!$shift) {
                $shift = Shift::where('status', 1)->first();
            }

            $view->with(compact('karyawan','shift'));
        });
    }
}