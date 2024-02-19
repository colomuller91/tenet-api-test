<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceConsumptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from_date' => 'date|before:to_date',
            'to_date' => 'date|after:from_date',
            'customer_id' => [
                'exists:App\Models\Customer,id',
            ],
            'quantity' => 'numeric|gt:0'
        ];
    }


}
