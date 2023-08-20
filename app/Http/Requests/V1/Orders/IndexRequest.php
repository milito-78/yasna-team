<?php

namespace App\Http\Requests\V1\Orders;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "per_page"  => "sometimes|nullable|integer|gte:5",
            "page"      => "sometimes|nullable|integer|gte:1",
            "status"    => "sometimes|nullable|string",
            "date"      => "sometimes|nullable|string|date_format:Y-m-d"
        ];
    }
}
