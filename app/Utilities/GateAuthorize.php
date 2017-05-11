<?php

namespace App\Utilities;

use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Factory as Auth;

class GateAuthorize
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The gate instance.
     *
     * @var \Illuminate\Contracts\Auth\Access\Gate
     */
    protected $gate;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory $auth
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function __construct(Auth $auth, Gate $gate)
    {
        $this->auth = $auth;
        $this->gate = $gate;
    }

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
        $this->auth->authenticate();

        $gateArgs = $this->getGateArguments($request, $models);

        // Authorize user against the given gate
        // Check if one of the gate parameters is User model
        // this means that we need to authorize this particular user
        // not the currently authenticated user (as laravel inject by default)

        $authorized = false;

        foreach ($gateArgs as $gateArg) {
            if ($gateArg instanceof User) {
                $this->gate->forUser($gateArgs[1])->authorize($ability, $gateArgs);
                $authorized = true;
                break;
            }
        }

        // If no gateArg is User model, authorize the currently signed in user
        if (!$authorized) {
            $this->gate->authorize($ability, $gateArgs);
        }

        return $next($request);
    }

    /**
     * Get the arguments parameter for the gate.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array|null $models
     * @return array|string|\Illuminate\Database\Eloquent\Model
     */
    protected function getGateArguments($request, $models)
    {
        if (is_null($models)) {
            return [];
        }

        return collect($models)->map(function ($model) use ($request) {
            return $model instanceof Model ? $model : $this->getModel($request, $model);
        })->all();
    }

    /**
     * Get the model to authorize.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  string $model
     * @return string|\Illuminate\Database\Eloquent\Model
     */
    protected function getModel($request, $model)
    {
        return $this->isClassName($model) ? $model : $request->route($model);
    }

    /**
     * Checks if the given string looks like a fully qualified class name.
     *
     * @param  string $value
     * @return bool
     */
    protected function isClassName($value)
    {
        return strpos($value, '\\') !== false;
    }
}
