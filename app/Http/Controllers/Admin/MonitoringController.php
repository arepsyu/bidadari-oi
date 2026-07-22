<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MonitoringExport;
use App\Exports\UserDetailExport;
use App\Http\Controllers\Controller;
use App\Models\DataRequirement;
use App\Models\Submission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::where('role', 'user')->count();
        $totalRequirements = DataRequirement::count();
        $totalSubmissions = Submission::count();

        $targetSubmissions = $totalUsers * max($totalRequirements, 1);
        $completionRate = $targetSubmissions > 0
            ? round(($totalSubmissions / $targetSubmissions) * 100)
            : 0;

        $users = User::where('role', 'user')
            ->withCount('submissions')
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($totalRequirements) {
                $user->progress = $totalRequirements > 0
                    ? round(($user->submissions_count / $totalRequirements) * 100)
                    : 0;
                return $user;
            });

        // Data untuk chart batang: kelengkapan per jenis data
        $requirementStats = DataRequirement::withCount('submissions')
            ->orderBy('urutan')
            ->get()
            ->map(function ($req) use ($totalUsers) {
                $req->percentage = $totalUsers > 0
                    ? round(($req->submissions_count / $totalUsers) * 100)
                    : 0;
                return $req;
            });

        $belumLengkap = $users->where('progress', '<', 100)->count();
        $sudahLengkap = $users->where('progress', '>=', 100)->count();

        return view('admin.monitoring.index', compact(
            'totalUsers',
            'totalRequirements',
            'totalSubmissions',
            'completionRate',
            'users',
            'requirementStats',
            'belumLengkap',
            'sudahLengkap'
        ));
    }

    public function show(User $user): View
    {
        $requirements = DataRequirement::orderBy('urutan')->get();
        $submissions = Submission::where('user_id', $user->id)
            ->get()
            ->keyBy('data_requirement_id');

        return view('admin.monitoring.show', compact('user', 'requirements', 'submissions'));
    }

    public function exportExcel(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'monitoring-bidadari-oi-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new MonitoringExport, $filename);
    }

    public function exportPdf(): Response
    {
        $totalRequirements = DataRequirement::count();

        $users = User::where('role', 'user')
            ->withCount('submissions')
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($totalRequirements) {
                $user->progress = $totalRequirements > 0
                    ? round(($user->submissions_count / $totalRequirements) * 100)
                    : 0;
                return $user;
            });

        $pdf = Pdf::loadView('admin.monitoring.pdf', compact('users', 'totalRequirements'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('monitoring-bidadari-oi-' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportUserExcel(User $user): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = 'data-' . str($user->organisasi ?? $user->name)->slug() . '.xlsx';
        return Excel::download(new UserDetailExport($user), $filename);
    }

    public function exportUserPdf(User $user): Response
    {
        $requirements = DataRequirement::orderBy('urutan')->get();
        $submissions = Submission::where('user_id', $user->id)->get()->keyBy('data_requirement_id');

        $pdf = Pdf::loadView('admin.monitoring.pdf_detail', compact('user', 'requirements', 'submissions'))
            ->setPaper('a4', 'portrait');

        $filename = 'data-' . str($user->organisasi ?? $user->name)->slug() . '.pdf';
        return $pdf->download($filename);
    }
}
