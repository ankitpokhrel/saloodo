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
    public function it_returns_paginated_products_for_admin_user()
    {
        $products = $this->get('/products', [
            'token' => $this->getAdminUser(),
        ]);

        $response = $products->response->getOriginalContent();

        $expectedMeta = [
            'total' => 15,
            'current_page' => 1,
            'last_page' => 3,
            'per_page' => 5,
            'from' => 1,
            'to' => 5,
        ];

        $expectedLinks = [
            'first_page_url' => 'http://localhost/products?page=1',
            'last_page_url' => 'http://localhost/products?page=3',
            'next_page_url' => 'http://localhost/products?page=2',
            'prev_page_url' => null,
        ];

        $this->assertResponseOk();
        $this->assertEquals(5, count($response['data']));
        $this->assertEquals($expectedMeta, $response['meta']);
        $this->assertEquals($expectedLinks, $response['links']);
    }

    /**
     * @test
     *
     * @covers ::index
     */
    public function it_returns_products_from_next_page()
    {
        $products = $this->get('/products?page=2', [
            'token' => $this->getAdminUser(),
        ]);

        $response = $products->response->getOriginalContent();

        $expectedMeta = [
            'total' => 15,
            'current_page' => 2,
            'last_page' => 3,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
        ];

        $expectedLinks = [
            'first_page_url' => 'http://localhost/products?page=1',
            'last_page_url' => 'http://localhost/products?page=3',
            'next_page_url' => 'http://localhost/products?page=3',
            'prev_page_url' => 'http://localhost/products?page=1',
        ];

        $this->assertResponseOk();
        $this->assertEquals(5, count($response['data']));
        $this->assertEquals($expectedMeta, $response['meta']);
        $this->assertEquals($expectedLinks, $response['links']);
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::index
     */
    public function it_returns_paginated_products_for_customer_user()
    {
        $products = $this->get('/products', [
            'token' => $this->getCustomerUser(),
        ]);

        $response = $products->response->getOriginalContent();

        $expectedMeta = [
            'total' => 15,
            'current_page' => 1,
            'last_page' => 3,
            'per_page' => 5,
            'from' => 1,
            'to' => 5,
        ];

        $expectedLinks = [
            'first_page_url' => 'http://localhost/products?page=1',
            'last_page_url' => 'http://localhost/products?page=3',
            'next_page_url' => 'http://localhost/products?page=2',
            'prev_page_url' => null,
        ];

        $this->assertResponseOk();
        $this->assertEquals(5, count($response['data']));
        $this->assertEquals($expectedMeta, $response['meta']);
        $this->assertEquals($expectedLinks, $response['links']);
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
