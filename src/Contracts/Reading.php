<?php

namespace KlinikPintar\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface Reading
{
    /**
     * get list of object
     */
    public function getList(Request $request, bool $shouldPaginate): Collection | LengthAwarePaginator;

    /**
     * get list of object
     */
    public function getDetail(Request $request, $id): Model;
}
