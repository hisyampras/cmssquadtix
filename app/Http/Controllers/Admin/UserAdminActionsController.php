<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserAdminActionsController extends Controller
{
    use AuthorizesRequests;

    // 1️⃣ Kirim link reset password via email
    public function sendResetLink(User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        Password::sendResetLink(['email' => $user->email]);
        return back()->with('status', 'Link reset password dikirim ke: '.$user->email);
    }

    // 2️⃣ Paksa user reset password saat login berikutnya
    public function forceResetOnNextLogin(User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $user->update(['must_reset_password' => true]);
        return back()->with('status', 'User akan dipaksa reset password pada login berikutnya.');
    }

    // 3️⃣ Suspend / Activate user
    public function suspend(User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        if (Auth::id() === $user->id) {
            return back()->withErrors('Tidak bisa suspend diri sendiri.');
        }

        $user->update(['status' => 'suspended']);
        return back()->with('status', 'User telah disuspend.');
    }

    public function activate(User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $user->update(['status' => 'active']);
        return back()->with('status', 'User diaktifkan kembali.');
    }

    // 4️⃣ Impersonate / Stop Impersonate
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        if (Auth::id() === $user->id) {
            return back()->withErrors('Tidak dapat impersonate diri sendiri.');
        }

        // Simpan ID admin asli
        $request->session()->put('impersonator_id', Auth::id());

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('status', 'Anda sekarang masuk sebagai '.$user->name);
    }

    public function stopImpersonate(Request $request): RedirectResponse
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (!$impersonatorId) {
            abort(403, 'Tidak sedang dalam mode impersonate.');
        }

        Auth::loginUsingId($impersonatorId);

        return redirect()->route('admin.users.index')
            ->with('status', 'Kembali ke akun admin.');
    }

    
}
