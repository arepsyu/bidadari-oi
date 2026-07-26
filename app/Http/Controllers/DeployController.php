<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * ==================================================================================
 *  PERINGATAN: Controller ini CUMA buat sekali pakai pas deploy pertama kali
 *  di hosting yang gak ada SSH/artisan (misal InfinityFree).
 *
 *  SETELAH SELESAI DEPLOY, WAJIB HAPUS file ini + routenya dari routes/web.php
 *  (lihat README-INFINITYFREE.md), soalnya kalau dibiarin siapa aja yang tau
 *  URL-nya bisa reset/ubah database lo.
 * ==================================================================================
 */
class DeployController extends Controller
{
    private function checkToken(Request $request): void
    {
        $token = $request->query('token');
        $validToken = env('DEPLOY_TOKEN');

        if (! $validToken || $token !== $validToken) {
            abort(404);
        }
    }

    public function migrate(Request $request)
    {
        $this->checkToken($request);

        Artisan::call('migrate', ['--force' => true]);

        return '<pre>' . e(Artisan::output()) . '</pre><p>Migrate selesai. Lanjut ke /deploy/seed?token=xxx buat isi data awal.</p>';
    }

    public function seed(Request $request)
    {
        $this->checkToken($request);

        Artisan::call('db:seed', ['--force' => true]);

        return '<pre>' . e(Artisan::output()) . '</pre><p>Seed selesai. Data KLA, 16 akun Kecamatan, dan admin udah masuk ke database.</p>';
    }

    public function fresh(Request $request)
    {
        $this->checkToken($request);

        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);

        return '<pre>' . e(Artisan::output()) . '</pre><p>Migrate fresh + seed selesai (database di-reset total lalu diisi ulang).</p>';
    }

    public function clearCache(Request $request)
    {
        $this->checkToken($request);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return '<pre>Cache berhasil dibersihkan.</pre>';
    }

    public function storageLink(Request $request)
    {
        $this->checkToken($request);

        // Catatan: symlink biasanya GAK jalan di hosting share kayak InfinityFree.
        // Endpoint ini disediakan buat jaga-jaga kalau hostingnya ternyata dukung symlink.
        Artisan::call('storage:link');

        return '<pre>' . e(Artisan::output()) . '</pre>';
    }
}
