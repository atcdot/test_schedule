var server = require('tiny-lr')(); // Минивебсервер для livereload
var gulp = require('gulp'); // Сообственно Gulp JS
var stylus = require('gulp-stylus'); // Плагин для Stylus
var livereload = require('gulp-livereload'); // Livereload для Gulp
var uglify = require('gulp-uglify'); // Минификация JS
var concat = require('gulp-concat'); // Склейка файлов
var postcss = require('gulp-postcss');
var autoprefixer = require('autoprefixer');
var cssnano = require('cssnano');
var gulpIf = require('gulp-if');
var sourceMaps = require('gulp-sourcemaps');
var newer = require('gulp-newer');

var NODE_ENV = process.env.NODE_ENV || 'dev';
var isDev = NODE_ENV == 'dev';

if (isDev) {
  var browserSync = require('browser-sync').create()
}

gulp.task('serve', function () {
  gulpIf(isDev, browserSync.init({
    proxy: {
      target: "http://localhost:8000",
      ws    : true
    }
  }));
  browserSync.watch('web/**/*.*').on('change', browserSync.reload);
});

var config = {
  bowerDir: 'bower_components',
  npmDir  : 'node_modules'
};

/*
 *****************************************************************************************
 ***** READY FUNCTIONS
 *****************************************************************************************
 */

gulp.task('setProd', function () {
  isDev = false;
});

gulp.task('default', ['watch']);
gulp.task('watch', [
  'styles',
  'js_custom',
  'livereload'
]);

gulp.task('build', [
  'setProd',
  'copy',
  'styles',
  'js_custom_min',
  'css_vendor',
  'js_vendor',
  'fonts'
]);

gulp.task('fonts', [
  'fonts_fontawesome',
  'fonts_bootstrap'
]);

/*
 *****************************************************************************************
 ***** OTHER FUNCTIONS
 *****************************************************************************************
 */

gulp.task('livereload', function () {
  server.listen(35729, function (err) {
    if (err) return console.log(err);
    gulp.watch('assets/{css,stylus}/**/*', ['styles']);
    if (err) return console.log(err);
    gulp.watch('assets/js/*', ['js_custom']);
  });
});

// Копирование шрифтов
gulp.task('fonts_fontawesome', function () {
  return gulp.src(config.bowerDir + '/font-awesome/fonts/**.*')
    .pipe(newer('web/fonts'))
    .pipe(gulp.dest('web/fonts'));
});
gulp.task('fonts_bootstrap', function () {
  return gulp.src(config.bowerDir + '/bootstrap/fonts/**.*')
    .pipe(newer('web/fonts'))
    .pipe(gulp.dest('web/fonts'));
});

/*
 *****************************************************************************************
 ***** COPY
 *****************************************************************************************
 */

gulp.task('copy', function () {
  return gulp.src('assets/dataTablesRussian.json')
    .pipe(newer('web/js'))
    .pipe(gulp.dest('web/js'))
});

/*
 *****************************************************************************************
 ***** CSS
 *****************************************************************************************
 */

gulp.task('styles', function () {
  var processors = [
    autoprefixer({browsers: ['last 2 version']})
  ];
  if (!isDev) processors.push(cssnano());

  return gulp.src([
    'assets/{stylus,css}/**.*'
  ])
    .pipe(gulpIf(isDev, sourceMaps.init()))
    .pipe(stylus())
    .on('error', emitError)
    .pipe(concat('styles.css'))
    .on('error', emitError)
    .pipe(postcss(processors))
    .on('error', emitError)
    .pipe(gulpIf(isDev, sourceMaps.write()))
    .pipe(gulp.dest('web/css'))
    ;
});

gulp.task('css_vendor', function () {
  var processors = [
    autoprefixer({browsers: ['last 2 version']})
  ];
  if (!isDev) processors.push(cssnano());

  return gulp.src([
    config.bowerDir + '/bootstrap/dist/css/bootstrap.min.css',
    config.bowerDir + '/font-awesome/css/font-awesome.min.css',
    config.bowerDir + '/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css',
    config.bowerDir + '/datatables/media/css/dataTables.bootstrap.min.css'
  ])
    .pipe(gulpIf(isDev, sourceMaps.init()))
    .pipe(concat('vendor.css', {
      rebaseUrls: false
    }))
    .on('error', emitError)
    .pipe(postcss(processors))
    .on('error', emitError)
    .pipe(gulpIf(isDev, sourceMaps.write()))
    .pipe(gulp.dest('web/css'));
});

/*
 *****************************************************************************************
 ***** JS Live
 *****************************************************************************************
 */

gulp.task('js_custom', function () {
  return gulp.src([
    'assets/js/*.js'
  ])
    .pipe(gulpIf(isDev, sourceMaps.init()))
    .pipe(concat('custom.js')) // Собираем все JS
    .pipe(gulpIf(isDev, sourceMaps.write()))
    .pipe(gulp.dest('web/js'))
    .pipe(livereload(server)); // даем команду на перезагрузку страницы
});

/*
 *****************************************************************************************
 ***** JS_MIN
 *****************************************************************************************
 */

gulp.task('js_custom_min', function () {
  return gulp.src([
    'assets/js/*.js'
  ])
    .pipe(gulpIf(isDev, sourceMaps.init()))
    .pipe(concat('custom.js'))
    // .pipe(uglify())
    .pipe(gulpIf(isDev, sourceMaps.write()))
    .pipe(gulp.dest('web/js'));
});

gulp.task('js_vendor', function () {
  return gulp.src([
    config.bowerDir + '/jquery/dist/jquery.min.js',
    config.bowerDir + '/bootstrap/dist/js/bootstrap.min.js',
    config.bowerDir + '/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
    config.bowerDir + '/bootstrap-datepicker/dist/locales/bootstrap-datepicker.ru.min.js',
    config.bowerDir + '/moment/min/moment-with-locales.min.js',
    config.bowerDir + '/datatables/media/js/jquery.dataTables.min.js',
    config.bowerDir + '/datatables/media/js/dataTables.bootstrap.min.js',
  ])
    .pipe(gulpIf(isDev, sourceMaps.init()))
    .pipe(concat('vendor.js')) // Собираем все JS
    // .pipe(uglify())
    .pipe(gulpIf(isDev, sourceMaps.write()))
    .pipe(gulp.dest('web/js'));
});

function emitError(error) {
  console.log(error.toString());
  this.emit('end')
}