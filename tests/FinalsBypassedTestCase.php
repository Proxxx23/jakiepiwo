<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use DG\BypassFinals;

class FinalsBypassedTestCase extends TestCase
{
    public function __construct()
    {
        BypassFinals::enable();
        parent::__construct();
    }
}