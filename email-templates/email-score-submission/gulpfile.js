var gulp = require('gulp');
var gutil = require('gulp-util');
var fs = require("fs");
var path = require('path');
var less = require('gulp-less');
var minifyCss = require('gulp-minify-css');
var rename = require("gulp-rename");
var inlineCss = require('gulp-inline-css');
var replace = require('gulp-replace');

// Compile all less files
// https://github.com/plus3network/gulp-less
gulp.task('less', function() {
	// compile all less files
    var lessTask = less({
        paths: [ path.join(__dirname, 'less', 'includes') ]
    });

    // Stop task on error
    lessTask.on('error',function(e){
        gutil.log(e);
        lessTask.end();
    });

    return gulp.src('*.less')
        .pipe(lessTask)
        .pipe(gulp.dest('.'));
});

// Minify Compiled CSS
gulp.task('minify-css', ['less'], function() {
  return gulp.src(['./*.css', '!./*.min.css'])
    .pipe(minifyCss({
        compatibility: 'ie7',
        advanced: false,
        aggressiveMerging: false,

    }))
    .pipe(rename(function (path) {
        path.basename += ".min";
    }))
    .pipe(gulp.dest('.'));
});

// Insert compiled css files into source file
// TODO: make this generic for all *.min.css files
gulp.task('insert-css', ['minify-css'], function() {
    var campaignCssContent = fs.readFileSync("styles.min.css", "utf8");

    return gulp.src(['*.php'])
        .pipe(replace('{{styles-css}}', campaignCssContent))
        .pipe(gulp.dest('build/'));
});

// Inline css files
// https://github.com/jonkemp/gulp-inline-css
gulp.task('inline-css', ['insert-css'], function() {
    return gulp.src(['./build/*.php', '!./build/*-inlined.php'])
        .pipe(inlineCss({
            applyStyleTags: true,
            applyLinkTags: true,
            removeStyleTags: false,
            removeLinkTags: true,
            applyWidthAttributes: false
        }))
        .pipe(rename(function (path) {
            path.basename += "-inlined"
        }))
        .pipe(gulp.dest('build/'));
});

// Build task
gulp.task('build', ['inline-css']);

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch(['./*.php', 'styles.less'], ['build']);
});

// Default Task (alias for build)
gulp.task('default', ['build']);