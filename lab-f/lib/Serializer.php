<?php

declare(strict_types=1);

namespace App;

use App\Encoder\EncoderInterface;
use InvalidArgumentException;

final class Serializer
{
    /**
     * @param EncoderInterface[] $encoders
     */
    public function __construct(
        private array $encoders
    ) {
    }

    //konwertuje dane z jednego formatu na drugi
    public function convert(string $input, string $inputFormat, string $outputFormat): string
    {
        $decoder = $this->getEncoder($inputFormat);

        $data = $decoder->decode($input);

        $this->validateData($data);

        if ($inputFormat === $outputFormat) {
            return $input;
        }

        $encoder = $this->getEncoder($outputFormat);

        return $encoder->encode($data);
    }

    //szuka encodera obslugujacego wsakzany format
    private function getEncoder(string $format): EncoderInterface
    {
        foreach ($this->encoders as $encoder) {
            if ($encoder->supports($format)) {
                return $encoder;
            }
        }

        throw new InvalidArgumentException('Nieobsługiwany format: '.$format);
    }

    //sprawdza czy dane sa tablica rekordow z takimi samymi kluczami
    private function validateData(array $data): void
    {
        if ([] === $data) {
            return;
        }

        if (!$this->isList($data)) {
            throw new InvalidArgumentException('Dane muszą być tablicą obiektów.');
        }

        $expectedKeys = null;

        foreach ($data as $row) {
            if (!is_array($row)) {
                throw new InvalidArgumentException('Każdy element musi być obiektem/tablicą.');
            }

            $keys = array_keys($row);

            foreach ($keys as $key) {
                if (!is_string($key) || '' === trim($key)) {
                    throw new InvalidArgumentException('Każdy obiekt musi mieć nazwane pola.');
                }
            }

            if (null === $expectedKeys) {
                $expectedKeys = $keys;

                continue;
            }

            $currentKeys = $keys;

            sort($expectedKeys);
            sort($currentKeys);

            if ($expectedKeys !== $currentKeys) {
                throw new InvalidArgumentException('Każdy element tablicy musi mieć te same klucze.');
            }
        }
    }

    //sprawdza czy tablica jest lista z indeksami 0,1,2...
    private function isList(array $data): bool
    {
        $expectedKey = 0;

        foreach ($data as $key => $_) {
            if ($key !== $expectedKey) {
                return false;
            }

            ++$expectedKey;
        }

        return true;
    }
}
