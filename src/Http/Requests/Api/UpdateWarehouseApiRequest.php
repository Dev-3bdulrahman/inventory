<?php

namespace Dev3bdulrahman\Inventory\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateWarehouseApiRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|nullable|string|max:50',
            'address' => 'sometimes|nullable|string',
            'branch_id' => 'sometimes|nullable|exists:branches,id',
            'status' => 'sometimes|nullable|in:active,inactive',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('inventory::inventory.validation_failed'),
                'data' => null,
                'meta' => [],
                'errors' => $validator->errors()->toArray(),
            ], 422)
        );
    }
}
