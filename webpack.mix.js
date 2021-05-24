const mix = require("laravel-mix");

mix
    .sass("src/resources/assets/scss/app.scss", "src/resources/assets/css/app.css")
    .js("src/resources/assets/es6/app.js", "src/resources/assets/js/app.js")
