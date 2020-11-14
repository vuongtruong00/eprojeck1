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
      if (e.target.dataset.imgUrl && e.target.dataset.imgUrl !== 'null') {
        await fetchData('../lib/delete-file.php', { fileName: e.target.dataset.imgUrl })
      }
      
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'users' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

async function renderItems() {
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', { table: 'users'});
  const items = response.rows;

  if (items) {
    items.forEach(item => {
      const imgUrl = item.img_url ? '.' + item.img_url : '../img/svg/default-image.png'

      const html = `
        <tr>
          <td>
            <img class='rounded-img' src="${imgUrl}" alt="Image">
          </td>
          <td>${item.username}</td>
          <td><a href="mailto:${item.email}" title="${item.email}" data-toggle="tooltip">${item.email}</a></td>
          <td>${item.created_at}</td>
          <td>
            <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-img-url="${imgUrl}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}



