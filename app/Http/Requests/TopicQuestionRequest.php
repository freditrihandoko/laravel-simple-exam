<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopicQuestionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,essay',
            'answers' => 'required_if:question_type,multiple_choice|array',
            'answers.*.text' => 'required_if:question_type,multiple_choice',
            'answers.*.correct' => 'nullable|integer',
        ];
    }
}
