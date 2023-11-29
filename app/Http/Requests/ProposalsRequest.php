<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProposalsRequest extends FormRequest
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
			'title' => 'required|string',
			'dollar_amount' => 'required|numeric',
			'client_id' => 'required|integer|exists:clients,id',
			'canva_link' => 'required|string',
			'notes' => '',
            'status' => '',
			'tcuploads' => 'required',
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
