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
    src: ['#{SRC}/pug/**/*.pug', '!#{SRC}/pug/includes/*']
    dest: DEST
  css:
    src: ['#{SRC}/sass/main.sass']
    dest: DEST
  php:
    src: ['#{SRC}/php/*.php']
    dest: DEST
  build: ['#{DEST}/*.php', '#{DEST}/assets']

# Alias gulp functions
from = gulp.src
to   = gulp.dest

# Compile the SASS and copy to build directory
css = ->
  from paths().css
    .pipe sass().on 'error', sass.logError
    .pipe name (path) ->
      path.basename = 'style'
    .pipe to paths().css.dest

# Render the pug templates
templates = ->
  from paths().templates
    .pipe name (path) ->
      path.extname = '.php'
    .pipe to paths().templates.dest

# Copy the PHP source files to the destination
php = ->
  from paths().php
    .pipe to paths().php.dest

# Wipe everything if necessary
gulp.task 'clean', ->
  del paths().build,
    force: true

# Build everything
gulp.task 'default', (gulp.parallel php, templates, css)
