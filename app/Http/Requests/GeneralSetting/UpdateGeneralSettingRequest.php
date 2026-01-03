<?php

namespace App\Http\Requests\GeneralSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'logo' => [
                'image',
                'max:2048',
               
            ],
            'favicon' => [
                'image',
                'max:2048',
                // 'dimensions:width=32,height=32',
            ],
            'dark_logo' => [
                'image',
                'max:2048',
                
            ],
            'guest_logo' => [
                'image',
                'max:2048',
                // 'dimensions:width=122,height=32',
            ],
            'guest_background' => [
                'image',
                'max:2048',
                // 'dimensions:width=580,height=501',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // 'favicon.dimensions' => 'The favicon must be 32x32 pixels.',
            // 'guest_logo.dimensions' => 'The guest logo must be 122x32 pixels.',
            // 'guest_background.dimensions' => 'The guest background must be 580x501 pixels.',
        ];
    }
}
