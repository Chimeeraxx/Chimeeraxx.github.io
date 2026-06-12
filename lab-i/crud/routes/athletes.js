var express = require("express");
var router = express.Router();

const { DatabaseSync } = require("node:sqlite");
const path = require("node:path");

const dbPath = path.resolve(__dirname, "..", "data.db");
const db = new DatabaseSync(dbPath);

// lista zawodników
router.get("/", function (req, res, next) {
    const athletes = db.prepare("SELECT * FROM athlete").all();

    res.render("athletes/index", {
        title: "Athletes List",
        athletes: athletes,
    });
});

// formularz tworzenia
router.get("/create", function (req, res, next) {
    res.render("athletes/create", {
        title: "Create Athlete",
        athlete: {},
    });
});

// zapis nowego zawodnika
router.post("/create", function (req, res, next) {
    const { name, sport_name, age } = req.body;

    db.prepare(
        "INSERT INTO athlete (name, sport_name, age) VALUES (?, ?, ?)",
    ).run(name, sport_name, age);

    res.redirect("/athletes");
});

// podgląd jednego zawodnika
router.get("/:id", function (req, res, next) {
    const athlete = db
        .prepare("SELECT * FROM athlete WHERE id = ?")
        .get(req.params.id);

    if (!athlete) {
        return next();
    }

    res.render("athletes/show", {
        title: `${athlete.name} (${athlete.id})`,
        athlete: athlete,
    });
});

// formularz edycji
router.get("/:id/edit", function (req, res, next) {
    const athlete = db
        .prepare("SELECT * FROM athlete WHERE id = ?")
        .get(req.params.id);

    if (!athlete) {
        return next();
    }

    res.render("athletes/edit", {
        title: `Edit Athlete ${athlete.name} (${athlete.id})`,
        athlete: athlete,
    });
});

// zapis edycji
router.post("/:id/edit", function (req, res, next) {
    const { name, sport_name, age } = req.body;

    db.prepare(
        "UPDATE athlete SET name = ?, sport_name = ?, age = ? WHERE id = ?",
    ).run(name, sport_name, age, req.params.id);

    res.redirect("/athletes");
});

// kasowanie zawodnika
router.post("/:id/delete", function (req, res, next) {
    db.prepare("DELETE FROM athlete WHERE id = ?").run(req.params.id);

    res.redirect("/athletes");
});

module.exports = router;