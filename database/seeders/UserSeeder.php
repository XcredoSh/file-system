<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->count(1)
                            ->create()
                            ->each(
                                function($user){
                                    $user->assignRole('super-admin');
                                }
                            );

        \App\Models\User::factory()->count(1)
                            ->create()
                            ->each(
                                function($user){
                                    $user->assignRole('simple-user');
                                }
                            );  
        \App\Models\User::factory()->count(1)
                            ->create()
                            ->each(
                                function($user){
                                    $user->assignRole('moderator');
                                }
                            );                     
    }
}
