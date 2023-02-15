<?php

$colorsvibrant = array(
    array(140,81,10),
    array(216,179,101),
    array(246,232,195),
    array(199,234,229),
    array(90,180,172),
    array(1,102,94),

    array(215,48,39),
    array(252,141,89),
    array(254,224,144),
    array(224,243,248),
    array(145,191,219),
    array(69,117,180),

    /* colors above are color-blind approved */
    array(31,120,180),
    array(178,223,138),
    array(51,160,44),
    array(251,154,153),
    array(227,26,28),
    array(253,191,111),
    array(255,127,0),
    array(202,178,214),
    array(106,61,154),
    array(255,255,153),
    array(177,89,40),

    array(255,255,179),
    array(190,186,218),
    array(251,128,114),
    array(128,177,211),
    array(253,180,98),
    array(179,222,105),
    array(252,205,229),
    array(217,217,217),
    array(188,128,189),
    array(204,235,197),
    array(255,237,111),

    "scheme" => "dark"
);

$colorspastel = array(
    array(215,48,39),
    array(252,141,89),
    array(254,224,144),
    array(224,243,248),
    array(145,191,219),
    array(69,117,180),
    /* colors above are color-blind approved */

    array(140,81,10),
    array(216,179,101),
    array(246,232,195),
    array(199,234,229),
    array(90,180,172),
    array(1,102,94),

    array(255,255,179),
    array(190,186,218),
    array(251,128,114),
    array(128,177,211),
    array(253,180,98),
    array(179,222,105),
    array(252,205,229),
    array(217,217,217),
    array(188,128,189),
    array(204,235,197),
    array(255,237,111),

    array(31,120,180),
    array(178,223,138),
    array(51,160,44),
    array(251,154,153),
    array(227,26,28),
    array(253,191,111),
    array(255,127,0),
    array(202,178,214),
    array(106,61,154),
    array(255,255,153),
    array(177,89,40),

    "scheme" => "light"
);

$color_scheme = "auto";
if ($_COOKIE["color_scheme"]) $color_scheme = $_COOKIE["color_scheme"];


/*** FOR TESTING ONLY ***/


if ($color_scheme === "auto") {                                 /*** ELSEIF FOR TESTING ***/
    setcookie("color_scheme", "auto", time()+31556926 ,'/');
    $GLOBALS["scheme_mode"] = "auto";
    if ($_COOKIE["prefers_color_scheme"] == "dark") {
        $GLOBALS["backgroundColorRed"] = 47;
        $GLOBALS["backgroundColorGreen"] = 20;
        $GLOBALS["backgroundColorBlue"] = 27;

        $GLOBALS["textColorRed"] = 201;
        $GLOBALS["textColorGreen"] = 198;
        $GLOBALS["textColorBlue"] = 198;

        $GLOBALS["colors"] = $colorsvibrant;
    }
    else {
        $GLOBALS["backgroundColorRed"] = 238;
        $GLOBALS["backgroundColorGreen"] = 212;
        $GLOBALS["backgroundColorBlue"] = 200;

        $GLOBALS["textColorRed"] = 60;
        $GLOBALS["textColorGreen"] = 59;
        $GLOBALS["textColorBlue"] = 59;

        $GLOBALS["colors"] = $colorspastel;
    }
} elseif ($color_scheme === "light") {
    echo "<link rel='stylesheet' href='assets/css/light.css' />";
    setcookie("color_scheme", "light", time()+31556926 ,'/');
    $GLOBALS["backgroundColorRed"] = 238;
    $GLOBALS["backgroundColorGreen"] = 212;
    $GLOBALS["backgroundColorBlue"] = 200;

    $GLOBALS["textColorRed"] = 60;
    $GLOBALS["textColorGreen"] = 59;
    $GLOBALS["textColorBlue"] = 59;

    $GLOBALS["scheme_mode"] = "light";
    $GLOBALS["colors"] = $colorspastel;
} elseif ($color_scheme === "dark") {
    echo "<link rel='stylesheet' href='assets/css/dark.css' />";
    setcookie("color_scheme", "dark", time()+31556926 ,'/');
    $GLOBALS["backgroundColorRed"] = 47;
    $GLOBALS["backgroundColorGreen"] = 20;
    $GLOBALS["backgroundColorBlue"] = 27;

    $GLOBALS["textColorRed"] = 201;
    $GLOBALS["textColorGreen"] = 198;
    $GLOBALS["textColorBlue"] = 198;

    $GLOBALS["scheme_mode"] = "dark";
    $GLOBALS["colors"] = $colorsvibrant;
} elseif ($color_scheme === "contrast") {
    echo "<link rel='stylesheet' href='assets/css/contrast.css' />";
    setcookie("color_scheme", "contrast", time()+31556926 ,'/');
    $GLOBALS["backgroundColorRed"] = 255;
    $GLOBALS["backgroundColorGreen"] = 255;
    $GLOBALS["backgroundColorBlue"] = 255;

    $GLOBALS["textColorRed"] = 0;
    $GLOBALS["textColorGreen"] = 0;
    $GLOBALS["textColorBlue"] = 0;

    $GLOBALS["scheme_mode"] = "contrast";
    $GLOBALS["colors"] = $colorspastel;
}
setcookie('prefers_color_scheme', '', time() - 3600, '/'); // empty value and past timestamp
?>
<script src="assets/js/src/js.cookie.min.js"></script>
<script type="text/javascript">
    window.addEventListener("load", function() {
        var selectMenu = document.getElementById('color_scheme');
        selectMenu.onchange = function() {
            var selectedValue = selectMenu.value;
            console.log("selectMenu.onchange");
            if (selectedValue === "1") {
                auto();
            } else if (selectedValue === "2") {
                light();
            } else if (selectedValue === "3") {
                dark();
            } else if (selectedValue === "4") {
                contrast();
            }
        }
    });
    function auto() {
        var CookieDate = new Date;
        CookieDate.setFullYear(CookieDate.getFullYear() +1);
        document.cookie = 'color_scheme=auto; expires=' + CookieDate.toUTCString() + ';';
        location.reload();
    }
    function light() {
        var CookieDate = new Date;
        CookieDate.setFullYear(CookieDate.getFullYear() +1);
        document.cookie = 'color_scheme=light; expires=' + CookieDate.toUTCString() + ';';
        location.reload();
    }
    function dark() {
        var CookieDate = new Date;
        CookieDate.setFullYear(CookieDate.getFullYear() +1);
        document.cookie = 'color_scheme=dark; expires=' + CookieDate.toUTCString() + ';';
        location.reload();
    }
    function contrast() {
        var CookieDate = new Date;
        CookieDate.setFullYear(CookieDate.getFullYear() +1);
        document.cookie = 'color_scheme=contrast; expires=' + CookieDate.toUTCString() + ';';
        location.reload();
    }
    /* color scheme handler */
    /*! js-cookie v3.0.1 | MIT */
    //!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self,function(){var n=e.Cookies,o=e.Cookies=t();o.noConflict=function(){return e.Cookies=n,o}}())}(this,(function(){"use strict";function e(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var o in n)e[o]=n[o]}return e}return function t(n,o){function r(t,r,i){if("undefined"!=typeof document){"number"==typeof(i=e({},o,i)).expires&&(i.expires=new Date(Date.now()+864e5*i.expires)),i.expires&&(i.expires=i.expires.toUTCString()),t=encodeURIComponent(t).replace(/%(2[346B]|5E|60|7C)/g,decodeURIComponent).replace(/[()]/g,escape);var c="";for(var u in i)i[u]&&(c+="; "+u,!0!==i[u]&&(c+="="+i[u].split(";")[0]));return document.cookie=t+"="+n.write(r,t)+c}}return Object.create({set:r,get:function(e){if("undefined"!=typeof document&&(!arguments.length||e)){for(var t=document.cookie?document.cookie.split("; "):[],o={},r=0;r<t.length;r++){var i=t[r].split("="),c=i.slice(1).join("=");try{var u=decodeURIComponent(i[0]);if(o[u]=n.read(c,u),e===u)break}catch(e){}}return e?o[e]:o}},remove:function(t,n){r(t,"",e({},n,{expires:-1}))},withAttributes:function(n){return t(this.converter,e({},this.attributes,n))},withConverter:function(n){return t(e({},this.converter,n),this.attributes)}},{attributes:{value:Object.freeze(o)},converter:{value:Object.freeze(n)}})}({read:function(e){return'"'===e[0]&&(e=e.slice(1,-1)),e.replace(/(%[\dA-F]{2})+/gi,decodeURIComponent)},write:function(e){return encodeURIComponent(e).replace(/%(2[346BF]|3[AC-F]|40|5[BDE]|60|7[BCD])/g,decodeURIComponent)}},{path:"/"})}));
    // code to set the `color_scheme` cookie
    var prefers_color_scheme = Cookies.get("prefers_color_scheme");
    function get_color_scheme() {
        return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light";
    }
    function update_color_scheme() {
        Cookies.set("prefers_color_scheme", get_color_scheme());
    }
    // read & compare cookie `color-scheme`
    if ((typeof prefers_color_scheme === "undefined") || (get_color_scheme() !== <?php echo $color_scheme; ?>))
        update_color_scheme();
    // detect changes and change the cookie
    if (window.matchMedia)
        //window.matchMedia("(prefers-color-scheme: dark)").addListener( update_color_scheme ); //deprecated
        window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", () => {
            this.update_color_scheme();
        });
    var php_color_scheme = "<?php echo $GLOBALS['colors']['scheme']; ?>";
    if (
        navigator.cookieEnabled
        && "<?php echo $GLOBALS["scheme_mode"]; ?>" === "auto"
        && php_color_scheme !== get_color_scheme()
    ) location.reload();
</script>