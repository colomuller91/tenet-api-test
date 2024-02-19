<?php

namespace Tests\Feature;

use App\Enums\ServiceIdentifier;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceConsumption;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceConsumptionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void {
        $response = $this->get('api/services/1/consumptions/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            "*" => [
                "id",
                "from_date",
                "to_date",
                "quantity",
                "customer" => [
                    "id",
                    "name",
                    "vat_id"
                ],
                "service" => [
                    "id",
                    "name",
                    "memo",
                    "unit_price"
                ]
            ]
        ]);
    }

    public function test_create() {
        $customer = Customer::inRandomOrder()->first();
        $factoryResult = ServiceConsumption::factory(1)->create([
            'customer_id' => $customer->id
        ]);

        $consumption = $factoryResult->first();

        $response = $this->get("api/service-consumptions/{$consumption->id}");

        $response->assertJsonStructure([
            "id",
            "from_date",
            "to_date",
            "quantity",
            "customer" => [
                "id",
                "name",
                "vat_id"
            ]
        ]);

        $response->assertJsonPath('customer.id', $customer->id);
    }

    public function test_create_same_period() {
        $customer = Customer::inRandomOrder()->first();
        $backofficeService = Service::where('identifier', ServiceIdentifier::Backoffice)->first();

        $consumption = ServiceConsumption::create([
            'customer_id' => $customer->id,
            'service_id' => $backofficeService->id,
            'from_date' => now()->firstOfMonth()->startOfDay(),
            'to_date' => now()->endOfMonth()->endOfDay(),
            'quantity' => 1
        ]);

        $response = $this->post("api/services/{$backofficeService->id}/consumptions/", [
            "customer_id" => $customer->id,
            "from_date" => $consumption->from_date->format('Y-m-d H:i:s'),
            "to_date" => $consumption->to_date->format('Y-m-d H:i:s'),
            "quantity" => 1
        ]);

        $response->assertStatus(400);
    }

    public function test_destroy() {
        $customer = Customer::inRandomOrder()->first();
        $factoryResult = ServiceConsumption::factory(1)->create([
            'customer_id' => $customer->id
        ]);

        $consumption = $factoryResult->first();

        $daysUntilConsumption = $consumption->created_at->diffInDays(now());

        /** @var InvoiceService $invoiceService */
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createInvoice($customer, $daysUntilConsumption + 1);

        //try to delete billed consumption
        $response = $this->delete("api/service-consumptions/{$consumption->id}");
        $response->assertStatus(400);

        //try to delete consumption related to an invoices marked as deleted
        $this->delete("api/invoices/{$invoice->id}");
        $response = $this->delete("api/service-consumptions/{$consumption->id}");
        $response->assertStatus(200);
    }

}
