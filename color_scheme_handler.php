<script src="assets/js/src/js.cookie.min.js"></script>
<script type="text/javascript">
    var color_scheme = "";
    var prefers_color_scheme = get_color_scheme();
    if (Cookies.get("color_scheme")) {
        color_scheme = Cookies.get("color_scheme");
        setTheme(color_scheme);
    }
    else setTheme(prefers_color_scheme);

    window.addEventListener("load", function() {
        var selectMenu = document.getElementById('color_scheme');
        selectMenu.onchange = function() {
            var selectedValue = selectMenu.value;
            setTheme(selectedValue);
        }
    });

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
        var skyandweatherWrapper = document.getElementById("skyandweather-wrapper");

        if (theme === 'contrast') {
            weatherButton.style.display = "none";
            skyandweatherWrapper.style.display = "none";
        } else {
            weatherButton.style.display = "block";
            skyandweatherWrapper.style.display = "block";
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