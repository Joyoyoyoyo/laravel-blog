<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserSubscribersPageController extends Controller
{
    public function __invoke(User $user): Response
    {
        $paginator = $user->subscribers()
            ->orderByPivot('created_at', 'desc')
            ->paginate(24)
            ->withQueryString();

        $paginator->getCollection()->transform(fn (User $subscriber): array => [
            'id' => $subscriber->id,
            'name' => $subscriber->name,
        ]);

        return Inertia::render('Users/Subscribers', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'subscribers' => $paginator,
        ]);
    }
}
