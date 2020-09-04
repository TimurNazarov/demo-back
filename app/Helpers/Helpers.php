<?php

namespace App\Helpers;

use App\User;
use Illuminate\Support\Str;
use \Gumlet\ImageResize;
use Elasticsearch\ClientBuilder;

class Helpers {
    public static function get_user_posts($user_id, $page = 1) {
        $elasticsearch = ClientBuilder::create()->build();

        $per_page = config('constants.blogs_per_page');
        $offset = $per_page * ($page - 1);
        
        $params = [
            'index' => 'posts',
            'body'  => [
                'from' => $offset,
                'size' => $per_page,
                'sort' => ['created_at' => 'desc'],
                'query' => [
                    'term' => [
                        'user.id' => $user_id
                    ]
                ]
            ]
        ];
        $response = $elasticsearch->search($params);
        return $response['hits']['hits'];
    }
    public static function handle_image_resize($image, $folder, $sizes) {
        $image_name = Str::random(64);
        $image = new ImageResize($image);
        // disable gamma (reason: multiple resizing of single image breaks gamma after first resize)
        $image->gamma(false);
        foreach ($sizes as $size_name) {
            $image_size = config('constants.image_sizes.' . $size_name);
            $image->resizeToBestFit($image_size, $image_size);
            $small_image_name = $image_name . '-' . $size_name . '.jpg';
            $image->save('uploads/' . $folder . '/' . $small_image_name);
        }
        return $image_name;
    }

    public static function paginate($models, $page = 1, $per_page = false) {
        if(!$per_page) {
            $per_page = config('constants.default_per_page');
        }
        $offset = $per_page * ($page - 1);
        $models = $models->skip($offset)->take($per_page);
        return $models;
    }

    public static function notification_pool($user_id) {
        if(is_array($user_id)) {
            foreach ($user_id as $id) {
                self::clear_user_notifications($id);
            }
        } else {
            self::clear_user_notifications($user_id);
        }
        return;
    }

    private static function clear_user_notifications($user_id) {
        $max = config('constants.notification_limit');
        $user = User::find($user_id);
        $notifications = $user->notifications;
        $notification_count = count($notifications);
        $limit = $notification_count - $max;
        if($notification_count >= $max) {
            $user->notifications()->orderBy('created_at', 'asc')->limit($limit)->delete();
        }
        return;
    }

    public static function file_url($file_name, $folder, $size = 'average') {
        return url('/') . '/' . config('constants.main_uploads_folder_name') . '/' . $folder . '/' . $file_name . '-' . $size . '.jpg';
    }
}