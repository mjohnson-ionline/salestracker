<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class InvoiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\SendXeroInvoiceOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Invoice::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invoice');
        CRUD::setEntityNameStrings('invoice', 'invoices');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        // filter
        $filter_id = request()->get('deal_id');
        if ($filter_id) {
            CRUD::addClause('where', 'deal_id', $filter_id);
        }

        // add the name, dont truncate
        CRUD::addColumn([
            'name' => 'name',
            'label' => 'Name',
            'type' => 'text',
            'limit' => 100,
        ]);

        // add a column called status
        CRUD::addColumn([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'text',
            'value' => function ($entry) {
                if (isset($entry->xero_invoice_id)) {
                    return 'Sent';
                } else {
                    return 'Unsent';
                }
            },
            'wrapper' => [
                'element' => 'span',
                'style' => function ($crud, $column, $entry, $related_key) {
                    if (isset($entry->xero_invoice_id)) {
                        return 'background-color:#0A5230;color:white;padding:5px;padding-left:10px;padding-right:10px;border-radius:5px;';
                    } else {
                        return 'background-color:#002B5A;color:white;padding:5px;padding-left:10px;padding-right:10px;border-radius:5px;';
                    }
                },
            ],
        ]);

        // add date_sent
        CRUD::addColumn([
            'name' => 'date_sent',
            'label' => 'Date Sent',
            'type' => 'date',
        ]);

        // add the deal
        CRUD::addColumn([
            'name' => 'deal_id',
            'label' => 'Deal',
            'type' => 'select',
            'entity' => 'deal',
            'attribute' => 'name',
            'model' => 'App\Models\Deal',
        ]);

        // add amount but it is a closesure
        CRUD::addColumn([
            'name' => 'amount',
            'label' => 'Amount',
            'type' => 'closure',
            'prefix' => '$',
            'function' => function ($entry) {
                $invoice = Invoice::where('id', $entry->id)->with('lineItems')->first();
                $total = 0;
                foreach ($invoice->lineItems as $lineItem) {
                    $total += $lineItem->amount;
                }
                return number_format($total, 2);

            },
        ]);

        CRUD::addButtonFromView('line', 'view_line_items', 'view_line_items', 'end');
        // CRUD::addButtonFromView('line', 'send_invoice', 'send_invoice', 'end');

        // hide the buttons
        CRUD::removeButton('show');
        CRUD::removeButton('delete');
        CRUD::removeButton('update');
        CRUD::denyAccess('create');

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {

    }

    // https://backpackforlaravel.com/docs/6.x/crud-operation-show
    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
