import { ClientValidator } from '../lib/js/validator.class.js';


const inputs = document.querySelectorAll('.live-validate');
inputs.forEach(inp => {
  inp.addEventListener('input', () => {

    const validator = new ClientValidator(inp)
    const error = validator.validate();
    const errorBox = document.querySelector(`#${inp.id}-error`);

    if (error) {
      errorBox.innerHTML = `<div class='alert alert-danger' role='alert'><strong>${error}</strong></div>`;
      inp.classList.add('border-danger');
      inp.classList.remove('border-success');
    } else {
      errorBox.innerHTML = '';
      inp.classList.add('border-success');
      inp.classList.remove('border-danger');
    }
  })
})

