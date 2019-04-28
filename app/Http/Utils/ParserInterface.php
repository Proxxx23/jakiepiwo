<?php
declare( strict_types=1 );

namespace Nwt\Utils\Interfaces;

interface ParserInterface
{
    /**
     * @return array
     */
    public function parseToArray(): array;

    /**
     * @return string
     */
    public function parseToJson(): string;
}
