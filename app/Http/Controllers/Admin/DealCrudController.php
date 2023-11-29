<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DealRequest;
use App\Models\Deal;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class DealCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class DealCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Deal::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/deal');
        CRUD::setEntityNameStrings('deal', 'deals');
    }

    protected function setupListOperation()
    {
        CRUD::column('created_at');
        CRUD::column('name')->label('Name');
        CRUD::column('amount')->label('Amount');
        CRUD::addColumn([
            'name' => 'user_id',
            'label' => 'Client',
            'type' => 'select',
            'entity' => 'user',
            'attribute' => 'organisation_name',
            'model' => 'App\Models\User',
        ]);
        CRUD::addColumn([
            'name' => 'reseller_id',
            'label' => 'Reseller',
            'type'  => 'closure',
            'function' => function($entry) {
                $reseller = \App\Models\User::find($entry->reseller_id);
                return $reseller->organisation_name;
            }
        ]);
        CRUD::column('status')->label('Status');

        CRUD::removeButton('show');
        CRUD::removeButton('create');
        CRUD::removeButton('delete');
        CRUD::denyAccess('create');
        CRUD::addButtonFromView('line', 'view_invoices', 'view_invoices', 'end');

    }

    protected function setupCreateOperation()
    {

        // dd the current entry
        // $deal = Deal::where('id', $this->crud->getCurrentEntry()->id)->with('reseller')->first();
        // dd($deal);

        CRUD::setValidation([
            'name' => 'required',
            'amount' => 'required',
            'user_id' => 'required',
            'reseller_id' => '',
            'status' => 'required',
        ]);

        CRUD::field('name')
            ->label('Deal Name')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('amount')
            ->label('Deal Name')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('user_id')
            ->type('select2')
            ->label('Client')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->attribute('organisation_name');

        CRUD::field('reseller_id')
            ->type('select2')
            ->label('Reseller')
            ->model('App\Models\User')
            ->entity('reseller')
            ->placeholder('Select Reseller')
            ->attribute('organisation_name')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('status')
            ->label('Deal Status')
            ->wrapper(['class' => 'form-group col-md-6 disabled'])
            ->attribute('disabled', 'disabled');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
