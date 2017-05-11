<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Utilities\Constants;
use Illuminate\Database\Seeder;

class BlogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_POSTS)->delete();
        DB::table(Constants::TBL_COMMENTS)->delete();
        DB::table(Constants::TBL_VOTES)->delete();

        $faker = Faker\Factory::create();

        // Limit of posts
        $limit = 100;

        // Get all user IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();

        // Insert posts
        for ($i = 0; $i < $limit; ++$i) {
            DB::table(Constants::TBL_POSTS)->insert([
                Constants::FLD_POSTS_OWNER_ID => $faker->randomElement($userIDs),
                Constants::FLD_POSTS_TITLE => $faker->sentence,
                Constants::FLD_POSTS_BODY => $faker->realText(3500),
            ]);
        }

        // Get all posts IDs
        $postIDs = Post::pluck(Constants::FLD_POSTS_ID)->toArray();

        // Insert first Level comments
        foreach ($postIDs as $postID) {
            $commentsLimit = $faker->numberBetween(2, 20);

            for ($i = 0; $i < $commentsLimit; ++$i) {
                DB::table(Constants::TBL_COMMENTS)->insert([
                    Constants::FLD_COMMENTS_USER_ID => $faker->randomElement($userIDs),
                    Constants::FLD_COMMENTS_BODY => $faker->realText(160),
                    Constants::FLD_COMMENTS_POST_ID => $postID,
                    Constants::FLD_COMMENTS_PARENT_ID => null,
                ]);
            }
        }

        // Insert replies
        foreach (Comment::all() as $comment) {
            $repliesLimit = $faker->numberBetween(0, 7);

            for ($i = 0; $i < $repliesLimit; ++$i) {
                DB::table(Constants::TBL_COMMENTS)->insert([
                    Constants::FLD_COMMENTS_USER_ID => $faker->randomElement($userIDs),
                    Constants::FLD_COMMENTS_BODY => $faker->realText(160),
                    Constants::FLD_COMMENTS_POST_ID => $comment[Constants::FLD_COMMENTS_POST_ID],
                    Constants::FLD_COMMENTS_PARENT_ID => $comment[Constants::FLD_COMMENTS_ID],
                ]);
            }
        }
    }
}
