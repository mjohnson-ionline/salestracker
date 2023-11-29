<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    public function store(Request $request)
    {
        // if the password is blank generate a random one
        if (empty($request->input('password'))) {
            $request->request->add(['password' => str_random(8)]);
        }

        // save it
        $user = $this->crud->create($request->all());

        // redirect back to the user list
        return $this->crud->performSaveAction($user->getKey());

    }

    public function update(Request $request)
    {
        $user = $this->crud->getCurrentEntry();
        $data = $this->crud->getStrippedSaveRequest($request);

        // if the password field is empty, remove it from the request so it doesn't get updated
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        // save
        $this->crud->update($user->getKey(), $data);

        // redirect
        return $this->crud->performSaveAction($user->getKey());
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        // add a filter for roles
        CRUD::addFilter([
            'type' => 'select2',
            'name' => 'role',
            'label' => 'Role',
        ], config('app.roles'), function ($value) { // if the filter is active
            $this->crud->addClause('where', 'role', $value);
        });

        CRUD::column('role')->label('Role')->type('select_from_array')->options(config('app.roles'));

        CRUD::column('organisation_name')->label('Company');

        CRUD::column('first_name')->label('First Name');

        CRUD::column('last_name')->label('Last Name');

        CRUD::column('email')->label('Email Address');

        CRUD::removeButton('show');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupCreateOperation()
    {

        CRUD::field('custom_html_title_0')
            ->type('custom_html')
            ->value('<h4 style="margin-bottom:0;padding-bottom:0;">All User Common Fields</h4>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('organisation_name')
            ->label('Company')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('first_name', 'text')
            ->label('First Name')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('last_name')
            ->label('Last Name')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('phone')
            ->label('Best Contact Number')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('email')
            ->label('Email Address')
            ->type('email')
            ->attributes(['placeholder' => 'Enter email, user@domain.com'])
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('password')
            ->label('Password')
            ->type('password')
            ->attributes(['placeholder' => 'Leave blank to keep current password'])
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::addField([
            'name' => 'role',
            'label' => 'Role',
            'type' => 'select2_from_array',
            'options' => config('app.roles'),
            'allows_null' => false,
            'wrapper' => ['class' => 'form-group col-md-4'],
        ]);

        CRUD::field('custom_html_sep_1')
            ->type('custom_html')
            ->value('<hr>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('custom_html_title_1')
            ->type('custom_html')
            ->value('<h4 style="margin-bottom:0;padding-bottom:0;">Reseller Specific Fields</h4>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('discount_comission')
            ->label('Reseller Global Comission (Default 10%)')
            ->type('number')
            ->attributes(['placeholder' => 'Enter a number between 0 and 100'])
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('custom_html_sep_2')
            ->type('custom_html')
            ->value('<hr>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('custom_html_title_2')
            ->type('custom_html')
            ->value('<h4 style="margin-bottom:0;padding-bottom:0;">Client Specific Fields</h4>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('accounts_email')
            ->label('Accounts Email')
            ->type('email')
            ->attributes(['placeholder' => 'Enter email, user@domain.com'])
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('custom_html_sep_3')
            ->type('custom_html')
            ->value('<hr>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('custom_html_title_3')
            ->type('custom_html')
            ->value('<h4 style="margin-bottom:0;padding-bottom:0;">Sales Specific Fields</h4>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::field('quarterly_sales')
            ->label('Quarterly Sales Target')
            ->type('number')
            ->attributes(['placeholder' => 'Enter a whole dollar value, ie, 1000'])
            ->prefix('$')
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('custom_html_sep_4')
            ->type('custom_html')
            ->value('<hr>')
            ->wrapper(['class' => 'form-group col-md-12']);

        CRUD::addSaveAction([
            'name' => 'save_action_one',
            'button_text' => 'Save',
            'visible' => function ($crud) {
                return true;
            },
            'referrer_url' => function ($crud, $request, $itemId) {
                return $crud->route;
            },
            'order' => 1,
        ]);

        CRUD::removeSaveActions(['save_and_back', 'save_and_edit', 'save_and_new', 'save_and_preview']);

    }
}
