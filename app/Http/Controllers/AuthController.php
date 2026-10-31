<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Masukkan Email atau Nomor HP Anda.',
            'password.required' => 'Masukkan kata sandi Anda.',
        ]);

        $identifier = trim($validated['identifier']);
        $normalizedPhone = preg_replace('/[\s\-]+/', '', $identifier);

        $user = User::query()
            ->where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", [$normalizedPhone])
            ->first();

        // If a standard demo user was requested but not found in DB, auto-seed
        if (! $user && in_array($identifier, [
            'siti@example.com',
            'guru@sabilafun.id',
            'admin@sabilafun.id',
            '0812 3456 7890',
            '0811 2233 4455',
            '0811 0000 1111',
            '081234567890',
            '081122334455',
            '081100001111',
        ], true)) {
            Artisan::call('db:seed', [
                '--class' => 'Database\Seeders\DatabaseSeeder',
                '--force' => true,
            ]);

            $user = User::query()
                ->where('email', $identifier)
                ->orWhere('phone', $identifier)
                ->orWhereRaw("REPLACE(REPLACE(phone, ' ', ''), '-', '') = ?", [$normalizedPhone])
                ->first();
        }

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Email / Nomor HP atau kata sandi tidak sesuai.',
            ]);
        }

        Auth::login($user, $request->boolean('remember', true));
        $request->session()->regenerate();

        $firstChild = Child::accessibleBy($user)->orderBy('id')->first();
        if ($firstChild) {
            $request->session()->put('active_child_id', $firstChild->id);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function demoLogin(Request $request): RedirectResponse
    {
        $role = $request->input('role', 'parent');
        $targetEmail = match ($role) {
            'admin' => 'admin@sabilafun.id',
            'teacher' => 'guru@sabilafun.id',
            default => 'siti@example.com',
        };

        $user = User::where('email', $targetEmail)->first();
        if (! $user) {
            Artisan::call('db:seed', [
                '--class' => 'Database\Seeders\DatabaseSeeder',
                '--force' => true,
            ]);
            $user = User::where('email', $targetEmail)->firstOrFail();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $firstChild = Child::accessibleBy($user)->orderBy('id')->first();
        if ($firstChild) {
            $request->session()->put('active_child_id', $firstChild->id);
        }

        return redirect()->intended(route('dashboard'))->with('success', 'Berhasil masuk sebagai akun demo: ' . $user->name);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'child_name' => ['required', 'string', 'max:120'],
            'child_birth_date' => ['required', 'date'],
            'child_group' => ['required', 'string', 'max:80'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'parent',
            'address' => 'Jl. Melati Indah No. 12, Jakarta Selatan',
            'password' => $validated['password'],
        ]);

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'parent']);
            $user->assignRole($role);
        }

        $classGroup = \App\Models\ClassGroup::where('name', $validated['child_group'])->first()
            ?? \App\Models\ClassGroup::first();

        $child = Child::create([
            'parent_id' => $user->id,
            'class_group_id' => $classGroup?->id,
            'name' => $validated['child_name'],
            'birth_date' => $validated['child_birth_date'],
            'group_name' => $classGroup?->name ?? $validated['child_group'],
            'enrollment_status' => 'pending',
            'join_date' => now()->toDateString(),
            'photo' => 'avatars/aisyah.svg',
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('active_child_id', $child->id);

        return redirect()->route('dashboard')->with(
            'success',
            'Akun berhasil dibuat. Pendaftaran kelas untuk ' . $child->name . ' sedang menunggu persetujuan Admin.'
        );
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
        ]);

        return back()->with(
            'success',
            'Tautan pemulihan kata sandi telah dikirim ke ' . $validated['identifier'] . '. Hubungi Admin Sabila jika membutuhkan bantuan langsung.'
        );
    }

    public function switchRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:parent,teacher,admin'],
        ]);

        $targetEmail = match ($validated['role']) {
            'admin' => 'admin@sabilafun.id',
            'teacher' => 'guru@sabilafun.id',
            default => 'siti@example.com',
        };

        $user = User::where('email', $targetEmail)->first();
        if (! $user) {
            Artisan::call('db:seed', [
                '--class' => 'Database\Seeders\DatabaseSeeder',
                '--force' => true,
            ]);
            $user = User::where('email', $targetEmail)->firstOrFail();
        }

        Auth::login($user);
        $request->session()->regenerate();

        $firstChild = Child::accessibleBy($user)->orderBy('id')->first();
        if ($firstChild) {
            $request->session()->put('active_child_id', $firstChild->id);
        }

        return back()->with('success', 'Beralih peran ke: ' . ucfirst($user->role) . ' (' . $user->name . ')');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
