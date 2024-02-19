<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        foreach ($customers as $customer) {
            Invoice::factory(2)->create([
                'customer_id' => $customer->id
            ]);
        }

    }
}
