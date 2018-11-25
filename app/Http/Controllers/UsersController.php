<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Validators\UserValidator;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    /** @var UserService */
    protected $userService;

    /** @var UserValidator */
    protected $validator;

    /**
     * UsersController constructor.
     *
     * @param UserService   $user
     * @param UserValidator $validator
     */
    public function __construct(UserService $user, UserValidator $validator)
    {
        $this->validator = $validator;

        $this->userService = $user;
    }

    /**
     * Authenticate user.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function authenticate(Request $request) : JsonResponse
    {
        $credentials = $request->only('email', 'password');

        $this->validator->validateAuthentication($credentials);

        return $this->userService->authenticate($credentials);
    }
}
