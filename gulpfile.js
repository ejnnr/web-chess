const gulp = require('gulp');
const del = require('del');
const typescript = require('gulp-typescript');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const changed = require('gulp-changed');
const babel = require('gulp-babel');
const tscConfig = require('./tsconfig.json');

var vendor = [
    'bower_components/**',
    '!bower_components',
    'node_modules/core-js/client/shim.min.js',
    'node_modules/zone.js/dist/zone.js',
    'node_modules/reflect-metadata/Reflect.js'
]

gulp.task('clean', function () {
    return del(['public/**', '!public', '!public/jspm_packages', '!public/config.js', '!public/index.php', '!public/.htacces', '!public/.gitignore']);
});

// Typescript compilation
gulp.task('compile:typescript', function () {
    return gulp
        .src('resources/assets/**/*.ts')
        .pipe(changed('public/assets', {extension: '.js'}))
        .pipe(sourcemaps.init())
        .pipe(typescript(tscConfig.compilerOptions))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/assets'));
});

gulp.task('compile:sass', function () {
    return gulp
        .src('resources/assets/**/*.scss')
        .pipe(changed('public/assets', {extension: '.css'}))
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/assets'));
});

gulp.task('copy:css', function () {
    return gulp
        .src('resources/assets/**/*.css')
        .pipe(changed('public/assets'))
        .pipe(gulp.dest('public/assets'));
});

gulp.task('copy:html', function () {
    return gulp
        .src('resources/assets/**/*.html')
        .pipe(changed('public/assets'))
        .pipe(gulp.dest('public/assets'));
});

gulp.task('copy:images', function () {
    return gulp
        .src(['resources/assets/images/**', '!resources/assets/images'])
        .pipe(changed('public/assets/images'))
        .pipe(gulp.dest('public/assets/images'));
});

gulp.task('copy:libraries', function () {
    return gulp
        .src(vendor)
        .pipe(changed('public/lib'))
        .pipe(gulp.dest('public/lib'));
});

gulp.task('nolib', ['compile:typescript', 'compile:sass', 'copy:css', 'copy:images', 'copy:html']);
gulp.task('build', ['nolib', 'copy:libraries']);
gulp.task('default', ['build']);
