<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ResellerRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Str;

/**
 * Class ResellerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ResellerCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Reseller::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/reseller');
        CRUD::setEntityNameStrings('reseller', 'resellers');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('first_name');
        CRUD::column('last_name');
        CRUD::column('email');
        CRUD::column('phone');
        CRUD::removeButton('show');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ResellerRequest::class);

        CRUD::field('first_name')->size(6, 6)->tab('Details');
        CRUD::field('last_name')->size(6, 6)->tab('Details');
        CRUD::field('email')->size(6, 6)->tab('Details');
        CRUD::field('phone')->size(6, 6)->tab('Details');
        CRUD::field('company_name')->size(6, 6)->label('Company Name')->tab('Details');
        CRUD::field('abn')->size(6, 6)->label('ABN')->tab('Details');
        CRUD::field('status')->size(12, 6)->type('select2_from_array')->options(['active' => 'Active', 'inactive' => 'Inactive'])->tab('Details');
        CRUD::field('additional_notes')->size(12, 6)->type('textarea')->label('Additional Notes')->tab('Details');
        CRUD::field('monthly_target_once_off')->size(6, 6)->label('Monthly - Once Off')->type('number')->default(0)->tab('Targets');
        CRUD::field('monthly_target_recurring')->size(6, 6)->label('Monthly - Recurring')->type('number')->default(0)->tab('Targets');

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
