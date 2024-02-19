<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

class CustomerService
{
    /**
     * @return Collection
     */
    public function listAll(): Collection {
        return Customer::all();
    }

}
