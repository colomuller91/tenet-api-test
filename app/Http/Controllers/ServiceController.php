<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * List all services
     */
    public function index(): Collection
    {
        return Service::all();
    }


    /**
     * Display the specified resource.
     */
    public function show(Service $service): Service
    {
        if (request()->query('with-consumptions', false)) {
            $service->load([
                'consumptions',
                'consumptions.customer' => fn($q) => $q->withTrashed(),
            ]);
        }
        return $service;
    }
}
