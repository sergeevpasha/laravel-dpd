<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DPDCalculatePriceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules()
    {
        return [
            'arrival_city_id' => ['required', 'string'],
            'derival_city_id' => ['required', 'string'],
            'arrival_terminal' => ['required', 'boolean'],
            'derival_terminal' => ['required', 'boolean'],
            'parcel_total_weight' => ['required', 'numeric'],
            'parcel_total_volume' => ['sometimes', 'required', 'numeric'],
            'parcel_total_value' => ['sometimes', 'required', 'numeric'],
            'services' => ['sometimes', 'required', 'array'],
            'services.*' => ['sometimes', 'required', 'string'],
            'pickup_date' => ['sometimes', 'required', 'string'],
            'max_delivery_days' => ['sometimes', 'required', 'string'],
            'max_delivery_price' => ['sometimes', 'required', 'string']
        ];
    }
}
