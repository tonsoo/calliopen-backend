<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasPaginations
{
    public function paginate(Builder $query, Request $request) {
        $limit = $request->get('limit', 15);

        if ($limit === 'unlimited' || (int) $limit <= 0) {
            return $query->get();
        }

        $perPage = (int) $limit;
        $page = (int) $request->get('page', 1);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
