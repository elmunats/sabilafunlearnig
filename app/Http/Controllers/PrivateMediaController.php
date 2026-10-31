<?php

namespace App\Http\Controllers;

use App\Models\ActivityPhoto;
use App\Models\Child;
use App\Models\Invoice;
use App\Services\PrivateMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrivateMediaController extends Controller
{
    public function show(Request $request, string $path): Response
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Signed URL telah kedaluwarsa atau tidak valid.');
        }

        $decodedPath = base64_decode($path, true);
        if (! $decodedPath || str_contains($decodedPath, '..')) {
            abort(404);
        }

        $user = $request->user();
        if (! $user) {
            abort(403);
        }

        // Row-level authorization check when parent accesses child photo, activity photo, or invoice proof
        if ($user->isParent()) {
            $ownsChildAvatar = Child::where('parent_id', $user->id)
                ->where('photo', $decodedPath)
                ->exists();

            $ownsActivityPhoto = ActivityPhoto::where('photo_path', $decodedPath)
                ->whereHas('dailyReport.child', fn ($q) => $q->where('parent_id', $user->id))
                ->exists();

            $ownsInvoiceProof = Invoice::where('payment_proof_path', $decodedPath)
                ->whereHas('child', fn ($q) => $q->where('parent_id', $user->id))
                ->exists();

            $isSharedClassroomAsset = str_starts_with($decodedPath, 'seed/');

            if (! $ownsChildAvatar && ! $ownsActivityPhoto && ! $ownsInvoiceProof && ! $isSharedClassroomAsset) {
                abort(403, 'Anda tidak memiliki akses ke berkas media ini.');
            }
        }

        $disk = PrivateMediaService::diskName();
        if (! Storage::disk($disk)->exists($decodedPath)) {
            abort(404, 'Berkas media tidak ditemukan.');
        }

        $mimeType = Storage::disk($disk)->mimeType($decodedPath) ?: 'image/svg+xml';
        if (str_ends_with($decodedPath, '.svg')) {
            $mimeType = 'image/svg+xml';
        }

        return response(Storage::disk($disk)->get($decodedPath), 200, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=1800',
        ]);
    }
}

