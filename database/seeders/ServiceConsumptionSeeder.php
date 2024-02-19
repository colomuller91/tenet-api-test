<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\ServiceConsumption;
use Illuminate\Database\Seeder;

class ServiceConsumptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $customers = Customer::all();

        foreach ($customers as $customer){
            ServiceConsumption::factory(rand(3,15))->create([
                'customer_id' => $customer->id,
            ]);
        }
    }
}
