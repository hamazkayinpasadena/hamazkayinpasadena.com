gulp = require 'gulp'
path = require 'path'
pug  = require 'gulp-pug'
del  = require 'del'
sass = require 'gulp-sass'
name = require 'gulp-rename'

# Get all relevant paths
SRC = '.'
DEST = '..'

paths = ->
  templates:
    src: ["#{SRC}/pug/*.pug", "!#{SRC}/pug/includes/*"]
    dest: DEST
  css:
    src: ["#{SRC}/sass/main.sass"]
    dest: DEST
  php:
    src: ["#{SRC}/php/*.php"]
    dest: DEST
  build: ["#{DEST}/*.php", "#{DEST}/assets"]

# Alias gulp functions
from = gulp.src
to   = gulp.dest

# Compile the SASS and copy to build directory
css = ->
  from paths().css.src
    .pipe sass().on 'error', sass.logError
    .pipe name (path) ->
      path.basename = 'style'
    .pipe to paths().css.dest
gulp.task css

# Render the pug templates
templates = ->
  from paths().templates.src
    .pipe pug()
    .pipe name (path) ->
      path.extname = '.php'
    .pipe to paths().templates.dest
gulp.task templates

# Copy the PHP source files to the destination
php = ->
  from paths().php.src
    .pipe to paths().php.dest
gulp.task php

# Wipe everything if necessary
gulp.task 'clean', ->
  del paths().build,
    force: true

# automatic builds
gulp.task 'watch', ->
  gulp.watch paths().css.src, css
  gulp.watch paths().templates.src, templates
  gulp.watch paths().php.src, php

# Build everything
gulp.task 'default', (gulp.parallel php, templates, css)
