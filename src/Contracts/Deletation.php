<?php

namespace Cariuk\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface Deletation
{
    /**
     * delete object
     */
    public function delete(Request $request, $id): Model;
}
