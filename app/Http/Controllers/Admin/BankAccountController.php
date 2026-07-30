<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BankAccountController extends Controller
{
    /**
     * Display all bank accounts.
     */
    public function index(Request $request)
    {
        $query = BankAccount::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('bank_name', 'LIKE', "%{$search}%")
                    ->orWhere('account_title', 'LIKE', "%{$search}%")
                    ->orWhere('account_number', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $bankAccounts = $query
            ->ordered()
            ->paginate(20)
            ->withQueryString();


        return view(
            'admin.bank_accounts.index',
            compact('bankAccounts')
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view(
            'admin.bank_accounts.create'
        );
    }


    /**
     * Store bank account.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);


        DB::beginTransaction();

        try {

            if ($request->hasFile('qr_code')) {

                $validated['qr_code'] = $request
                    ->file('qr_code')
                    ->store('bank_accounts', 'public');
            }
            if ($request->hasFile('picture')) {

                $validated['picture'] = $request
                    ->file('picture')
                    ->store('bank_accounts', 'public');
            }


            $validated['is_active'] = $request
                ->boolean('is_active');


            BankAccount::create($validated);


            DB::commit();


            return redirect()
                ->route('admin.bank-accounts.index')
                ->with(
                    'success',
                    'Bank account created successfully.'
                );


        } catch (Throwable $e) {

            DB::rollBack();

            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create bank account.'
                );
        }
    }


    /**
     * Show edit form.
     */
    public function edit(BankAccount $bankAccount)
    {
        return view(
            'admin.bank_accounts.edit',
            compact('bankAccount')
        );
    }


    /**
     * Update bank account.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $this->validateRequest($request);


        DB::beginTransaction();

        try {

            if ($request->hasFile('qr_code')) {


                if ($bankAccount->qr_code) {

                    Storage::disk('public')
                        ->delete($bankAccount->qr_code);
                }


                $validated['qr_code'] = $request
                    ->file('qr_code')
                    ->store('bank_accounts', 'public');
            }


            $validated['is_active'] = $request
                ->boolean('is_active');


            $bankAccount->update($validated);


            DB::commit();


            return redirect()
                ->route('admin.bank-accounts.index')
                ->with(
                    'success',
                    'Bank account updated successfully.'
                );


        } catch (Throwable $e) {

            DB::rollBack();

            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update bank account.'
                );
        }
    }


    /**
     * Delete bank account.
     */
    public function destroy(BankAccount $bankAccount)
    {
        try {


            if (!$bankAccount->canDelete()) {

                return back()
                    ->with(
                        'error',
                        'This bank account cannot be deleted because deposits already exist.'
                    );
            }


            if ($bankAccount->qr_code) {

                Storage::disk('public')
                    ->delete($bankAccount->qr_code);
            }


            $bankAccount->delete();


            return back()
                ->with(
                    'success',
                    'Bank account deleted successfully.'
                );


        } catch (Throwable $e) {

            report($e);


            return back()
                ->with(
                    'error',
                    'Unable to delete bank account.'
                );
        }
    }



    /**
     * Validation rules.
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([

            'title' => [
                'required',
                'string',
                'max:255'
            ],

            'bank_name' => [
                'required',
                'string',
                'max:255'
            ],

            'account_title' => [
                'required',
                'string',
                'max:255'
            ],

            'account_number' => [
                'required',
                'string',
                'max:255'
            ],

            'iban' => [
                'nullable',
                'string',
                'max:255'
            ],

            'swift_code' => [
                'nullable',
                'string',
                'max:100'
            ],

            'branch_name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'branch_code' => [
                'nullable',
                'string',
                'max:100'
            ],

            'currency' => [
                'required',
                'string',
                'max:10'
            ],

            'type' => [
                'required',
                'in:bank,jazzcash,easypaisa,other'
            ],
            'conversion_rate' => [
                'required',
            ],

            'picture' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096'
            ],
            'qr_code' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096'
            ],

            'instructions' => [
                'nullable',
                'string'
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0'
            ],

            'is_active' => [
                'nullable'
            ],
        ]);
    }
}