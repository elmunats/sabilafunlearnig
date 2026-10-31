<?php

namespace App\Http\Controllers;

use App\Models\ActivityPhoto;
use App\Models\Child;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $children = Child::accessibleBy($user)->orderBy('id')->get();

        $activeChildId = (int) $request->session()->get('active_child_id');
        $activeChild = $children->firstWhere('id', $activeChildId) ?? $children->first();

        $latestActivity = null;
        $currentInvoice = null;
        $latestReportId = null;

        if ($activeChild) {
            $latestPhoto = ActivityPhoto::query()
                ->whereHas('dailyReport', fn ($q) => $q->where('child_id', $activeChild->id))
                ->with('dailyReport')
                ->orderByDesc('id')
                ->first();

            if ($latestPhoto) {
                $reportDate = $latestPhoto->dailyReport->report_date;
                $isTodayOrRef = $reportDate->format('Y-m-d') === '2026-10-14' || $reportDate->isToday();
                $dateLabel = $isTodayOrRef
                    ? 'Hari ini, ' . $latestPhoto->taken_time
                    : $this->formatIndonesianDate($reportDate->format('Y-m-d')) . ', ' . $latestPhoto->taken_time;

                $latestActivity = [
                    'id' => $latestPhoto->id,
                    'daily_report_id' => $latestPhoto->daily_report_id,
                    'caption' => $latestPhoto->caption,
                    'category' => $latestPhoto->category,
                    'timestamp_label' => $dateLabel,
                    'photo_url' => $latestPhoto->photo_url,
                ];
                $latestReportId = $latestPhoto->daily_report_id;
            }

            $invoice = Invoice::where('child_id', $activeChild->id)
                ->orderByDesc('month_year')
                ->first();

            if ($invoice) {
                $currentInvoice = [
                    'id' => $invoice->id,
                    'month_year' => $invoice->month_year,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                ];
            }
        }

        return Inertia::render('Dashboard', [
            'latestActivity' => $latestActivity,
            'latestReportId' => $latestReportId,
            'currentInvoice' => $currentInvoice,
        ]);
    }

    public function selectChild(Request $request, Child $child): RedirectResponse
    {
        Gate::authorize('view', $child);

        $request->session()->put('active_child_id', $child->id);

        return back()->with('success', 'Menampilkan data anak: ' . $child->name);
    }

    private function formatIndonesianDate(string $ymd): string
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];
        $ts = strtotime($ymd);

        return date('d', $ts) . ' ' . ($months[(int) date('n', $ts)] ?? '') . ' ' . date('Y', $ts);
    }
}

