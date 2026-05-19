<?php

declare(strict_types=1);

namespace App\Encoder;

use InvalidArgumentException;

final class CsvEncoder implements EncoderInterface
{
    private string $format;

    private string $delimiter;

    public function __construct(string $format)
    {
        //separatory
        $this->format = $format;
        $this->delimiter = match ($format) {
            'csv' => ',',
            'ssv' => ';',
            'tsv' => "\t",
            default => throw new InvalidArgumentException('Nieobsługiwany format tabelaryczny.'),
        };
    }

    //sprawdzanie czy obiekt obsluguje podany format
    public function supports(string $format): bool
    {
        return $this->format === $format;
    }

    //dekoduje tekst do tablicy asocj
    public function decode(string $input): array
    {
        $input = str_replace(["\r\n", "\r"], "\n", $input);

        if ('' === trim($input)) {
            return [];
        }

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $input);
        rewind($handle);

        //pierwzy wiersz jako naglowki
        $headers = fgetcsv($handle, null, $this->delimiter, '"', '\\');

        if (false === $headers) {
            fclose($handle);

            return [];
        }

        //usuawwnie niepotrzebnuch spacji
        $headers = array_map(
            static fn ($header): string => trim((string) $header),
            $headers
        );

        if (in_array('', $headers, true)) {
            throw new InvalidArgumentException('Nagłówki nie mogą być puste.');
        }

        if (count($headers) !== count(array_unique($headers))) {
            throw new InvalidArgumentException('Nagłówki nie mogą się powtarzać.');
        }

        $rows = [];

        //odczytywanie kolejnych wierszy
        while (false !== ($fields = fgetcsv($handle, null, $this->delimiter, '"', '\\'))) {
            if (1 === count($fields) && '' === trim((string) $fields[0])) {
                continue;
            }

            if (count($fields) !== count($headers)) {
                throw new InvalidArgumentException('Wiersz ma inną liczbę pól niż nagłówek.');
            }

            $rows[] = array_combine($headers, $fields);
        }

        fclose($handle);

        return $rows;
    }

    //koduje tablice asocj do tekstu
    public function encode(array $data): string
    {
        if ([] === $data) {
            return '';
        }

        //klucze z pierwszego rekordu staja sie naglowkami
        $headers = array_keys($data[0]);

        //strumien tymczasowy zeby uzyc fputcsv
        $handle = fopen('php://temp', 'r+');

        //zapisanie naglowkow
        fputcsv($handle, $headers, $this->delimiter, '"', '\\');

        //kolejne wiersze
        foreach ($data as $row) {
            $line = [];

            foreach ($headers as $header) {
                $line[] = $row[$header] ?? '';
            }

            fputcsv($handle, $line, $this->delimiter, '"', '\\');
        }

        rewind($handle);

        //gotowy tekst z strumienia
        $output = stream_get_contents($handle);

        fclose($handle);

        //usuwanie koncowego znaku nowej linii
        return rtrim($output, "\n");
    }
}
