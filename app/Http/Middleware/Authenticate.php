<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Firebase\JWT\ExpiredException;
use App\Exceptions\ResourceException;

class Authenticate
{
    /** @var User */
    protected $user;

    /** @var JWT */
    protected $jwt;

    /**
     * JwtMiddleware constructor.
     *
     * @param User $user
     * @param JWT  $jwt
     */
    public function __construct(User $user, JWT $jwt)
    {
        $this->user = $user;
        $this->jwt  = $jwt;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param array   $roles
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $token = $request->header('token');

        if ( ! $token) {
            throw new ResourceException(
                ResourceException::AUTH_ERROR_CODE,
                ['Invalid token.'],
                Response::HTTP_BAD_REQUEST);
        }

        try {
            $credentials = $this->jwt->decode($token, env('JWT_ENCRYPT_KEY'), ['HS256']);
        } catch (ExpiredException $e) {
            throw new ResourceException(
                ResourceException::AUTH_ERROR_CODE,
                ['Token expired.'],
                Response::HTTP_BAD_REQUEST
            );
        } catch (Exception $e) {
            throw new ResourceException(
                ResourceException::AUTH_ERROR_CODE,
                ['Cannot decode token.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->user->findOrFail($credentials->subject);

        if ( ! in_array($user->role, $roles)) {
            throw new ResourceException(
                ResourceException::AUTH_ERROR_CODE,
                ['Unauthorized.'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $request->user = $user;

        return $next($request);
    }
}
