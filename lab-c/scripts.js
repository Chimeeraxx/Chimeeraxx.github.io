let map;
let marker;
let gameState = "incomplete";  //stan gry, moze byc: incomplete, correct albo wrong

//prosba o zgode na powiadomienia
document.addEventListener("DOMContentLoaded", () => {
  if ("Notification" in window) {
    Notification.requestPermission();
  }

  //leaflet
  map = L.map("map", {
    preferCanvas: true
  }).setView([53.430127, 14.564802], 18);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    crossOrigin: "anonymous"
  }).addTo(map);

  marker = L.circleMarker([53.430127, 14.564802], {
    radius: 8
  }).addTo(map);

  //przygotowanie planszy, puzzli i dropzones
  prepareBoard();
  preparePieces();
  prepareDropZones();

  //przysick getlocation
  document.getElementById("getLocation").addEventListener("click", () => {
    if (!navigator.geolocation) {
      alert("Brak geolokalizacji");
      return;
    }
  //pobranie aktualnej pozycji usera
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        document.getElementById("latitude").innerText = lat.toFixed(6);
        document.getElementById("longitude").innerText = lon.toFixed(6);

        map.setView([lat, lon], 18);
        marker.setLatLng([lat, lon]);
      },
      () => alert("Błąd lokalizacji")
    );
  });
//zapis mapy jako obraz
  document.getElementById("saveButton").addEventListener("click", () => {
    leafletImage(map, (err, canvas) => {
      if (err) {
        console.error("Błąd eksportu mapy:", err);
        alert("Nie udało się wyeksportować mapy.");
        return;
      }

      const raster = document.getElementById("rasterMap");
      const ctx = raster.getContext("2d");

      //czysczenie i rysowanie obrazu mapy
      ctx.clearRect(0, 0, raster.width, raster.height);
      ctx.drawImage(canvas, 0, 0, raster.width, raster.height);

      //ukrywanie mapy i pokazanie canvas z obrazem
      document.getElementById("map").style.display = "none";
      raster.style.display = "block";

      generatePuzzle();
    });
  });
});

//przypisanie indeksu dla kazdego bloku
function prepareBoard() {
  const slots = document.querySelectorAll(".board-slot");

  slots.forEach((slot, index) => {
    slot.dataset.index = index;
  });
}

//ustawienie id, przeciaganie
function preparePieces() {
  const pieces = document.querySelectorAll(".puzzle-piece");

  pieces.forEach((piece, index) => {
    piece.id = `piece-${index}`;
    piece.draggable = true;

    piece.addEventListener("dragstart", (event) => {
      piece.classList.add("dragging");
      event.dataTransfer.setData("text/plain", piece.id);
    });

    piece.addEventListener("dragend", () => {
      piece.classList.remove("dragging");
    });
  });
}

//przygotowanie slotow na puzzle
function prepareDropZones() {
  const slots = document.querySelectorAll(".board-slot");
  const puzzleContainer = document.getElementById("puzzleContainer");

  //oblusga przyciagania puzzli na sloty
  slots.forEach((slot) => {
    slot.addEventListener("dragover", (event) => {
      event.preventDefault();
    });

    slot.addEventListener("dragenter", () => {
      slot.classList.add("drag-over");
    });

    slot.addEventListener("dragleave", () => {
      slot.classList.remove("drag-over");
    });

    slot.addEventListener("drop", (event) => {
      event.preventDefault();
      slot.classList.remove("drag-over");

      const pieceId = event.dataTransfer.getData("text/plain");
      const draggedPiece = document.getElementById(pieceId);

      if (!draggedPiece) return;
    //jesli slot zajety to poprzedni puzzel wraca
      if (slot.children.length > 0) {
        puzzleContainer.appendChild(slot.children[0]);
      }

      slot.appendChild(draggedPiece);
      checkBoardState();
    });
  });
//przyciaganie puzzli z powrotem do pojemnika
  puzzleContainer.addEventListener("dragover", (event) => {
    event.preventDefault();
  });

  puzzleContainer.addEventListener("dragenter", () => {
    puzzleContainer.classList.add("drag-over");
  });

  puzzleContainer.addEventListener("dragleave", () => {
    puzzleContainer.classList.remove("drag-over");
  });

  puzzleContainer.addEventListener("drop", (event) => {
    event.preventDefault();
    puzzleContainer.classList.remove("drag-over");

    const pieceId = event.dataTransfer.getData("text/plain");
    const draggedPiece = document.getElementById(pieceId);

    if (!draggedPiece) return;

    puzzleContainer.appendChild(draggedPiece);
    checkBoardState();
  });
}
//generowanie puzzli z obrazu
function generatePuzzle() {
  const canvas = document.getElementById("rasterMap");
  const pieces = document.querySelectorAll(".puzzle-piece");
  const boardSlots = document.querySelectorAll(".board-slot");
  const tray = document.getElementById("puzzleContainer");

  const rows = 4;
  const cols = 4;
  const pieceWidth = canvas.width / cols;
  const pieceHeight = canvas.height / rows;

  gameState = "incomplete";

  boardSlots.forEach(slot => {
    slot.innerHTML = "";
  });
//tlo, rozmiar, pozycja
  pieces.forEach((piece, index) => {
    const row = Math.floor(index / cols);
    const col = index % cols;

    piece.dataset.correctIndex = index;
    piece.style.width = `${pieceWidth}px`;
    piece.style.height = `${pieceHeight}px`;
    piece.style.backgroundImage = `url(${canvas.toDataURL("image/png")})`;
    piece.style.backgroundSize = `${canvas.width}px ${canvas.height}px`;
    piece.style.backgroundPosition = `-${col * pieceWidth}px -${row * pieceHeight}px`;
    piece.style.backgroundRepeat = "no-repeat";

    tray.appendChild(piece);
  });

  shufflePuzzle();
}

//tasowanie puzzli
function shufflePuzzle() {
  const container = document.getElementById("puzzleContainer");
  const pieces = Array.from(container.children);

  for (let i = pieces.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    container.appendChild(pieces[j]);
  }
}

//sprawdzanie stanu planszy i poprawnosci ulozenia puzzli
function checkBoardState() {
  const slots = document.querySelectorAll(".board-slot");

  //czy wszystkie sloty sa wypelnione?
  const allFilled = Array.from(slots).every(slot => {
    return slot.querySelector(".puzzle-piece");
  });

  if (!allFilled) {
    gameState = "incomplete";
    return;
  }

  //czy wszystkie puzzle sa ulozone poprawnie?
  const allCorrect = Array.from(slots).every(slot => {
    const piece = slot.querySelector(".puzzle-piece");
    return piece.dataset.correctIndex === slot.dataset.index;
  });

  console.debug("Czy wszystkie puzzle są poprawne?", allCorrect);

  if (allCorrect) {
    console.debug("Puzzle ułożone poprawnie.");
    showNotification("Brawo!", "Wszystkie puzzle zostały ułożone poprawnie :)");
    gameState = "correct";
  } else {
    console.debug("Układ puzzli jest niepoprawny.");
    showNotification("Niepoprawny układ", "Niestety Ci się nie udało :(");
    gameState = "wrong";
  }
}

//sytem notyfikacji
function showNotification(title, body) {
  if ("Notification" in window && Notification.permission === "granted") {
    new Notification(title, { body });
  } else {
    alert(`${title} ${body}`);
  }
}
