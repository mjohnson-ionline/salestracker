<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LineItemRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LineItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LineItemCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\LineItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/line-item');
        CRUD::setEntityNameStrings('line item', 'line items');
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
        $filter_id = request()->get('invoice_id');
        if ($filter_id) {
            CRUD::addClause('where', 'invoice_id', $filter_id);
        }

        CRUD::column('invoice_id')
            ->type('text');
        CRUD::column('name')
            ->type('text')
            ->limit(100);
        CRUD::column('code')
            ->type('text');
        CRUD::column('amount')
            ->type('currency')
            ->label('Amount')
            ->prefix('$');
        CRUD::column('quantity')
            ->type('text');

        CRUD::removeButton('show');
        CRUD::removeButton('update');
        CRUD::removeButton('delete');
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
        CRUD::setValidation(LineItemRequest::class);

        CRUD::field('invoice_id');
        CRUD::field('product_pipedrive_id');
        CRUD::field('name');
        CRUD::field('code');
        CRUD::field('amount');
        CRUD::field('quantity');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
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
