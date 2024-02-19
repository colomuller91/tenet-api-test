<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceController extends Controller
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService) {
        $this->invoiceService = $invoiceService;
    }



    public function index()
    {
        return $this->invoiceService->listAll();
    }

    public function customerList(Customer $customer)
    {
        $invoices = $this->invoiceService->listAllForCustomer($customer);

        return response()->json($invoices);
    }

    /**
     * Create an invoice for supplied customer
     */
    public function createInvoiceForCustomer(Customer $customer)
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

        return response()->json($invoice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return $invoice->load([
            'lines',
            'customer' => function($q) { return $q->withTrashed(); }
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(['message' => 'Invoice deleted']);
    }
}
