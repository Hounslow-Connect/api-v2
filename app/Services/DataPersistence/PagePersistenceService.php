<?php

namespace App\Services\DataPersistence;

use App\Contracts\DataPersistenceService;
use App\Generators\UniqueSlugGenerator;
use App\Models\Model;
use App\Models\Page;
use App\Models\UpdateRequest as UpdateRequestModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PagePersistenceService implements DataPersistenceService
{
    use ResizesImages;

    /**
     * Unique Slug Generator.
     *
     * @var \App\Generators\UniqueSlugGenerator
     */
    protected $slugGenerator;

    public function __construct(UniqueSlugGenerator $slugGenerator)
    {
        $this->slugGenerator = $slugGenerator;
    }

    /**
     * Store the model.
     *
     * @return \App\Models\UpdateRequest|\App\Models\OrganisationEvent
     */
    public function store(FormRequest $request)
    {
        return $request->user()->isSuperAdmin()
        ? $this->processAsNewEntity($request)
        : $this->processAsUpdateRequest($request, null);
    }

    /**
     * Update the model.
     *
     * @return \App\Models\UpdateRequest
     */
    public function update(FormRequest $request, Model $model)
    {
        return $this->processAsUpdateRequest($request, $model);
    }

    /**
     * Create a new model from the provided request.
     *
     * @param  Illuminate\Foundation\Http\FormRequest  $request
     * @return \App\Models\Page
     */
    public function processAsNewEntity(FormRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Create the Page.
            $parent = $request->filled('parent_id') ? Page::find($request->parent_id) : null;

            $page = Page::make(
                [
                    'title' => $request->input('title'),
                    'slug' => $this->slugGenerator->generate($request->input('slug', $request->input('title')), 'pages'),
                    'excerpt' => $request->input('excerpt'),
                    'content' => $request->input('content', []),
                    'page_type' => $request->input('page_type', Page::PAGE_TYPE_INFORMATION),
                ],
                $parent
            );

            // Update relationships
            $page->updateParent($parent ? $parent->id : null)
                ->updateStatus($request->input('enabled'))
                ->updateOrder($request->input('order'))
                ->updateImage($request->input('image_file_id'))
                ->updateCollections($request->input('collections'));

            // Update model so far
            $page->save();

            return $page;
        });
    }

    /**
     * Process the requested changes and either update the model or store an update request.
     *
     * @param  Illuminate\Foundation\Http\FormRequest  $request
     * @param  \App\Models\Page  $page
     * @return \App\Models\UpdateRequest
     */
    public function processAsUpdateRequest(FormRequest $request, ?Page $page)
    {
        return DB::transaction(function () use ($request, $page) {
            $data = array_filter_missing([
                'title' => $request->missingValue('title'),
                'slug' => $request->missingValue('slug'),
                'excerpt' => $request->missingValue('excerpt'),
                'content' => $request->missingValue('content'),
                'page_type' => $request->missingValue('page_type'),
                'parent_id' => $request->missingValue('parent_id'),
                'enabled' => $request->missingValue('enabled'),
                'order' => $request->missingValue('order'),
                'image_file_id' => $request->missingValue('image_file_id'),
                'collections' => $request->missingValue('collections'),
            ]);

            $updateableType = UpdateRequestModel::EXISTING_TYPE_PAGE;

            if (! $page) {
                $updateableType = UpdateRequestModel::NEW_TYPE_PAGE;

                $data['page_type'] = $data['page_type'] ?? Page::PAGE_TYPE_INFORMATION;
            }
            /** @var \App\Models\UpdateRequest $updateRequest */
            $updateRequest = new UpdateRequestModel([
                'updateable_type' => $updateableType,
                'updateable_id' => $page->id ?? null,
                'user_id' => $request->user()->id,
                'data' => $data,
            ]);

            // Only persist to the database if the user did not request a preview.
            if ($updateRequest->updateable_type === UpdateRequestModel::EXISTING_TYPE_PAGE) {
                // Preview currently only available for update operations
                if (! $request->isPreview()) {
                    $updateRequest->save();
                }
            } else {
                $updateRequest->save();
            }

            return $updateRequest;
        });
    }
}
