'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass')(require('sass'));
var plumber = require('gulp-plumber');
var sourcemaps = require('gulp-sourcemaps');
var cleanCSS = require('gulp-clean-css');

var paths = {
    styles: {
        src: 'resources/scss/style.scss',
        watch: 'resources/scss/**/*.scss',
        dest: 'public/css',
    },
};

function buildStyles() {
    return gulp
        .src(paths.styles.src)
        .pipe(plumber())
        .pipe(sourcemaps.init())
        .pipe(
            sass({
                outputStyle: 'compressed',
                sourceMap: true,
                errLogToConsole: true,
            })
        )
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(paths.styles.dest));
}

exports.buildStyles = buildStyles;

function watch() {
    gulp.watch(paths.styles.watch, buildStyles);
}

exports.watch = watch;
exports.default = buildStyles;
