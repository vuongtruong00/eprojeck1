import { fetchData } from '../../lib/js/functions.async.js';


window.addEventListener('load', async () => {
  await renderImages();
  $('#table').DataTable({
    "lengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]] 
  });
})

const itemContainer = document.querySelector('#item-container');

itemContainer.addEventListener('click', (e) => {
  if (e.target.matches('#deleteBtn')) {
    document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
      await fetchData('../lib/delete-file.php', { fileName: e.target.dataset.imgUrl })
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'team_members' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

async function renderImages() {
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', { table: 'team_members'});
  const items = response.rows;

  if (items) {
    items.forEach(item => {
      const shortDescription = item.description.length > 20 ? item.description.slice(0, 18) + "..." : item.description;
      const html = `
        <tr>
          <td>
            <img src="${item.img_url}" alt="Image">
          </td>
          <td>${item.fullname}</td>
          <td>${item.role}</td>
          <td>${shortDescription}</td>
          <td>
            <a class="btn btn-primary" href="our-team.edit.php?id=${item.id}">Edit</a>
            <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-img-url="${item.img_url}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}



