<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackRequestController extends Controller
{
    /**
     * Show the track request form.
     */
    public function trackRequest()
    {
        return view('track_request');
    }

    /**
     * Handle track request form submission.
     */
    public function trackSubmit(Request $request)
    {
        $request->validate([
            'ticket_number' => 'required|string',
        ]);

        // TODO: Look up ticket by ticket_number
        $ticket = null;

        return view('track_request', compact('ticket'));
    }
}
