<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $q = $request->q;

        $users = User::query()
            ->when($q, fn($s) => $s->where(fn($w) =>
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
            ))
            ->when($request->role, fn($s, $r) => $s->where('role', $r))
            ->when($request->status, fn($s, $st) => $s->where('status', $st))
            ->when($request->branch, fn($s, $b) => $s->where('branch', $b))
            ->orderBy($request->get('sort', 'name'))
            ->paginate(15)
            ->withQueryString();

        $branches = User::whereNotNull('branch')
            ->distinct()
            ->pluck('branch')
            ->sort()
            ->values();

        return view('admin.users.index', compact('users', 'q', 'branches'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:admin,user'],
            'password' => ['nullable', 'string', 'min:8'],

            // kolom baru (opsional)
            'status'       => ['nullable', 'in:active,suspended'],
            'branch'       => ['nullable', 'string', 'max:255'],
            'department'   => ['nullable', 'string', 'max:255'],
            'must_reset_password' => ['nullable', 'boolean'],
            'two_factor_enabled'  => ['nullable', 'boolean'],

            'send_reset'   => ['nullable', 'boolean'],
        ]);

        $rawPassword = $data['password'] ?: \Illuminate\Support\Str::random(12);

        $payload = [
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($rawPassword),

            // isi kolom baru dengan default yang masuk akal
            'status'     => $data['status'] ?? 'active',
            'branch'     => $data['branch'] ?? null,
            'department' => $data['department'] ?? null,

            'must_reset_password' => $request->boolean('must_reset_password'),
            'two_factor_enabled'  => $request->boolean('two_factor_enabled'),
        ];

        $user = User::create($payload);

        // kirim email reset password jika diminta
        if ($request->boolean('send_reset')) {
            Password::sendResetLink(['email' => $user->email]);

            return redirect()
                ->route('admin.users.index')
                ->with('status', 'User berhasil dibuat dan link reset password telah dikirim ke email.');
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil dibuat. Password sementara: ' . $rawPassword);
    }

    public function edit(User $user): View
    {
        $this->authorize('manage-users');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'  => ['required', 'in:admin,user'],

            'status'     => ['nullable', 'in:active,suspended'],
            'branch'     => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],

            'password'           => ['nullable', 'string', 'min:8'],
            'must_reset_password'=> ['nullable', 'boolean'],
            'two_factor_enabled' => ['nullable', 'boolean'],

            // kalau mau bisa diedit manual (opsional)
            'last_login_at' => ['nullable', 'date'],
            'last_login_ip' => ['nullable', 'ip'],
        ]);

        $payload = [
            'name'  => $data['name'],
            'email' => $data['email'],
            'role'  => $data['role'],
        ];

        // status
        if (array_key_exists('status', $data)) {
            $payload['status'] = $data['status'];
        }

        // branch & department
        if (array_key_exists('branch', $data)) {
            $payload['branch'] = $data['branch'];
        }

        if (array_key_exists('department', $data)) {
            $payload['department'] = $data['department'];
        }

        // password baru (opsional)
        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        // must_reset_password: pakai boolean dari request (checkbox)
        $payload['must_reset_password'] = $request->boolean('must_reset_password');

        // two_factor_enabled: on/off
        $payload['two_factor_enabled'] = $request->boolean('two_factor_enabled');

        // last_login_* kalau memang mau di-edit manual
        if (array_key_exists('last_login_at', $data)) {
            $payload['last_login_at'] = $data['last_login_at'];
        }
        if (array_key_exists('last_login_ip', $data)) {
            $payload['last_login_ip'] = $data['last_login_ip'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User berhasil diperbarui.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role' => ['required', 'in:admin,user']]);

        if ($request->user()->id === $user->id && $request->role !== 'admin') {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan role Anda sendiri.']);
        }

        $user->update(['role' => $request->role]);

        return back()->with('status', 'Role user berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return back()->withErrors(['delete' => 'Anda tidak dapat menghapus diri sendiri.']);
        }

        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $rows = User::select(
            'name',
            'email',
            'role',
            'status',
            'branch',
            'department',
            'last_login_at',
            'last_login_ip'
        )->get();

        $csv = implode(",", [
            'name',
            'email',
            'role',
            'status',
            'branch',
            'department',
            'last_login_at',
            'last_login_ip',
        ]) . "\n";

        foreach ($rows as $r) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $r->name,
                $r->email,
                $r->role,
                $r->status,
                $r->branch,
                $r->department,
                optional($r->last_login_at)->toDateTimeString(),
                $r->last_login_ip
            );
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=users.csv'
        ]);
    }

    /**
     * 📨 Kirim reset link password ke email user (dari halaman edit)
     */
    public function sendResetLink(User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Link reset password berhasil dikirim ke ' . $user->email);
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
