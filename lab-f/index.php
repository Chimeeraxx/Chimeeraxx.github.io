<?php

declare(strict_types=1);

require __DIR__.'/autoload.php';

use App\Encoder\CsvEncoder;
use App\Encoder\JsonEncoder;
use App\Encoder\YamlEncoder;
use App\Serializer;

$formats = [
    'csv' => 'CSV',
    'ssv' => 'SSV',
    'tsv' => 'TSV',
    'json' => 'JSON',
    'yaml' => 'YAML',
];

//domyslne wartosci
$inputData = '';
$inputFormat = 'csv';
$outputFormat = 'json';
$output = '';
$error = '';

$isSubmitted = 'POST' === $_SERVER['REQUEST_METHOD'];

if ($isSubmitted) {
    //odczytywanie danych
    $inputData = (string) ($_POST['input_data'] ?? '');
    $inputFormat = getFormat((string) ($_POST['input_format'] ?? 'csv'), $formats, 'csv');
    $outputFormat = getFormat((string) ($_POST['output_format'] ?? 'json'), $formats, 'json');

    //zapisywanie do ciasteczek
    saveCookie('input_data', $inputData);
    saveCookie('input_format', $inputFormat);
    saveCookie('output_format', $outputFormat);
} else {
    //jesli nie wyslany formularz, probuje uzyc ciasteczek
    $inputData = (string) ($_COOKIE['input_data'] ?? '');
    $inputFormat = getFormat((string) ($_COOKIE['input_format'] ?? 'csv'), $formats, 'csv');
    $outputFormat = getFormat((string) ($_COOKIE['output_format'] ?? 'json'), $formats, 'json');
}

//tworzenie serializera i przekazanie encoderow
$serializer = new Serializer([
    new CsvEncoder('csv'),
    new CsvEncoder('ssv'),
    new CsvEncoder('tsv'),
    new JsonEncoder(),
    new YamlEncoder(),
]);

//konwersja danych, jesli input nie jest pusty
if ('' !== trim($inputData)) {
    try {
        $output = $serializer->convert($inputData, $inputFormat, $outputFormat);
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

require __DIR__.'/templates/layout.php';

//sprawdzanie czy podany format istnieje
function getFormat(string $format, array $formats, string $default): string
{
    if (array_key_exists($format, $formats)) {
        return $format;
    }

    return $default;
}

//zapisyawnie pojedynczego ciasteczka
function saveCookie(string $name, string $value): void
{
    setcookie($name, $value, [
        //wazne 30 dni
        'expires' => time() + 60 * 60 * 24 * 30,
        //dla calej aplikacji
        'path' => '/',
        //tylko dla tej domeny
        'samesite' => 'Lax',
    ]);

    $_COOKIE[$name] = $value;
}
