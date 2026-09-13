<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Update User Request',
    description: 'Request body for updating a user'
)]

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
        #[OA\Property(type: 'string')]
        public $f_name;

        #[OA\Property(type: 'string')]
        public $l_name;

        #[OA\Property(type: 'string', format: 'email')]
        public $email;

        #[OA\Property(
            type: 'string',
            format: 'password',
            writeOnly: true
        )]
        public $current_password;

        #[OA\Property(
            type: 'string',
            format: 'password',
            writeOnly: true
        )]
        public $password;

        #[OA\Property(
            type: 'string',
            format: 'password',
            writeOnly: true
        )]
        public $password_confirmation;

        #[OA\Property(type: 'integer')]
        public $role_id;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'f_name' => [
                'sometimes', 'string', 'max:255'
            ],
            'l_name' => [
                'sometimes', 'string', 'max:255'
            ],
            'email'  => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],          
            'current_password' => [
                'required_with:password',
                'current_password',
            ],

            'password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('roles', 'id'),
            ],
        ];
    }
}