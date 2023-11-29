<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;

class ParseLogsToXeroPayments extends Command
{
    protected $signature = 'parse:logs-to-xero-payments';

    protected $description = 'This command will review all logs and parse them to xero payments. It does this by looking for logs that have is_parse to 0 and the type payment.received';

    public function handle(OauthCredentialManager $xeroCredentials): void
    {

        $logs = \App\Models\Log::where('xero_log_parsed_to_payment', 0)
            ->where('platform', 'xero')
            ->where('type', 'payment.received')
            ->get();

        // now remove all the invoices that are not tied to a deal
        $resource_ids = [];
        foreach ($logs as $log) {
            $resource_ids[] = substr($log->payload, strpos($log->payload, 'resourceId') + 14, 36);
        }
        $invoices = \App\Models\Invoice::whereIn('xero_invoice_id', $resource_ids)->get();
        foreach ($logs as $log) {
            $invoice = $invoices->where('xero_invoice_id', substr($log->payload, strpos($log->payload, 'resourceId') + 14, 36))->first();
            if (is_null($invoice)) {
                $log->delete();
            }
        }

        $xero = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);

        foreach ($logs as $log)
        {
            // get all the information we need from the payload
            $resourceId = substr($log->payload, strpos($log->payload, 'resourceId') + 14, 36);
            $tenantId = substr($log->payload, strpos($log->payload, 'tenantId') + 12, 36);
            $eventCategory = substr($log->payload, strpos($log->payload, 'eventCategory') + 17, 7);
            $eventType = substr($log->payload, strpos($log->payload, 'eventType') + 13, 6);

            // if the event category is INVOICE and the event type is UPDATE, then get all the information needed about the invoice and save the payment
            if ($eventCategory == 'INVOICE' && $eventType == 'UPDATE') {

                $this->info('Parsing log with id: ' . $log->id);
                $this->info('Resource id: ' . $resourceId);
                $this->info('Tenant id: ' . $tenantId);
                $this->info('Event category: ' . $eventCategory);
                $this->info('Event type: ' . $eventType);

                // get the invoice by resource ID from Xero
                $xeroInvoice = $xero->getInvoice($xeroCredentials->getTenantId(), $resourceId);

                // remove all the protected properties and make them public
                $array = json_decode(json_encode($xeroInvoice), true);

                // get the payments in a nice array
                $payments = [];
                if (is_array($array) && array_key_exists('Payments', $array[0])) {
                    foreach ($array[0]['Payments'] as $payment) {
                        $payments[] = $payment;
                    }
                }

                // get the other details in a nice array
                $invoice_details = [];
                if (is_array($array) && array_key_exists('Contact', $array[0])) {
                    foreach ($array[0]['Contact'] as $details) {
                        $invoice_details[] = $details;
                    }
                }

                // get the invoice_id from the invoices tavke
                $invoice = \App\Models\Invoice::where('xero_invoice_id', $resourceId)->first();

                // now process it
                if (!is_null($payments) && count($payments) > 0) {
                    foreach ($payments as $p) {

                        // see if there is a resourceId already in the database for this payment
                        $payment = Payment::where('xero_payment_id', $p['PaymentID'])->first();

                        // if there is no payment, then create one - we dont want duplicate payments from the logs
                        if (is_null($payment)) {
                            $payment = new Payment();
                            $payment->resourceId = $resourceId;
                            $payment->tenantId = $tenantId;
                            $payment->amount = $p['Amount'];
                            $payment->xero_contact_id = $invoice_details[0];
                            $payment->xero_invoice_id = $resourceId;
                            $payment->client_email = $invoice_details[5];
                            $payment->log_id = $log->id;
                            $payment->invoice_id = $invoice->id;
                            $payment->xero_payment_id = $p['PaymentID'];
                            $payment->save();
                            $this->info('Created New Payment: ' . $payment->id);
                        }

                    }

                    // there was a payment made, so mark the deal with an updated status
                    $deal = \App\Models\Deal::where('id', $invoice->deal_id)->first();
                    $deal->status = 'Xero Payment Received';
                    $deal->save();

                }

                // now mark the log as pasrsed
                $log->xero_log_parsed_to_payment = 1;
                $log->save();
                $this->info('Log Parsed: ' . $log->id);
                echo "\n";

            }

        }
    }
}
