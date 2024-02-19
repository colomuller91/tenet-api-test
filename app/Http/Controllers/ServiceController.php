<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Service::all();
    }


    /**
     * Display the specified resource.
     */
    public function show(Service $service)
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
