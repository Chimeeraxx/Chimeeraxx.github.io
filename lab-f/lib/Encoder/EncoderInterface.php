<?php

declare(strict_types=1);

namespace App\Encoder;

interface EncoderInterface
{
    //sprawza czy encoder obsluguje dany format
    public function supports(string $format): bool;

    //zamienia tekst wejsciowy na tablice
    public function decode(string $input): array;

    //zamienia tablice an tekst w wybranym formacie
    public function encode(array $data): string;
}
