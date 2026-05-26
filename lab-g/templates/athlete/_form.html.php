<?php
/** @var $athlete ?\App\Model\Athlete */
?>

<div class="form-group">
    <label for="name">Name</label>
    <input type="text" id="name" name="athlete[name]" value="<?= $athlete ? $athlete->getName() : '' ?>">
</div>

<div class="form-group">
    <label for="sport_name">Sport name</label>
    <input type="text" id="sport_name" name="athlete[sport_name]" value="<?= $athlete ? $athlete->getSportName() : '' ?>">
</div>

<div class="form-group">
    <label for="age">Age</label>
    <input type="number" id="age" name="athlete[age]" value="<?= $athlete ? $athlete->getAge() : '' ?>">
</div>

<div class="form-group">
    <label></label>
    <input type="submit" value="Submit">
</div>
