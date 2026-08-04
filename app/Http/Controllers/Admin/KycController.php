<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index()
    {
        $kycs = Kyc::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.kyc.index', compact('kycs'));
    }
    public function show(Kyc $kyc)
    {
        $kyc->load('user');

        return view('admin.kyc.show', compact('kyc'));
    }
    public function approve(Kyc $kyc)
    {
        $kyc->update([
            'status' => 'approved',
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return redirect()
            ->route('admin.kyc.show', $kyc)
            ->with('success', 'KYC approved successfully.');
    }

    public function reject(Request $request, Kyc $kyc)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $kyc->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_at' => null,
        ]);

        return redirect()
            ->route('admin.kyc.show', $kyc)
            ->with('success', 'KYC rejected successfully.');
    }
}