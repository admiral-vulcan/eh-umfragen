const sky = document.getElementById("sky");
const sun = document.getElementById("sun");
const moon = document.getElementById("moon");
//const now = new Date("2023-02-05T02:00:00+01:00");
//const now = new Date("2023-02-05T12:00:00+01:00");
let now = new Date();
const latitude = 48.894051; //Ludwigsburg lat
const longitude = 9.195517; //Ludwigsburg long
const starfield = document.getElementById("starfield");
const skyandweathercontainer = document.getElementById("skyandweather-container");
const weathertemperature = document.getElementById("weather_temperature");
const times = SunCalc.getTimes(now, latitude, longitude);
const moonPhaseFrac = SunCalc.getMoonIllumination(now).phase;
let moonPositionAlt = SunCalc.getMoonPosition(now, latitude, longitude).altitude;
const currentTime = now.getHours() * 60 + now.getMinutes();
const sunset = times.sunset.getHours() * 60 + times.sunset.getMinutes();
const sunrise = times.sunrise.getHours() * 60 + times.sunrise.getMinutes();
const noon = times.solarNoon.getHours() * 60 + times.solarNoon.getMinutes();
const nauticalDusk = times.nauticalDusk.getHours() * 60 + times.nauticalDusk.getMinutes();
const nauticalDawn = times.nauticalDawn.getHours() * 60 + times.nauticalDawn.getMinutes();
const temperature = document.getElementById("temperature").value;
const topDiv = document.getElementById("top");
const computedStyle = window.getComputedStyle(topDiv);
const color = computedStyle.getPropertyValue("color");
const background = computedStyle.getPropertyValue("background-color");

const maxScreenWidth = 2935;
const minScreenWidth = 500;
const minSunHeight = 0.35;
const minBorderTop = 0.39;
const maxBorderTop = 0.70;
//moonPhaseFrac = 0.6;
var moonphase = (moonPhaseFrac * 360).toFixed();

var script = document.createElement('script');
script.src = 'assets/js/src/js.cookie.min.js';
document.head.appendChild(script);

/*
//mega testing block
const simulationSpeed = 1800000; // 30 minutes per 100ms
const daysToSubtract = 2;

// Subtract the specified number of days from the current time
now = new Date(new Date().getTime() - daysToSubtract * 24 * 3600000);

// Create the date and time display element
const dateTimeDisplay = document.createElement('div');
dateTimeDisplay.style.position = 'fixed';
dateTimeDisplay.style.top = '10px';
dateTimeDisplay.style.left = '50%';
dateTimeDisplay.style.transform = 'translateX(-50%)';
dateTimeDisplay.style.fontSize = '24px';
dateTimeDisplay.style.zIndex = '1000';
document.body.appendChild(dateTimeDisplay);

setInterval(function (){
    // Add 30 minutes to the current time for each 100ms that passes
    now = new Date(now.getTime() + simulationSpeed);
    //now = new Date(Date.UTC(2023, 2, 24, 21, 0, 0));

    // Update the date and time display
    dateTimeDisplay.textContent = now.toLocaleString();

    // Update the Moon's position
    const moonPosition = SunCalc.getMoonPosition(now, latitude, longitude);
    moonPositionAlt = moonPosition.altitude;

    // Calculate the parallactic angle
    const parallacticAngleValue = parallacticAngle(latitude, longitude, now);

    // Apply the parallactic angle rotation
    moon.style.transform = `rotate(${parallacticAngleValue}deg)`;

    // Update the moon phase
    moonphase = (SunCalc.getMoonIllumination(now).phase * 360).toFixed();
    setMoonPhase(moonphase);

    // Set Moon position
    setMoon(calculateMinMoonHeight(), 0.94, moonPositionAlt);
}, 100); // Update every 100ms
//mega testing block END
*/

function interpolateColors(color1, color2, ratio) {
    if (ratio > 1) ratio = 1;
    else if (ratio < 0) ratio = 0;
    // Parse colors
    const color1Components = parseColor(color1);
    const color2Components = parseColor(color2);

    // Interpolate colors
    const interpolatedComponents = [];
    for (let i = 0; i < 3; i++) {
        interpolatedComponents[i] = Math.round(color1Components[i] + (color2Components[i] - color1Components[i]) * ratio);
    }

    // Return interpolated color as hex string
    return `#${interpolatedComponents.map(c => c.toString(16).padStart(2, '0')).join('')}`;
}

function parseColor(color) {
    // Parse color as hex string
    if (color.startsWith('#')) {
        return [
            parseInt(color.slice(1, 3), 16),
            parseInt(color.slice(3, 5), 16),
            parseInt(color.slice(5, 7), 16)
        ];
    }

    // Parse color as RGB string
    if (color.startsWith('rgb(')) {
        return color.slice(4, -1).split(',').map(c => parseInt(c.trim()));
    }

    // Return default color
    return [0, 0, 0];
}

function calculateMinSunHeight() {
    const screenWidth = window.innerWidth;
    if (screenWidth < minScreenWidth) {
        return minSunHeight;
    } else if (screenWidth > maxScreenWidth) {
        return 0;
    } else {
        return minSunHeight * (maxScreenWidth - screenWidth) / (maxScreenWidth - minScreenWidth);
    }
}

function calculateMinMoonHeight() {
    let minMoonHeight;
    const screenWidth = window.innerWidth;
    const absoluteMinMin = 0.7;
    const absoluteMinMax = 0.2;
    const minScreenWidth = 940; //in px
    const maxScreenWidth = 5000; //in px
    if (screenWidth <= minScreenWidth) {
        minMoonHeight = absoluteMinMin;
    } else if (screenWidth >= maxScreenWidth) {
        minMoonHeight = absoluteMinMax;
    } else {
        minMoonHeight = absoluteMinMin + (absoluteMinMax - absoluteMinMin) * (screenWidth - minScreenWidth) / (maxScreenWidth - minScreenWidth);
    }

    return minMoonHeight;
}

/*
const sunCutter = document.getElementById("sunCutter");

function calculateBorderTop() {
    const screenWidth = window.innerWidth;

    let borderTop = minBorderTop + (maxBorderTop - minBorderTop) * (screenWidth - minScreenWidth) / (maxScreenWidth - minScreenWidth);
    let borderStart = borderTop - minBorderTop +0.1;
    borderStart = (borderStart*100).toFixed() + "%";
    borderTop = (borderTop*100).toFixed() + "%";
    //sunCutter.style.clipPath = "polygon(0 10%, 100% "+borderTop+", 100% 0, 0 0)";
    return borderTop;
}
*/

function calculateOpacityGradient(sunHeight) {
    let opacityGradient;
    if (sunHeight <= 0.40) {
        opacityGradient = 100;
    } else if (sunHeight >= 0.62) {
        opacityGradient = 0;
    } else {
        const t = (sunHeight - 0.40) / (0.62 - 0.40);
        opacityGradient = 100 - (100 * t);
    }
    return opacityGradient.toFixed();
}

function calculateSunColorAuraGradient(sunHeight) {
    let sunColorAuraGradient;
    if (sunHeight <= 0.40) {
        sunColorAuraGradient = 100;
    } else if (sunHeight >= 0.62) {
        sunColorAuraGradient = 50;
    } else {
        const t = (sunHeight - 0.40) / (0.62 - 0.40);
        sunColorAuraGradient = 100 - (100 - 50) * t;
    }
    return sunColorAuraGradient.toFixed();
}

function calculateSunColorCenterGradient(sunHeight) {
    let sunColorCenterGradient;
    if (sunHeight <= 0.40) {
        sunColorCenterGradient = 100;
    } else if (sunHeight >= 0.62) {
        sunColorCenterGradient = 90;
    } else {
        const t = (sunHeight - 0.40) / (0.62 - 0.40);
        sunColorCenterGradient = 100 - (100 - 90) * t;
    }
    return sunColorCenterGradient.toFixed();
}

function updateColors(sunHeight) {
    let skyColor;
    let sunColorAura;
    let sunColorCenter;
    const maxDay = 24 * 60;

    skyColorNoon = "#37b4ee";
    sunColorAuraNoon = "#ffbf00";
    sunColorCenterNoon = "#ffffff";

    skyColorRiseSet = "#ee3d37";
    sunColorAuraRiseSet = "#ea2f0e";
    sunColorCenterRiseSet = "#ff9900";

    skyColorNight = "#000000";
    sunColorAuraNight = "#000000";
    sunColorCenterNight = "#000000";

    starfield.style.opacity = "0";

    if (currentTime > nauticalDusk || currentTime <= nauticalDawn) {
        //full night
        skyColor = "#000000";
        sunColorAura = "#000000";
        sunColorCenter = "#000000";
        starfield.style.opacity = "1";
    }
    else if (currentTime <= sunrise) {
        //nauticalDawn to sunrise
        const nauticalDawnRatio = (currentTime - nauticalDawn) / (sunrise - nauticalDawn);
        skyColor = interpolateColors(skyColorNight, skyColorRiseSet, nauticalDawnRatio);
        sunColorAura = interpolateColors(sunColorAuraNight, sunColorAuraRiseSet, nauticalDawnRatio);
        sunColorCenter = interpolateColors(sunColorCenterNight, sunColorCenterRiseSet, nauticalDawnRatio);
        starfield.style.opacity = (nauticalDawnRatio*-1+1).toString();
    }
    else if (currentTime <= noon) {
        //sunrise to noon
        const sunriseRatio = (currentTime - sunrise) / (noon - sunrise);
        skyColor = interpolateColors(skyColorRiseSet, skyColorNoon, sunriseRatio);
        sunColorAura = interpolateColors(sunColorAuraRiseSet, sunColorAuraNoon, sunriseRatio);
        sunColorCenter = interpolateColors(sunColorCenterRiseSet, sunColorCenterNoon, sunriseRatio);
    }
    else if (currentTime <= sunset) {
        //noon to sunset
        const sunsetRatio = (currentTime - noon) / (sunset - noon);
        skyColor = interpolateColors(skyColorNoon, skyColorRiseSet, sunsetRatio);
        sunColorAura = interpolateColors(sunColorAuraNoon, sunColorAuraRiseSet, sunsetRatio);
        sunColorCenter = interpolateColors(sunColorCenterNoon, sunColorCenterRiseSet, sunsetRatio);
    }
    else if (currentTime <= nauticalDusk) {
        //sunset to nauticalDusk
        const nauticalDuskRatio = (currentTime - sunset) / (nauticalDusk - sunset);
        skyColor = interpolateColors(skyColorRiseSet, skyColorNight, nauticalDuskRatio);
        sunColorAura = interpolateColors(sunColorAuraRiseSet, sunColorAuraNight, nauticalDuskRatio);
        sunColorCenter = interpolateColors(sunColorCenterRiseSet, sunColorCenterNight, nauticalDuskRatio);
        starfield.style.opacity = nauticalDuskRatio.toString();
    }
    else if (currentTime <= maxDay || currentTime <= nauticalDawn) {
        //full night
        const nauticalDuskRatio = (currentTime - sunset) / (nauticalDusk - sunset);
        skyColor = interpolateColors(skyColorRiseSet, skyColorNight, nauticalDuskRatio);
        sunColorAura = interpolateColors(sunColorAuraRiseSet, sunColorAuraNight, nauticalDuskRatio);
        sunColorCenter = interpolateColors(sunColorCenterRiseSet, sunColorCenterNight, nauticalDuskRatio);
        starfield.style.opacity = "1";
    }
    else {
        //should never be
        skyColor = "#000000";
        sunColorAura = "#000000";
    }

    let opacityGradient = calculateOpacityGradient(sunHeight);
    let sunColorAuraGradient = calculateSunColorAuraGradient(sunHeight);
    let sunColorCenterGradient = calculateSunColorCenterGradient(sunHeight);

    sky.style.background = "linear-gradient(190deg, "+skyColor+" 5%, rgba(0,0,0,0) 40%)";
    sun.style.background = "linear-gradient(24deg, rgba(0,0,0,0) "+opacityGradient+"%, "+sunColorAura+" "+sunColorAuraGradient+"%, "+sunColorCenter+" "+sunColorCenterGradient+"%)"; //sun color
}

function setSun(currentTime, minSunHeight, maxSunHeight, maxDay, nauticalDawn, nauticalDusk) {
    let sunHeight;
    if (currentTime >= nauticalDawn && currentTime < noon) {
        // Rising phase: fast start, slow finish
        const t = (currentTime - nauticalDawn) / (noon - nauticalDawn);
        sunHeight = minSunHeight + (maxSunHeight - minSunHeight) * Math.pow(t, 0.5);
    } else if (currentTime >= noon && currentTime < nauticalDusk) {
        // Setting phase: slow start, fast finish
        const t = (currentTime - noon) / (nauticalDusk - noon);
        sunHeight = maxSunHeight - (maxSunHeight - minSunHeight) * Math.pow(t, 2);
    } else if (currentTime >= nauticalDusk && currentTime < maxDay) {
        // Low phase: at minimum height
        sunHeight = minSunHeight;
    } else {
        // At night: at minimum height
        sunHeight = minSunHeight;
    }
    updateColors(sunHeight);
    sunHeight = (sunHeight*100).toFixed() + "%";
    sun.style.bottom = sunHeight;
}

function setMoon(minMoonHeight, maxMoonHeight, moonAltitude) {
    let moonHeight;
    let moonHeightTop;
    if (moonAltitude >= 0) {
        // Moon is above the horizon: calculate moon height based on altitude
        moonHeight = minMoonHeight + (maxMoonHeight - minMoonHeight) * (moonAltitude + 1.57) / (2 * 1.57);
        // Invert moonHeight to work with CSS top
        moonHeight = 1 - moonHeight;
        moonHeightTop = (moonHeight*100).toFixed() + "%";
    } else {
        // Moon is below the horizon: set minimum height
        moonHeightTop = "-100%";
    }
    moon.style.top = moonHeightTop;
}


function setMoonPhase(deg, parallacticAngle) {
    deg = deg*-1+180;
    if (deg < 0) deg = 360 + deg;

    let degphase = deg;
    if (deg >  90) degphase += 180;
    if (deg > 270) degphase += 180;

    document.querySelector('.divider').style.transform = `rotate3d(0, -1, 0, ${degphase}deg)`

    const hemispheres = document.querySelectorAll('.hemisphere')
    const divider = document.querySelectorAll('.divider')

    if (degphase > 360) {
        // Left
        hemispheres[0].classList.remove('dark')
        hemispheres[0].classList.add('light')

        hemispheres[1].classList.add('dark')
        hemispheres[1].classList.remove('light')
    } else {
        hemispheres[0].classList.add('dark')
        hemispheres[0].classList.remove('light')

        hemispheres[1].classList.remove('dark')
        hemispheres[1].classList.add('light')
    }

    if (degphase > 180 && degphase < 540) {
        // Left
        divider[0].style.backgroundColor = '#21211f';
    } else {
        divider[0].style.backgroundColor = '#F4F6F0';

    }
    // Apply the parallactic angle rotation
    moon.style.transform = `rotate(${parallacticAngle}deg)`;

}

//set crescent moon rotation as seen from earth
function parallacticAngle(observerLat, observerLon, date) {
    const moonPosition = SunCalc.getMoonPosition(date, observerLat, observerLon);
    const parallacticAngleRadians = moonPosition.parallacticAngle;

    // Convert parallactic angle from radians to degrees
    const parallacticAngleDegrees = parallacticAngleRadians * (180 / Math.PI);

    return parallacticAngleDegrees;
}





// Get the checkbox element
var weatherCheckbox = document.getElementById('weather_checkbox');

if (color !== "rgb(0, 0, 0)" && background !== "rgb(255, 255, 255)") {
    sky.style.display = "block";
    sun.style.display = "block";
    moon.style.display = "flex";

    const moonPosition = SunCalc.getMoonPosition(now, latitude, longitude); //these two get rotation
    const moonParallacticAngle = parallacticAngle(latitude, longitude, now);

    setInterval(function (){
        setSun(currentTime, calculateMinSunHeight(), 0.87, 1440, nauticalDawn, nauticalDusk);
        setMoonPhase(moonphase, moonParallacticAngle);
        setMoon(calculateMinMoonHeight(), 0.94, moonPositionAlt)
    }, 1000*60);  //100 for testing

    setSun(currentTime, calculateMinSunHeight(), 0.87, 1440, nauticalDawn, nauticalDusk);
    setMoonPhase(moonphase, moonParallacticAngle);
    setMoon(calculateMinMoonHeight(), 0.94, moonPositionAlt)
    if (weatherCheckbox.checked) showWeather();
}
else hideWeather();

function checkUserWeatherState() {

    // Check if the "weather" cookie exists
    var weatherCookie = Cookies.get("weather");
    if (weatherCookie) {
        // If the cookie is present, check if its value is "show" or "hide"
        if (weatherCookie === "show") {
            // If the value is "show", show the weather
            showWeather();
            weatherCheckbox.checked = true;
        } else {
            // If the value is "hide", hide the weather
            hideWeather();
            weatherCheckbox.checked = false;
        }
    } else {
        weatherCheckbox.checked = true;
        // If the cookie is not present, create a new cookie with the value "show" or "hide" based on the checkbox state
        if (weatherCheckbox.checked) {
            Cookies.set("weather", "show");
            showWeather();
        } else {
            Cookies.set("weather", "hide");
            hideWeather();
        }
    }
}

function reactUserWeatherState() {
    if (weatherCheckbox.checked) {
        Cookies.set("weather", "show");
        showWeather();
    } else {
        Cookies.set("weather", "hide");
        hideWeather();
    }
}

// This function shows the weather
function showWeather() {
    // Show the weather here

    if (color !== "rgb(0, 0, 0)" && background !== "rgb(255, 255, 255)") {
        skyandweathercontainer.style.opacity = "0.5";
        skyandweathercontainer.style.display = "block";
        weathertemperature.style.opacity = "1";
        weathertemperature.style.display = "block";
    }
    else hideWeather();
}

// This function hides the weather
function hideWeather() {
    // Hide the weather here
    skyandweathercontainer.style.opacity = "0";
    skyandweathercontainer.style.display = "none";
    weathertemperature.style.opacity = "0";
    weathertemperature.style.display = "none";
}

function printWithoutWeather() {
    if (weatherCheckbox.checked) {
        hideWeather();
        setTimeout(showWeather, 1000);
    }
}

// Run the function when the site is loaded
window.addEventListener('load', checkUserWeatherState);
// Add an event listener for the checkbox's "click" event
weatherCheckbox.addEventListener("click", reactUserWeatherState);