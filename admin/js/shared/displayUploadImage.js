import { displayUploadedImages } from '../../../lib/js/functions.js';


const uploadBtn = document.querySelector('#upload');
const imgContainer = document.querySelector('.image-area');

uploadBtn.addEventListener('input', () => {
  displayUploadedImages(uploadBtn.files, imgContainer, 'img-fluid rounded shadow-sm mx-auto d-block');
})