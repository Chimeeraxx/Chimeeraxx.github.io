class Todo {

  constructor() {
    this.tasks = [];
    this.term = "";
  }

  getFilteredTasks() {
    if (this.term.length < 2) {
      return this.tasks;
    }
  
    return this.tasks.filter(task =>
      task.text.toLowerCase().includes(this.term.toLowerCase())
    );
  }

  highlight(text) {
    if (!this.term || this.term.length < 2) return text;
  
    const regex = new RegExp(`(${this.term})`, "gi");
    return text.replace(regex, `<mark>$1</mark>`);
  }

  draw() {
    const list = document.getElementById("taskList");
    list.innerHTML = "";
  
    const tasksToShow = this.getFilteredTasks();
  
    tasksToShow.forEach(task => {
      const realIndex = this.tasks.indexOf(task);
  
      const li = document.createElement("li");
  
      li.innerHTML = `
        <span class="task-text">${this.highlight(task.text)}</span>
        <span class="task-date">${task.date ? task.date.replace("T", " ") : ""}</span>
        <button class="delete-btn" onclick="document.todo.delete(${realIndex})">Delete</button>
      `;
  
      li.addEventListener("click", (e) => {
        if (e.target.tagName === "BUTTON") return;
        this.edit(realIndex);
      });
  
      list.appendChild(li);
    });
  }

  add(text, date) {
    this.tasks.push({ text, date });
    this.save();
    this.draw();
  }

  delete(index) {
    this.tasks.splice(index, 1);
    this.save();
    this.draw();
  }

  edit(index) {
    const task = this.tasks[index];

    const newText = prompt("Edit the name of the fruit:", task.text);
    if (newText === null) return;

    const newDate = prompt("Edit the date:", task.date);

    this.tasks[index] = {
      text: newText,
      date: newDate
    };

    this.save();
    this.draw();
  }
  
  save() {
    console.log("SAVE działa", this.tasks);
    localStorage.setItem("tasks", JSON.stringify(this.tasks));
  }

  load() {
    const data = localStorage.getItem("tasks");
    if (data) {
      this.tasks = JSON.parse(data);
    }
  }
  
}
document.todo = new Todo();
document.todo.load();

if (document.todo.tasks.length === 0) {
  document.todo.tasks = [
    { text: "Apple", date: "2026-04-10 12:00" },
    { text: "Banana", date: "2026-04-12 15:00" },
    { text: "Pineapple", date: "2026-04-15 18:00" }
  ];

  document.todo.save(); 
}


document.todo.draw();

document.getElementById("addBtn").addEventListener("click", () => {
  const text = document.getElementById("newTask").value;
  const date = document.getElementById("taskDate").value;

  if (text.length < 3 || text.length > 255) {
    alert("Text must be 3–255 characters");
    return;
  }

  if (date && new Date(date) < new Date()) {
    alert("Date must be in the future");
    return;
  }

  document.todo.add(text, date);

  document.getElementById("newTask").value = "";
  document.getElementById("taskDate").value = "";
});

document.getElementById("search").addEventListener("input", (e) => {
  document.todo.term = e.target.value;
  document.todo.draw();
});