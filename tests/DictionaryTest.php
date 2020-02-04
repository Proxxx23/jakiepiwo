<?php
declare( strict_types=1 );

namespace Tests;

use App\Http\Utils\Dictionary;
use PHPUnit\Framework\TestCase;

final class DictionaryTest extends TestCase
{
    public function testGetsTranslatedIdProperly(): void
    {
        self::assertEquals( 28, ( new Dictionary() )->getById( 69 ) );
    }
}
