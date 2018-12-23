# WEATHER APP

> A simple weather app built with vue.js frontend and php backend. Weather data comes from MetaWeather public api.

## Quick start

```bash
  # install php dependencies
  composer install
  # start php server
  php -S localhost:8000 weather.php

  # install vue.js dependencies
  # first cd into client folder
  cd client

  # then run npm command to start dev server
  npm run serve
```

## App Info

---

### Author

Mohammad mohiuddin mostafa kamal akib [akibbd.com](https://www.akibbd.com)

### Live project

Go to [https://akibbd.com/weather-app-vue-frontend](https://akibbd.com/weather-app-vue-frontend) to see the live project on apache server

### Version

1.0.0

## Summary

---

### PHP Backend

weather.php at root directory is the heart of all php logic. Everything is centralized here. In app folder models folder contains the Weather.php model which is the data source and communicate with MetaWeather api. In root weather.php file different file from app/api is required based on requested route. Those files contain specific data for specific request. They serve data as json for vue frontend to connect.

### Vue frontend

Vue frontend has 3 main routes:

- home route '/' fetches data for Istanbul, Berlin, London, Helsinki, Dublin, Vancouver from [http://localhost:8000/weather.php](http://localhost:8000/weather.php) where PHP is fetching those data from MetaWeather api and serving as json. These data are passed inside weather custom component as props.

```bash
  <weather :weather="weather"></weather>
```

- Route /search/:keyword is to query location and show their weather. The PHP api route for this purpose is [http://localhost/weather.php?command=search&keyword=dhaka](http://localhost/weather.php?command=search&keyword=dhaka). Based on the keyword the weather data for that will be fetched and displayed on frontend. Each of these data are passed into weather custom component.

```bash
  <weather :weather="weather"></weather>
```

- Route weather/:woeid is reached when any city on homepage is clicked. Details data for that city will be on this page.
