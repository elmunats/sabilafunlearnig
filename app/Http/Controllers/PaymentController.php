<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Child;
use App\Models\Invoice;
use App\Services\PrivateMediaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
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

        $invoices = $activeChild
            ? Invoice::where('child_id', $activeChild->id)
                ->orderByDesc('month_year')
                ->get()
                ->map(fn (Invoice $inv) => $this->transformInvoice($inv))
                ->values()
            : collect();

        $currentInvoice = $invoices->first();

        return Inertia::render('Payments/Index', [
            'currentInvoice' => $currentInvoice,
            'invoices' => $invoices->all(),
        ]);
    }

    public function show(Request $request, Invoice $invoice): Response
    {
        $invoice->load('child');
        Gate::authorize('view', $invoice);

        $request->session()->put('active_child_id', $invoice->child_id);

        return Inertia::render('Payments/Show', [
            'invoice' => $this->transformInvoice($invoice),
        ]);
    }

    public function uploadProof(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->load('child');
        Gate::authorize('view', $invoice);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'max:60'],
            'proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,svg', 'max:5120'],
        ]);

        $proofPath = $invoice->payment_proof_path ?: 'seed/payment_proof_okt2026.svg';
        if ($request->hasFile('proof')) {
            $proofPath = PrivateMediaService::store($request->file('proof'), 'payment_proofs');
        }

        $invoice->update([
            'payment_method' => $validated['payment_method'],
            'payment_proof_path' => $proofPath,
            'status' => $request->user()->isAdmin() ? 'paid' : 'pending',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim dan sedang diverifikasi.');
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->load('child');
        Gate::authorize('update', $invoice);

        $validated = $request->validate([
            'status' => ['required', 'in:paid,unpaid,pending'],
            'payment_method' => ['nullable', 'string', 'max:60'],
        ]);

        $invoice->update([
            'status' => $validated['status'],
            'payment_method' => $validated['payment_method'] ?: $invoice->payment_method,
            'paid_at' => $validated['status'] === 'paid' ? ($invoice->paid_at ?? now()) : null,
            'payment_proof_path' => $validated['status'] === 'paid'
                ? ($invoice->payment_proof_path ?: 'seed/payment_proof_okt2026.svg')
                : $invoice->payment_proof_path,
        ]);

        if ($validated['status'] === 'paid') {
            AppNotification::create([
                'user_id' => $invoice->child->parent_id,
                'child_id' => $invoice->child_id,
                'type' => 'pembayaran',
                'title' => 'Pembayaran bulan ' . $this->formatMonthYear($invoice->month_year) . ' sudah dikonfirmasi',
                'message' => 'Pembayaran SPP untuk ' . $invoice->child->name . ' sebesar Rp ' . number_format($invoice->amount, 0, ',', '.') . ' telah lunas.',
                'action_url' => route('payments.show', $invoice->id, false),
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Status tagihan berhasil diperbarui menjadi ' . strtoupper($validated['status']) . '.');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Invoice::class);

        $validated = $request->validate([
            'child_id' => ['required', 'exists:children,id'],
            'month_year' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'tuition_amount' => ['required', 'integer', 'min:0'],
            'extra_amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:unpaid,paid,pending'],
        ]);

        $child = Child::findOrFail($validated['child_id']);
        $total = (int) $validated['tuition_amount'] + (int) $validated['extra_amount'];

        $invoice = Invoice::updateOrCreate(
            [
                'child_id' => $child->id,
                'month_year' => $validated['month_year'],
            ],
            [
                'amount' => $total,
                'breakdown' => [
                    ['label' => 'Bimbel Calistung', 'amount' => (int) $validated['tuition_amount']],
                    ['label' => 'Kegiatan tambahan', 'amount' => (int) $validated['extra_amount']],
                ],
                'status' => $validated['status'],
                'payment_method' => 'Transfer Bank',
                'paid_at' => $validated['status'] === 'paid' ? now() : null,
                'payment_proof_path' => $validated['status'] === 'paid' ? 'seed/payment_proof_okt2026.svg' : null,
            ]
        );

        return redirect()->route('payments.show', $invoice->id)->with('success', 'Tagihan bulanan berhasil disimpan.');
    }

    private function transformInvoice(Invoice $invoice): array
    {
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

        $paidShort = null;
        $paidFull = null;
        if ($invoice->paid_at) {
            $dt = $invoice->paid_at;
            $paidShort = 'Dibayar ' . $dt->format('d') . ' ' . ($monthsShort[(int) $dt->format('n')] ?? '') . ' ' . $dt->format('Y');
            $paidFull = 'Dibayar pada ' . $dt->format('d') . ' ' . ($monthsFull[(int) $dt->format('n')] ?? '') . ' ' . $dt->format('Y') . ' melalui ' . ($invoice->payment_method ?: 'Transfer Bank');
        }

        return [
            'id' => $invoice->id,
            'child_id' => $invoice->child_id,
            'month_year' => $invoice->month_year,
            'month_label' => $this->formatMonthYear($invoice->month_year),
            'amount' => $invoice->amount,
            'formatted_amount' => 'Rp ' . number_format($invoice->amount, 0, ',', '.'),
            'breakdown' => collect($invoice->breakdown ?? [])->map(fn ($item) => [
                'label' => $item['label'] ?? 'Komponen Biaya',
                'amount' => (int) ($item['amount'] ?? 0),
                'formatted_amount' => 'Rp ' . number_format((int) ($item['amount'] ?? 0), 0, ',', '.'),
            ])->values()->all(),
            'status' => $invoice->status,
            'status_badge' => match ($invoice->status) {
                'paid' => 'Sudah dibayar',
                'pending' => 'Menunggu verifikasi',
                default => 'Belum dibayar',
            },
            'history_badge' => match ($invoice->status) {
                'paid' => 'Lunas',
                'pending' => 'Diproses',
                default => 'Belum Lunas',
            },
            'payment_method' => $invoice->payment_method ?: 'Transfer Bank',
            'paid_short_label' => $paidShort ?: 'Menunggu pembayaran',
            'paid_full_label' => $paidFull ?: 'Tagihan belum dilunasi',
            'payment_proof_url' => $invoice->payment_proof_url,
        ];
    }

    private function formatMonthYear(string $ym): string
    {
        $monthsFull = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        try {
            $dt = Carbon::createFromFormat('Y-m-d', $ym . '-01');
            return ($monthsFull[(int) $dt->format('n')] ?? 'Oktober') . ' ' . $dt->format('Y');
        } catch (\Throwable) {
            return $ym;
        }
    }
}

