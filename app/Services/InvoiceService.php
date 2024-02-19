<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ServiceConsumption;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InvoiceService
{
    private InvoiceLineService $invoiceLineService;

    /**
     * @param InvoiceLineService $invoiceLineService
     */
    public function __construct(
        InvoiceLineService $invoiceLineService
    ) {
        $this->invoiceLineService = $invoiceLineService;
    }

    /**
     * @param Customer $customer
     * @param $daysTo
     * @return Invoice
     */
    public function createInvoice(Customer $customer, $daysTo) {
        $consumptions = $customer->serviceConsumptions()
            ->with('service')
            ->where('created_at', '>=', now()->subDays($daysTo))
            ->whereDoesntHave('invoiceLine.invoice')
            ->get();

        if ($consumptions->count() === 0) {
            throw new NotFoundHttpException('There are not consumptions on this period');
        }

        $invoice = new Invoice();
        $invoice->fill([
            ...Invoice::factory()->definition(),
            'customer_id' => $customer->id]);

        $invoiceDetails = [];
        $consumptions->each(
        /** @param ServiceConsumption $item */
            function(ServiceConsumption $item) use (&$invoiceDetails) {
                $invoiceDetails[] = $this->invoiceLineService->create($item);
            }
        );

        $invoice->save();
        $invoice->lines()->saveMany($invoiceDetails);

        return $invoice->load('lines');
    }

    public function listAll() {
        return Invoice::with(['customer','lines']);
    }

    public function listAllForCustomer(Customer $customer) {
        return Invoice::with(['customer','lines'])
            ->where('customer_id', $customer->id)
            ->get();
    }
}
