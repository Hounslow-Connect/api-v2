<?php

namespace App\Http\Requests\TaxonomyCategory;

use App\Models\Taxonomy;
use App\Rules\RootTaxonomyIs;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->isSuperAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $parentId = $this->parent_id ?? Taxonomy::category()->id;
        $siblingTaxonomiesCount = Taxonomy::where('parent_id', $parentId)->count() + 1;

        return [
            'parent_id' => ['present', 'nullable', 'exists:taxonomies,id', new RootTaxonomyIs(Taxonomy::NAME_CATEGORY)],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'order' => ['required', 'integer', 'min:1', "max:$siblingTaxonomiesCount"],
        ];
    }
}
