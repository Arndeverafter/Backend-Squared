<?php

namespace App\Http\Controllers;

use App\Helpers\FeedBackHelper;

use App\Helpers\UtilsHelper;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{

    /**
     * Get all latest Posts
     * We are going to use this function
     *
     * @return JsonResponse
     * @params $user
     * @params $sort
     * @params $search
     *
     * Here we have 8 cases
     * first when no argument is given for retrieval say homepage
     * $author is given
     * $search is given
     * $sort is given
     * $sort & $search
     * $search & $author
     * $sort & $author
     * $sort, $search & $author
     *
     *
     * Even if all these cases were handled in by the cache especially since the data is paginated it would result in a very complicated cache management
     * Again, even if sorting was the only case we would still run into the same issue.
     *
     * Maybe Opting for dedicated db servers for write and reads would help after observing slow response times
     * But even then, we wouldn't want to cache paginated data.
     * or employing some cache strategy would serve the needs: https://en.wikipedia.org/wiki/Cache_replacement_policies
     *
     * Conclusion:
     * By not caching the data here we do not have to worry about clearing the cache when new posts are added
     *
     * So I am Opting to only paginate the data for now.
     *
     */
    public function index(): JsonResponse
    {
        // if sort param is passed in the request then sort otherwise default the latest publications will be returned
        $posts = Post::filterSort(request()->toArray())->paginate(30);
        return UtilsHelper::apiResponseConstruct('payload', $posts, 201);
    }

    /**
     * Show A given Post
     *
     * @param $id
     * @return JsonResponse
     *
     * Here we are going to store the item in cache forever since there is no editing or deletion of posts allowed
     * This should be fine for getting one post
     * This will alleviate the strain on our the app infra
     */
    public function show($id): JsonResponse
    {
        $cacheKey = 'gSquared-' . $id;
        return Cache::rememberForever($cacheKey,
            fn() => UtilsHelper::apiResponseConstruct('payload', Post::findOrFail($id), 201)
        );
    }

    /**
     * Create Blog Post
     *
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $post = UtilsHelper::newPostCreate(request()->toArray());

        return $post ? UtilsHelper::apiResponseConstruct('payload', $post, 201)
            : UtilsHelper::apiResponseConstruct('message', FeedBackHelper::EMPTY_RESPONSE, 490);
    }

    /**
     * This action originates from the frontend and as such only the admin is allowed to initiate this request
     *
     * @return JsonResponse|void
     */
    public function queryNewPostsFromThirdParty()
    {
        // just a simple check should do
        if (auth()->id() == 1) {
            try {
                $resp = UtilsHelper::queryPosts();
                if (is_array($resp) && array_key_exists('error', $resp)) // An error Occurred
                    return $resp['error']; // return resp to Frontend
                else
                    return UtilsHelper::constructResponse($resp);
            } catch (\Exception $e) {
                // Just Log whatever happened here
                Log::error($e->getMessage(), [$e->getLine(), $e->getCode()]);
            }
        }
        else
            abort(401);
    }
}
