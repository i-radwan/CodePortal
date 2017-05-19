<?php

namespace App\Http\Controllers;

use App\Utilities\Constants;

class HomeController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $features = [
            [
                'title' => 'Contests',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'img' => '/images/features/contest-md.jpg',
                'link_title' => 'Prepare Contests',
                'url' => route(Constants::ROUTES_CONTESTS_INDEX)
            ],
            [
                'title' => 'Problems',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'img' => '/images/features/problem-md.jpeg',
                'link_title' => 'Solve Problems',
                'url' => route(Constants::ROUTES_PROBLEMS_INDEX)
            ],
            [
                'title' => 'Blogs',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'img' => '/images/features/blog-md.jpg',
                'link_title' => 'Write Blogs',
                'url' => route(Constants::ROUTES_BLOGS_INDEX)
            ],
            [
                'title' => 'Groups',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'img' => '/images/features/group-md.jpg',
                'link_title' => 'Manage Groups',
                'url' => route(Constants::ROUTES_GROUPS_INDEX)
            ]
        ];

        $quotes = [
            [
                'name' => 'Omar Wael',
                'description' => '我喜欢有竞争力的编程，我不喜欢Bassem医生',
                'img' => '/images/quotes/omar.jpg'
            ],
            [
                'name' => 'Mostafa Darwish',
                'description' => '我喜欢有竞争力的编程，我不喜欢Bassem医生',
                'img' => '/images/quotes/omar-darwish.jpg'
            ],
            [
                'name' => 'Omar Wael',
                'description' => '我喜欢有竞争力的编程，我不喜欢Bassem医生',
                'img' => '/images/quotes/omar.jpg'
            ],
            [
                'name' => 'Mostafa Darwish',
                'description' => '我喜欢有竞争力的编程，我不喜欢Bassem医生',
                'img' => '/images/quotes/omar-darwish.jpg'
            ]
        ];

        $sponsors = [
            [
                'name' => 'Codeforces',
                'img' => '/images/sponsors/codeforces.png',
                'url' => 'http://codeforces.com/'
            ],
            [
                'name' => 'UVA Online Judge',
                'img' => '/images/sponsors/uva.png',
                'url' => 'https://uva.onlinejudge.org/'
            ],
            [
                'name' => 'Live Archive',
                'img' => '/images/sponsors/live-archive.png',
                'url' => 'https://icpcarchive.ecs.baylor.edu/'
            ]
        ];

        return view('home.index')
            ->with('features', $features)
            ->with('quotes', $quotes)
            ->with('sponsors', $sponsors)
            ->with('pageTitle', config('app.name'));
    }
}
