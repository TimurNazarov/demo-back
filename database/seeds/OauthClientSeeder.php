<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OauthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            'id' => 1,
            'user_id' => null,
            'name' => 'Laravel Personal Access Client',
            'secret' => 'paqAaA4gR1aR0rTLws9OBGPOSMCXqXk9HqUAV3QF',
            'provider' => null,
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('oauth_clients')->insert([
            'id' => 2,
            'user_id' => null,
            'name' => 'Laravel Password Grant Client',
            'secret' => 'rGQCYy37YguUYIRPmDXmzMiLhPjvI5aKRJq7QQxY',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
