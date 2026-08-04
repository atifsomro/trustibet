<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class KycController extends Controller
{
    /**
     * Show KYC page.
     */
    public function index(): View
    {
        $kyc = Kyc::where('user_id', Auth::id())->first();
        return view('user.kyc.kyc', compact('kyc'));
    }

    /**
     * Store KYC request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'country' => 'required|string|max:100',
            'id_type' => 'required|string|max:50',
            'id_number' => 'required|string|max:100',

            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',

            'front_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'back_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'selfie_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',

            'confirm_documents' => 'accepted',
            'agree_policy' => 'accepted',
        ]);
        // Prevent duplicate pending KYC
        $existingKyc = Kyc::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existingKyc) {
            return back()->withErrors([
                'error' => 'Your KYC request is already pending.'
            ]);
        }

        // Upload Images
        $frontImage = $request->file('front_image')->store('kyc', 'public');
        $backImage = $request->file('back_image')->store('kyc', 'public');
        $selfieImage = $request->file('selfie_image')->store('kyc', 'public');

        // Save
        Kyc::create([
            'user_id' => auth()->id(),
            'full_name' => $request->full_name,
            'date_of_birth' => $request->date_of_birth,
            'country' => $request->country,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'address' => $request->address,
            'city' => $request->city,
            'front_image' => $frontImage,
            'back_image' => $backImage,
            'selfie_image' => $selfieImage,
        ]);

        return redirect()
            ->route('user-account')
            ->with('success', 'KYC submitted successfully.');
    }

}