<?php

namespace App\Http\Controllers\Core\V1;

use App\Events\EndpointHit;
use App\Generators\UniqueSlugGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaxonomyServiceEligibility\DestroyRequest;
use App\Http\Requests\TaxonomyServiceEligibility\IndexRequest;
use App\Http\Requests\TaxonomyServiceEligibility\ShowRequest;
use App\Http\Requests\TaxonomyServiceEligibility\StoreRequest;
use App\Http\Requests\TaxonomyServiceEligibility\UpdateRequest;
use App\Http\Resources\TaxonomyCategoryResource;
use App\Http\Responses\ResourceDeleted;
use App\Models\ServiceEligibility;
use App\Models\Taxonomy;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

class TaxonomyServiceEligibilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('index', 'show');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(IndexRequest $request)
    {
        $baseQuery = Taxonomy::query()
            ->topLevelServiceEligibilities()
            ->with('children.children.children.children.children.children')
            ->orderBy('order');

        $serviceEligibilities = QueryBuilder::for($baseQuery)
            ->get();

        event(EndpointHit::onRead($request, 'Viewed all taxonomy service eligibilities'));

        return TaxonomyCategoryResource::collection($serviceEligibilities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, UniqueSlugGenerator $slugGenerator)
    {
        return DB::transaction(function () use ($request, $slugGenerator) {
            $parent = $request->filled('parent_id')
                ? Taxonomy::query()->findOrFail($request->parent_id)
                : Taxonomy::serviceEligibility();

            $serviceEligibility = Taxonomy::create([
                'parent_id' => $parent->id,
                'slug' => $slugGenerator->generate($request->name, table(Taxonomy::class)),
                'name' => $request->name,
                'order' => $request->order,
                'depth' => 0, // Placeholder
            ]);

            $serviceEligibility->updateDepth();

            event(EndpointHit::onCreate($request, "Created taxonomy service eligibility [{$serviceEligibility->id}]", $serviceEligibility));

            return new TaxonomyCategoryResource($serviceEligibility);
        });
    }

    /**
     * Display the specified resource.
     *
     * @return \App\Http\Resources\TaxonomyCategoryResource
     */
    public function show(ShowRequest $request, Taxonomy $taxonomy)
    {
        $baseQuery = Taxonomy::query()
            ->where('id', $taxonomy->id);

        $taxonomy = QueryBuilder::for($baseQuery)
            ->firstOrFail();

        event(EndpointHit::onRead($request, "Viewed taxonomy service eligibility [{$taxonomy->id}]", $taxonomy));

        return new TaxonomyCategoryResource($taxonomy->load('children.children.children.children.children.children'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, UniqueSlugGenerator $slugGenerator, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $slugGenerator, $taxonomy) {
            $parent = $request->filled('parent_id')
                ? Taxonomy::query()->findOrFail($request->parent_id)
                : Taxonomy::serviceEligibility();

            $taxonomy->update([
                'parent_id' => $parent->id,
                'slug' => $slugGenerator->compareEquals($request->name, $taxonomy->slug)
                    ? $taxonomy->slug
                    : $slugGenerator->generate($request->name, table(Taxonomy::class)),
                'name' => $request->name,
                'order' => $request->order,
                'depth' => 0, // Placeholder
            ]);

            $taxonomy->updateDepth();

            event(EndpointHit::onUpdate($request, "Updated taxonomy service eligibility [{$taxonomy->id}]", $taxonomy));

            return new TaxonomyCategoryResource($taxonomy);
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRequest $request, Taxonomy $taxonomy)
    {
        return DB::transaction(function () use ($request, $taxonomy) {
            event(EndpointHit::onDelete($request, "Deleted taxonomy service eligibility [{$taxonomy->id}]", $taxonomy));

            ServiceEligibility::where('taxonomy_id', $taxonomy->id)->delete();

            $taxonomy->delete();

            return new ResourceDeleted('taxonomy service eligibility');
        });
    }
}
