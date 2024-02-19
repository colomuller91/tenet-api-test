<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\ServiceConsumption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
     * @param $consumptionsFrom
     * @return Invoice
     */
    public function createInvoice(Customer $customer, $consumptionsFrom): Invoice {

        //Find not billed consumptions
        $consumptions = $customer->serviceConsumptions()
            ->with('service')
            ->where('created_at', '>=', now()->subDays($consumptionsFrom))
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

        //Generate invoice details
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

    /**
     * @return Collection
     */
    public function listAll(): Collection {
        return Invoice::with(['customer','lines'])->get();
    }

    /**
     * @param Customer $customer
     * @return Collection
     */
    public function listAllForCustomer(Customer $customer): Collection {
        return Invoice::with(['customer','lines'])
            ->where('customer_id', $customer->id)
            ->get();
    }
}
