<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('username', 'admin')->first();
        if(empty($admin)) {
            User::create([
                'username'=>'admin',
                'firstname'=>'admin',
                'lastname'=>'user',
                'email'=>'admin@laravelassesment.com',
                'password'=>bcrypt('Admin@123'),
                'user_role'=>1,
                'status'=>1,
            ]);
        }
    }
}
