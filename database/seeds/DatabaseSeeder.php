<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       // $this->call(UsersTableSeeder::class);
       DB::table('users')->insert([
        'name' => 'Joni',
        'email' => 'rodhiyanto@gmail.com',
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        //'pegawai_alamat' => 'Jl. Panglateh'
    ]);
    }
}
