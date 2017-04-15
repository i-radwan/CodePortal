<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authorize;
use App\Utilities\Constants;
use Auth;

class ContestAccessAuthorization extends Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $ability
     * @param  array|null $models
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $ability, ...$models)
    {
        // If guest check if public or not
        if (Auth::guest()) {
            $contest = $this->getGateArguments($request, $models)[0];
            if ($contest->visibility == Constants::CONTEST_VISIBILITY[Constants::CONTEST_VISIBILITY_PUBLIC_KEY])
                return $next($request);
            else return redirect('errors/401');
        }

        // Else if member check view-join-contest gate
        $this->auth->authenticate();

        $this->gate->authorize($ability, $this->getGateArguments($request, $models));

        return $next($request);
    }
}