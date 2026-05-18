<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:200', 'unique:usuarios'],
            'clave' => ['required', 'string', 'min:8'],
            'clave_confirmation' => ['required', 'string', 'same:clave'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nombres.required' => 'El campo nombres es requerido.',
            'apellidos.required' => 'El campo apellidos es requerido.',
            'email.required' => 'El campo correo es requerido.',
            'email.email' => 'El campo correo debe ser una dirección de correo válida.',
            'email.unique' => 'Este correo ya ha sido registrado.',
            'clave.required' => 'El campo contraseña es requerido.',
            'clave.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'clave_confirmation.required' => 'La confirmación de la contraseña es requerida.',
            'clave_confirmation.same' => 'La confirmación de la contraseña no coincide.',
        ];
    }
}
