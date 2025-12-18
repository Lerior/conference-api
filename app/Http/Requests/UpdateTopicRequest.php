<?php

namespace App\Http\Requests;

use App\Models\Topic;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTopicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $topic = Topic::find($this->route('id'));
        
        if (!$topic){
            return false;
        }

        return $this->user()->can('update', $topic);
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
            'conference_id' => 'required|exists:conferences,id',
            'speaker_name' => 'required_without:user_id|string|max:120|nullable',
            'user_id' => 'required_without:speaker_name|exists:users,id|nullable',
        ];
    }
}
