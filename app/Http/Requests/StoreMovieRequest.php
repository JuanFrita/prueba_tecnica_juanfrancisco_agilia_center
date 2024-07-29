<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $userId = $this->input('user_id');
        return $this->user()->id === $userId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'release_year' => 'required|date_format:Y',
            'cover' => 'nullable|url',
            'user_id' => 'required|integer|exists:users,id',
            'categories' => 'required|array|min:1|max:4',
            'categories.*' => 'required|integer|exists:categories,id',
        ];
    }
}
