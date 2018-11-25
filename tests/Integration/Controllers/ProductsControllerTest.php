<?php

namespace App\Test\Integration\Controllers;

/**
 * @coversDefaultClass \App\Http\Controllers\ProductsController
 */
class ProductsControllerTest extends \IntegrationTestCase
{
    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::index
     */
    public function it_returns_all_products_for_admin_user()
    {
        $products = $this->get('/products', [
            'token' => $this->getAdminUser(),
        ]);

        $this->assertResponseOk();
        $this->assertEquals(15, $products->response->getOriginalContent()->count());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::index
     */
    public function it_returns_all_products_for_customer_user()
    {
        $products = $this->get('/products', [
            'token' => $this->getCustomerUser(),
        ]);

        $this->assertResponseOk();
        $this->assertEquals(15, $products->response->getOriginalContent()->count());
    }

    /**
     * @test
     *
     * @covers ::create
     */
    public function it_creates_product_for_admin_user()
    {
        $data = [
            'name' => 'Test product',
            'description' => 'Test product description',
            'quantity' => 5,
            'price' => 10,
            'discount' => 0,
        ];

        $this->post('/products', $data, ['token' => $this->getAdminUser()]);

        $this->assertResponseStatus(201);
        $this->seeInDatabase('products', $data);
    }

    /**
     * @test
     *
     * @covers ::create
     */
    public function it_will_not_create_product_for_customer_user()
    {
        $data = [
            'name' => 'Test product',
            'description' => 'Test product description',
            'quantity' => 5,
            'price' => 10,
            'discount' => 0,
        ];

        $this->post('/products', $data, ['token' => $this->getCustomerUser()]);

        $this->assertResponseStatus(401);
        $this->notSeeInDatabase('products', $data);
    }

    /**
     * @test
     *
     * @covers ::update
     */
    public function it_updates_product_for_admin_user()
    {
        $data = [
            'name' => 'Test product',
            'description' => 'Test product description',
        ];

        $this->patch('/products/1', $data, ['token' => $this->getAdminUser()]);

        $this->assertResponseStatus(204);
        $this->seeInDatabase('products', $data);
    }

    /**
     * @test
     *
     * @covers ::update
     */
    public function it_will_not_update_product_for_customer_user()
    {
        $data = [
            'name' => 'Test product',
            'description' => 'Test product description',
        ];

        $this->patch('/products/1', $data, ['token' => $this->getCustomerUser()]);

        $this->assertResponseStatus(401);
        $this->notSeeInDatabase('products', $data);
    }

    /**
     * @test
     *
     * @covers ::delete
     */
    public function it_deletes_product_for_admin_user()
    {
        $this->delete('/products/1', [], ['token' => $this->getAdminUser()]);

        $this->assertResponseStatus(204);
        $this->notSeeInDatabase('products', ['id' => 1]);
    }

    /**
     * @test
     *
     * @covers ::delete
     */
    public function it_will_not_delete_product_for_customer_user()
    {
        $this->delete('/products/1', [], ['token' => $this->getCustomerUser()]);

        $this->assertResponseStatus(401);
        $this->seeInDatabase('products', ['id' => 1]);
    }

    /**
     * @test
     *
     * @covers ::fixedDiscount
     */
    public function it_applies_fixed_discount()
    {
        $this->patch('/products/1/discount/10/fixed', [], ['token' => $this->getAdminUser(),]);

        $this->assertResponseStatus(204);
        $this->seeInDatabase('products', ['id' => 1, 'discount' => 10, 'discount_type' => 'fixed']);
    }

    /**
     * @test
     *
     * @covers ::fixedDiscount
     */
    public function it_throws_validation_error_for_invalid_fixed_discount()
    {
        $response = $this->patch('/products/1/discount/250/fixed', [], ['token' => $this->getAdminUser(),]);

        $response = json_decode($response->response->getContent(), true);

        $this->assertResponseStatus(422);
        $this->assertEquals([
            'status_code' => 422,
            'error_code' => 'validation_error',
            'errors' => [
                'discount' => [
                    'Invalid discount amount.',
                ],
            ],
        ], $response);
    }

    /**
     * @test
     *
     * @covers ::percentDiscount
     */
    public function it_applies_percent_discount()
    {
        $this->patch('/products/1/discount/5', [], ['token' => $this->getAdminUser(),]);

        $this->assertResponseStatus(204);
        $this->seeInDatabase('products', ['id' => 1, 'discount' => 5, 'discount_type' => 'percent']);
    }

    /**
     * @test
     *
     * @covers ::percentDiscount
     */
    public function it_throws_validation_error_for_invalid_percent_discount()
    {
        $response = $this->patch('/products/1/discount/200', [], ['token' => $this->getAdminUser(),]);

        $response = json_decode($response->response->getContent(), true);

        $this->assertResponseStatus(422);
        $this->assertEquals([
            'status_code' => 422,
            'error_code' => 'validation_error',
            'errors' => [
                'discount' => [
                    'Invalid discount percent.',
                ],
            ],
        ], $response);
    }
}
