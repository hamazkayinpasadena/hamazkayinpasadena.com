const gulp = require('gulp');
const pug  = require('gulp-pug');
const del  = require('del');
const sass = require('gulp-sass');
const rename = require('gulp-rename');

// Get all relevant paths
const SRC = '.';
const DEST = '..';

const paths = () => ({
  templates: {
    src: [`${SRC}/pug/*.pug`, `!${SRC}/pug/includes/*`],
    dest: DEST,
  },
  css: {
    src: [`${SRC}/sass/**/*.sass`],
    dest: DEST,
  },
  php: {
    src: [`${SRC}/php/*.php`],
    dest: DEST,
  },
  static: {
    src: [`${SRC}/static/**/*`],
    dest: `${DEST}/static`
  },
  build: [`${DEST}/*.php`, `${DEST}/assets`],
});

// Compile the SASS and copy to build directory
function css (done) {
  gulp.src(paths().css.src)
    .pipe(sass().on('error', sass.logError))
    // .pipe(rename((path) => path.basename = 'style'))
    .pipe(gulp.dest(paths().css.dest));
  done();
};
gulp.task(css);

// Render the pug templates
function templates (done) {
  gulp.src(paths().templates.src)
    .pipe(pug())
    .pipe(rename((path) => path.extname = '.php'))
    .pipe(gulp.dest(paths().templates.dest));
  done();
};
gulp.task(templates);

// Copy the PHP source files to the destination
function php (done) {
  gulp.src(paths().php.src)
    .pipe(gulp.dest(paths().php.dest));
  done();
};
gulp.task(php);

// Copy the static assets
function static (done) {
  gulp.src(paths().static.src)
    .pipe(gulp.dest(paths().static.dest));
  done();
};
gulp.task(static);

// Wipe everything if necessary
gulp.task('clean', (done) => {
  del(paths().build, {force: true});
  done();
});

// automatic builds
gulp.task('watch', () => {
  gulp.watch(paths().css.src, css);
  gulp.watch(paths().templates.src, templates);
  gulp.watch(paths().php.src, php);
});

// Build everything
gulp.task('default', gulp.parallel(css, templates, php, static));
