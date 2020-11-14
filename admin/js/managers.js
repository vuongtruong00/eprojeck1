import { fetchData } from '../../lib/js/functions.async.js';


const itemContainer = document.querySelector('#item-container');

window.addEventListener('load', async () => {
  await renderManagers();
  $('#table').DataTable({
    "lengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]]
  });
})

itemContainer.addEventListener('click',  (e) => {
  if (e.target.matches('#deleteBtn')) {
    document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
      await fetchData('../lib/delete-file.php', { fileName: e.target.dataset.imgUrl });
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'managers' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

async function renderManagers() {
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', { table: 'managers'});
  const items = response.rows;
  const currentLevel = response.currentUser.level;

  if (items) {
    items.forEach(item => {
      let btns = `
        <a href="managers.edit.php?id=${item.id}" class="btn btn-primary">Edit</a>
        <button id="deleteBtn" data-id="${item.id}" data-img-url="${item.img_url}" class="btn btn-danger" data-toggle="modal" data-target="#confirmDelete">Delete</button>
      `;

      switch (currentLevel) {
        case 'super-admin':
          if (item.level === 'super-admin') {
            btns = `
            <a href="managers.edit.php?id=${item.id}" id="editBtn" class="btn btn-primary mr-1 ">Edit</a>
          `;
          }
          break;

        case 'admin':
          if (['super-admin', 'admin'].includes(item.level)) {
            btns = '';
          }
          break;

        case 'manager':
          btns = '';
          break;
      }
      
      const status = Date.now() - Date.parse(item.last_activity_time) > 2 * 60000 ? 'offline' : 'online';
      const shortEmail = item.email.slice(0, item.email.indexOf('@') + 1) + '...';
      const html = `
        <tr>
          <td><img class='rounded-img' src="${item.img_url ? item.img_url : '../img/svg/default-user.svg'}" alt="Image"></td>
          <td>${item.fullname}</td>
          <td>${item.username}</td>
          <td><a href="mailto:${item.email}" title="${item.email}" data-toggle="tooltip">${shortEmail}</a></td>
          <td class="text-${item.level === 'super-admin' ? 'danger' : item.level === 'admin' ? 'warning' : 'info'}">${item.level}</td>
          <td class="text-${status === 'online' ? 'success' : 'secondary'}">${status}</td>
          <td>${btns}</td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}


