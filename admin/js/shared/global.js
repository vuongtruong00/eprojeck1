$(document).ready(function() {
  $("body").tooltip({ selector: '[data-toggle=tooltip]' });
});

const sideBarToggler = document.querySelector('.side-bar-toggler');
const sideBar = document.querySelector('.side-bar');

sideBarToggler.addEventListener('click', () => {
  sideBar.classList.toggle('toggled');
});

