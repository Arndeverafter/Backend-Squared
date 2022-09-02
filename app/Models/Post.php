<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * App\Models\Post
 *
 * @property int $id
 * @property string $title
 * @property int $author
 * @property string $description
 * @property string $publication_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PostFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post wherePublicationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Post whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Post extends Model
{
    use HasFactory;

    /*
     * I will always want the post be returned with its author so
     * */

    protected $with = ['user'];

    /**
     * @var array
     *
     * We want to handle posts insertion and updates ourselves, This will just give us a bit of control over this model
     */
    protected $guarded = [];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "author");
    }

    /**
     * Query Scopes for searching this Model
     *
     * Possible Values in the request can be null, search, author, sort
     *
     * You can search Model according to title & Description, Filter Posts by User, Sort Posts according to Publication Date
     *
     */

    public function scopeFilterSort($query, array $filters)
    {
        // in case user wants to get posts by given author then just user id should suffice here atm
        $query->when($filters['author'] ?? false, fn($query, $owner) =>
                $query->whereHas('user', fn($query) =>
                    $query->where('users.id', $owner)
                )
        );

        // Search
        $query->when($filters['search'] ?? false, fn($query, $search) =>
             $query->where(fn($query) =>
                $query->where('title', 'LIKE', '%' . $search . '%')
                      ->orWhere('description', 'LIKE', '%' . $search . '%')
             )
        );

        // Sort : Default is Desc Publication Date,  but if sort is passed then sort Publication Date in ASC
        $query->when($filters['sort'] ?? false, function ($query, $sort) {
            return $query->orderBy('publication_date');
        }, function ($query) {
            return $query->orderByDesc('publication_date');
        });
    }

    /**
     * Get the posts publication date in human readable format
     *
     * @return string
     */
    public function getPublicationDateHumanAttribute(): string
    {
        return Carbon::createFromTimeStamp(strtotime($this->attributes['publication_date']))->diffForHumans();
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['publication_date_human'];
}
