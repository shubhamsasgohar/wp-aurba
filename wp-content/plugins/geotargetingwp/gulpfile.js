var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');

gulp.task('minify-css', function() {
    return gulp.src('public/css/geotarget-public.css')
        .pipe(cleanCSS())
        .pipe(rename('geotarget-public.min.css'))
        .pipe(gulp.dest('public/css/'));
});

gulp.task('watch', function(){
    gulp.watch('public/css/**/*.css', ['minify-css']);
    // Other watchers
});
gulp.task("session-start", (cb) => {
    return gulp.series('minify-css')(cb);
});

gulp.task('default', gulp.series('session-start'));