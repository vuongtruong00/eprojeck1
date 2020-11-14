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
      await fetchData('cms.alter-data.php', {
        query: `
          DELETE FROM event_comments 
          WHERE id = ? OR parent_comment_id = ?;
        `,
        params: JSON.stringify([e.target.dataset.id, e.target.dataset.id])
      });
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
      event_comments.created_at,
      parent_comment_id
    FROM 
      event_comments
    JOIN 
      users ON event_comments.user_id = users.id
    WHERE 
      event_id = ?;
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

      const replies = items.filter(el => el.parent_comment_id === item.id);

      if (item.parent_comment_id === null) {
        const html = `
          <tr>
            <td>${item.username}</td>
            <td>${item.comment_text}</td>
            <td>${replies.length}<a class="btn btn-primary ml-2" href="our-work.comments.replies.php?id=${item.id}">See</a></td>
            <td>${item.created_at}</td>
            <td>
              <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
            </td>
          </tr>
        `;
  
        itemContainer.innerHTML += html;
      }
    });
  }
}



