module.exports = (grunt) ->
  grunt.initConfig
    bump:
      files: ['package.json', 'composer.json']
      push: false

  grunt.loadNpmTasks 'grunt-bump'

  grunt.registerTask 'release', ['bump']
  grunt.registerTask 'default', []