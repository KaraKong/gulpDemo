"use strict";
! function() {
    function t() {
        var t = window.navigator.userAgent,
            e = document.documentElement,
            o = document.querySelector("html"),
            i = /Android/i.test(t),
            n = window.devicePixelRatio || 1;
        console.log(e.clientWidth);
        var a = e.clientWidth / 10;
        o.style.fontSize = a + "px";
        var d = window.getComputedStyle,
            r = parseFloat(o.style.fontSize, 10),
            l = parseFloat(d(o)["font-size"], 10);
        d && Math.abs(r - l) >= 1 && (o.style.fontSize = r * (r / l) + "px"), o.setAttribute("data-dpr", n), i && o.setAttribute("data-platform", "android")
    }
    t(), window.onresize = t
}();