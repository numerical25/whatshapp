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
         $this->call(ArticlesTableSeeder::class);
         $this->call(UserTableSeeder::class);
         $this->call(VenueTableSeeder::class);
         $this->call(EventTableSeeder::class);
         $this->call(CommentTableSeeder::class);
    }
}
