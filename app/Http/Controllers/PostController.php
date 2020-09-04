<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
use Carbon\Carbon;
use App\Helpers\Helpers;

class PostController extends Controller
{
	private $elasticsearch;

	public function __construct() {
		$this->elasticsearch = ClientBuilder::create()->build();
	}

	// add post
    public function add(Request $request) {
    	$user = auth()->user();
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'required|string|max:10000',
        ]);
    	$title = $request->post('title');
    	$content = $request->post('content');
    	$params = [
		    'index' => 'posts',
		    'body'  => [
		    	'title' => $title,
		    	'content' => $content,
		    	'created_at' => Carbon::now()->timestamp,
		    	'user' => [
		    		'id' => $user->id,
		    		'name' => $user->name,
		    		'profile_picture_path' => $user->profile_picture_path
		    	]
		    ]
		];
		$created = $this->elasticsearch->index($params);

		$response_params = [
			'index' => 'posts',
			'id' => $created['_id']
		];
		$response = $this->elasticsearch->get($response_params);
    	return $response;
    }

    public function user_posts(Request $request) {
        $request->validate([
            'page' => 'numeric',
            'user_id' => 'required|numeric',
        ]);
        $page = $request->post('page');
        $user_id = $request->post('user_id');

    	return Helpers::get_user_posts($user_id, $page);
    }

    public function search(Request $request) {
        $request->validate([
            'page' => 'numeric',
            'q' => 'required|string|max:50'
        ]);
        $page = $request->post('page') ? $request->post('page') : 1;
        $q = $request->post('q');
        //
        $per_page = config('constants.blogs_per_page');
        $offset = $per_page * ($page - 1);
        //
		$params = [
		    'index' => 'posts',
		    'body'  => [
		    	'from' => $offset,
		    	'size' => $per_page,
		        'sort' => [
		        	['_score' => 'desc'],
		        	['created_at' => 'desc']
		        ],
		        'query' => [
		            'query_string' => [
		            	'query' => '*' . $q . '*',
		            	'fields' => ['title', 'content']
		            ]
		        ]
		        // 'query' => [
		        //     'wildcard' => [
		        //     	'title' => '*' . $q . '*',
		        //     	// 'content' => $q
		        //     ]
		        // ]
		    ]
		];
		$response = $this->elasticsearch->search($params);
    	return $response;
    }
}
