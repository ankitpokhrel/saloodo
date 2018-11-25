<?php

namespace App\Test\Integration\Controllers;

/**
 * @coversDefaultClass \App\Http\Controllers\BundlesController
 */
class BundlesControllerTest extends \IntegrationTestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function it_creates_bundle_for_admin_user()
    {
        $data = [
            'name' => 'Test bundle',
            'price' => 10.5,
            'products' => [1, 2, 3],
        ];

        $this->post('/bundles', $data, ['token' => $this->getAdminUser()]);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('bundles', array_only($data, ['name', 'price']));
        $this->seeInDatabase('bundle_product', ['bundle_id' => 1, 'product_id' => 1]);
        $this->seeInDatabase('bundle_product', ['bundle_id' => 1, 'product_id' => 2]);
        $this->seeInDatabase('bundle_product', ['bundle_id' => 1, 'product_id' => 3]);
    }
}
