<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kyc;

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
}