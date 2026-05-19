<?php

declare(strict_types=1);

namespace App\Encoder;

use InvalidArgumentException;
use JsonException;

final class YamlEncoder implements EncoderInterface
{
    public function supports(string $format): bool
    {
        return 'yaml' === $format;
    }

    public function decode(string $input): array
    {
        //usuawnie niepotrzebnuch spacji i normalizacja nowych linii
        $input = str_replace(["\r\n", "\r"], "\n", trim($input));

        if ('' === $input) {
            return [];
        }

        $rows = [];
        $current = null;

        //czytanie linia po linii
        foreach (explode("\n", $input) as $lineNumber => $line) {
            if ('' === trim($line)) {
                continue;
            }

            //linia zaczynajaca sie od - oznacza nowy obiekt
            if (preg_match('/^\s*-\s*([^:]+):\s*(.*)$/u', $line, $matches)) {
                if (null !== $current) {
                    $rows[] = $current;
                }

                $current = [];
                $current[trim($matches[1])] = $this->parseValue(trim($matches[2]));

                continue;
            }

            //linia z wcieciem oznacza kolejne pole
            if (preg_match('/^\s+([^:]+):\s*(.*)$/u', $line, $matches)) {
                if (null === $current) {
                    throw new InvalidArgumentException('Błędna struktura YAML.');
                }

                $current[trim($matches[1])] = $this->parseValue(trim($matches[2]));

                continue;
            }

            throw new InvalidArgumentException('Błąd YAML w linii '.($lineNumber + 1).'.');
        }

        if (null !== $current) {
            $rows[] = $current;
        }

        return $rows;
    }

    /**
     * @throws JsonException
     */
    public function encode(array $data): string
    {
        $lines = [];

        //kazdy rekord jako element listy yaml
        foreach ($data as $row) {
            $first = true;

            //pierwsze pole od - a kolejne wciecia
            foreach ($row as $key => $value) {
                $prefix = $first ? '- ' : '  ';
                $lines[] = $prefix.$key.': '.$this->formatValue((string) $value);
                $first = false;
            }
        }
        //laczenie wszystkich linii w jeden tekst
        return implode("\n", $lines);
    }

    private function parseValue(string $value): string
    {
        //obsluga wartosci w cudzyslowie
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $decoded = json_decode($value, true);

            return is_string($decoded) ? $decoded : (string) $decoded;
        }

        //oblusga wartosci w apostrofach
        if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            return str_replace("''", "'", substr($value, 1, -1));
        }

        return $value;
    }

    /**
     * @throws JsonException
     */
    private function formatValue(string $value): string
    {
        if ('' === $value) {
            return '""';
        }

        $lower = strtolower($value);

        //niektore wartosci wymagaja cudzyslowia, zeby nie bylo bledu
        $needsQuotes =
            str_contains($value, ':')
            || str_contains($value, '#')
            || str_contains($value, "\n")
            || trim($value) !== $value
            || in_array($lower, ['true', 'false', 'null', 'yes', 'no'], true);

        if ($needsQuotes) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        return $value;
    }
}