<?php

namespace App\Test\Integration\Controllers;

/**
 * @coversDefaultClass \App\Http\Controllers\UsersController
 */
class UsersControllerTest extends \IntegrationTestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::authenticate
     */
    public function it_throws_validation_exception_for_invalid_request()
    {
        $user = $this->post('/users/authenticate', [
            'email' => 'hello@ankit.pl',
        ]);

        $response = json_decode($user->response->getContent(), true);

        $this->assertResponseStatus(422);
        $this->assertEquals([
            'status_code' => 422,
            'error_code' => 'validation_error',
            'errors' => [
                'password' => [
                    'The password field is required.',
                ],
            ],
        ], $response);
    }

    /**
     * @test
     *
     * @covers ::authenticate
     */
    public function it_fails_authentication_for_invalid_user()
    {
        $this->post('/users/authenticate', [
            'email' => 'invalid@ankit.pl',
            'password' => 'invalid',
        ]);

        $this->assertResponseStatus(500);
    }

    /**
     * @test
     *
     * @covers ::authenticate
     */
    public function it_authenticates_user()
    {
        $user = $this->post('/users/authenticate', [
            'email' => 'hello@ankit.pl',
            'password' => 'pokhrel',
        ]);

        $response = json_decode($user->response->getContent(), true);

        $this->assertResponseOk();
        $this->assertNotEmpty($response['data']['token']);
    }
}
