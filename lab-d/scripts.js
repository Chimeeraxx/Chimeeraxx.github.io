
const API_KEY = "01587098cc86cc2faed0334c0ecc26e0";

document.getElementById('weather-btn').addEventListener('click', () => {
  const city = document.getElementById('city-input').value.trim();
  if (!city) {
    alert("Musisz wpisać nazwę miasta.");
    return;
  }

  const weatherResults = document.getElementById('weather-results');
  weatherResults.classList.remove('hidden');


  //Current Weather - XMLHttpRequest

  const xhr = new XMLHttpRequest();
  const currentUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${API_KEY}&units=metric&lang=pl`;

  xhr.open('GET', currentUrl, true);
  xhr.onload = function() {
    if (this.status === 200) {
      const data = JSON.parse(this.responseText);
      console.log("Otrzymano dane (Current Weather)");
      console.log(data);
      displayCurrentWeather(data);
    } else {
      console.error("Błąd podczas pobierania aktualnej pogody");
    }
  };
  xhr.send();

  //5 Day Forecast - Fetch API

  const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?q=${city}&appid=${API_KEY}&units=metric&lang=pl`;

  fetch(forecastUrl)
    .then(response => {
      if (!response.ok) throw new Error("Błąd sieci");
      return response.json();
    })
    .then(data => {
      console.log("Otrzymano dane (5 Day Forecast)");
      console.log(data);
      displayForecast(data);
    })
    .catch(error => console.error("Błąd Fetch API:", error));
});

//funkcja pomocnicza do wyswietlania aktualnej pogody
function displayCurrentWeather(data) {
  const container = document.getElementById('current-weather');
  const iconUrl = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;

  container.innerHTML = `
        <img src="${iconUrl}" alt="ikona pogody">
        <div>
            <div class="temp">${data.main.temp} °C</div>
            <p><strong>${data.name}</strong></p>
            <p>Odczuwalna: ${data.main.feels_like} °C</p>
            <p>Opis: ${data.weather[0].description}</p>
        </div>
    `;
}

//funkcja pomocnicza do wyswietlania prognozy
function displayForecast(data) {
  const container = document.getElementById('forecast-weather');
  container.innerHTML = ''; //czyszczenie poprzednich wynikwo

  //wyswietlanie 8 pomiarow
  const forecasts = data.list.slice(0, 8);

  forecasts.forEach(item => {
    const iconUrl = `https://openweathermap.org/img/wn/${item.weather[0].icon}.png`;
    const div = document.createElement('div');
    div.className = 'forecast-item';

    div.innerHTML = `
            <div class="date-time">${item.dt_txt}</div>
            <img src="${iconUrl}" alt="ikona prognozy">
            <div class="temp">${item.main.temp} °C</div>
            <p style="font-size: 0.85rem">Odczuwalna: ${item.main.feels_like} °C</p>
            <p style="font-size: 0.85rem">${item.weather[0].description}</p>
        `;
    container.appendChild(div);
  });
}
