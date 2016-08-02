module.exports = ()->

  #Project configuration.
  @initConfig
    
    concat:
      phpDev:
        src: ['config.dev.php', 'model/**/*.php', 'controller/**.php', 'helpers/**.php', 'lib/**.php', 'core/**.php']
        dest: 'publish/index.php'
      phpProd:
        src: ['config.prod.php', 'model/**/*.php', 'controller/**.php', 'helpers/**.php', 'lib/**.php', 'core/**.php']
        dest: 'publish/index.php'

    watch:
      backend:
        files: ['model/**/*.php', 'controller/**.php', 'helpers/**.php', 'lib/**.php', 'core/**.php']
        tasks: ['concat:phpDev']
      views:
        files: ['views/**']
        tasks: ['sync:views']
      js:
        files: ['assets/**/*.js']
        tasks: ['webpack:dev']
      styling:
        files: ['assets/styles/less/**/*.less']
        tasks: ['less:dev']
      assets:
        files: ['assets/images/**', 'assets/uploads/**', 'assets/fonts/**']
        tasks: ['sync:assets']

    less:
      dev:
        files:
          'publish/assets/css/base.css': 'assets/styles/less/base.less'

    webpack:
      dev:
        entry: './assets/scripts/main.js'
        output:
          path: 'publish/assets/scripts/'
          filename: 'bundle.js'

    sync:
      views:
        files: [
          cwd: 'views/',
          src: ['**'],
          dest: 'publish/views'
        ]
        updateAndDelete: true
      assets:
        files: [
          cwd: 'assets/',
          src: ['fonts/**', 'images/**', 'uploads/**'],
          dest: 'publish/assets/'
        ]
        ignoreInDest: ['uploads/**', 'css/**', 'scripts/**']
        updateAndDelete: true
        verbose: true
      routes:
        files: [
          src: ['routes.json'],
          dest: 'publish/'
        ]

    concurrent:
      tasks: ['watch:backend', 'watch:styling', 'watch:views', 'watch:js', 'watch:assets']
      options:
        logConcurrentOutput: true


    @loadNpmTasks('grunt-contrib-concat')
    @loadNpmTasks('grunt-contrib-watch')
    @loadNpmTasks('grunt-contrib-less')
    @loadNpmTasks('grunt-webpack')
    @loadNpmTasks('grunt-sync')
    @loadNpmTasks('grunt-concurrent')

  # Default task(s).
    @registerTask('dev', ['concat:phpDev', 'sync:views', 'sync:assets', 'sync:routes', 'webpack:dev', 'less:dev', 'concurrent:tasks'])
    @registerTask('prod', ['concat:phpProd', 'sync:views', 'sync:assets', 'sync:routes', 'webpack:dev', 'less:dev'])