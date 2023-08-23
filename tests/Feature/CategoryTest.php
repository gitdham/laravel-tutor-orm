<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Scopes\IsActiveScope;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

        // select with global scope
        $category = Category::find('FOOD');
        $this->assertNull($category);

        // select without global scope
        $category = Category::withoutGlobalScopes([IsActiveScope::class])->find('FOOD');
        $this->assertNotNull($category);
    }

    public function test_query_category() {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $category = Category::find('FOOD');
        $this->assertNotNull($category);

        $product = $category->products;
        $this->assertNotNull($product);
        $this->assertCount(1, $product);

        Log::info(json_encode($category));
    }

    public function test_many_to_many_query() {
        $category = new Category();
        $category->id = '1';
        $category->name = 'Category 1';
        $category->description = 'Description 1';
        $category->is_active = true;
        $category->save();
        $this->assertNotNull($category);

        $products = [
            new Product([
                'id' => '1',
                'name' => 'Product 1',
                'description' => 'Description Product 1',
                'price' => 1000,
            ]),
            new Product([
                'id' => '2',
                'name' => 'Product 2',
                'description' => 'Description Product 2',
                'price' => 2000,
            ]),
            new Product([
                'id' => '3',
                'name' => 'Product 3',
                'description' => 'Description Product 3',
                'price' => 3000,
            ]),
        ];

        $category->products()->saveMany($products);
        $this->assertNotNull($products);
    }

    public function test_has_one_of_many() {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $category = Category::find('FOOD');

        $cheapestProduct = $category->cheapestProduct;
        $this->assertNotNull($cheapestProduct);
        $this->assertEquals('1', $cheapestProduct->id);
        Log::info(json_encode($cheapestProduct));

        $mostExpensiveProduct = $category->mostExpensiveProduct;
        $this->assertNotNull($mostExpensiveProduct);
        $this->assertEquals('2', $mostExpensiveProduct->id);
        Log::info(json_encode($mostExpensiveProduct));
    }

    public function test_has_many_through() {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            ReviewSeeder::class,
        ]);

        $category = Category::find('FOOD');
        $this->assertNotNull($category);

        $reviews = $category->reviews;
        $this->assertNotNull($reviews);
        $this->assertCount(2, $reviews);

        Log::info(json_encode($category));
    }
}
