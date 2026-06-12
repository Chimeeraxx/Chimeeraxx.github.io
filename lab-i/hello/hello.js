import express from "express";
import path from "node:path";
import { fileURLToPath } from "node:url";

const app = express();
const port = 57728;

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

app.get("/", (req, res) => {
  res.send("Hello World from Express!");
});

app.get("/hello/:name", (req, res) => {
  const name = req.params.name;

  res.render("hello", {
    name: name,
  });
});

app.listen(port, () => {
  console.log(`Hello World app listening on port ${port}`);
});
