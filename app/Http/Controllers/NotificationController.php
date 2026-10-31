<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $selectedFilter = $request->query('filter', 'Semua');
        $filters = ['Semua', 'Laporan', 'Pembayaran', 'Info'];
        if (! in_array($selectedFilter, $filters, true)) {
            $selectedFilter = 'Semua';
        }

        $typeMap = [
            'Laporan' => 'laporan',
            'Pembayaran' => 'pembayaran',
            'Info' => 'info',
        ];

        $notifications = AppNotification::query()
            ->with('child:id,name,group_name')
            ->where('user_id', $user->id)
            ->when($selectedFilter !== 'Semua', fn ($q) => $q->where('type', $typeMap[$selectedFilter]))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (AppNotification $n) => $this->transformNotification($n))
            ->values()
            ->all();

        $recipientUsers = $user->isAdmin()
            ? User::select('id', 'name', 'email', 'role')
                ->orderBy('role')
                ->orderBy('name')
                ->get()
                ->map(fn (User $u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'role' => $u->role,
                    'role_label' => match ($u->role) {
                        'admin' => 'Admin',
                        'teacher' => 'Guru',
                        default => 'Orang Tua',
                    },
                ])
                ->values()
                ->all()
            : [];

        return Inertia::render('Notifications/Index', [
            'filters' => $filters,
            'selectedFilter' => $selectedFilter,
            'notifications' => $notifications,
            'recipientUsers' => $recipientUsers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat mengirim notifikasi.');
        }

        $validated = $request->validate([
            'target_audience' => ['required', 'in:all,parents,teachers,specific'],
            'target_user_id' => ['nullable', 'required_if:target_audience,specific', 'exists:users,id'],
            'type' => ['required', 'in:info,laporan,pembayaran'],
            'title' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ], [
            'title.required' => 'Judul notifikasi wajib diisi.',
            'message.required' => 'Deskripsi notifikasi wajib diisi.',
        ]);

        $recipients = match ($validated['target_audience']) {
            'parents' => User::where('role', 'parent')->pluck('id')->toArray(),
            'teachers' => User::where('role', 'teacher')->pluck('id')->toArray(),
            'specific' => [(int) $validated['target_user_id']],
            default => User::pluck('id')->toArray(),
        };

        // Ensure Admin also gets a copy in their notification center so they can inspect it
        if (! in_array($request->user()->id, $recipients, true)) {
            $recipients[] = $request->user()->id;
        }

        foreach (array_unique($recipients) as $recipientId) {
            AppNotification::create([
                'user_id' => $recipientId,
                'child_id' => null,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'action_url' => null,
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Notifikasi "' . $validated['title'] . '" berhasil dikirim.');
    }

    public function markRead(Request $request, AppNotification $notification): RedirectResponse
    {
        if ((int) $notification->user_id !== (int) $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if ($request->boolean('navigate') && $notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    private function transformNotification(AppNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'type_label' => match ($n->type) {
                'laporan' => 'Laporan Belajar',
                'pembayaran' => 'Pembayaran & SPP',
                default => 'Informasi & Pengumuman',
            },
            'title' => $n->title,
            'message' => $n->message,
            'child_name' => $n->child?->name,
            'child_group' => $n->child?->group_name,
            'action_url' => $n->action_url,
            'is_read' => $n->is_read,
            'timestamp_label' => $this->formatTimestamp($n->created_at),
        ];
    }

    private function formatTimestamp($dt): string
    {
        if (! $dt) {
            return '';
        }

        $monthsShort = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return $dt->format('d') . ' '
            . ($monthsShort[(int) $dt->format('n')] ?? '') . ' '
            . $dt->format('Y') . ', '
            . $dt->format('H.i');
    }
}
