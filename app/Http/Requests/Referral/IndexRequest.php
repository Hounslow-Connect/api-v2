<?php

namespace App\Http\Requests\Referral;

use App\Http\Requests\QueryBuilderUtilities;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use QueryBuilderUtilities;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !($this->user()->isGlobalAdmin() && !$this->user()->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
