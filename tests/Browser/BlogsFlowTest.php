<?php

namespace Tests\Browser;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Utilities\Constants;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Browser\Pages\AddBlogPage;
use Tests\Browser\Pages\Blogs;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class BlogsFlowTest extends DuskTestCase
{
    /**
     * Test blogs flow
     *
     * @group blogs
     * @return void
     */
    public function testBlogsFlow()
    {
        sleep(1);
        $faker = Factory::create();
        $this->browse(function (Browser $browser, Browser $browser2) use ($faker) {
            // Login
            $browser->visit(new Login)
                ->loginUser('asd', 'asdasd');

            $browser->script(['sessionStorage.setItem(\'disableMDE\', \'true\')']);
            //============================================================
            // • User can add new blog
            //============================================================
            $browser->visit(new Blogs)
                ->press('New Post')
                ->on(new AddBlogPage)
                ->addBlog($faker->sentence(5), $faker->sentence(100));

            $latestEntry = Post::query()->orderByDesc(Constants::FLD_POSTS_ID)->first();
            $latestEntryID = $latestEntry[Constants::FLD_POSTS_ID];
            $latestEntryTitle = $latestEntry[Constants::FLD_POSTS_TITLE];
            $latestEntryBody = $latestEntry[Constants::FLD_POSTS_BODY];

            $browser
                ->assertPathIs(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $latestEntryID, false))
                ->assertSee('Post added successfully!')
                ->assertSee($latestEntryTitle)
                ->assertSee($latestEntryBody);

            //============================================================
            // • User can comment
            //============================================================

            $browser->type('.add-comment-text', $comment = $faker->sentence((30)))
                ->press('Reply')
                ->assertSee('Comment Added Successfully')
                ->assertSee($comment);

            $latestComment = Comment::query()->orderByDesc(Constants::FLD_COMMENTS_ID)->first();
            $latestCommentID = $latestComment[Constants::FLD_COMMENTS_ID];

            //============================================================
            // • User can vote blog
            //============================================================
            $browser
                ->click('#blog-up-vote-icon')// upvote
                ->assertSeeIn('#blog-up-votes-count', "1")
                ->click('#blog-up-vote-icon')// remove up vote
                ->assertSeeIn('#blog-up-votes-count', "0")
                ->click('#blog-down-vote-icon')// down vote
                ->assertSeeIn('#blog-down-votes-count', "1")
                ->click('#blog-down-vote-icon')// remove down vote
                ->assertSeeIn('#blog-down-votes-count', "0")
                ->click('#blog-down-vote-icon')// down vote
                ->assertSeeIn('#blog-down-votes-count', "1")
                ->click('#blog-up-vote-icon')// up vote
                ->assertSeeIn('#blog-down-votes-count', "0")
                ->assertSeeIn('#blog-up-votes-count', "1")
                ->click('#blog-down-vote-icon')// down vote
                ->assertSeeIn('#blog-down-votes-count', "1")
                ->assertSeeIn('#blog-up-votes-count', "0");

            //============================================================
            // • User can vote comment
            //============================================================
            $browser
                ->click("#comment-$latestCommentID-up-vote-icon")// upvote
                ->assertSeeIn("#comment-$latestCommentID-up-votes-count", "1")
                ->click("#comment-$latestCommentID-up-vote-icon")// remove up vote
                ->assertSeeIn("#comment-$latestCommentID-up-votes-count", "0")
                ->click("#comment-$latestCommentID-down-vote-icon")// down vote
                ->assertSeeIn("#comment-$latestCommentID-down-votes-count", "1")
                ->click("#comment-$latestCommentID-up-vote-icon")// remove down vote
                ->assertSeeIn("#comment-$latestCommentID-down-votes-count", "0")
                ->click("#comment-$latestCommentID-down-vote-icon")// down vote
                ->assertSeeIn("#comment-$latestCommentID-down-votes-count", "1")
                ->click("#comment-$latestCommentID-up-vote-icon")// up vote
                ->assertSeeIn("#comment-$latestCommentID-up-votes-count", "1")
                ->assertSeeIn("#comment-$latestCommentID-down-votes-count", "0")
                ->click("#comment-$latestCommentID-down-vote-icon")// down vote
                ->assertSeeIn("#comment-$latestCommentID-up-votes-count", "0")
                ->assertSeeIn("#comment-$latestCommentID-down-votes-count", "1");


            //============================================================
            // • Non-user cannot vote blogs/comments
            //============================================================
            $browser2->visit(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $latestEntryID))
                ->click('#blog-up-vote-icon')
                ->assertPathIs(route(Constants::ROUTES_AUTH_LOGIN, [], false))
                ->visit(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $latestEntryID))
                ->click('#blog-down-vote-icon')
                ->assertPathIs(route(Constants::ROUTES_AUTH_LOGIN, [], false))
                ->visit(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $latestEntryID))
                ->click("#comment-$latestCommentID-up-vote-icon")// up vote
                ->assertPathIs(route(Constants::ROUTES_AUTH_LOGIN, [], false))
                ->visit(route(Constants::ROUTES_BLOGS_POST_DISPLAY, $latestEntryID))
                ->click("#comment-$latestCommentID-down-vote-icon")//  down vote
                ->assertPathIs(route(Constants::ROUTES_AUTH_LOGIN, [], false));

            //============================================================
            // • User can view blogs
            //============================================================
            $browser->visit(new Blogs)
                ->assertSee($latestEntryTitle)
                ->assertSee('by asd')
                ->assertSee(str_limit($latestEntryBody, 100, ''));

            //============================================================
            // • User search blogs
            //============================================================

            //============================================================
            // • User can edit blog
            //============================================================

            //============================================================
            // • User can edit comment
            //============================================================

            //============================================================
            // • User can delete comment
            //============================================================

            //============================================================
            // • User can delete blog
            //============================================================

            //============================================================
            // • User can see top contributions
            //============================================================

            //============================================================
            // • User can replay to comment
            //============================================================


            $browser->script(['sessionStorage.setItem(\'disableMDE\', \'\')']);
        });
    }
}
