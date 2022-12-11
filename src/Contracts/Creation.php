<?php

namespace Cariuk\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

interface Creation
{
    /**
     * create new object
     */
    public function create(FormRequest $request): Model;

    /**
     * update object
     */
    public function update(FormRequest $request, int $id): Model;
}
