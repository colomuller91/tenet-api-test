<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void {
        $response = $this->get('api/services');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            "*" => [
                "id",
                "name",
                "memo",
                "unit_price"
            ]
        ]);
    }

    public function test_show_with_consumptions(): void {
        $response = $this->get('api/services/1?with-consumptions=1');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            "id",
            "name",
            "memo",
            "unit_price",
            "consumptions" => [
                "*" => [
                    "from_date",
                    "to_date",
                    "quantity",
                    "customer" => [
                        "id",
                        "name",
                        "vat_id"
                    ]
                ]
            ]
        ]);
    }
}
