<?php

namespace App\Http\Controllers;

use App\Mail\ContactUsMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    } 
    /**
     * Send contact us request.
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'subject' => [
                'required',
                'string',
                'max:150',
            ],

            'department' => [
                'required',
                'in:General Inquiry,Account Support,Payments,Technical Issue',
            ],

            'message' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],
        ]);
        $supportEmail = config('settings.support_email', 'somrosoft786@gmail.com');
        Mail::to($supportEmail)->send(new ContactUsMail($validated));

        return redirect()
            ->back()
            ->with('success', 'Your message has been sent successfully. We will get back to you shortly.');
    }
}