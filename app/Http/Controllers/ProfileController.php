<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        // Use a transaction to ensure atomic update
        DB::beginTransaction();
        try {
            // 1. Update Password jika diisi (do explicit assignment)
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Hapus password & foto dari array validated agar tidak bentrok dengan fill()
            unset($validated['password'], $validated['foto']);

            // 2. Assign explicit fields to avoid accidental mass-assignment issues
            if (array_key_exists('name', $validated)) {
                $user->name = $validated['name'];
            }
            if (array_key_exists('email', $validated)) {
                if ($user->email !== $validated['email']) {
                    $user->email = $validated['email'];
                    $user->email_verified_at = null;
                }
            }
            if (array_key_exists('level', $validated)) {
                $user->level = $validated['level'];
            }

            // 3. Upload Foto Profil jika ada
            if ($request->hasFile('foto')) {
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    Storage::disk('public')->delete($user->foto);
                }
                $user->foto = $request->file('foto')->store('avatars', 'public');
            }

            $saved = $user->save();

            if (! $saved) {
                Log::error('Profile update failed to save for user id ' . $user->id, ['validated' => $validated]);
                DB::rollBack();
                return Redirect::route('profile.edit')->with('status', 'profile-update-failed')->withErrors(['save_error' => 'Gagal menyimpan profil. Coba lagi nanti.']);
            }

            // Regenerate session to ensure auth state is fresh (useful after email/password changes)
            // This ensures the flash message is added to the new session
            $request->session()->regenerate();
            
            DB::commit();

            Log::info('Profile updated successfully for user id ' . $user->id, ['user_id' => $user->id, 'email' => $user->email]);

            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Exception while updating profile for user id ' . ($user->id ?? 'unknown') . ': ' . $e->getMessage());
            return Redirect::route('profile.edit')->with('status', 'profile-update-error');
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}