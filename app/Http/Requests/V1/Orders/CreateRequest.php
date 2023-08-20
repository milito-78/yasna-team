<?php

namespace App\Http\Requests\V1\Orders;

use App\Models\Enums\PaymentGatewayEnum;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "items"         => "required|array",
            "items.*.id"    => "required|integer|gt:0",
            "items.*.count" => "required|integer|gt:0",
            "gateway"       => "required|string|in:" . PaymentGatewayEnum::validationNames(),
        ];
    }
}
