<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class SubmitRequestController extends Controller
{
    /**
     * Show the submit request form.
     */
    public function showSubmitRequestForm()
    {
        return view('submit_request');
    }

    /**
     * Handle submit request form submission.
     */
    public function submitStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'department' => 'required|string|max:255',
            'category' => 'required|string|in:Hardware,Software,Network,Other',
            'priority' => 'required|string|in:Low,Medium,High,Urgent',
            'description' => 'required|string',
        ]);

        // TODO: Create ticket in database

        return redirect()->route('submit.request')
            ->with('success', 'Your request has been submitted successfully!');
    }
}