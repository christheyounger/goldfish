
var gulp = require('gulp');

// Include plugins
var plugins = require('gulp-load-plugins')({
	pattern: ['gulp-*', 'gulp.*', 'main-bower-files'],
	replaceString: /\bgulp[\-.]/
});

// Define the default destination folder
var dest = 'web/';

gulp.task('js', function () {
	var jsFiles = ["bower_components/angular-bootstrap/ui-bootstrap-tpls.js"];

	var jsFilter = plugins.filter('**/*.js');
	var stream = gulp.src(plugins.mainBowerFiles().concat(jsFiles))
		.pipe(jsFilter)
		.pipe(plugins.concat('vendor.js'))
		.pipe(plugins.uglify())
		.pipe(gulp.dest(dest + 'assets'));

	return stream;
});

gulp.task('css', function () {
	var lessFilter = plugins.filter('**/*.less',{restore: true});
	gulp.src(plugins.mainBowerFiles())
		// Compile Less files
		.pipe(lessFilter)
		.pipe(plugins.less())
		.pipe(lessFilter.restore)
		// Combine and minify CSS
		.pipe(plugins.filter('**/*.css'))
		.pipe(plugins.concat('vendor.css'))
		.pipe(plugins.uglifycss())
		.pipe(gulp.dest(dest + 'assets'));
});

// Fonts
gulp.task('fonts', function() {
    return gulp.src([
                    'bower_components/font-awesome/fonts/fontawesome-webfont.*',
                    'bower_components/bootstrap/dist/fonts/glyphicons-halflings-regular.*'
                    ])
            .pipe(gulp.dest(dest + 'fonts'));
});

// Clean
gulp.task('clean', function () {
    return gulp.src(['web/assets', 'web/fonts'], { read: false }).pipe(plugins.clean());
});

// Build
gulp.task('build', ['js', 'css', 'fonts']);
