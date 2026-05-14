<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Test User',
            'email' => 'testingRole@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin', 
        ]);
        // Wallet::create([
        //     'user_id' => 1,
        //     'balance' => 1000,
        // ]);
        // Transaction::create([
        //     'user_id' => 2,
        //     'wallet_id' => 2,
        //     'order_id' => null,
        //     'amount' => 1000,
        //     'description' => 'income',
        // ]);
        // Category::create([
        //     'name' => 'clothes',
        // ]);

        // Product::create([
        //     'name' => 'super model',
        //     'description' => 'Latest model super with advanced features.',
        //     'price' => 699.99,
        //     'stock' => 50,
        //     'image' => 'https://plus.unsplash.com/premium_photo-1667520043080-53dcca77e2aa?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Y2xvdGhpbmclMjBicmFuZHN8ZW58MHx8MHx8fDA%3D',
        //     'category_id' => 1, // Assuming the first category is Electronics
        // ]);

        // Order::create([
        //     'user_id' => 1, // Assuming the first user is Test User
        //     'total_amount' => 699.99,
        //     'status' => 'pending',
        // ]);

        // OrderItem::create([
        //     'order_id' => 1, // Assuming the first order
        //     'product_id' => 2, // Assuming the first product is Smartphone
        //     'quantity' => 1,
        //     'price' => 699.99,
        // ]);
        // OrderItem::create([
        //     'order_id' => 1, // Assuming the first order
        //     'product_id' => 3, // Assuming the first product is Smartphone
        //     'quantity' => 2,
        //     'price' => 699.99,
        // ]); 
    }
}
