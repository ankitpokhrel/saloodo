<?php

namespace App\Validators;

use App\Exceptions\ResourceException;

class BundleValidator extends AbstractValidator
{
    /**
     * Validate bundle creation.
     *
     * @param array $data
     */
    public function validateCreate(array $data, array $products)
    {
        if (empty($products)) {
            throw new ResourceException(
                ResourceException::VALIDATION_ERROR_CODE,
                ['Product is required to create a bundle.']
            );
        }

        $this->validate($data, [
            'name' => 'required|unique:bundles,name',
            'price' => 'required|numeric|min:0',
        ]);
    }
}
