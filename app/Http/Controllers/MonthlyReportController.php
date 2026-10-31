<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\MonthlyReport;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MonthlyReportController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $children = Child::accessibleBy($user)->orderBy('id')->get();
        $activeChildId = (int) $request->query('child_id', $request->session()->get('active_child_id'));
        $activeChild = $children->firstWhere('id', $activeChildId) ?? $children->first();

        if ($activeChild) {
            Gate::authorize('view', $activeChild);
            $request->session()->put('active_child_id', $activeChild->id);
        }

        $selectedMonth = $request->query('month', '2026-10');
        if (! preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = '2026-10';
        }

        $carbonMonth = Carbon::createFromFormat('Y-m-d', $selectedMonth . '-01');
        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $daysFull = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
        ];
        $monthLabel = ($monthsFull[(int) $carbonMonth->format('n')] ?? 'Oktober') . ' ' . $carbonMonth->format('Y');

        $reportModel = $activeChild
            ? MonthlyReport::where('child_id', $activeChild->id)
                ->where('month_year', $selectedMonth)
                ->first()
            : null;

        $availableMonths = $activeChild
            ? MonthlyReport::where('child_id', $activeChild->id)
                ->orderByDesc('month_year')
                ->pluck('month_year')
                ->toArray()
            : [];

        // Reference attendance statistics directly from the child's DailyReport records in this month
        $dailyReports = $activeChild
            ? DailyReport::where('child_id', $activeChild->id)
                ->whereYear('report_date', $carbonMonth->year)
                ->whereMonth('report_date', $carbonMonth->month)
                ->orderBy('report_date')
                ->get()
            : collect();

        $totalHadir = $dailyReports->filter(fn (DailyReport $r) => ($r->attendance_status ?: 'hadir') === 'hadir')->count();
        $totalIzin = $dailyReports->where('attendance_status', 'izin')->count();
        $totalSakit = $dailyReports->where('attendance_status', 'sakit')->count();
        $totalDays = $dailyReports->count();

        $leaveRecords = $dailyReports
            ->filter(fn (DailyReport $r) => in_array($r->attendance_status, ['izin', 'sakit'], true))
            ->map(function (DailyReport $r) use ($daysFull, $monthsFull) {
                $dt = $r->report_date;
                return [
                    'id' => $r->id,
                    'report_date' => $dt->format('Y-m-d'),
                    'formatted_date' => ($daysFull[(int) $dt->format('N')] ?? '') . ', '
                        . $dt->format('d') . ' '
                        . ($monthsFull[(int) $dt->format('n')] ?? '') . ' '
                        . $dt->format('Y'),
                    'attendance_status' => $r->attendance_status,
                    'attendance_label' => $r->attendance_status === 'sakit' ? 'Sakit' : 'Izin',
                    'absence_reason' => $r->absence_reason,
                ];
            })
            ->values()
            ->all();

        $attendanceStats = [
            'month_year' => $selectedMonth,
            'month_label' => $monthLabel,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_days' => $totalDays,
            'attendance_rate' => $totalDays > 0 ? (int) round(($totalHadir / $totalDays) * 100) : 0,
            'leave_records' => $leaveRecords,
        ];

        return Inertia::render('Reports/Monthly', [
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $monthLabel,
            'prevMonth' => $carbonMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $carbonMonth->copy()->addMonth()->format('Y-m'),
            'availableMonths' => $availableMonths,
            'attendanceStats' => $attendanceStats,
            'report' => $reportModel ? [
                'id' => $reportModel->id,
                'month_year' => $reportModel->month_year,
                'reading_score' => $reportModel->reading_score,
                'writing_score' => $reportModel->writing_score,
                'counting_score' => $reportModel->counting_score,
                'attitude_score' => $reportModel->attitude_score,
                'notes' => $reportModel->notes ?? [
                    'reading' => '',
                    'writing' => '',
                    'counting' => '',
                    'attitude' => '',
                ],
            ] : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', MonthlyReport::class);

        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'month_year' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'reading_score' => ['required', 'integer', 'min:10', 'max:100'],
            'writing_score' => ['required', 'integer', 'min:10', 'max:100'],
            'counting_score' => ['required', 'integer', 'min:10', 'max:100'],
            'attitude_score' => ['required', 'integer', 'min:10', 'max:100'],
            'note_reading' => ['required', 'string', 'max:500'],
            'note_writing' => ['required', 'string', 'max:500'],
            'note_counting' => ['required', 'string', 'max:500'],
            'note_attitude' => ['required', 'string', 'max:500'],
        ]);

        $child = Child::findOrFail($validated['child_id']);

        MonthlyReport::updateOrCreate(
            [
                'child_id' => $child->id,
                'month_year' => $validated['month_year'],
            ],
            [
                'reading_score' => $validated['reading_score'],
                'writing_score' => $validated['writing_score'],
                'counting_score' => $validated['counting_score'],
                'attitude_score' => $validated['attitude_score'],
                'notes' => [
                    'reading' => $validated['note_reading'],
                    'writing' => $validated['note_writing'],
                    'counting' => $validated['note_counting'],
                    'attitude' => $validated['note_attitude'],
                ],
            ]
        );

        AppNotification::create([
            'user_id' => $child->parent_id,
            'child_id' => $child->id,
            'type' => 'laporan',
            'title' => 'Laporan bulanan ' . $validated['month_year'] . ' sudah tersedia',
            'message' => 'Perkembangan bulanan untuk ' . $child->name . ' telah diperbarui oleh guru.',
            'action_url' => route('reports.monthly', ['month' => $validated['month_year'], 'child_id' => $child->id], false),
            'is_read' => false,
        ]);

        return redirect()
            ->route('reports.monthly', ['month' => $validated['month_year'], 'child_id' => $child->id])
            ->with('success', 'Laporan bulanan ' . $child->name . ' berhasil disimpan.');
    }
}
