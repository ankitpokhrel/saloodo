<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Validators\ProductValidator;

class ProductsController extends Controller
{
    /** @var Product Service */
    protected $productService;

    /** @var ProductValidator */
    protected $validator;

    /**
     * ProductsController constructor.
     *
     * @param ProductService   $product
     * @param ProductValidator $validator
     */
    public function __construct(ProductService $product, ProductValidator $validator)
    {
        $this->validator = $validator;

        $this->productService = $product;
    }

    /**
     * Get all products.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $allProducts = $this->productService->getAllProducts();

        return response()->json($allProducts);
    }

    /**
     * Create a resource.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $data = $request->only($this->productService->getFillable());

        $this->validator->validateCreate($data);

        $this->productService->create($data);

        return response(null, Response::HTTP_CREATED);
    }

    /**
     * Update resource.
     *
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     */
    public function update(int $id, Request $request) : Response
    {
        $product = $this->productService->findOrFail($id);
        $fields  = $request->only($this->productService->getFillable());

        $this->validator->validateCreate($fields);

        $this->productService->update($product, $fields);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete resource.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Exception
     */
    public function delete(int $id) : Response
    {
        $this->productService->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Apply fixed discount.
     *
     * @param int   $id
     * @param float $discount
     *
     * @return Response
     */
    public function fixedDiscount(int $id, float $discount) : Response
    {
        $product = $this->productService->findOrFail($id);

        $this->validator->validateFixedDiscount($product->price, $discount);

        $this->productService->update($product, [
            'discount' => $discount,
            'discount_type' => Product::DISCOUNT_FIXED,
        ]);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Apply percent discount.
     *
     * @param int   $id
     * @param float $discount
     *
     * @return Response
     */
    public function percentDiscount(int $id, float $discount) : Response
    {
        $product = $this->productService->findOrFail($id);

        $this->validator->validatePercentDiscount($product->price, $discount);

        $this->productService->update($product, [
            'discount' => $discount,
            'discount_type' => Product::DISCOUNT_PERCENT,
        ]);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
