<?php

namespace KlinikPintar\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface SoftDeletation
{
    /**
     * get list of trash object
     */
    public function getTrashList(Request $request, bool $houldPaginate): Collection | LengthAwarePaginator;

    /**
     * force delete object
     */
    public function forceDelete(Request $request, $id): Model;

    /**
     * restore deleted object
     */
    public function restore(Request $request, $id): Model;
}
