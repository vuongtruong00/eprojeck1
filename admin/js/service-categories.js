import { fetchData } from '../../lib/js/functions.async.js';


window.addEventListener('load', async () => {
  await renderItems();
  $('#table').DataTable({
    "lengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]]
  });
})

const itemContainer = document.querySelector('#item-container');

itemContainer.addEventListener('click', (e) => {
  if (e.target.matches('#deleteBtn')) {
    document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'service_categories' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

const saveBtn = document.querySelector('#saveBtn');
const catInput = document.querySelector('#catInput');
const errorContainer = document.querySelector('#addModal .error');

saveBtn.addEventListener('click', async () => {
  const response = await fetchData('cms.get-data.php', { table: 'service_categories' });
  const categories = response.rows;
  const inputValue = catInput.value.trim().toLowerCase()
  if (categories.some(cat => cat.name.toLowerCase() === inputValue)) {
    errorContainer.innerHTML = `
      <div class="alert alert-danger" role="alert">
        This category already exists.
      </div>
    `;
  } else {
    const res = await fetchData('cms.alter-data.php', {
      query: 'INSERT INTO service_categories SET name = ?;',
      params: JSON.stringify([inputValue])
    })

    if (res.result === -1) {
      errorContainer.innerHTML = `
        <div class="alert alert-danger" role="alert">
          ${res.error}
        </div>
      `;
    } else if (res.result === 1) {
      document.querySelector('#addBtn').click();
      renderItems();
    }
  }
})

async function renderItems() {
  itemContainer.innerHTML = '';

  const response = await fetchData('cms.get-data.php', { table: 'service_categories' });

  const items = response.rows;

  if (items) {
    items.forEach(item => {
      const html = `
        <tr>
          <td>${item.name}</td>
          <td>
            <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}



