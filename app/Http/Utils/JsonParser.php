<?php
declare( strict_types=1 );

namespace Nwt\Utils\Parsers;

use Nwt\Utils\Exceptions\ParserException;
use Nwt\Utils\Interfaces\ParserInterface;

class JsonParser implements ParserInterface
{
    /** @var mixed $content */
    protected $content;

    /**
     * Constructor.
     *
     * @param mixed $content
     */
    public function __construct( $content )
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function parseToArray(): array
    {
        $result = json_decode( (string) $this->content, true );
        if ( $result === null || \json_last_error() !== \JSON_ERROR_NONE ) {
            throw new ParserException( 'JSON invalid or null' );
        }

        //TODO: JSON_THROW_ON_ERROR from 7.4

        return $result;
    }

    /**
     * @return string
     */
    public function parseToJson(): string
    {
        if ( !\is_array( $this->content ) ) {
            throw new ParserException( 'Cannot parse non-array values to JSON' );
        }

        $result = \json_encode( (array) $this->content );
        if ( $result === null || \json_last_error() !== \JSON_ERROR_NONE ) {
            throw new ParserException( 'JSON invalid or null' );
        }

        return $result;
    }
}
