<?php

namespace App\Test\Unit\Services;

use Mockery as m;
use Carbon\Carbon;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Services\UserService;

/**
 * @coversDefaultClass \App\Services\UserService
 */
class UserServiceTest extends \UnitTestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::authenticate
     */
    public function it_authenticates_user()
    {
        $credentials = [
            'email' => 'hello@ankit.pl',
            'password' => 'pokhrel',
        ];

        $user = m::mock(User::class);

        $user
            ->shouldReceive('where')
            ->with('email', $credentials['email'])
            ->andReturnSelf();

        $user
            ->shouldReceive('firstOrFail')
            ->andReturnSelf();

        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $user
            ->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn(app('hash')->make($credentials['password']));

        $payload = [
            'issuer' => 'saloodo',
            'subject' => 1,
            'issued_at' => Carbon::now()->toDateTimeString(),
            'expired_at' => Carbon::now()->addHour()->toDateTimeString(),
        ];

        $jwt = m::mock(JWT::class);

        $jwt
            ->shouldReceive('encode')
            ->with($payload, env('JWT_ENCRYPT_KEY'))
            ->andReturn(123);

        $expected = response()->json([
            'data' => [
                'token' => 123,
            ],
        ], 200);

        $this->assertEquals($expected, (new UserService($user, $jwt))->authenticate($credentials));
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::authenticate
     *
     * @expectedException \App\Exceptions\ResourceException
     */
    public function it_throws_resource_exception_for_invalid_user()
    {
        $credentials = [
            'email' => 'hello@ankit.pl',
            'password' => 'pokhrel',
        ];

        $user = m::mock(User::class);

        $user
            ->shouldReceive('where')
            ->with('email', $credentials['email'])
            ->andReturnSelf();

        $user
            ->shouldReceive('firstOrFail')
            ->andReturnSelf();

        $user
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $user
            ->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn('invalid');

        (new UserService($user, m::mock(JWT::class)))->authenticate($credentials);
    }
}
