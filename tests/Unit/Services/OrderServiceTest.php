<?php

namespace App\Test\Unit\Services;

use Mockery as m;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Services\OrderService;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @coversDefaultClass \App\Services\OrderService
 */
class OrderServiceTest extends \UnitTestCase
{
    /**
     * @test
     *
     * @covers ::getFillable
     */
    public function it_gets_fillable()
    {
        $orderService = app(OrderService::class);

        $this->assertEquals([
            'user_id',
            'order_number',
            'payment_method',
            'transaction_id',
            'cart_total',
            'discount_total',
            'status',
        ], $orderService->getFillable());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function it_creates_order()
    {
        $order = m::mock(Order::class)->makePartial();

        $orderItem       = m::mock(OrderItem::class);
        $productService  = m::mock(ProductService::class);
        $hasManyRelation = m::mock(HasMany::class);

        $products = [
            new Product([
                'name' => 'Test',
                'description' => 'Test data',
                'quantity' => 5,
                'price' => 5.5,
                'discount' => 0,
                'discount_type' => 'fixed',
            ]),
            new Product([
                'name' => 'Test2',
                'description' => 'Test data2',
                'quantity' => 15,
                'price' => 7.5,
                'discount' => 5,
                'discount_type' => 'fixed',
            ]),
        ];

        $products[0]->id = 1;
        $products[1]->id = 2;

        $productService
            ->shouldReceive('getProductsById')
            ->once()
            ->with([1, 2])
            ->andReturn(collect($products));

        $orderItem
            ->shouldReceive('newInstance')
            ->once()
            ->andReturn($products[0]);

        $orderItem
            ->shouldReceive('newInstance')
            ->once()
            ->andReturn($products[1]);

        $order
            ->shouldReceive('create')
            ->once()
            ->andReturnSelf();

        $order
            ->shouldReceive('items')
            ->once()
            ->andReturn($hasManyRelation);

        $hasManyRelation
            ->shouldReceive('saveMany')
            ->once()
            ->with($products)
            ->andReturnSelf();

        $expected = [
            'data' => [
                'items' => [
                    [
                        'product_id' => 1,
                        'name' => 'Test',
                        'price' => 5.5,
                        'discount' => 0,
                        'sale_price' => 5.5,
                    ],
                    [
                        'product_id' => 2,
                        'name' => 'Test2',
                        'price' => 7.5,
                        'discount' => 5,
                        'sale_price' => 2.5,
                    ],
                ],
                'total_price' => 8,
            ],
        ];

        $this->assertEquals(
            $expected,
            (new OrderService(app('db'), $order, $orderItem, $productService))->create([1, 2], 1)
        );
    }
}
