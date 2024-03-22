const plugins =
  process.env.PLUGINS && ['transform-es2015-modules-strip', '@babel/proposal-object-rest-spread']

module.exports = {
  presets: [
    ['@babel/env', {
      loose: true,
      modules: false,
      exclude: ['transform-typeof-symbol']
    }]
  ],
  plugins: plugins.filter(Boolean)
};
