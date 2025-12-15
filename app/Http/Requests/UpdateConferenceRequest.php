<?php

namespace App\Http\Requests;

use App\Models\Conference;
use Illuminate\Foundation\Http\FormRequest;

class UpdateConferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {   
        $conference = Conference::find($this->route('id'));

        if (!$conference) {
            return false;
        }
        return $this->user()->can('update', $conference);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|string|max:5000',
            'date' => 'required|date|after_or_equal:today',
        ];
    }
}
