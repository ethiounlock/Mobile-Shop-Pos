// package metadata file for Meteor.js

/* global Package:true */

Package.describe({
  name: 'twbs:bootstrap',  // https://atmospherejs.com/twbs/bootstrap
  summary: 'The most popular front-end framework for developing responsive, mobile-first projects on the web.',
  version: '4.6.0',
  git: 'https://github.com/twbs/bootstrap.git'
});

Package.onUse(function (api) {
  api.versionsFrom('METEOR@1.0');

  // Only add jQuery as a dependency for the client
  api.use('jquery', 'client');

  // Use the less version of Bootstrap instead of css
  api.addFiles([
    'dist/css/bootstrap.less',
    'dist/js/bootstrap.js'
  ], 'client');

  // Add an optional package dependency for server-side usage
  api.optionalAssets({
    server: [
      'dist/js/bootstrap.js'
    ]
  });
});

Package.onTest(function (api) {
  api.use('tinytest');
  api.use('twbs:bootstrap');
});
