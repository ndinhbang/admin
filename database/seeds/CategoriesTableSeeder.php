<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = config('default.categories');
        if (Category::withoutGlobalScopes()->get()->isEmpty()) {
            \DB::transaction(function () use ($categories) {
                $roleArr = [];
                foreach ($categories as $type => $cats) {
                    $pos = 0;
                    foreach ($cats as $key => $name) {
                        $pos = $pos + 1;
                        $cat = Category::create([
                            'uuid' => nanoId(),
                            'name' => $name,
                            'type' => $type,
                            'position' => $pos,
                            'state' => 1,
                            'fixed' => 1,
                            'place_id' => 0,
                        ]);
                    }
                }
            });
        } else {
            dump('Categories table is not empty!');
        }
    }
}
