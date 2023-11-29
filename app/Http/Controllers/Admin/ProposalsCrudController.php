<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\SendOperation;
use App\Http\Requests\ProposalsRequest;
use App\Models\Clients;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;


/**
 * Class ProposalsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProposalsCrudController extends CrudController
{
	use SendOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
	use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
		store as traitStore;
	}


	/**
	 * Configure the CrudPanel object. Apply settings to all operations.
	 *
	 * @return void
	 */
	public function setup()
	{
		CRUD::setModel(\App\Models\Proposals::class);
		CRUD::setRoute(config('backpack.base.route_prefix') . '/proposals');
		CRUD::setEntityNameStrings('proposals', 'proposals');
	}

	/**
	 * Define what happens when the List operation is loaded.
	 *
	 * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
	 * @return void
	 */
	protected function setupListOperation()
	{
		CRUD::column('client')
			->label('Client Name');

		CRUD::column('title')
			->label('Title');

		CRUD::column('status')
			->label('Status');

		CRUD::column('canva_link')
			->label('Presentation Link');

		CRUD::column('external_link')
			->label('e-Signature Link');

		CRUD::removeButton('show');

		CRUD::addButton('line', 'send', 'view', 'crud::buttons.send', 'beginning');

	}

	/**
	 * Define what happens when the Create operation is loaded.
	 *
	 * @see https://backpackforlaravel.com/docs/crud-operation-create
	 * @return void
	 */
	protected function setupCreateOperation()
	{
		CRUD::setValidation(ProposalsRequest::class);

		CRUD::field('title')
			->label('Proposal Title')
			->wrapper(['class' => 'form-group col-md-12']);

		// only save a token if none exist
		CRUD::field('token')
			->type("hidden")
			->value(md5(uniqid(rand(), true)));

		CRUD::field('dollar_amount')
			->type("number")
			->label("Dollar Amount (ex Gst)")
			->prefix("$")
			->wrapper(['class' => 'form-group col-md-6']);

		CRUD::field('client_id')
			->label('Client')
			->type('select')
			->name('client_id')
			->entity('client')
			->attribute('company_name')
			->wrapper(['class' => 'form-group col-md-6']);

		CRUD::field('sent_at')
			->type("hidden")
			->value(date("Y-m-d H:i:s"));

		CRUD::field('status')
			->type('hidden')
			->value('Unsigned');

		CRUD::field('canva_link')
			->label('Presentational Sales Deck')
			->wrapper(['class' => 'form-group col-md-6']);

        /*$string = '<a href="' . config('app.url') . '/storage/' . $this->crud->entry->tcuploads . '" target="_blank">Download T&C</a>';
        CRUD::field('file_url_field')
            ->type('hidden')
            ->value($string);*/

		// https://backpackforlaravel.com/docs/5.x/crud-fields#upload-1
		CRUD::field('tcuploads')
			->type('upload')
			->label('Contract / Terms & Conditions PDF')
			->upload(true)
			->wrapper(['class' => 'form-group col-md-6']);

		if (!is_null($this->crud->entry)) {
			CRUD::field('external_link')
				->label('e-Signature Link')
				->type('text')
				->value(config('app.url') . '/sign/' . $this->crud->entry->id . '/' . $this->crud->entry->token)
				->attributes(['readonly' => 'readonly'])
				->suffix('<a href="#" id="copy_to_clip">Copy</a>')
				->wrapper(['class' => 'form-group col-md-12']);
		}

		CRUD::field('notes')
			->type('textarea')
			->label('Private Admin Notes');

		CRUD::removeSaveActions(['save_and_back', 'save_and_new', 'save_and_preview']);

	}

	/**
	 * Define what happens when the Update operation is loaded.
	 *
	 * @see https://backpackforlaravel.com/docs/crud-operation-update
	 * @return void
	 */
	protected function setupUpdateOperation()
	{

		// js to disable the fields if the proposal is signed
		if (!is_null($this->crud->getCurrentEntry()->signed_at)) {
			Widget::add()->type('script')->content('js/admin/proposals.js');
		}

		$this->SetupCreateOperation();

		// if it is signed, then display the additional fields
		if (!is_null($this->crud->getCurrentEntry()->signed_at)) {

			$client = Clients::where('id', $this->crud->getCurrentEntry()->client_id)->first();
			CRUD::field('customer_name')
				->type('custom_html')
				->wrapper(['class' => 'form-group col-md-12'])
				->value('<label>Signed By</label><br>' . $client->first_name . ' ' . $client->last_name);

			$image = json_decode($this->crud->getCurrentEntry()->image);
			CRUD::field('image')
				->type('custom_html')
				->wrapper(['class' => 'form-group col-md-12'])
				->value('<label>Signature</label><br><img src="data:image/svg+xml;base64,'.$image[1].'" style="width: 100%; height: auto; max-width: 300px; max-height: 300px;" />');

			CRUD::field('sign_at')
				->type('custom_html')
				->wrapper(['class' => 'form-group col-md-6'])
				->value('<label>Signed At</label><br>' . date('m/d/Y h:i A', $this->crud->getCurrentEntry()->sign_at));

			CRUD::field('signed_ip')
				->type('custom_html')
				->wrapper(['class' => 'form-group col-md-6'])
				->value('<label>Signed IP Address</label><br>' . $this->crud->getCurrentEntry()->signed_ip);
		}

	}

}
