<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');


        Model::unguard();

        // 创建用户
        factory(App\User::class)->create([
            'email' => 'user1@example.com',
            'password' => app('hash')->make('1234')
        ]);
        factory(App\User::class)->create([
            'email' => 'user2@example.com',
            'password' => app('hash')->make('1234')
        ]);
        factory(App\User::class)->create([
            'email' => 'user3@example.com',
            'password' => app('hash')->make('1234')
        ]);

        Model::reguard();
    }
}
