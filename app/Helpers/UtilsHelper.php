<?php

namespace App\Helpers;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UtilsHelper
{

    /**
     * Format response for given request
     *
     * @param $type
     * @param $body
     * @param $code
     * @return JsonResponse
     */
    public static function apiResponseConstruct($type, $body, $code): JsonResponse
    {
        return response()->json([$type => $body], $code);
    }


    /*
     * Validate and create new Blog Post
     *
     * @param array $item
     * @param bool $import
     */
    public static function newPostCreate(array $item, bool $import = false)
    {
        try {
            Validator::make($item, [
                'title' => ['required', 'string'],
                'description' => ['required'],
            ])->validate();

            return Post::firstOrCreate(
                ['title' => $item['title']],
                ['description' => $item['description'],
                    'author' => $import ? 1 : auth()->id(),
                    'publication_date' => $import ? date('Y-m-d H:i:s', strtotime($item['publishedAt'])) : Carbon::now()]);
            /* You may use the wasRecentlyCreated method (eg. $post->wasRecentlyCreated() ? '': '') to distinctively tell which records were added and which ones were just fetched from DB */
        } catch (\Exception $e) {
            /*
             * Since this method is also used by the import we are just going to catch the exception and log it,
             * We do not want to stop execution if the title is not unique or something happens while executing the query
             *
             * */
            Log::error("Blog Post Creation Failed : " . $e->getMessage());
        }
    }

    /*
   * A couple of Assumptions
   *
   * 1. Admin user will be the first user created with an id of 1
   * 2. One user will be able to login by this account
   * 3. User will be able to query for new posts by a click of a button from the frontend
   * 4. Again we are going to make a command that will be executed by the scheduler every 30 minutes to fetch new posts given its 2/3 posts an hour
   *
   * */
    public static function queryPosts()
    {
        $resp = HttpHelper::send('get');
        if (!is_string($resp) && $resp->getStatusCode() > 300) // means an error occurred somewhere so just return this response : Exceptions Already handled
            return ['error' => $resp];
        else {
            $items = json_decode($resp);
            $newPosts = collect($items->articles);

            return $newPosts->map(fn($item) => static::newPostCreate((array)$item, true)
            )->map(fn($item) => $item != null ? $item['title'] : false
            );
        }
    }

    public static function fetchPostsScheduler()
    {
        $resp = static::queryPosts();
        if (is_array($resp) && array_key_exists('error', $resp))
            Log::error("Scheduler : Error : " . $resp['error']); // Something happened while fetching
        else {
            // Log action : one can log titles fetched
            Log::info('success', (array)(static::constructResponse($resp)));
            // or
            Log::info("Fetched New Posts Successfully");
        }
    }

    /**
     * Construct Nice response for the given result
     *
     * @param $item
     * @return JsonResponse
     */
    public static function constructResponse($item): JsonResponse
    {
        if ($item) {
            $count = $item->count();
            $titles = $item->implode(", \n ");
            /*
             * Since we are using Update or Create we*/
            $text = nl2br($count . "  " . Str::plural("Post", $count) . " were Fetched Successfully with the following titles \n" . $titles);
            return UtilsHelper::apiResponseConstruct('message', $text, 201);
        } else
            return UtilsHelper::apiResponseConstruct('message', FeedBackHelper::EMPTY_RESPONSE, 201);
    }
}
