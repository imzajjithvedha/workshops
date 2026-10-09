<?php

namespace App\Traits;

trait ResponseRedirectTrait
{
    protected function redirectWithMessage(
        string $route,
        string $type,
        string $title,
        string $message,
        mixed $parameters = []
    ) {
        return redirect()
            ->route($route, $parameters)
            ->with([
                $type => $title,
                'message' => $message,
            ]);
    }
}
