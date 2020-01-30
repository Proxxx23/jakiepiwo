<?php
declare( strict_types=1 );

use App\Http\Utils\Dictionary;
use PHPUnit\Framework\TestCase;

class DictionaryTest extends TestCase
{
    public function testGetsTranslatedIdProperly(): void
    {
        self::assertEquals( 28, ( new Dictionary() )->getById( 69 ) );
    }
}
