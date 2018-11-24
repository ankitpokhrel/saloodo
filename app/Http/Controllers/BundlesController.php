<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Validators\BundleValidator;
use App\Services\BundleService;

class BundlesController extends Controller
{
    /** @var BundleService */
    protected $bundleService;

    /** @var BundleValidator */
    protected $validator;

    /**
     * BundlesController constructor.
     *
     * @param BundleService   $bundle
     * @param BundleValidator $validator
     */
    public function __construct(BundleService $bundle, BundleValidator $validator)
    {
        $this->bundleService = $bundle;
        $this->validator  = $validator;
    }

    /**
     * Create a Bundle.
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function create(Request $request) : Response
    {
        $bundle   = $request->only($this->bundleService->getFillable());
        $products = $request->products ?? [];

        $this->validator->validateCreate($bundle, $products);
        $this->bundleService->create($bundle + ['products' => $products]);

        return response(null, Response::HTTP_CREATED);
    }
}
