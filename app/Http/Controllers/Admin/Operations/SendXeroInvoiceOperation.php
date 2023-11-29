<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Webfox\Xero\OauthCredentialManager;
use XeroAPI\XeroPHP\AccountingObjectSerializer;
use XeroAPI\XeroPHP\PayrollAuObjectSerializer;
use XeroAPI\XeroPHP\FinanceObjectSerializer;

trait SendXeroInvoiceOperation
{
    /**
     * Method to handle the GET request and display the View with a Backpack form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function sendXeroInvoice(int $id)
    {
        $this->crud->hasAccessOrFail('sendXeroInvoice');

        $invoice = Invoice::where('id', $id)->with('deal', 'lineItems', 'user', 'deal.reseller')->first();

        return view("crud::operations.send_xero_invoice", compact('invoice'));
    }

    /**
     * Method to handle the POST request and perform the operation
     *
     * @param int $id
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function sendXeroInvoiceAction(int $id, OauthCredentialManager $xeroCredentials)
    {
        // setup the crud, get all the details for the invoicing
        $this->crud->hasAccessOrFail('sendXeroInvoice');
        $entry = $this->crud->getEntry($id);
        $invoice = Invoice::where('id', $id)->with('deal', 'lineItems', 'user', 'deal.reseller')->first();

        // determine which email to use
        $email_to_use = !is_null($invoice->user->accounts_email) ? $invoice->user->accounts_email : $invoice->user->email;

        \Log::error("Email to use: " . $email_to_use);

        // create a connection to xero
        try {
            $xero = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
        } catch (\Exception $e) {
            \Log::error("General exception: " . $e->getMessage());
            \Alert::add('danger', 'Could not connect to Xero. Please try again later.')->flash();
            return \Redirect::to($this->crud->route);
        }

        // if the xero_id is null, then we need to find or create that user in xero and store the details
        if (is_null($invoice->user->xero_id)) {
            try {
                $contacts = $xero->getContacts($xeroCredentials->getTenantId(), null, null, null, null, null, null, null, $email_to_use);
            } catch (\Exception $e) {
                // write this exception to the log
                \Log::error("General exception: " . $e->getMessage());
                \Alert::add('danger', 'Could not get all contacts in Xero.')->flash();
                return \Redirect::to($this->crud->route);
            }
            if (count($contacts) > 0) {
                $invoice->user->xero_id = $contacts[0]->getContactID();
            } else {
                try {
                    $xero_org_object = $xero->createContacts($xeroCredentials->getTenantId(), [
                        'Contacts' => [
                            [
                                'Name' => $invoice->user->organisation_name,
                                'FirstName' => $invoice->user->first_name,
                                'LastName' => $invoice->user->last_name,
                                'EmailAddress' => $email_to_use,
                            ]
                        ]
                    ]);
                } catch (\Exception $e) {
                    \Log::error("General exception: " . $e->getMessage());
                    return \Redirect::to($this->crud->route);
                }
                $invoice->user->xero_id = $xero_org_object->getContacts()[0]->getContactID();
            }
            $invoice->user->save();
        }

        // generate a LineItems array from all the LineItems attached to our local invocie
        $lineItems = [];
        foreach ($invoice->lineItems as $lineItem) {
            $lineItems[] = [
                'Description' => $lineItem->name,
                'Quantity' => $lineItem->quantity,
                'UnitAmount' => $lineItem->amount,
                'AccountCode' => $lineItem->code,
            ];
        }

        // now generate a new invoice with the line items from the invoice, then save it against the invoice model
        try {
            $xero_invoice_object = $xero->createInvoices($xeroCredentials->getTenantId(), [
                'Invoices' => [
                    [
                        'Type' => 'ACCREC',
                        'Contact' => [
                            'ContactID' => $invoice->user->xero_id,
                        ],
                        'Date' => date('Y-m-d'),
                        'DueDate' => date('Y-m-d', strtotime('+7 days')),
                        'LineItems' => $lineItems,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error("General exception: " . $e->getMessage());
            \Alert::add('danger', 'Could not create invoice in Xero. ')->flash();
            return \Redirect::to($this->crud->route);
        }
        $invoice->xero_invoice_id = $xero_invoice_object->getInvoices()[0]->getInvoiceID();
        $invoice->date_sent = date('Y-m-d');
        $invoice->save();

        // now update the deal with a status
        $invoice->deal->status = 'Sent Xero Invoice - ' . $invoice->name;
        $invoice->deal->save();

        // message and redirect
        $inspirationalMessages = [
            "May your proposals be as convincing as a toddler wanting a cookie. Here's to sweet success!",
            "Go forth and conquer those deals like it's Black Friday and you're the last flat-screen TV!",
            "Remember, every 'no' is just a 'yes' in a grumpy disguise. Keep smiling, keep dialing!",
            "If sales were easy, it would be called 'your order is ready'. You've got this!",
            "Think of every proposal as a love letter to your commission. Seal it with a 'ka-ching'!",
            "You're not just selling a product, you're selling a new best friend to your client. Play matchmaker!",
            'Be like coffee: strong, invigorating, and able to close any deal at 8 am.',
            'Sales is like fishing; use your best lure and reel in that big contract!',
            'Your sales pitch should be like a good haircut: smooth, stylish, and impossible to ignore.',
            "Knock on the door of opportunity with your proposal – don't worry, it's a push door.",
            "Treat objections like Sudoku puzzles – they may be tricky, but there's always a solution.",
            "Your enthusiasm is contagious – let's hope your clients catch the success bug!",
            "Don't just chase your dreams; send them a well-crafted proposal and close the deal!",
            'Be like a squirrel with a winter stash – always hungry for that next big sale.',
            "Sell like you're telling a secret; make your client feel they're in on the best deal ever!",
            'Aim to be the superhero of sales: swooping in to solve problems with your amazing proposals!',
            "Remember, you're not just selling a product, you're providing a ticket to the 'better life' lottery.",
            'Your proposal should be like a good meme – unforgettable and shareable. Go viral!',
            'Imagine your proposal is a pizza – everybody wants a slice of that success pie.',
            "Let's get that bread – or better yet, let's sell that bread-making machine!"
        ];
        \Alert::add('success', $inspirationalMessages[array_rand($inspirationalMessages)])->flash();
        return \Redirect::to($this->crud->route);
    }

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupSendXeroInvoiceRoutes(string $segment, string $routeName, string $controller): void
    {
        Route::get($segment . '/{id}/send-xero-invoice', [
            'as' => $routeName . '.sendXeroInvoice',
            'uses' => $controller . '@sendXeroInvoice',
            'operation' => 'sendXeroInvoice',
        ]);
        Route::post($segment . '/{id}/send-xero-invoice', [
            'as' => $routeName . '.sendXeroInvoiceAction',
            'uses' => $controller . '@sendXeroInvoiceAction',
            'operation' => 'sendXeroInvoice',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupSendXeroInvoiceDefaults(): void
    {
        // Access
        $this->crud->allowAccess('sendXeroInvoice');

        // Config
        $this->crud->operation('sendXeroInvoice', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        // Button
        $this->crud->operation('list', function () {
            $this->crud->addButton('line', 'sendXeroInvoice', 'view', 'crud::buttons.send_xero_invoice', 'end');
        });
    }
}
