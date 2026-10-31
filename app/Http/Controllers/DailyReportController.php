<?php

namespace App\Http\Controllers;

use App\Models\ActivityPhoto;
use App\Models\AppNotification;
use App\Models\Child;
use App\Models\DailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DailyReportController extends Controller
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

        // Default date matches reference design: Rabu, 14 Oktober 2026 (2026-10-14) or latest report date
        $selectedDate = $request->query('date', '2026-10-14');
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Throwable) {
            $carbonDate = Carbon::parse('2026-10-14');
        }

        // Build Monday-Friday week strip around the selected date (e.g. Sen 12, Sel 13, Rab 14, Kam 15, Jum 16)
        $monday = $carbonDate->copy()->startOfWeek(Carbon::MONDAY);
        $dayNamesShort = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
        ];

        $childReportsByDate = $activeChild
            ? DailyReport::where('child_id', $activeChild->id)
                ->get(['report_date', 'attendance_status'])
                ->keyBy(fn (DailyReport $r) => Carbon::parse($r->report_date)->format('Y-m-d'))
            : collect();

        $availableReportDates = $childReportsByDate->keys()->values()->all();

        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $day = $monday->copy()->addDays($i);
            $ymd = $day->format('Y-m-d');
            $dayReport = $childReportsByDate->get($ymd);
            $weekDays[] = [
                'date' => $ymd,
                'day_short' => $dayNamesShort[$i + 1],
                'day_number' => $day->format('d'),
                'is_selected' => $ymd === $carbonDate->format('Y-m-d'),
                'has_report' => $dayReport !== null,
                'attendance_status' => $dayReport ? ($dayReport->attendance_status ?: 'hadir') : null,
            ];
        }

        $monthsShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $monthHeader = ($monthsShort[(int) $carbonDate->format('n')] ?? 'Okt') . ' ' . $carbonDate->format('Y');
        $monthFullLabel = ($monthsFull[(int) $carbonDate->format('n')] ?? 'Oktober') . ' ' . $carbonDate->format('Y');

        // Monthly attendance statistics referenced from daily_reports for the current month
        $monthlyReports = $activeChild
            ? DailyReport::where('child_id', $activeChild->id)
                ->whereYear('report_date', $carbonDate->year)
                ->whereMonth('report_date', $carbonDate->month)
                ->get(['report_date', 'attendance_status', 'absence_reason'])
            : collect();

        $totalHadir = $monthlyReports->filter(fn (DailyReport $r) => ($r->attendance_status ?: 'hadir') === 'hadir')->count();
        $totalIzin = $monthlyReports->where('attendance_status', 'izin')->count();
        $totalSakit = $monthlyReports->where('attendance_status', 'sakit')->count();

        $attendanceStats = [
            'month_year' => $carbonDate->format('Y-m'),
            'month_label' => $monthFullLabel,
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_days' => $monthlyReports->count(),
        ];

        $reportModel = $activeChild
            ? DailyReport::with(['teacher:id,name', 'activityPhotos'])
                ->where('child_id', $activeChild->id)
                ->whereDate('report_date', $carbonDate->format('Y-m-d'))
                ->first()
            : null;

        $report = $reportModel ? $this->transformReport($reportModel) : null;

        return Inertia::render('Reports/Daily', [
            'selectedDate' => $carbonDate->format('Y-m-d'),
            'monthHeader' => $monthHeader,
            'prevWeekDate' => $monday->copy()->subWeek()->addDays(2)->format('Y-m-d'),
            'nextWeekDate' => $monday->copy()->addWeek()->addDays(2)->format('Y-m-d'),
            'weekDays' => $weekDays,
            'report' => $report,
            'availableDates' => $availableReportDates,
            'attendanceStats' => $attendanceStats,
        ]);
    }

    public function show(Request $request, DailyReport $dailyReport): Response
    {
        $dailyReport->load(['child', 'teacher:id,name', 'activityPhotos']);
        Gate::authorize('view', $dailyReport);

        $request->session()->put('active_child_id', $dailyReport->child_id);

        return Inertia::render('Reports/DailyDetail', [
            'report' => $this->transformReport($dailyReport),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', DailyReport::class);

        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'report_date' => ['required', 'date'],
            'attendance_status' => ['nullable', 'in:hadir,izin,sakit'],
            'absence_reason' => ['nullable', 'string', 'max:500'],
            'time_range' => ['required', 'string', 'max:50'],
            'materials' => ['required', 'array', 'min:1'],
            'materials.*' => ['required', 'string', 'max:255'],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*' => ['required', 'string', 'max:255'],
            'behavior_fokus' => ['required', 'string', 'max:80'],
            'behavior_antusias' => ['required', 'string', 'max:80'],
            'behavior_kemandirian' => ['required', 'string', 'max:80'],
            'teacher_notes' => ['required', 'string', 'max:1000'],
        ]);

        $child = Child::findOrFail($validated['child_id']);
        $attendanceStatus = $validated['attendance_status'] ?? 'hadir';
        $absenceReason = $attendanceStatus === 'hadir' ? null : ($validated['absence_reason'] ?? null);

        $report = DailyReport::updateOrCreate(
            [
                'child_id' => $child->id,
                'report_date' => $validated['report_date'],
            ],
            [
                'teacher_id' => $request->user()->id,
                'attendance_status' => $attendanceStatus,
                'absence_reason' => $absenceReason,
                'time_range' => $validated['time_range'],
                'materials' => array_values(array_filter($validated['materials'])),
                'activities' => array_values(array_filter($validated['activities'])),
                'behavior' => [
                    'Fokus' => $validated['behavior_fokus'],
                    'Antusias' => $validated['behavior_antusias'],
                    'Kemandirian' => $validated['behavior_kemandirian'],
                ],
                'teacher_notes' => $validated['teacher_notes'],
            ]
        );

        if ($attendanceStatus === 'hadir' && $report->activityPhotos()->count() === 0) {
            ActivityPhoto::create([
                'daily_report_id' => $report->id,
                'category' => str_contains(strtolower($child->group_name), 'pracalis') ? 'Pracalis' : 'Calistung',
                'photo_path' => 'seed/writing_b_main.svg',
                'caption' => $validated['materials'][0] ?? 'Latihan belajar harian',
                'taken_time' => '10.00',
            ]);
        }

        AppNotification::create([
            'user_id' => $child->parent_id,
            'child_id' => $child->id,
            'type' => 'laporan',
            'title' => 'Laporan harian hari ini sudah tersedia',
            'message' => 'Laporan harian untuk ' . $child->name . ' tanggal ' . $validated['report_date'] . ' telah diperbarui oleh guru.',
            'action_url' => route('reports.daily', ['date' => $validated['report_date'], 'child_id' => $child->id], false),
            'is_read' => false,
        ]);

        return redirect()
            ->route('reports.daily', ['date' => $validated['report_date'], 'child_id' => $child->id])
            ->with('success', 'Laporan harian untuk ' . $child->name . ' berhasil disimpan.');
    }

    /**
     * Parent (or Teacher/Admin) submits an Izin or Sakit attendance request for a child.
     */
    public function submitLeave(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'report_date' => ['required', 'date'],
            'attendance_status' => ['required', 'in:izin,sakit'],
            'absence_reason' => ['required', 'string', 'max:500'],
        ]);

        $child = Child::with('classGroup.teachers')->findOrFail($validated['child_id']);
        Gate::authorize('view', $child);

        $statusLabel = $validated['attendance_status'] === 'sakit' ? 'Sakit' : 'Izin';
        $defaultTeacherId = $child->classGroup?->teachers?->first()?->id
            ?? User::where('role', 'teacher')->value('id')
            ?? $user->id;

        DailyReport::updateOrCreate(
            [
                'child_id' => $child->id,
                'report_date' => $validated['report_date'],
            ],
            [
                'teacher_id' => $defaultTeacherId,
                'attendance_status' => $validated['attendance_status'],
                'absence_reason' => $validated['absence_reason'],
                'time_range' => 'Pengajuan ' . $statusLabel,
                'materials' => ['Siswa ' . $statusLabel . ' (' . $validated['absence_reason'] . ')'],
                'activities' => ['Tidak mengikuti kegiatan kelas karena ' . strtolower($statusLabel)],
                'behavior' => [
                    'Status Kehadiran' => $statusLabel,
                    'Keterangan' => $validated['absence_reason'],
                ],
                'teacher_notes' => 'Pengajuan ' . $statusLabel . ' oleh Orang Tua (' . $user->name . '): ' . $validated['absence_reason'],
            ]
        );

        $actionUrl = route('reports.daily', ['date' => $validated['report_date'], 'child_id' => $child->id], false);

        // Notify class teachers & admins
        $teacherIds = $child->classGroup?->teachers?->pluck('id')->all() ?? [];
        if (empty($teacherIds)) {
            $teacherIds = User::where('role', 'teacher')->pluck('id')->all();
        }
        $adminIds = User::where('role', 'admin')->pluck('id')->all();
        $recipientIds = array_unique(array_merge($teacherIds, $adminIds));

        foreach ($recipientIds as $recipientId) {
            AppNotification::create([
                'user_id' => $recipientId,
                'child_id' => $child->id,
                'type' => 'laporan',
                'title' => 'Pengajuan ' . $statusLabel . ': ' . $child->name,
                'message' => 'Orang tua ' . $child->name . ' (' . $child->group_name . ') mengajukan keterangan ' . strtoupper($statusLabel) . ' untuk tanggal ' . $validated['report_date'] . '. Alasan: ' . $validated['absence_reason'],
                'action_url' => $actionUrl,
                'is_read' => false,
            ]);
        }

        // Also record confirmation notification for Parent
        if ($user->isParent()) {
            AppNotification::create([
                'user_id' => $user->id,
                'child_id' => $child->id,
                'type' => 'laporan',
                'title' => 'Keterangan ' . $statusLabel . ' ' . $child->name . ' tercatat',
                'message' => 'Pengajuan ' . strtolower($statusLabel) . ' untuk ' . $child->name . ' pada tanggal ' . $validated['report_date'] . ' (' . $validated['absence_reason'] . ') telah dikirim ke Guru & Admin.',
                'action_url' => $actionUrl,
                'is_read' => false,
            ]);
        }

        return redirect()
            ->route('reports.daily', ['date' => $validated['report_date'], 'child_id' => $child->id])
            ->with('success', 'Pengajuan ' . $statusLabel . ' untuk ' . $child->name . ' pada tanggal ' . $validated['report_date'] . ' berhasil disimpan.');
    }

    private function transformReport(DailyReport $report): array
    {
        $daysFull = [
            1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
            5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu',
        ];
        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $dt = $report->report_date;
        $formattedDate = ($daysFull[(int) $dt->format('N')] ?? '') . ', '
            . $dt->format('d') . ' '
            . ($monthsFull[(int) $dt->format('n')] ?? '') . ' '
            . $dt->format('Y');

        $status = $report->attendance_status ?: 'hadir';
        $statusLabel = match ($status) {
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            default => 'Hadir',
        };

        return [
            'id' => $report->id,
            'child_id' => $report->child_id,
            'report_date' => $dt->format('Y-m-d'),
            'formatted_date' => $formattedDate,
            'attendance_status' => $status,
            'attendance_label' => $statusLabel,
            'absence_reason' => $report->absence_reason,
            'time_range' => $report->time_range,
            'materials' => $report->materials ?? [],
            'activities' => $report->activities ?? [],
            'behavior' => $report->behavior ?? [],
            'teacher_notes' => $report->teacher_notes,
            'teacher_name' => $report->teacher?->name ?? 'Guru Sabila',
            'photos' => $report->activityPhotos->map(fn (ActivityPhoto $photo) => [
                'id' => $photo->id,
                'category' => $photo->category,
                'caption' => $photo->caption,
                'taken_time' => $photo->taken_time,
                'photo_url' => $photo->photo_url,
            ])->values()->all(),
        ];
    }
}
