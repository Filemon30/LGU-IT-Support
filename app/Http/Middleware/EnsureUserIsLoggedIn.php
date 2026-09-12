<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLoggedIn
{
    public function handle(
        Request $request,
        Closure $next,
        string $mode = 'auth'
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Check Authentication
        |--------------------------------------------------------------------------
        */

        $isLoggedIn =
            $request->session()->get('logged_in') === true;

        /*
        |--------------------------------------------------------------------------
        | Protected Routes
        |--------------------------------------------------------------------------
        */

        if (
            $mode === 'auth' &&
            ! $isLoggedIn
        ) {

            return response()->view(
                'error.401',
                [],
                401
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Guest Routes
        |--------------------------------------------------------------------------
        */

        if ($mode === 'guest') {

            /*
             * Already logged in
             */
            if ($isLoggedIn) {

                $dashboardRoute =
                    $request->session()->get('role') === 'admin'
                        ? 'admin.dashboard'
                        : 'dashboard';

                return redirect()
                    ->route($dashboardRoute);
            }

            /*
             * Prevent directly typing /login
             */
            if ($request->isMethod('GET')) {

                $referer =
                    (string) $request->header('referer');

                if (
                    empty($referer) ||
                    ! str_contains(
                        $referer,
                        $request->getHost()
                    )
                ) {

                    return redirect()
                        ->route('home');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Continue
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}
