import sqlite3
from pathlib import Path

from flask import Flask, abort, g, redirect, render_template, request, url_for

app = Flask(__name__)

BASE_DIR = Path(__file__).resolve().parent
DATABASE = BASE_DIR / "database.db"


def get_db():
    if "db" not in g:
        g.db = sqlite3.connect(DATABASE)
        g.db.row_factory = sqlite3.Row

    return g.db


@app.teardown_appcontext
def close_db(error):
    db = g.pop("db", None)

    if db is not None:
        db.close()


def init_db():
    db = sqlite3.connect(DATABASE)

    with open(BASE_DIR / "migrations" / "init.sql", encoding="utf-8") as file:
        db.executescript(file.read())

    db.commit()
    db.close()


@app.route("/")
def home():
    return redirect(url_for("athlete_index"))


@app.route("/athletes")
def athlete_index():
    db = get_db()
    athletes = db.execute("SELECT * FROM athlete ORDER BY id DESC").fetchall()

    return render_template("athlete/index.html", athletes=athletes)


@app.route("/athletes/<int:athlete_id>")
def athlete_show(athlete_id):
    db = get_db()
    athlete = db.execute(
        "SELECT * FROM athlete WHERE id = ?",
        (athlete_id,)
    ).fetchone()

    if athlete is None:
        abort(404)

    return render_template("athlete/show.html", athlete=athlete)


@app.route("/athletes/create", methods=["GET", "POST"])
def athlete_create():
    if request.method == "POST":
        name = request.form["name"]
        sport_name = request.form["sport_name"]
        age = request.form["age"]

        db = get_db()
        db.execute(
            "INSERT INTO athlete (name, sport_name, age) VALUES (?, ?, ?)",
            (name, sport_name, age)
        )
        db.commit()

        return redirect(url_for("athlete_index"))

    return render_template("athlete/create.html")


@app.route("/athletes/<int:athlete_id>/edit", methods=["GET", "POST"])
def athlete_edit(athlete_id):
    db = get_db()
    athlete = db.execute(
        "SELECT * FROM athlete WHERE id = ?",
        (athlete_id,)
    ).fetchone()

    if athlete is None:
        abort(404)

    if request.method == "POST":
        name = request.form["name"]
        sport_name = request.form["sport_name"]
        age = request.form["age"]

        db.execute(
            "UPDATE athlete SET name = ?, sport_name = ?, age = ? WHERE id = ?",
            (name, sport_name, age, athlete_id)
        )
        db.commit()

        return redirect(url_for("athlete_index"))

    return render_template("athlete/edit.html", athlete=athlete)


@app.route("/athletes/<int:athlete_id>/delete", methods=["POST"])
def athlete_delete(athlete_id):
    db = get_db()

    athlete = db.execute(
        "SELECT * FROM athlete WHERE id = ?",
        (athlete_id,)
    ).fetchone()

    if athlete is None:
        abort(404)

    db.execute(
        "DELETE FROM athlete WHERE id = ?",
        (athlete_id,)
    )
    db.commit()

    return redirect(url_for("athlete_index"))


if __name__ == "__main__":
    init_db()
    app.run(port=57728, debug=True)