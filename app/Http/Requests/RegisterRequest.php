<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class RegisterRequest extends FormRequest
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
            "name"=>"required|max:50",
            "username"=>"required|max:50|unique:users,username",
            "email"=>"required|max:50|unique:users,email|email",
            "password"=>"required|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/",
            "confirm_password"=>"required|same:password",
        ];
    }

    public function attributes(){
        return [
            "name"=>"Name",
            "photo"=>"Photo",
            "password"=>"Password",
            "confirm_password"=>"Confirm Password",
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
