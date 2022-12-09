<?php

namespace KlinikPintar;

use KlinikPintar\Contracts\SoftDeletation as SoftDeletationContract;
use KlinikPintar\Traits\SoftDeletation;

abstract class RepositorySoftDelete extends Repository implements SoftDeletationContract
{
    use SoftDeletation;
}
