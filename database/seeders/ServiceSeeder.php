<?php

namespace Database\Seeders;

use App\Enums\ServiceIdentifier;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        Service::factory()->createMany([
            [
                'name' => 'Backoffice',
                'identifier' => ServiceIdentifier::Backoffice->value,
                'unit_price' => 7.00,
                'memo' => 'Monthly billed',
            ],
            [
                'name' => 'Storage',
                'identifier' => ServiceIdentifier::Storage->value,
                'unit_price' => 0.03,
                'memo' => 'The formula is (cost * total units * billing period) ',
            ],
            [
                'name' => 'Proxy',
                'identifier' => ServiceIdentifier::Proxy->value,
                'unit_price' => 0.03,
                'memo' => 'Minutely billed',
            ],
            [
                'name' => 'Speech',
                'identifier' => ServiceIdentifier::Speech->value,
                'unit_price' => 0.00003,
                'memo' => 'Billed by letter',
            ]
        ]);
    }
}
