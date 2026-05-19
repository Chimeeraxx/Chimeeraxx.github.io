<?php

declare(strict_types=1);

namespace App\Encoder;

use InvalidArgumentException;
use JsonException;

final class JsonEncoder implements EncoderInterface
{
    public function supports(string $format): bool
    {
        return 'json' === $format;
    }

    /**
     * @throws JsonException
     */
    public function decode(string $input): array
    {
        //json na tablice
        $decoded = json_decode($input, true, 512, JSON_THROW_ON_ERROR);

        //wynikiem musi byc tablica obiektow
        if (!is_array($decoded)) {
            throw new InvalidArgumentException('JSON musi zawierać tablicę obiektów.');
        }

        return $decoded;
    }

    /**
     * @throws JsonException
     */
    public function encode(array $data): string
    {
        //tablica na json
        return json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }
}
