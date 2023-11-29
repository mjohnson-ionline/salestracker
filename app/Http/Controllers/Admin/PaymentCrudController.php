<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentRequest;
use App\Models\Deal;
use App\Models\Invoice;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PaymentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaymentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Payment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/payment');
        CRUD::setEntityNameStrings('payment', 'payments');
    }

    protected function setupListOperation()
    {
        // created_at
        CRUD::addColumn([
            'name' => 'created_at',
            'label' => 'Date',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'closure',
            'prefix' => '$',
            'function' => function ($entry) {
                return number_format($entry->amount, 2);

            },
        ]);

        CRUD::addColumn([
            'name' => 'client',
            'label' => 'Client Name',
            'type' => 'closure',
            'function' => function ($entry) {
                $invoice = Invoice::where('xero_invoice_id', $entry->xero_invoice_id)->with('deal', 'deal.user')->first();
                return $invoice->user->first_name . ' ' . $invoice->user->last_name;
            },
        ]);

        CRUD::addColumn([
            'name' => 'client',
            'label' => 'Client Company',
            'type' => 'closure',
            'function' => function ($entry) {
                $invoice = Invoice::where('xero_invoice_id', $entry->xero_invoice_id)->with('deal', 'deal.user')->first();
                return $invoice->user->organisation_name;
            },
        ]);

        CRUD::addColumn([
            'name' => 'deal',
            'label' => 'Deal',
            'type' => 'closure',
            'function' => function ($entry) {
                $invoice = Invoice::where('xero_invoice_id', $entry->xero_invoice_id)->with('deal')->first();
                return $invoice->deal->name;
            },
        ]);

        CRUD::removeButton('show');
        CRUD::removeButton('create');
        CRUD::removeButton('delete');
        CRUD::removeButton('update');
        CRUD::denyAccess('create');
    }

    protected function setupCreateOperation()
    {

        // amount
        CRUD::addField([
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'number',
            'prefix' => '$',
            'attributes' => [
                'step' => '0.01',
            ],
        ]);

    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
