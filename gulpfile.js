const gulp = require('gulp');
const del = require('del');
const typescript = require('gulp-typescript');
const sourcemaps = require('gulp-sourcemaps');
const tscConfig = require('./tsconfig.json');

gulp.task('clean', function () {
    return del(['public/**', '!public', '!public/index.php']);
});

// Typescript compilation
gulp.task('compile', function () {
    return gulp
        .src('resources/assets/ts/**/*.ts')
        .pipe(sourcemaps.init())
        .pipe(typescript(tscConfig.compilerOptions))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('copy:componenthtml', function () {
    return gulp
        .src('resources/assets/ts/components/**/*.html')
        .pipe(gulp.dest('public/js/components'));
});

gulp.task('copy:images', function () {
    return gulp
        .src(['resources/assets/img/**', '!resources/assets/img'])
        .pipe(gulp.dest('public/img'));
});

gulp.task('copy:libraries', function () {
    return gulp
        .src(['node_modules/**', 'bower_components/**', '!node_modules', '!bower_components'])
        .pipe(gulp.dest('public/lib'));
});

gulp.task('build', ['compile', 'copy:images', 'copy:componenthtml', 'copy:libraries']);
gulp.task('default', ['build']);
