<?php

namespace App\Http\Controllers\Leaderboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\TopSubscribersLeaderboardCache;
use Inertia\Inertia;
use Inertia\Response;

class TopSubscribersLeaderboardController extends Controller
{
    public function __invoke(): Response
    {
        $ranking = TopSubscribersLeaderboardCache::remember(function (): array {
            return User::query()
                ->select(['users.id', 'users.name'])
                ->withCount('subscribers')
                ->orderByDesc('subscribers_count')
                ->orderBy('users.name')
                ->limit(10)
                ->get()
                ->toArray();
        });

        return Inertia::render('Leaderboard/TopSubscribers', [
            'ranking' => $ranking,
        ]);
    }
}
