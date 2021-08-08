<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueueImportPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'users' => [
                'required',
                'file',
                'mimetypes:'
                . 'application/vnd.openxmlformats-officedocument.spread' . ','
                . 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        ];
    }
}
