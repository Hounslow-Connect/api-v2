<?php

namespace App\Models;

use App\Models\Mutators\PageFeedbackMutators;
use App\Models\Relationships\PageFeedbackRelationships;
use App\Models\Scopes\PageFeedbackScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PageFeedback extends Model
{
    use HasFactory;
    use PageFeedbackMutators;
    use PageFeedbackRelationships;
    use PageFeedbackScopes;

    const AUTO_DELETE_MONTHS = 6;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'page_feedbacks';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'consented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function userDetailsProvided(): bool
    {
        return ($this->name !== null) || ($this->email !== null) || ($this->phone !== null);
    }
}
