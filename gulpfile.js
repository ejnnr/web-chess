const gulp = require('gulp');
const del = require('del');
const typescript = require('gulp-typescript');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const changed = require('gulp-changed');
const tscConfig = require('./tsconfig.json');

gulp.task('clean', function () {
    return del(['public/**', '!public', '!public/index.php']);
});

// Typescript compilation
gulp.task('compile:typescript', function () {
    return gulp
        .src('resources/assets/ts/**/*.ts')
        .pipe(changed('public/js', {extension: '.js'}))
        .pipe(sourcemaps.init())
        .pipe(typescript(tscConfig.compilerOptions))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('compile:sass', function () {
    return gulp
        .src('resources/assets/sass/**/*.scss')
        .pipe(changed('public/css', {extension: '.css'}))
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/css'));
});

gulp.task('compile:componentsass', function () {
    return gulp
        .src('resources/assets/ts/components/**/*.scss')
        .pipe(changed('public/js/components', {extension: '.css'}))
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/js/components'));
});

gulp.task('copy:componenthtml', function () {
    return gulp
        .src('resources/assets/ts/components/**/*.html')
        .pipe(changed('public/js/components'))
        .pipe(gulp.dest('public/js/components'));
});

gulp.task('copy:images', function () {
    return gulp
        .src(['resources/assets/img/**', '!resources/assets/img'])
        .pipe(changed('public/img'))
        .pipe(gulp.dest('public/img'));
});

gulp.task('copy:libraries', function () {
    return gulp
        .src(['node_modules/**', 'bower_components/**', '!node_modules', '!bower_components'])
        .pipe(changed('public/lib'))
        .pipe(gulp.dest('public/lib'));
});

gulp.task('build', ['compile:typescript', 'compile:sass', 'compile:componentsass', 'copy:images', 'copy:componenthtml', 'copy:libraries']);
gulp.task('default', ['build']);
