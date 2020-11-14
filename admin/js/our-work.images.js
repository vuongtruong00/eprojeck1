import { fetchData, uploadFiles } from '../../lib/js/functions.async.js';



/* initialize */
window.addEventListener('load', async () => {
  const url = new URL(window.location.href);
  const currentEventId = url.searchParams.get('id');
  const uploadBtn = document.querySelector('#upload');
  
  /* handle upload */
  uploadBtn.addEventListener('input', async () => {
    const response = await uploadFiles('cms.upload.php', uploadBtn.files, 'upload[]', {
      query: `
        INSERT INTO 
          event_images
        SET 
        img_url = ?,
        event_id = ?;
      `,
      params: JSON.stringify([currentEventId]),
      savePath: '../img/our-work/images/',
      readPath: '../img/our-work/images/'
    }) 
  
    if (response.result !== 1) {
      const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Fail to upload images (an unkown error occurred).</strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      `;
      uploadBtn.parentElement.insertAdjacentHTML('afterend', alert);
    } else {
      renderItems(currentEventId);
    }
  })
  
  const itemContainer = document.querySelector('#item-container');
  
  /* handle delete */
  itemContainer.addEventListener('click', (e) => {
    if (e.target.matches('#deleteBtn')) {
      document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
        if (e.target.dataset.imgUrl && e.target.dataset.imgUrl !== 'null') {
          await fetchData('../lib/delete-file.php', { fileName: e.target.dataset.imgUrl })
        }
        await fetchData('cms.delete-data.php', { id: e.target.dataset.id, table: 'event_images' });
        e.target.parentNode.parentNode.remove();
      }, { once: true });
    }
  })

  /* handle delete all images */
  const deleteAllBtn = document.querySelector('#delete-all');
  deleteAllBtn.addEventListener('click', () => {
  
    document.querySelector('#confirmDelete .btn-danger').addEventListener('click', async () => {
      const getResponse = await fetchData('cms.get-data.php', {
        query: 'SELECT * FROM event_images WHERE event_id = ?',
        params: JSON.stringify([currentEventId])
      });

      const fileNames = getResponse.rows ? getResponse.rows.map(el => el.img_url) : [];
      const deleteResponse = await fetchData('../lib/delete-file.php', { 
        fileNames: JSON.stringify(fileNames) 
      });
  
      if (deleteResponse.failure.length > 0) {
        console.log('Failed to write these files to server: ' + failure);
      }
  
      await fetchData('cms.delete-data.php', { field: 'event_id', value: currentEventId, table: 'event_images' });
      renderItems(currentEventId);
    }, { once: true });
    
  })
  
  /* render images */
  renderItems(currentEventId);
})



/* FUNCTIONS */

async function renderItems(currentEventId) {
  const itemContainer = document.querySelector('#item-container');
  itemContainer.innerHTML = '';
  const response = await fetchData('cms.get-data.php', {
    query: "SELECT * FROM event_images WHERE event_id = ?;",
    params: JSON.stringify([currentEventId])
  });
  const items = response.rows;

  if (items) {
    items.forEach(item => {
      const html = `
        <div class="col-11 col-sm-6 col-lg-4 col-xl-3 mx-auto mx-sm-0 img-wrapper">
          <div>
            <img src='${item.img_url}' alt='image'>
            <i id='deleteBtn' class="fas fa-trash-alt" data-toggle="modal" data-target="#confirmDelete" data-id="${item.id}" data-img-url='${item.img_url}'></i>
            <div class='hover-overlay'></div>
          </div>
        </div>
      `;

      itemContainer.innerHTML += html;
    });

    document.querySelector('#delete-all').classList.remove('d-none');
  } else {
    document.querySelector('#delete-all').classList.add('d-none');
  }

}
