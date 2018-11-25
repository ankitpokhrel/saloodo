<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\OrderService;
use App\Validators\OrderValidator;

class OrdersController extends Controller
{
    /** @var OrderService */
    protected $orderService;

    /** @var OrderValidator */
    protected $validator;

    /**
     * BundlesController constructor.
     *
     * @param OrderService   $order
     * @param OrderValidator $validator
     */
    public function __construct(OrderService $order, OrderValidator $validator)
    {
        $this->validator = $validator;

        $this->orderService = $order;
    }

    /**
     * Create one or more orders.
     *
     * @param Request $request
     *
     * @throws \Throwable
     *
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $products = $request->get('products') ?? [];

        $this->validator->validateCreate($products);

        $orderMeta = $this->orderService->create($products, $request->user->id);

        return response($orderMeta, Response::HTTP_CREATED);
    }
}
