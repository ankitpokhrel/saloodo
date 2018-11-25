<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ResourceException;

class UserService
{
    /** @var JWT */
    protected $jwt;

    /** @var User Model */
    protected $user;

    /**
     * ProductService constructor.
     *
     * @param User $user
     * @param JWT  $jwt
     */
    public function __construct(User $user, JWT $jwt)
    {
        $this->jwt  = $jwt;
        $this->user = $user;
    }

    /**
     * @param array $credentials
     *
     * @return JsonResponse
     */
    public function authenticate(array $credentials) : JsonResponse
    {
        $user = $this->user->where('email', $credentials['email'])->firstOrFail();

        if (app('hash')->check($credentials['password'], $user->password)) {
            $payload = [
                'issuer' => env('APP_NAME'),
                'subject' => $user->id,
                'issued_at' => Carbon::now()->toDateTimeString(),
                'expired_at' => Carbon::now()->addHour()->toDateTimeString(),
            ];

            return response()->json([
                'data' => [
                    'token' => $this->jwt->encode($payload, env('JWT_ENCRYPT_KEY')),
                ],
            ], Response::HTTP_OK);
        }

        throw new ResourceException(
            ResourceException::AUTH_ERROR_CODE,
            ['auth' => 'Wrong credentials.'],
            Response::HTTP_BAD_REQUEST);
    }
}
