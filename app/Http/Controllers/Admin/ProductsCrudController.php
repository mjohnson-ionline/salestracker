<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProductsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ProductsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProductsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Products::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/products');
        CRUD::setEntityNameStrings('products', 'products');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('xero_code');
        CRUD::column('percentage');

        CRUD::removeButton('show');
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
        CRUD::setValidation(ProductsRequest::class);

        $this->crud->addField([  // Select
            'label' => 'Product Name',
            'name' => 'name',
            'type' => 'text',
            'allows_null' => true,
            'hint' => 'What is the product name?',
            'attributes' => [
                'readonly' => 'readonly',
            ],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4'
            ]
        ]);

        $this->crud->addField([  // Select
            'label' => 'Xero Code',
            'name' => 'xero_code',
            'type' => 'text',
            'allows_null' => true,
            'hint' => 'Enter the Xero Code for this product',
            'attributes' => [
                'readonly' => 'readonly',
            ],
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4'
            ]
        ]);
        $this->crud->addField([  // Select
            'label' => 'Percentage to Reseller',
            'name' => 'percentage',
            'type' => 'number',
            'allows_null' => true,
            'hint' => 'Enter a number between 0 and 100',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-4'
            ]
        ]);
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
