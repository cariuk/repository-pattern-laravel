<?php

namespace RepositoryPatternLaravel\ValueObjects;

/**
 * Sort Direction Enum
 *
 * Represents sort direction using enum instead of primitive strings
 * Eliminates primitive obsession code smell
 */
enum SortDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';

    /**
     * Create from string value
     *
     * @param string $value
     * @return self
     */
    public static function fromString(string $value): self
    {
        return match (strtoupper($value)) {
            'ASC', 'FALSE' => self::ASC,
            'DESC', 'TRUE' => self::DESC,
            default => self::ASC,
        };
    }

    /**
     * Create from boolean (true = DESC, false = ASC)
     *
     * @param bool $descending
     * @return self
     */
    public static function fromBoolean(bool $descending): self
    {
        return $descending ? self::DESC : self::ASC;
    }
}