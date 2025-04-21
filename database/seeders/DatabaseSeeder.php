<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        foreach (\App\Models\Student::all() as $Student) {
            if($Student->password_1 != ''){
                $Student->password = bcrypt($Student->password_1);
                $Student->save(); 
            }
          
            // $attributes = $Student->getAttributes();
            // $update =[];
            // foreach ($attributes as $key => $value) {
            //     if (is_string($value)) {
            //         $update[$key] = Str::title($value);
            //     }
            // }
            // $Student->update($update);
        }
    }
}
