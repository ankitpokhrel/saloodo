<?php

use Laravel\Lumen\Testing\TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

abstract class IntegrationTestCase extends TestCase
{
    use DatabaseMigrations;

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    /**
     * Set up.
     */
    public function setUp()
    {
        parent::setUp();

        $this->runDatabaseMigrations();
        $this->artisan('db:seed');
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        $this->beforeApplicationDestroyed(function () {
            app('db')->connection()->disconnect();
        });

        parent::tearDown();
    }

    /**
     * Get admin user.
     *
     * @return string|null
     */
    public function getAdminUser()
    {
        $admin = $this->post($this->baseUrl . '/users/authenticate', [
            'email' => 'hello@ankit.pl',
            'password' => 'pokhrel',
        ]);

        $response = json_decode($admin->response->getContent(), true);

        return $response['data']['token'] ?? null;
    }

    /**
     * Get customer user.
     *
     * @return string|null
     */
    public function getCustomerUser()
    {
        $customer = $this->post($this->baseUrl . '/users/authenticate', [
            'email' => 'john@ankit.pl',
            'password' => 'doe',
        ]);

        $response = json_decode($customer->response->getContent(), true);

        return $response['data']['token'] ?? null;
    }
}
