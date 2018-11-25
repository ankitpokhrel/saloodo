<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use App\Exceptions\ResourceException;

class ProductService
{
    /** @var Product Model */
    protected $product;

    /**
     * ProductService constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get fillable.
     *
     * @return array
     */
    public function getFillable() : array
    {
        return $this->product->getFillable();
    }

    /**
     * Get all products.
     *
     * @return array
     */
    public function getAllProducts() : array
    {
        $products = $this->product->paginate(5)->toArray();

        return [
            'data' => $products['data'],
            'links' => [
                'first_page_url' => $products['first_page_url'],
                'last_page_url' => $products['last_page_url'],
                'next_page_url' => $products['next_page_url'],
                'prev_page_url' => $products['prev_page_url'],
            ],
            'meta' => [
                'total' => $products['total'],
                'current_page' => $products['current_page'],
                'last_page' => $products['last_page'],
                'per_page' => $products['per_page'],
                'from' => $products['from'],
                'to' => $products['to'],
            ],
        ];
    }

    /**
     * Get products by id.
     *
     * @param array $productIds
     *
     * @return Collection
     */
    public function getProductsById(array $productIds) : Collection
    {
        return $this->product->findOrFail($productIds);
    }

    /**
     * Create product.
     *
     * @param array $data
     *
     * @return Product
     */
    public function create(array $data) : Product
    {
        return $this->product->create($data);
    }

    /**
     * @param int $id
     *
     * @return Product
     */
    public function findOrFail(int $id) : Product
    {
        return $this->product->findOrFail($id);
    }

    /**
     * Update resource.
     *
     * @param Product $resource
     * @param array   $attributes
     */
    public function update(Product $resource, array $attributes)
    {
        if ( ! $resource->update($attributes)) {
            throw new ResourceException(ResourceException::VALIDATION_ERROR_CODE, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete resource.
     *
     * @param int $id
     *
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $resource = $this->findOrFail($id);

        if ( ! $resource->delete()) {
            throw new ResourceException(ResourceException::VALIDATION_ERROR_CODE, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
