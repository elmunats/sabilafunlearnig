<?php

namespace App\Http\Controllers;

use App\Models\ActivityPhoto;
use App\Models\AppNotification;
use App\Models\Child;
use App\Models\DailyReport;
use App\Services\PrivateMediaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ActivityGalleryController extends Controller
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

        $selectedCategory = $request->query('category', 'Semua');
        $categories = ['Semua', 'Membaca', 'Menulis', 'Berhitung', 'Mengaji', 'Calistung', 'Pracalis', 'Kegiatan Lain'];
        if (! in_array($selectedCategory, $categories, true)) {
            $selectedCategory = 'Semua';
        }

        $photosQuery = ActivityPhoto::query()
            ->with(['dailyReport'])
            ->when($activeChild, fn ($q) => $q->whereHas('dailyReport', fn ($dq) => $dq->where('child_id', $activeChild->id)))
            ->when($selectedCategory !== 'Semua', fn ($q) => $q->where('category', $selectedCategory))
            ->orderByDesc('id');

        $photos = $photosQuery->get();

        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $groupedByMonth = $photos->groupBy(function (ActivityPhoto $photo) use ($monthsFull) {
            $date = $photo->dailyReport?->report_date ?? Carbon::parse('2026-10-14');
            return ($monthsFull[(int) $date->format('n')] ?? 'Oktober') . ' ' . $date->format('Y');
        })->map(function ($items, $monthTitle) {
            return [
                'month_title' => $monthTitle,
                'items' => $items->map(fn (ActivityPhoto $photo) => [
                    'id' => $photo->id,
                    'daily_report_id' => $photo->daily_report_id,
                    'category' => $photo->category,
                    'caption' => $photo->caption,
                    'taken_time' => $photo->taken_time,
                    'report_date' => $photo->dailyReport?->report_date?->format('d M Y'),
                    'photo_url' => $photo->photo_url,
                ])->values()->all(),
            ];
        })->values()->all();

        return Inertia::render('Activities/Index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'photoGroups' => $groupedByMonth,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', DailyReport::class);

        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'report_date' => ['required', 'date'],
            'category' => ['required', 'in:Membaca,Menulis,Berhitung,Mengaji,Calistung,Pracalis,Kegiatan Lain'],
            'caption' => ['required', 'string', 'max:200'],
            'taken_time' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:5120'],
            'preset_illust' => ['nullable', 'string', 'max:100'],
        ]);

        $child = Child::findOrFail($validated['child_id']);

        $report = DailyReport::firstOrCreate(
            [
                'child_id' => $child->id,
                'report_date' => $validated['report_date'],
            ],
            [
                'teacher_id' => $request->user()->id,
                'time_range' => '10.00 - 11.00',
                'materials' => [$validated['caption']],
                'activities' => ['Kegiatan belajar di kelas', $validated['caption']],
                'behavior' => [
                    'Fokus' => 'Baik',
                    'Antusias' => 'Baik',
                    'Kemandirian' => 'Mulai berkembang',
                ],
                'teacher_notes' => $child->name . ' mengikuti kegiatan ' . strtolower($validated['caption']) . ' dengan antusias.',
            ]
        );

        if ($request->hasFile('photo')) {
            $photoPath = PrivateMediaService::store($request->file('photo'), 'activities');
        } else {
            $photoPath = $validated['preset_illust'] ?: 'seed/writing_b_main.svg';
        }

        ActivityPhoto::create([
            'daily_report_id' => $report->id,
            'category' => $validated['category'],
            'photo_path' => $photoPath,
            'caption' => $validated['caption'],
            'taken_time' => $validated['taken_time'] ?: now()->format('H.i'),
        ]);

        AppNotification::create([
            'user_id' => $child->parent_id,
            'child_id' => $child->id,
            'type' => 'laporan',
            'title' => 'Foto kegiatan baru diunggah',
            'message' => 'Foto baru "' . $validated['caption'] . '" untuk ' . $child->name . ' telah diunggah.',
            'action_url' => route('activities.index', ['category' => $validated['category']], false),
            'is_read' => false,
        ]);

        return back()->with('success', 'Foto kegiatan "' . $validated['caption'] . '" berhasil diunggah ke penyimpanan privat.');
    }
}

