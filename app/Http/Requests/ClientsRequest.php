<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientsRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		// only allow updates if the user is logged in
		return backpack_auth()->check();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'first_name' => 'required|string',
			'last_name' => 'required|string',
			'phone' => 'required|string',
			'email' => 'required|email|unique:clients,email,' . $this->id . ',id',
			'nimble_crm_id' => '',
			'created_by' => 'required|integer|exists:users,id',
			'company_name' => 'required',
		];
	}

	/**
	 * Get the validation attributes that apply to the request.
	 *
	 * @return array
	 */
	public function attributes()
	{
		return [
			//
		];
	}

	/**
	 * Get the validation messages that apply to the request.
	 *
	 * @return array
	 */
	public function messages()
	{
		return [
			//
		];
	}
}
