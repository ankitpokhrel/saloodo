<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Validators\ProductValidator;
use App\Repositories\ProductRepository;

class ProductsController extends Controller
{
    /** @var Product Repository */
    protected $productRepo;

    /** @var ProductValidator */
    protected $validator;

    /**
     * ProductsController constructor.
     *
     * @param ProductRepository $product
     * @param ProductValidator  $validator
     */
    public function __construct(ProductRepository $product, ProductValidator $validator)
    {
        $this->productRepo = $product;
        $this->validator   = $validator;
    }

    /**
     * Get all products.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $allProducts = $this->productRepo->getAllProducts();

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
        $data = $request->only($this->productRepo->getFillable());

        $this->validator->validateCreate($data);

        $this->productRepo->create($data);

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
        $product = $this->productRepo->findOrFail($id);
        $fields  = $request->only($this->productRepo->getFillable());

        $this->validator->validateCreate($fields);

        $this->productRepo->update($product, $fields);

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
        $this->productRepo->delete($id);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
