'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass')(require('sass'));
var plumber = require('gulp-plumber');
var sourcemaps = require('gulp-sourcemaps');
var cleanCSS = require('gulp-clean-css');

const {series} = require('gulp');

var pathsInvite = {
    styles: {
        src: 'resources/scss/invite-email/invite.scss',
        dest: 'public/css',
    },
};

var pathsNewCompany = {
    styles: {
        src: 'resources/scss/new-company-email/new-company.scss',
        dest: 'public/css',
    },
};

var pathsCommon = {
    styles: {
        src: 'resources/scss/common-email-template/common-email-template.scss',
        dest: 'public/css',
    },
};

function buildStylesInvite() {
    return gulp
        .src(pathsInvite.styles.src)
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
        .pipe(gulp.dest(pathsInvite.styles.dest));
}

function buildStylesNewCompany() {
    return gulp
        .src(pathsNewCompany.styles.src)
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
        .pipe(gulp.dest(pathsNewCompany.styles.dest));
}

function buildStylesCommon() {
    return gulp
        .src(pathsCommon.styles.src)
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
        .pipe(gulp.dest(pathsCommon.styles.dest));
}

exports.buildStylesInvite = buildStylesInvite;
exports.buildStylesNewCompany = buildStylesNewCompany;
exports.buildStylesCommon = buildStylesCommon;

exports.default = series(
    buildStylesInvite,
    buildStylesNewCompany,
    buildStylesCommon
);
