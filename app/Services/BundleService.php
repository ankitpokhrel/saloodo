<?php

namespace App\Services;

use App\Models\Bundle;
use Illuminate\Database\DatabaseManager;

class BundleService
{
    /** @var Bundle Model */
    protected $bundle;

    /** @var DatabaseManager */
    protected $db;

    /**
     * BundleService constructor.
     *
     * @param DatabaseManager $db
     * @param Bundle          $bundle
     */
    public function __construct(DatabaseManager $db, Bundle $bundle)
    {
        $this->db     = $db;
        $this->bundle = $bundle;
    }

    /**
     * Get fillable.
     *
     * @return array
     */
    public function getFillable() : array
    {
        return $this->bundle->getFillable();
    }

    /**
     * Create bundle.
     *
     * @param array $data
     *
     * @throws \Throwable
     *
     * @return Bundle
     */
    public function create(array $data) : Bundle
    {
        return $this->db->transaction(function () use ($data) {
            $bundle = $this->bundle->create($data);

            $bundle->products()->sync($data['products']);

            return $bundle;
        });
    }
}
