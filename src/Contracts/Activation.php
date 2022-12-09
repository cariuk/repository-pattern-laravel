<?php

namespace KlinikPintar\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface Activation
{
    /**
     * activate.
     *
     * @param mixed $id
     */
    public function activate(Request $request, $id): Model;

    /**
     * inactive.
     *
     * @param mixed $id
     */
    public function inactive(Request $request, $id): Model;
}
