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
      await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'home_introduction' });
      e.target.parentNode.parentNode.remove();
    }, { once: true });
  }
})

async function renderItems() {
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', { table: 'home_introduction'});
  const items = response.rows;

  if (items) {
    items.forEach(item => {
      const shortTitle = item.title.length > 20 ? item.title.slice(0, 18) + "..." : item.title;
      const shortSubtitle = item.subtitle.length > 20 ? item.subtitle.slice(0, 18) + "..." : item.subtitle;
      const replacements = {
        '<': '&lt;',
        '>': '&gt;',
        '&': '&amp;'
      }
      const regex = new RegExp(Object.keys(replacements).join('|'), 'gi');
      let shortContent = item.content.length > 30 ? item.content.slice(0, 28) + "..." : item.content;
      shortContent = shortContent.replace(regex, matched => replacements[matched]);

      const html = `
        <tr>
          <td>
            <img src="${item.img_url ? item.img_url : '../img/svg/default-image.png'}" alt="Image">
          </td>
          <td>${shortTitle}</td>
          <td>${shortSubtitle}</td>
          <td>${shortContent}</td>
          <td>
            <a class="btn btn-primary" href="home.introduction.edit.php?id=${item.id}">Edit</a>
            <a id="deleteBtn" class="btn btn-danger text-white" data-id="${item.id}" data-img-url="${item.img_url}" data-toggle="modal" data-target="#confirmDelete">Delete</a>
          </td>
        </tr>
      `;

      itemContainer.innerHTML += html;
    });
  }
}



