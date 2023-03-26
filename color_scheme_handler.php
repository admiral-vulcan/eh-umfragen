<script src="assets/js/src/js.cookie.min.js"></script>
<script type="text/javascript">
    window.addEventListener("load", function() {
        var selectMenu = document.getElementById('color_scheme');
        selectMenu.onchange = function() {
            var selectedValue = selectMenu.value;
            setTheme(selectedValue);
        }
    });
    window.addEventListener("DOMContentLoaded", function (){
        var selectMenu = document.getElementById('color_scheme');
        var selectedValue = selectMenu.value;
        setTheme(selectedValue);
    })

    function setTheme(theme) {
        update_color_scheme();
        setTimeout(function () {
            checkUserWeatherState();
            if (typeof setButtonsPosition === "function") setButtonsPosition();
        }, 100);

        const themeLink = document.getElementById('theme-css');
        let newHref;

        if (theme === 'auto') {
            newHref = 'colors.css';
        } else {
            newHref = `assets/css/${theme}.css`;
        }

        if (themeLink) {
            themeLink.parentNode.removeChild(themeLink);
        }
        var weatherButton = document.getElementById("weather-button");
        var skyandweathercontainer = document.getElementById("skyandweather-container");
        var starfield = document.getElementById("starfield");
        var sun = document.getElementById("sun");
        var moon = document.getElementById("moon");
        var sky = document.getElementById("sky");
        var clouds = document.getElementById("clouds");
        var clouds1 = document.getElementById("clouds-1");
        var clouds2 = document.getElementById("clouds-2");
        var clouds3 = document.getElementById("clouds-3");

        setEverythingWeather();

        if (theme === 'contrast') {
            weatherButton.style.display = "none";
            sky.style.display = "none";
            skyandweathercontainer.style.display = "none";
            skyandweathercontainer.style.opacity = "0";
            starfield.style.display = "none";
            sun.style.opacity = "0";
            moon.style.opacity = "0";
            clouds.style.opacity = "0";
            clouds1.style.opacity = "0";
            clouds2.style.opacity = "0";
            clouds3.style.opacity = "0";
        } else {
            weatherButton.style.display = "block";
            sky.style.display = "block";
            skyandweathercontainer.style.display = "block";
            skyandweathercontainer.style.opacity = "0.5";
            starfield.style.display = "block";
            sun.style.opacity = "1";
            moon.style.opacity = "1";
            clouds.style.opacity = "1";
            clouds1.style.opacity = "0.5";
            clouds2.style.opacity = "1";
            clouds3.style.opacity = "1";
        }

        if (newHref) {
            const mainCssLink = document.querySelector('link[href="assets/css/main.css"]');
            const newLink = document.createElement('link');
            newLink.rel = 'stylesheet';
            newLink.href = newHref;
            newLink.id = 'theme-css';
            mainCssLink.insertAdjacentElement('afterend', newLink);
        }

        const CookieDate = new Date();
        CookieDate.setFullYear(CookieDate.getFullYear() + 1);
        document.cookie = `color_scheme=${theme}; expires=${CookieDate.toUTCString()};`;
    }
    function get_color_scheme() {
        return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light";
    }
    function update_color_scheme() {
        Cookies.set("prefers_color_scheme", get_color_scheme());
    }

</script>