const fs = require("node:fs");
const path = require("node:path");
const { DatabaseSync } = require("node:sqlite");

const dbPath = path.resolve(__dirname, "data.db");
const sqlPath = path.resolve(__dirname, "sql", "02-athlete.sql");

if (fs.existsSync(dbPath)) {
    fs.unlinkSync(dbPath);
}

const db = new DatabaseSync(dbPath);

const sql = fs.readFileSync(sqlPath, "utf8");
db.exec(sql);

const tables = db
    .prepare("SELECT name FROM sqlite_master WHERE type = 'table'")
    .all();

console.log("Utworzono poprawną bazę data.db.");
console.log("Tabele w bazie:", tables);