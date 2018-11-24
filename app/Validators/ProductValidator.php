<?php

namespace App\Validators;

use App\Exceptions\ResourceException;

class ProductValidator extends AbstractValidator
{
    /**
     * Validate resource creation.
     *
     * @param array $data
     */
    public function validateCreate(array $data)
    {
        $this->validate($data, [
            'name' => 'required|unique:products,name',
            'description' => 'required|min:10',
            'quantity' => 'required|integer',
            'price' => 'required|numeric|min:0',
        ]);
    }

    /**
     * Validate discount.
     *
     * @param float $price
     * @param float $discount
     */
    public function validateFixedDiscount(float $price, float $discount)
    {
        $this->validate(
            ['price' => $price - $discount],
            ['price' => 'required|numeric|min:0'],
            ['min' => 'Invalid discount price']
        );
    }

    /**
     * Validate discount.
     *
     * @param float $price
     * @param float $discount
     */
    public function validatePercentDiscount(float $price, float $discount)
    {
        if ($discount < 0 || $discount > 100) {
            throw new ResourceException(
                ResourceException::VALIDATION_ERROR_CODE,
                ['Invalid discount percent.']
            );
        }

        $priceAfterDiscount = $price - ($price * $discount / 100);

        $this->validate(
            ['price' => $priceAfterDiscount],
            ['price' => 'required|numeric|min:0'],
            ['min' => 'Invalid discount percent.']
        );
    }
}
