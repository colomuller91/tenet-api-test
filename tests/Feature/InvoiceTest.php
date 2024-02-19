<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\ServiceConsumption;
use App\Services\InvoiceService;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void {
        $response = $this->get('api/invoices');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "*" => [
                "number",
                "date",
                "id",
                "total",
                "customer" => [
                    "id",
                    "name",
                    "vat_id"
                ],
                "lines" => [
                    "*" => [
                        "id",
                        "quantity",
                        "unit_price",
                        "description",
                        "total_line",
                    ]
                ]
            ]
        ]);
    }

    public function test_create() {
        $customer = Customer::inRandomOrder()->first();
        $lastDays = rand(10,30);

        $notBilledConsumptions = $customer->serviceConsumptions()
            ->where('created_at', '>=', now()->subDays($lastDays))
            ->whereDoesntHave('invoiceLine.invoice')
            ->count();

        $response = $this->post("api/customers/{$customer->id}/create-invoice?last-days={$lastDays}");

        if ($notBilledConsumptions == 0) {
            $response->assertStatus(404);
        } else {
            $response->assertStatus(201);
            $this->assertTrue(count($response->json('lines')) === $notBilledConsumptions);
        }
    }

    public function test_destroy() {
        $customer = Customer::inRandomOrder()->first();
        $lastDays = rand(10,30);

        /** @var InvoiceService $invoiceService */
        $invoiceService = app(InvoiceService::class);
        $invoice = $invoiceService->createInvoice($customer, 999);

        if ($invoice->exists()) {
            $this->get("api/invoices/{$invoice->id}")->assertStatus(200);

            $this->delete("api/invoices/{$invoice->id}")->assertOk();

            $this->get("api/invoices/{$invoice->id}")->assertStatus(404);
        }


    }
}
