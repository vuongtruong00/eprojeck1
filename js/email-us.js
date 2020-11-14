import { fetchData } from '../lib/js/functions.async.js';


const categoryInp = document.querySelector('#category_id');
updateServiceOptions();

categoryInp.addEventListener('input', () => {
  updateServiceOptions();
});

async function updateServiceOptions() {
  const response = await fetchData('get-data.php', {
    query: 'SELECT * FROM services WHERE category_id = ?',
    params: JSON.stringify([+categoryInp.value])
  })

  const services = response.rows;
  const serviceInp = document.querySelector('#service_id');
  serviceInp.innerHTML = '';
  const selectedServiceId = +document.querySelector('#selectedService').innerHTML;

  services.forEach(ser => {
    serviceInp.innerHTML += `<option ${selectedServiceId === ser.id ? 'selected' : ''} value='${ser.id}'>${ser.title}</option>`
  });
}