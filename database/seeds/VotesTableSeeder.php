<?php

use Illuminate\Database\Seeder;
use App\Utilities\Constants;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class VotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete previous records
        DB::table(Constants::TBL_VOTES)->delete();

        $faker = Faker\Factory::create();

        // Get all user IDs
        $userIDs = User::pluck(Constants::FLD_USERS_ID)->toArray();

        // Get all posts IDs
        $postIDs = Post::pluck(Constants::FLD_POSTS_ID)->toArray();

        // Get all comments IDs
        $commentIDs = Comment::pluck(Constants::FLD_COMMENTS_ID)->toArray();

        // Insert up/down votes for posts
        foreach ($postIDs as $postID){
            $votesLimit = $faker->numberBetween(0,500);
            for ($i = 0; $i < $votesLimit; ++$i) {
                DB::table(Constants::TBL_VOTES)->insert([
                    Constants::FLD_VOTES_USER_ID => $faker->randomElement($userIDs),
                    Constants::FLD_VOTES_VOTED_ID => $postID,
                    Constants::FLD_VOTES_VOTED_TYPE => Post::class,
//                    Constants::FLD_VOTES_RESOURCE_ID => $postID,
//                    Constants::FLD_VOTES_RESOURCE_TYPE => Post::class,
                    Constants::FLD_VOTES_TYPE => $faker->numberBetween(0,1)
                ]);
            }
        }

        // Insert up/down votes for comments
        foreach ($commentIDs as $commentID){
            $votesLimit = $faker->numberBetween(0,12);
            for ($i = 0; $i < $votesLimit; ++$i) {
                DB::table(Constants::TBL_VOTES)->insert([
                    Constants::FLD_VOTES_USER_ID => $faker->randomElement($userIDs),
                    Constants::FLD_VOTES_VOTED_ID => $commentID,
                    Constants::FLD_VOTES_VOTED_TYPE => Comment::class,
//                    Constants::FLD_VOTES_RESOURCE_ID => $commentID,
//                    Constants::FLD_VOTES_RESOURCE_TYPE => Comment::class,
                    Constants::FLD_VOTES_TYPE => $faker->numberBetween(0,1)
                ]);
            }
        }
    }
}
