<?php

namespace Cariuk\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait Activation
{
    /**
     * activate.
     *
     * @param mixed $id
     */
    public function activate(Request $request, $id): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, function (Builder &$builder) {
            $builder->whereStatus('inactive');
        });

        $object->status = 'active';
        $object->save();

        if (method_exists($this, 'onActivated')) {
            call_user_func_array([$this, 'onActivated'], [$request, $object]);
        }

        if (method_exists($this, 'onStatusChanged')) {
            call_user_func_array([$this, 'onStatusChanged'], [$request, $object]);
        }

        return $object;
    }

    /**
     * inactive.
     *
     * @param mixed $id
     */
    public function inactive(Request $request, $id): Model
    {
        if (!method_exists($this, 'getDetail')) {
            throw new \RuntimeException('This method required getDetail in Reading trait');
        }

        $object = $this->getDetail($request, $id, function (Builder &$builder) {
            $builder->whereStatus('active');
        });

        $object->status = 'inactive';
        $object->save();

        if (method_exists($this, 'onStatusChanged')) {
            call_user_func_array([$this, 'onStatusChanged'], [$request, $object]);
        }

        if (method_exists($this, 'onInactivated')) {
            call_user_func_array([$this, 'onInactivated'], [$request, $object]);
        }

        return $object;
    }
}
