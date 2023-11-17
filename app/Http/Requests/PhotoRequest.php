<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class PhotoRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "tags"=>"required|max:255",
            "photo" => "required|image|mimes:png,jpg,jpeg,webp|max:500000",
            "privacy"=>"required"
        ];
    }
    public function attributes(){
        return [
            "tags"=>"Tag",
            "photo"=>"The Photo",
            "privacy"=>"Select Privacy",
        ];

    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
            
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
