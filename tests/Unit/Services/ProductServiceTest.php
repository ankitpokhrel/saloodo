<?php

namespace App\Test\Unit\Services;

use Mockery as m;
use Carbon\Carbon;
use App\Models\Product;
use App\Services\ProductService;

/**
 * @coversDefaultClass \App\Services\ProductService
 */
class ProductServiceTest extends \UnitTestCase
{
    /**
     * @test
     *
     * @covers ::getFillable
     */
    public function it_gets_fillable()
    {
        $productService = app(ProductService::class);

        $this->assertEquals([
            'name',
            'description',
            'quantity',
            'price',
            'discount',
            'discount_type',
        ], $productService->getFillable());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getAllProducts
     */
    public function it_gets_all_products()
    {
        $product = m::mock(Product::class);

        $data = collect([
            [
                'name' => 'Test',
                'description' => 'Test data',
                'quantity' => 5,
                'price' => 5.5,
                'discount' => 0,
                'discount_type' => Product::DISCOUNT_FIXED,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $product
            ->shouldReceive('all')
            ->once()
            ->andReturn($data);

        (new ProductService($product))->getAllProducts($data);
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::getProductsById
     */
    public function it_gets_product_by_id()
    {
        $product = m::mock(Product::class);

        $data = collect([
            [
                'name' => 'Test',
                'description' => 'Test data',
                'quantity' => 5,
                'price' => 5.5,
                'discount' => 0,
                'discount_type' => Product::DISCOUNT_FIXED,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        $product
            ->shouldReceive('findOrFail')
            ->once()
            ->with([1])
            ->andReturn($data);

        (new ProductService($product))->getProductsById([1]);
    }

    /**
     * @test
     *
     * @covers ::create
     */
    public function it_creates_product()
    {
        $product = m::mock(Product::class);

        $data = [
            'name' => 'Test',
            'description' => 'Test data',
            'quantity' => 5,
            'price' => 5.5,
            'discount' => 0,
            'discount_type' => Product::DISCOUNT_FIXED,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $product
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($product);

        (new ProductService($product))->create($data);
    }

    /**
     * @test
     *
     * @covers ::findOrFail
     */
    public function it_wraps_find_or_fail()
    {
        $product = m::mock(Product::class);

        $data = [
            'name' => 'Test',
            'description' => 'Test data',
            'quantity' => 5,
            'price' => 5.5,
            'discount' => 0,
            'discount_type' => Product::DISCOUNT_FIXED,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $product
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn(new Product($data));

        (new ProductService($product))->findOrFail(1);
    }

    /**
     * @test
     *
     * @covers ::update
     */
    public function it_updates_data()
    {
        $product    = m::mock(Product::class);
        $attributes = ['price' => 123];

        $product
            ->shouldReceive('update')
            ->once()
            ->with($attributes)
            ->andReturn(true);

        (new ProductService($product))->update($product, $attributes);
    }

    /**
     * @test
     *
     * @covers ::update
     *
     * @expectedException \App\Exceptions\ResourceException
     */
    public function it_throws_resource_exception_if_it_cannot_update_data()
    {
        $product    = m::mock(Product::class);
        $attributes = ['price' => 123];

        $product
            ->shouldReceive('update')
            ->once()
            ->with($attributes)
            ->andReturn(false);

        (new ProductService($product))->update($product, $attributes);
    }

    /**
     * @test
     *
     * @covers ::delete
     */
    public function it_deletes_data()
    {
        $product        = m::mock(Product::class);
        $productService = m::mock(ProductService::class)->makePartial();

        $productService
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($product);

        $product
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $productService->delete(1);
    }

    /**
     * @test
     *
     * @covers ::delete
     *
     * @expectedException \App\Exceptions\ResourceException
     */
    public function it_throws_resource_exception_if_it_cannot_delete_data()
    {
        $product        = m::mock(Product::class);
        $productService = m::mock(ProductService::class)->makePartial();

        $productService
            ->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andReturn($product);

        $product
            ->shouldReceive('delete')
            ->once()
            ->andReturn(false);

        $productService->delete(1);
    }
}
