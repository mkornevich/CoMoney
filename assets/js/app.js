import 'bootstrap'
import '../css/app.scss';
import 'select2/dist/js/select2';
import 'lightgallery.js';

// select2 functionality
$('.js-select2').select2();

// gallery functionality
document.querySelectorAll('.js-gallery').forEach(el => {
    lightGallery(el, {thumbnail: true});
})


