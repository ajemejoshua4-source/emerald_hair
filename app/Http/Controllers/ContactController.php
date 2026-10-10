<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function contact()
    {
        return view('contact');
    }

    public function enquiry(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        Enquiry::create($data);

        return back()->with('success', 'Thank you. Emerald Hair will get back to you shortly.');
    }

    public function newsletter(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:150'],
        ]);

        NewsletterSubscriber::firstOrCreate(['email' => $request->string('email')->lower()->toString()]);

        return back()->with('success', 'You are on the Emerald Hair list.');
    }
}
