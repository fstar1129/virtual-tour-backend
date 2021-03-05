<?php

namespace App\Http\Requests;

use App\TourType;
use Illuminate\Foundation\Http\FormRequest;
use App\Tour;
use Illuminate\Validation\Rule;

class CreateTourRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => [
                'required',
                'string',
                'max:35',
                'min:3',
                Rule::unique('tours', 'title'),
            ],
            'description' => 'nullable|max:16000|min:100',
            'pricing_type' => [
                'required',
                Rule::in(Tour::$PRICING_TYPES),
            ],
            'type' => [
                'required',
                Rule::in(TourType::all()),
            ],
            'token_cost' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'local_token_cost' => 'nullable|numeric',
        ];

        if ($this->route()->getPrefix() == 'admin') {
            $rules['user_id'] = 'required|numeric|exists:clients,id';
        }

        return $rules;
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.unique' => 'A Tour with this name already exists.',
            'title.max' => 'Tour title must be less than 35 characters.',
            'title.min' => 'Tour title must be more than 3 characters.',
            'title.*' => 'Tour title is required.',
            'description.max' => 'Tour description is too long.',
            'description.min' => 'Tour description must be more than 100 characters.',
            'description.*' => 'Tour description is required',
            'pricing_type.*' => 'Tour pricing type must be selected.',
            'type.*' => 'Tour type must be selected.',
        ];
    }
}
