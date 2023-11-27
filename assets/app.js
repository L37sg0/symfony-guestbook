/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import '../node_modules/bootstrap';
import bsCustomFileInput from 'bs-custom-file-input';

// start the Stimulus application
import '../node_modules/bootstrap';

const $ = require('../node_modules/jquery');
require('../node_modules/bootstrap');
require('../node_modules/@popperjs/core');

bsCustomFileInput.init();
