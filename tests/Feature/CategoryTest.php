<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Scopes\IsActiveScope;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase {
    public function test_insert_category() {
        $category = new Category();
        $category->id = 'GADGET';
        $category->name = 'Gadget';

        $result = $category->save();
        $this->assertTrue($result);
    }

    public function test_insert_many_categories() {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                'id' => "ID $i",
                'name' => "Name $i",
            ];
        }

        $result = Category::insert($categories);
        $this->assertTrue($result);

        $total = Category::count();
        $this->assertEquals(10, $total);
    }

    public function test_find_category() {
        $this->seed(CategorySeeder::class);

        $category = Category::find('FOOD');
        $this->assertNotNull($category);
        $this->assertEquals('FOOD', $category->id);
        $this->assertEquals('Food', $category->name);
        $this->assertEquals('Food Category', $category->description);
    }

    public function test_update_category() {
        $this->seed(CategorySeeder::class);

        $category = Category::find('FOOD');
        $category->name = "Food Update";

        $result = $category->update();
        $this->assertTrue($result);
    }

    public function test_select_category() {
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->save();
        }

        $categories = Category::whereNull('description')->get();
        $this->assertEquals(5, $categories->count());

        $categories->each(function ($category) {
            $this->assertNull($category->description);

            // update hasil select
            $category->description = 'Updated';
            $category->update();
        });
    }

    public function test_update_many_categories() {
        // insert many
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                'id' => "ID $i",
                'name' => "Name $i",
            ];
        }

        $result = Category::insert($categories);
        $this->assertTrue($result);

        // select where description is null
        Category::whereNull('description')->update(['description' => 'Updated']);
        $total = Category::where('description', 'Updated')->count();
        $this->assertEquals(10, $total);
    }

    public function test_delete_category() {
        $this->seed(CategorySeeder::class);

        $category = Category::find('FOOD');
        $result = $category->delete();
        $this->assertTrue($result);

        $total = Category::count();
        $this->assertEquals(0, $total);
    }

    public function test_delete_many_categories() {
        // insert many
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                'id' => "ID $i",
                'name' => "Name $i",
            ];
        }

        $result = Category::insert($categories);
        $this->assertTrue($result);

        Category::whereNull('description')->delete();

        $total = Category::count();
        $this->assertEquals(0, $total);
    }

    public function test_create_category_with_save() {
        $request = [
            'id' => 'FOOD',
            'name' => 'Food',
            'description' => 'Food Category',
        ];

        $category = new Category($request);
        $result = $category->save();
        $this->assertTrue($result);
    }

    public function test_create_category_directly() {
        $request = [
            'id' => 'FOOD',
            'name' => 'Food',
            'description' => 'Food Category',
        ];

        $category = Category::create($request);
        $this->assertNotNull($category->id);
    }

    public function test_update_mass() {
        $this->seed(CategorySeeder::class);

        $request = [
            'name' => 'Food Updated',
            'description' => 'Food Category Updated'
        ];

        $category = Category::find('FOOD');
        $category->fill($request);
        $result = $category->save();

        $this->assertTrue($result);
    }

    public function test_global_scope() {
        $category = new Category();
        $category->id = 'FOOD';
        $category->name = 'Food';
        $category->description = 'Food Category';
        $category->is_active = false;
        $category->save();

        $category = Category::find('FOOD');
        $this->assertNull($category);
    }

    public function test_remove_global_scope() {
        $category = new Category();
        $category->id = 'FOOD';
        $category->name = 'Food';
        $category->description = 'Food Category';
        $category->is_active = false;
        $category->save();

        // select with global scope
        $category = Category::find('FOOD');
        $this->assertNull($category);

        // select without global scope
        $category = Category::withoutGlobalScopes([IsActiveScope::class])->find('FOOD');
        $this->assertNotNull($category);
    }
}
