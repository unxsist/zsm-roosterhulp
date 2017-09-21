<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoosterUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rooster-file' => 'required|file'
        ];
    }

    public function messages()
    {
        return [
            'rooster-file.required' => 'Selecteer eerst een roosterbestand',
            'rooster-file.file' => 'Geuploade data bevat geen bestand',
        ];
    }
}
