<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function listAll() {
        return Customer::all();
    }

}
