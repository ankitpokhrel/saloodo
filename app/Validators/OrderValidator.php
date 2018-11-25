<?php

namespace App\Validators;

use App\Exceptions\ResourceException;

class OrderValidator extends AbstractValidator
{
    /**
     * Validate order creation.
     *
     * @param array $products
     */
    public function validateCreate(array $products)
    {
        if (empty($products)) {
            throw new ResourceException(
                ResourceException::VALIDATION_ERROR_CODE,
                ['products' => 'No products in order.']
            );
        }
    }
}
