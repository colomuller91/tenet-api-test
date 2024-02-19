<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService) {
        $this->invoiceService = $invoiceService;
    }


    /**
     * List all invoices
     */
    public function index(): Collection {
        return $this->invoiceService->listAll();
    }

    /**
     * @param Customer $customer
     * @return Collection
     */
    public function customerList(Customer $customer): Collection
    {
        return $this->invoiceService->listAllForCustomer($customer);
    }

    /**
     * Create an invoice for supplied customer
     */
    public function createInvoiceForCustomer(Customer $customer): Invoice | JsonResponse
    {
        $lastDays = request()->query('last-days', 15);

        try {
            $invoice = $this->invoiceService->createInvoice($customer, $lastDays);
        } catch (NotFoundHttpException | \Exception $exception) {
            return response()->json(
                ['message' => $exception->getMessage()],
                $exception->getStatusCode()
            );
        }

        return $invoice;
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): Invoice
    {
        return $invoice->load([
            'lines',
            'customer' => function($q) { return $q->withTrashed(); }
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }
}
