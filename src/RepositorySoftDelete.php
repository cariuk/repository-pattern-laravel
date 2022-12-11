<?php

namespace Cariuk;

use Cariuk\Contracts\SoftDeletation as SoftDeletationContract;
use Cariuk\Traits\SoftDeletation;

abstract class RepositorySoftDelete extends Repository implements SoftDeletationContract
{
    use SoftDeletation;
}
