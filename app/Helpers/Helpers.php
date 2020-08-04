<?php

namespace App\Helpers;

use App\User;


class Helpers {
    public static function map_notification_data($type, $data) {
        if(!$data) {
            return [];
        }
        $data_map = [
            'FriendRequest' => [
                'model' => \App\FriendRequest::class,
                'resource' => \App\Http\Resources\FriendRequest::class,
                'field' => 'id'
            ],
            'Dummy' => [
                'model' => \App\FriendRequest::class,
                'resource' => \App\Http\Resources\FriendRequest::class,
                'field' => 'id'
            ],
            // other notifications types
        ];
        $data_model = $data_map[$type]['model']::find($data[$data_map[$type]['field']]);
        $data_resource = new $data_map[$type]['resource']($data_model);

        return $data_resource;
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

    public static function file_url($path) {
        // return url('/') . '/' . config('constants.main_uploads_folder_name') . '/' . $path;
        return url('/') . '/' . config('constants.main_uploads_folder_name') . '/temp.jpg';
    }
}