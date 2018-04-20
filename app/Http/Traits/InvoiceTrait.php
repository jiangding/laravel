<?php

namespace App\Http\Traits;
use Log;

use App\Repositories\InvoiceRepository;
use App\Transformers\InvoiceTransformer;
use App\Validators\InvoiceValidator;


trait InvoiceTrait
{

    public function getUserInvoice($userid,InvoiceRepository $invoiceRepository,InvoiceTransformer $invoiceTransformer)
    {
        return $invoiceRepository->findWhere(array(
            'userid' => $userid
        ));
    }
}