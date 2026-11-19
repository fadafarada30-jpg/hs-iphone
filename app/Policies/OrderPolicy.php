<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $order->isBuyer($user) || $order->isSeller($user) || $user->isAdmin();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $order->isBuyer($user) && $order->canBeCancelled();
    }

    public function advance(User $user, Order $order): bool
    {
        return $order->isSeller($user) || $user->isAdmin();
    }
}
