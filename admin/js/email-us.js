import { fetchData } from '../../lib/js/functions.async.js';


window.addEventListener('load', async () => {
  await renderItems();
  $('#table').DataTable({
    "lengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]]
  });
})

const itemContainer = document.querySelector('#item-container');

itemContainer.addEventListener('click', async (e) => {
  if (e.target.matches('#deleteBtn')) {
    document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'clients' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }

  if (e.target.matches('.action-container i')) {
    const status = +e.target.dataset.status;
    const newStatus = +!status;
    const id = e.target.dataset.id;

    await fetchData('cms.alter-data.php', {
      query: `
        UPDATE clients
        SET status = ?
        WHERE id = ?;
      `,
      params: JSON.stringify([newStatus, id])
    });

    e.target.parentNode.innerHTML = `
      <i data-id='${id}' data-status=${newStatus} class="${newStatus ? 'fas fa-check-circle' : 'far fa-circle'}"></i>
      <a id="deleteBtn" class="btn btn-danger text-white d-block" data-id="${id}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
    `;
  }
})

async function renderItems() {
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', { 
    query: `
      SELECT 
        clients.id,
        clients.fullname,
        clients.phone,
        clients.email,
        clients.event_location,
        clients.event_date,
        clients.submitted_at,
        clients.status,
        services.title
      FROM clients JOIN services ON clients.service_id = services.id;
    `,
    params: JSON.stringify([])
   });

  const items = response.rows;
  
  if (items) {
    items.forEach(item => {
      const shortEventDate = getShortDate(item.event_date);
      const shortSubmitDate = getShortDate(item.submitted_at);
      const shortEmail = item.email.slice(0, item.email.indexOf('@') + 1) + '...';

      const html = `
        <tr>
          <td>${item.fullname}</td>
          <td><a href="telto:${item.phone}">${item.phone}</a></td>
          <td><a href="mailto:${item.email}" title="${item.email}" data-toggle="tooltip">${shortEmail}</a></td>
          <td>${item.title}</td>
          <td>${shortEventDate}</td>
          <td>${item.event_location}</td>
          <td>${shortSubmitDate}</td>
          <td>
            <div class='action-container'>
              <i data-id='${item.id}' data-status=${item.status} class="${item.status ? 'fas fa-check-circle' : 'far fa-circle'}"></i>
              <a id="deleteBtn" class="btn btn-danger text-white d-block" data-id="${item.id}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
            </div>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}


function getShortDate(str) {
  const date = new Date(str);
  const year = date.getFullYear();
  const month = date.getMonth() + 1;
  const day = date.getDate();

  return `${day}-${month}-${year}`;
}


