<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => trim((string) $this->input('email'))]);
        }
    }

    /**
     * Add conditional validation checks.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $user = $this->user();
            $emailChanged = $this->filled('email') && $this->input('email') !== ($user->email ?? null);
            $passwordProvided = $this->filled('password');

            if (($emailChanged || $passwordProvided) && ! $this->filled('current_password')) {
                $v->errors()->add('current_password', 'Masukkan password saat ini untuk mengubah email atau password.');
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'foto' => ['nullable', 'image', 'max:2048'], // maks 2MB

            // Validasi password: optional, tapi jika diberikan harus dikonfirmasi
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // current_password is validated by Laravel's built-in rule when present
            'current_password' => ['nullable', 'string', 'current_password'],
        ];
    }
}