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
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'event_comments' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

async function renderItems() {
  itemContainer.innerHTML = '';
  const query = `
    SELECT
      event_comments.id,
      users.username,
      comment_text,
      event_comments.created_at
    FROM 
      event_comments
    JOIN 
      users ON event_comments.user_id = users.id
    WHERE 
      parent_comment_id = ?;
  `;
  const url = new URL(window.location.href);
  const id = url.searchParams.get('id');
  const response = await fetchData('cms.get-data.php', { 
    query,
    params: JSON.stringify([id])
  });

  const items = response.rows;

  if (items) {
    items.forEach(item => {

      const html = `
        <tr>
          <td>${item.username}</td>
          <td>${item.comment_text}</td>
          <td>${item.created_at}</td>
          <td>
            <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}



