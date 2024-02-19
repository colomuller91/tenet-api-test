<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceConsumptionRequest;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceConsumption;
use App\Services\ServiceConsumptionService;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ServiceConsumptionController extends Controller
{

    private ServiceConsumptionService $serviceConsumptionService;

    public function __construct(ServiceConsumptionService $serviceConsumptionService) {
        $this->serviceConsumptionService = $serviceConsumptionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Service $service)
    {
        return $service->consumptions()->get()->load([
            'customer' => fn($q) => $q->withTrashed(),
            'service'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Service $service, StoreServiceConsumptionRequest $request)
    {
        $data = $request->validated();

        try {
           $data = $this->serviceConsumptionService->validateConsumption($data, $service);
        } catch (BadRequestException $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                400
            );
        }

        return $this->serviceConsumptionService->createConsumption($data);
    }

    /**
     * @param Customer $customer
     */
    public function customerConsumptions(Customer $customer) {
        return ServiceConsumption::with('service')
            ->where('customer_id', $customer->id)
            ->get();
    }


    /**
     * Display the specified resource.
     */
    public function show(ServiceConsumption $service_consumption)
    {
        return $service_consumption->load('customer');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceConsumption $service_consumption)
    {
        try {
            $this->serviceConsumptionService->checkInvoice($service_consumption);
        } catch (BadRequestException $exception) {
            return response()->json(
                ['message' => 'Service consumption can\'t be deleted, it\'s associated to an invoice line'],
                400
            );
        }

        $service_consumption->deleteOrFail();
        return response()->json(['message' => 'Service consumption deleted']);


    }
}
