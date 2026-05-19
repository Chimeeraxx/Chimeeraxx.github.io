<?php

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Konwerter danych</title>
</head>
<body>
<h1>Konwerter danych CSV / SSV / TSV / JSON / YAML</h1>

<form method="post">
    <div>
        <label for="input_data">Dane wejściowe:</label><br>
        <textarea id="input_data" name="input_data" rows="14" cols="90"><?= $escape($inputData) ?></textarea>
    </div>

    <br>

    <div>
        <label for="input_format">Format wejściowy:</label>
        <select id="input_format" name="input_format">
            <?php foreach ($formats as $value => $label): ?>
                <option value="<?= $escape($value) ?>" <?= $inputFormat === $value ? 'selected' : '' ?>>
                    <?= $escape($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <div>
        <label for="output_format">Format wyjściowy:</label>
        <select id="output_format" name="output_format">
            <?php foreach ($formats as $value => $label): ?>
                <option value="<?= $escape($value) ?>" <?= $outputFormat === $value ? 'selected' : '' ?>>
                    <?= $escape($label) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <br>

    <button type="submit">Konwertuj</button>
</form>

<?php if ('' !== $error): ?>
    <p><strong>Błąd:</strong> <?= $escape($error) ?></p>
<?php endif; ?>

<h2>Output</h2>
<pre><?= $escape($output) ?></pre>
</body>
</html>
