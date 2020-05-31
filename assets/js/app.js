/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// -- CSS FILES ---------------------------------------- //
require('../scss/app.scss');

// -- JS FILES ----------------------------------------- //
// -- Bootstrap dependencies
const $ = require('jquery');
require('popper.js');
require('bootstrap');
// create global $ variable
global.$ = $;
// -- Additional JS files
//require('./subfolder/some_file');
