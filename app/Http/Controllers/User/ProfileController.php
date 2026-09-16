<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,webp|max:2048', // 2MB
        ]);
        $user = User::query()->find(auth('web')->user()->id);
        // Delete old avatar if it's a stored file (not the default)
        if ($user->avatar && Storage::disk('public')->exists(str_replace('/storage/', '', $user->avatar))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $url = Storage::url($path); // e.g. /storage/avatars/abc.jpg
        $user->avatar = $url;
        $user->save();
        return response()->json([
            'message' => 'Avatar updated successfully',
            'avatar_url' => $url,
        ]);
    }
}