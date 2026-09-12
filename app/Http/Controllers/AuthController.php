<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

            'email.required' => 'Must not be Empty',

            'email.regex' => 'Invalid email',

            'password.required' => 'Must not be Empty',

        ]);

        $email = strtolower($request->input('email'));

        $account = UserAccount::where('email', $email)->first();

        if (! $account || ! Hash::check($request->input('password'), $account->password_hash)) {

            return back()
                ->withErrors([
                    'email' => 'Invalid credentials.',
                ])
                ->onlyInput('email');
        }

        $user = User::where('user_acc_id', $account->user_acc_id)
            ->where('status', 'Active')
            ->first();

        if (! $user) {

            return back()
                ->withErrors([
                    'email' => 'Account is not active.',
                ])
                ->onlyInput('email');
        }

        $user->load('role', 'information');

        $account->update(['last_login_at' => now()]);

        $role = strtolower($user->role->role_name);

        session([

            'logged_in' => true,

            'user_id' => $user->user_id,

            'role' => $role,

            'user_name' => $user->information->first_name
                .' '
                .$user->information->last_name,

            'staff_ref_num' => $user->staff_ref_num,

        ]);

        $dashboardRoute =
            $role === 'admin'
                ? 'admin.dashboard'
                : 'dashboard';

        return redirect()
            ->route($dashboardRoute);
    }

    /**
     * Log the user out.
     */
    public function logout()
    {
        $request = request();

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
