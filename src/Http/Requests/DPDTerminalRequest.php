<?php

declare(strict_types=1);

namespace SergeevPasha\DPD\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DPDTerminalRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'bounds' => ['required', 'string'],
            'city' => ['required', 'string'],
        ];
    }
}
