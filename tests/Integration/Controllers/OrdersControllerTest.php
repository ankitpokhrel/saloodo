<?php

namespace App\Test\Integration\Controllers;

/**
 * @coversDefaultClass \App\Http\Controllers\OrdersController
 */
class OrdersControllerTest extends \IntegrationTestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function it_creates_orders()
    {
        $data = [
            'products' => [1, 2, 3],
        ];

        $this->post('/orders', $data, ['token' => $this->getCustomerUser()]);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('orders', ['user_id' => 2, 'payment_method' => 'COD', 'status' => 'pending']);
        $this->seeInDatabase('order_items', ['order_id' => 1, 'product_id' => 1]);
        $this->seeInDatabase('order_items', ['order_id' => 1, 'product_id' => 2]);
        $this->seeInDatabase('order_items', ['order_id' => 1, 'product_id' => 3]);
    }
}
