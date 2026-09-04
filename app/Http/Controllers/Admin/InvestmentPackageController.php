<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InvestmentPackageController extends Controller
{
    public function index(Request $request)
    {
        $query = InvestmentPackage::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $packages = $query
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view('admin.investment_packages.index', [
            'packages' => $packages,
            'active' => 'investment-packages',
        ]);
    }

    public function create()
    {
        return view('admin.investment_packages.create', [
            'active' => 'investment-packages',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        DB::beginTransaction();
        try {
            InvestmentPackage::create($validated);
            DB::commit();

            return redirect()
                ->route('admin.investment-packages.index')
                ->with('success', 'Investment package created successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to create investment package.');
        }
    }

    public function edit(InvestmentPackage $investmentPackage)
    {
        return view('admin.investment_packages.edit', [
            'package' => $investmentPackage,
            'active' => 'investment-packages',
        ]);
    }

    public function update(Request $request, InvestmentPackage $investmentPackage)
    {
        $validated = $this->validateRequest($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        DB::beginTransaction();
        try {
            $investmentPackage->update($validated);
            DB::commit();

            return redirect()
                ->route('admin.investment-packages.index')
                ->with('success', 'Investment package updated successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to update investment package.');
        }
    }

    public function destroy(InvestmentPackage $investmentPackage)
    {
        try {
            if ($investmentPackage->userInvestments()->exists()) {
                $investmentPackage->is_active = false;
                $investmentPackage->save();
                $investmentPackage->delete();

                return back()->with(
                    'success',
                    'Package deactivated and archived. Existing purchases are unaffected.'
                );
            }

            $investmentPackage->forceDelete();

            return back()->with('success', 'Investment package deleted successfully.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to delete investment package.');
        }
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'daily_roi' => ['required', 'numeric', 'min:0'],
            'total_days' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
