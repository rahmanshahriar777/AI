<?php

namespace App\Services\AI\Exceptions;

use RuntimeException;

class MissingAIConfigurationException extends RuntimeException
{
    /**
     * Create a new exception for missing AI configuration keys.
     *
     * @param array<string> $missingKeys
     */
    public static function forMissingKeys(array $missingKeys): self
    {
        $keysList = implode(', ', $missingKeys);
        return new self(
            "AI Configuration Error: Missing required AI environment variable(s): [{$keysList}]. " .
            "Please ensure all required keys are defined in your .env file."
        );
    }
}
