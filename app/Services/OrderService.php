<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\DatabaseManager;

class OrderService
{
    /** @var Order Model */
    protected $order;

    /** @var OrderItem */
    protected $orderItem;

    /** @var ProductService */
    protected $productService;

    /** @var DatabaseManager */
    protected $db;

    /**
     * OrderService constructor.
     *
     * @param DatabaseManager $db
     * @param Order           $order
     * @param OrderItem       $orderItem
     * @param ProductService  $product
     */
    public function __construct(
        DatabaseManager $db,
        Order $order,
        OrderItem $orderItem,
        ProductService $product
    ) {
        $this->db    = $db;
        $this->order = $order;

        $this->orderItem      = $orderItem;
        $this->productService = $product;
    }

    /**
     * Get fillable.
     *
     * @return array
     */
    public function getFillable() : array
    {
        return $this->order->getFillable();
    }

    /**
     * Create order.
     *
     * @param array $productIds
     *
     * @throws \Throwable
     *
     * @return array
     */
    public function create(array $productIds, int $userId) : array
    {
        $products = $this->productService->getProductsById($productIds);

        $response      = [];
        $orderItems    = [];
        $cartTotal     = 0;
        $discountTotal = 0;

        foreach ($products as $product) {
            $cartTotal     += $product->price;
            $discountTotal += $product->discount;

            $orderItem = [
                'product_id' => $product->id,
                'price' => $product->price,
                'discount' => $product->discount,
                'sale_price' => $product->price - $product->discount,
            ];

            $response[]   = $orderItem + ['name' => $product->name];
            $orderItems[] = $this->orderItem->newInstance($orderItem);
        }

        $this->db->transaction(function () use ($orderItems, $cartTotal, $discountTotal, $userId) {
            $order = $this->order->create([
                'user_id' => $userId,
                'order_number' => str_random('16'),
                'payment_method' => 'COD',
                'transaction_id' => str_random('32'),
                'cart_total' => $cartTotal,
                'discount_total' => $discountTotal,
                'status' => 'pending',
            ]);

            $order->items()->saveMany($orderItems);
        });

        return [
            'data' => [
                'items' => $response,
                'total_price' => $cartTotal - $discountTotal,
            ],
        ];
    }
}
