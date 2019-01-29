<?php

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
         $this->call(ArticlesTableSeeder::class);
         $this->call(UserTableSeeder::class);
         $this->call(VenueTableSeeder::class);
         $this->call(EventTableSeeder::class);
         $this->call(CommentTableSeeder::class);
    }
}
