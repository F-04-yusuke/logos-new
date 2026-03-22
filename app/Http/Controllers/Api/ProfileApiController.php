<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    // プロフィール取得（name_updated_at付き）
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'avatar'          => $user->avatar ?? null,
            'is_pro'          => $user->is_pro,
            'is_admin'        => $user->is_admin,
            'name_updated_at' => $user->name_updated_at,
        ]);
    }

    // プロフィール更新（multipart/form-data）
    public function update(Request $request)
    {
        $user = $request->user();

        $canChangeName = !$user->name_updated_at ||
            \Carbon\Carbon::parse($user->name_updated_at)->addDays(7)->isPast();

        $rules = [
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ];
        if ($canChangeName) {
            $rules['name'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->email = $data['email'];

        if ($canChangeName && isset($data['name']) && $data['name'] !== $user->name) {
            $user->name            = $data['name'];
            $user->name_updated_at = now();
        }

        $user->save();

        return response()->json([
            'message' => '保存しました',
            'user'    => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'avatar'          => $user->avatar ?? null,
                'name_updated_at' => $user->name_updated_at,
            ],
        ]);
    }

    // パスワード更新
    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['errors' => ['current_password' => ['現在のパスワードが正しくありません']]], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['message' => 'パスワードを更新しました']);
    }

    // アカウント削除
    public function destroy(Request $request)
    {
        $data = $request->validate(['password' => 'required|string']);

        $user = $request->user();

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['errors' => ['password' => ['パスワードが正しくありません']]], 422);
        }

        $request->user()->currentAccessToken()->delete();
        $user->delete();

        return response()->json(['message' => 'アカウントを削除しました']);
    }
}
