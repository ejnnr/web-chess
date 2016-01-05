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
        .pipe(sourcemaps.write('public/js'))
        .pipe(gulp.dest('public/js'));
});

gulp.task('copy:images', function () {
    return gulp
        .src(['resources/assets/img/**', '!resources/assets/img'])
        .pipe(gulp.dest('public/img'));
});

gulp.task('build', ['compile', 'copy:images']);
gulp.task('default', ['build']);
