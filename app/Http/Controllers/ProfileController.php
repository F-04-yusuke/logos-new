<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
        // ▼▼▼ ここから上書き ▼▼▼
        $user = $request->user();
        $validated = $request->validated();

        // 🌟 追加：名前が変更されようとしているかチェック
        if ($user->name !== $validated['name']) {
            // 前回変更時から7日経っていない場合はエラーを返す
            if ($user->name_updated_at && \Carbon\Carbon::parse($user->name_updated_at)->addDays(7)->isFuture()) {
                return back()->withErrors(['name' => 'アカウント名は前回の変更から7日間は変更できません。']);
            }
            // 変更OKなら、変更日時を「今」に更新する
            $user->name_updated_at = now();
        }

        if ($request->hasFile('avatar')) {
            // 古い画像があれば削除（容量節約）
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            // 新しい画像を storage/app/public/avatars フォルダに保存
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }
        // ▲▲▲ ここまで追加 ▲▲▲

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
        // ▲▲▲ ここまで上書き ▲▲▲
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

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
