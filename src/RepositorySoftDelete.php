<?php

namespace RepositoryPatternLaravel;

use RepositoryPatternLaravel\Contracts\SoftDeletation as SoftDeletationContract;
use RepositoryPatternLaravel\Traits\SoftDeletation;

abstract class RepositorySoftDelete extends Repository implements SoftDeletationContract
{
    use SoftDeletation;
}
