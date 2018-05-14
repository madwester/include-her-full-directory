var gulp = require('gulp');
var sass = require('gulp-sass');
var watch = require('gulp-watch');

var buildpath = 'build';

gulp.task('sass', function(){
    return gulp.src('build/scss/*.scss')
    .pipe(sass())
    .pipe(gulp.dest(buildpath+'/css'));
});

gulp.task('watch', function(){
    gulp.watch('build/scss/*.scss', ['sass']);
});