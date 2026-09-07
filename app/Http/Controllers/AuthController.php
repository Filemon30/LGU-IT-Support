<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([

            'email' => [
                'required',
                'email',
                'regex:/^[^\s@]+@biringancity\.gov\.ph$/i',
            ],

            'password' => [
                'required',
            ],

        ], [

            'email.required' =>
                'Must not be Empty',

            'email.regex' =>
                'Invalid email',

            'password.required' =>
                'Must not be Empty',

        ]);


        $email =
            strtolower(
                $request->input('email')
            );


        /*
        |--------------------------------------------------------------------------
        | Determine Role
        |--------------------------------------------------------------------------
        */

        $role = match (true) {

            str_contains(
                $email,
                'staff'
            )
                => 'staff',

            default
                => 'admin',

        };


        /*
        |--------------------------------------------------------------------------
        | Store Session
        |--------------------------------------------------------------------------
        */

        session([
            'logged_in' => true,

            'role' => $role,

            'user_name' =>
                ucfirst(
                    strtok(
                        $request->input('email'),
                        '@'
                    )
                ),
        ]);


        $dashboardRoute =
            $role === 'admin'
                ? 'admin.dashboard'
                : 'dashboard';

        return redirect()
            ->route($dashboardRoute);

        // return redirect()
        //     ->route('login');
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        $request = request();

        $role = $request->session()->get('role');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with(
                'from_logout',
                true
            );
    }
}
