<?php

use App\Models\Post;
use function Pest\Faker\faker;
use App\Models\User;
use Illuminate\Support\Facades\Log;

it('Can Create a new post by given Author', function () {
    login()->post('api/posts', [
        "title" => faker()->sentence(),
        "description" => faker()->paragraphs(5, true)
    ])->assertCreated();

    expect(Post::latest()->first())
        ->title->toBeString()->not->toBeEmpty()
        ->description->toBeString()->not->toBeEmpty();
});

it("Cannot Create Post if Validation fails", function () {
    login()->post("api/posts", [
        "title" => '',
        "description" => ''
    ])->assertStatus(490); // Our Custom Error Code
});

it(" Can not allow unauthenticated user to create Posts", function () {
    $res = $this->withHeader('Accept', 'application/json')->post("api/posts", [
        "title" => faker()->sentence,
        "description" => faker()->paragraphs('5', true)
    ]);

    $res->assertStatus(401);
});

it("Can not allow multiple Blog Posts with same Title", function () {
    // We are going to try and insert 5 blog posts with same title and query the DB to see how many were inserted
    $attributes = Post::factory(5)->raw(["title" => 'Testing Waters']);
    $resp = login()->withHeader('Accept', 'application/json')->postJson('api/posts', $attributes);
    expect($resp)
        ->assertStatus(490) // our custom error code : No Posts were added
        ->assertJsonCount(1); // But also expect only one post to be added of the five specified
});

it(" Can not allow logged on users other than admin to query posts from third Party", function () {
    $resp = login()->withHeader('Accept', 'application/json')->get("api/queryNewPosts");
    expect($resp)
        ->assertStatus(401);
});

it(" Allows only the admin to query posts from third party", function () {
    // Create admin user : here just use the seeder present
    $this->seed();
    $admin = User::find(1);

    $resp = login($admin)->withHeader('Accept', 'application/json')->get("api/queryNewPosts");
    expect($resp)
        ->assertStatus(201)
        ->and($admin)
        ->user_type->toBeString()->toEqual('admin'); // to show this user is indeed an admin
});

it(" Can get Posts", function () {
    $this->seed();
    $resp = $this->withHeader('Accept', 'application/json')->get("/api/posts");
    $resp->assertStatus(201);
});

it(" Can Get a given post", function ($id) {
    // Just use the given Seeder
    $this->seed(); // seeds Ten Posts
    $resp = $this->withHeader('Accept', 'application/json')->get("/api/posts/" . $id);
    $resp->assertStatus(201);
})->with(range(1, 10, 2));

it(" Can Sort Latest Or Oldest Posts", function ($orderBy) {
    // A couple of things here : Use our Seeder
    $this->seed();
    $isSort = !($orderBy == 'Ascending');
    Log::info($isSort);
    $resp = $this->withHeader('Accept', 'application/json')->postJson("/api/get-posts", [
        "sort" => $isSort
    ]);

    $apiDecodeResponse = collect($resp->decodeResponseJson()['payload']['data'])->pluck('publication_date')->toArray();

    if ($isSort) {
        $this->assertEquals(
            Post::orderBy('publication_date')->pluck('publication_date')->toArray(),
            $apiDecodeResponse
        );
    } else {
        $this->assertEquals(
            Post::orderByDesc('publication_date')->pluck('publication_date')->toArray(),
            $apiDecodeResponse
        );
    }
})->with(["Ascending", "Descending"]);

it("Can get posts by author", function($id){
    $this->seed();
    // With the seeder we get 1 author with a given post so proceed
    $resp = $this->withHeader('Accept', 'application/json')->postJson("/api/get-posts", [
        "author" => $id,
        "sort" => false
    ]);

    $apiDecodeResponse = collect($resp->decodeResponseJson()['payload']['data'])->pluck('author')->toArray();
    $this->assertEquals(
        Post::orderByDesc('publication_date')->where('author', $id)->pluck('author')->toArray(), // using orderByDesc because its the default Ordering
        $apiDecodeResponse
    );
})->with(range(2,10,3));
