<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = Category::factory()->count(10)->create();
        $users = User::factory()->count(100)->create();

        $this->call(UserSubscriptionSeeder::class);

        $users->each(function (User $user) use ($categories): void {
            Post::factory()
                ->count(10)
                ->for($user)
                ->state(fn (): array => [
                    'category_id' => $categories->random()->id,
                ])
                ->create();
        });

        $postIds = Post::query()->pluck('id');

        $users->each(function (User $user) use ($postIds): void {
            Comment::factory()
                ->count(30)
                ->for($user)
                ->state(fn (): array => [
                    'post_id' => $postIds->random(),
                ])
                ->create();
        });
    }
}
