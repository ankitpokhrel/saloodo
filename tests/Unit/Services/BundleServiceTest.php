<?php

namespace App\Test\Unit\Services;

use Mockery as m;
use App\Models\Bundle;
use App\Services\BundleService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @coversDefaultClass \App\Services\BundleService
 */
class BundleServiceTest extends \UnitTestCase
{
    /**
     * @test
     *
     * @covers ::getFillable
     */
    public function it_gets_fillable()
    {
        $bundleService = app(BundleService::class);

        $this->assertEquals([
            'name',
            'price',
        ], $bundleService->getFillable());
    }

    /**
     * @test
     *
     * @covers ::__construct
     * @covers ::create
     */
    public function it_creates_bundle()
    {
        $bundle = m::mock(Bundle::class);

        $belongsToManyRelation = m::mock(BelongsToMany::class);

        $data = [
            'name' => 'bundle',
            'price' => 10,
            'products' => [1, 2],
        ];

        $bundle
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($bundle);

        $bundle
            ->shouldReceive('products')
            ->once()
            ->andReturn($belongsToManyRelation);

        $belongsToManyRelation
            ->shouldReceive('sync')
            ->once()
            ->with($data['products'])
            ->andReturnSelf();

        (new BundleService(app('db'), $bundle))->create($data);
    }
}
