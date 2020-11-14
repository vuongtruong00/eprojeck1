import { fetchData } from '../lib/js/functions.async.js';



main();

/********************* MAIN FUNCTION ***********************/

async function main() {
  /* make thumbnail sliders */
  const imagesContainer = document.querySelector('.images-container');
  
  const prevBtn = document.querySelector('.images-container-wrapper .fa-chevron-left');
  prevBtn.addEventListener('click', () => {
    imagesContainer.scrollBy({
      left: -1425 / 8,
      behavior: 'smooth'
    });
  })
  
  const nextBtn = document.querySelector('.images-container-wrapper .fa-chevron-right');
  nextBtn.addEventListener('click', () => {
    imagesContainer.scrollBy({
      left: 1425 / 8,
      behavior: 'smooth'
    });
  })
  
  /* get event id */
  const url = new URL(window.location.href);
  const event_id = url.searchParams.get('id');

  /* get current user  */
  const currentUserResposnse = await fetchData('get-data.php', { getCurrentUser: true });
  const currentUser = currentUserResposnse.currentUser;
  
  /* initalize like counters */
  const likeCounter = document.querySelector('.like span');
  const likeBtn = document.querySelector('.like-btn');
  updateLikeStates(event_id, likeCounter, currentUser ? currentUser.id : null);
  
  /* control like button */
  likeBtn.addEventListener('click', async () => {
    if (currentUser) {
      await like(event_id, currentUser.id);
      updateLikeStates(event_id, likeCounter, currentUser ? currentUser.id : null);

    } else {
      displayLoginRequirement();
    }

  })

  /* toggle comment container */
  document.querySelector('.comment-btn').addEventListener('click', () => {
    document.querySelector('.comment-container').classList.remove('d-none');
  });

  document.querySelector('.comment').addEventListener('click', () => {
    document.querySelector('.comment-container').classList.toggle('d-none');
  });

  renderComments(event_id, currentUser);
}


/* *********************** ********* ************************* */
/* *********************** FUNCTIONS ************************* */
/* *********************** ********* ************************* */

async function renderComments(event_id, currentUser) {
  /* get comments */
  const comments = await getComments(event_id);

  /* initialize comment counter */
  const commentCounter = document.querySelector('.comment span');
  commentCounter.innerHTML = comments.length;
  const commentContainer = document.querySelector('.comment-container');

  /* render comments */
  for (const comment of comments) {
    if (comment.parent_comment_id === null) {
      const commentWrapper = renderComment(commentContainer, comment, 'comment');

      /* get all the replies of the comment */
      const replies = comments.filter(el => el.parent_comment_id === comment.id);
      
      /* render replies */
      const commentDetail = commentWrapper.querySelector('.comment-detail');
      commentDetail.insertAdjacentHTML('beforeend', "<div class='reply-container'></div>")
      const replyContainer = commentDetail.querySelector('.reply-container');
      
      for (const reply of replies) {
        renderComment(replyContainer, reply, 'reply');
      }
      
      if (currentUser) {
        /* prepare current user image url */
        const currentUserImgUrl = currentUser.img_url ? currentUser.img_url : './img/svg/default-user.svg';

        /* render reply form */
        renderCommentForm(replyContainer, 'reply', currentUserImgUrl);
  
        /* automate form resizing */
        const replyForm = replyContainer.querySelector('.reply-form');
        const replyInput = replyForm.querySelector('.reply-input');
        replyInput.addEventListener('input', () => {
          resizingInput(replyInput);
        })
        
        /* on submit reply */
        replyInput.addEventListener('keypress', async (e) => {
          
          if (e.keyCode === 13 && !e.shiftKey) {
            const reply_text = replyInput.value.trim();
  
            if (reply_text) {
              await onSubmitComment(
                `INSERT INTO 
                  event_comments
                SET
                  parent_comment_id = ?,
                  comment_text = ?,
                  user_id = ?,
                  event_id = ?;`,
                [comment.id, reply_text, currentUser.id, event_id],
                replyForm
              );
  
              commentContainer.innerHTML = '';
              renderComments(event_id, currentUser);
            } else {
              replyForm.reset();
              replyForm.classList.add('d-none');
            }
          }
        })
        
        /* Toggle reply form */
        commentDetail.addEventListener('click', (e) => {
          if (e.target.matches('.reply-btn')) {
            toggleForm(replyForm);
            const tagUsername = e.target.dataset.username;
            replyInput.innerHTML = '';
            
            if (tagUsername) {
              replyInput.innerHTML = `Reply to @${tagUsername}: `;
            }

            replyInput.select();
          }
        });

      } else {
        commentDetail.addEventListener('click', (e) => {
          if (e.target.matches('.reply-btn')) {
            displayLoginRequirement();
          }
        });
      }
    }
  }

  if (currentUser) {
    /* prepare current user image url */
    const currentUserImgUrl = currentUser.img_url ? currentUser.img_url : './img/svg/default-user.svg';
    
    /* render comment form */
    renderCommentForm(commentContainer, 'comment', currentUserImgUrl);
    
    /* automate form resizing */
    const commentForm = commentContainer.querySelector('.comment-form');
    const commentInput = commentForm.querySelector('.comment-input');
    commentInput.addEventListener('input', () => {
      resizingInput(commentInput);
    })
    
    /* on submit comment */
    commentInput.addEventListener('keypress', async (e) => {
      if (e.keyCode === 13 && !e.shiftKey) {
        const comment_text = commentInput.value.trim();
        
        if (comment_text) {
          await onSubmitComment(
            `INSERT INTO 
              event_comments
            SET
              comment_text = ?,
              user_id = ?,
              event_id = ?;`,
            [comment_text, currentUser.id, event_id],
            commentForm
          );
          
          commentContainer.innerHTML = '';
          renderComments(event_id, currentUser);
        } else {
          commentForm.reset();
          commentForm.classList.add('d-none');
        }
      }
    })
    
    /* Toggle comment form */
    const commentBtn = document.querySelector('.comment-btn');
    commentBtn.addEventListener('click', (e) => {
      toggleForm(commentForm);
      commentInput.select();
    });

  } else {
    const commentBtn = document.querySelector('.comment-btn');
    commentBtn.addEventListener('click', (e) => {
      displayLoginRequirement();
    });
  }
}

/**************************************************************************************/
//**************************************************************************************/
/**************************************************************************************/

function displayLoginRequirement() {
  const loginRequirement = document.querySelector('#loginRequirement');
  loginRequirement.classList.remove('d-none');
  loginRequirement.scrollIntoView({ 
    behavior: "smooth", 
    block: "center", 
    inline: "nearest"
  });
}

async function updateLikeStates(event_id, likeCounter, currentUserId) {
  const likes = await getLikes(event_id);
  likeCounter.innerHTML = likes.length;
  document.querySelector('.modal-container').innerHTML = renderLikeModal(likes);

  if (!currentUserId) return;

  const likeIcon = document.querySelector('.like-btn i');

  if (likes.some(like => like.user_id === currentUserId)) {
    likeIcon.className = 'fas fa-heart mx-1';
  } else {
    likeIcon.className = 'far fa-heart mx-1';
  }
}

function renderLikeModal(likes) {
  let likeItem;
  
  if (likes.length > 0) {
    likeItem = likes.map(like => {
      const imgUrl = like.img_url ? like.img_url : 'img/svg/default-user.svg';

      return `
        <div class='like-item'>
          <div>
            <img src='${imgUrl}'>
            <span>${like.username}</span>
          </div>
          <div>${like.created_at}</div>
        </div>
      `;
    }).join('');

  } else {
    likeItem = 'This event has no like yet';
  }

  return `
    <div class="modal fade" id="likeModal" tabindex="-1" role="dialog" aria-labelledby="likeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="likeModalLabel">
              <div><span class='mx-2'>${likes.length}</span><img src='img/svg/love.svg' alt='love-icon'></i></div>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body d-flex flex-column">
            ${likeItem}
          </div>
        </div>
      </div>
    </div>
  `;
}

async function like(event_id, currentUserId) {
  const response = await fetchData('our-work.add-like.php', {
    event_id,
    currentUserId
  });

  return response.result;
}

async function getLikes(event_id) {
  const query = `
    SELECT 
      event_likes.id, event_likes.user_id, event_likes.event_id, event_likes.created_at, 
      users.username, users.img_url
    FROM event_likes JOIN users ON event_likes.user_id = users.id 
    WHERE event_id = ?;
  `;
  const response = await fetchData('get-data.php', {
    query,
    params: JSON.stringify([event_id])
  });

  return response.rows;
}

async function getComments(event_id) {
  const query = `
    SELECT 
      event_comments.id, 
      event_comments.parent_comment_id, 
      event_comments.comment_text, 
      event_comments.created_at, 
      users.username, 
      users.img_url
    FROM event_comments 
    JOIN users ON event_comments.user_id = users.id 
    WHERE event_id = ?
    ORDER BY event_comments.created_at;
  `;

  const response = await fetchData('get-data.php', { 
    query,
    params: JSON.stringify([event_id])
  });

  return response.rows;
}


function renderComment (container, comment, commentType) {
  const wrapper = document.createElement('div');
  wrapper.classList.add(`${commentType}-wrapper`);
  container.insertAdjacentElement('beforeend', wrapper);
  const imgUrl = comment.img_url ? comment.img_url : 'img/svg/default-user.svg';
  const template = `
    <img class='${commentType}-user-image' src='${imgUrl}'>
    <div class='${commentType}-detail'>
      <div class='${commentType}-username-text-wrapper'>
        <div class='${commentType}-username'>${comment.username}</div>
        <div class='${commentType}-text'>${comment.comment_text}</div>
      </div>
      <div class='${commentType}-date'>${getShortDateTime(comment.created_at)}<span class='reply-btn ml-1' data-username=${commentType === 'reply' ? comment.username : ''}>Reply</span></div>
    </div>
  `;
  wrapper.insertAdjacentHTML('beforeend', template);

  return wrapper;
}


function toggleForm(commentForm) {
  const forms = document.querySelectorAll('.reply-form, .comment-form');

  forms.forEach(form => {
    form.classList.add('d-none');
  })

  commentForm.classList.remove('d-none');
  commentForm.scrollIntoView({ 
    behavior: "smooth", 
    block: "center", 
    inline: "nearest"
  });
  commentForm.reset();
}


function renderCommentForm(container, commentType, currentUserImgUrl) {
  const template = `
    <form class='${commentType}-form mb-2 ${commentType === 'comment' ? '' : 'd-none'}'>
      <img class='${commentType}-user-image' src='${currentUserImgUrl}'>
      <textarea type='text' class='${commentType}-input' rows='1' placeholder='Write a ${commentType}...'></textarea>
    </form>
  `;
  container.insertAdjacentHTML('beforeend', template);
}


function resizingInput(input) {
  input.style.height = 'auto';
  input.style.height = input.scrollHeight + 'px';
}


async function onSubmitComment(query, params, commentForm) {
  await fetchData('admin/cms.alter-data.php', {
    query,
    params: JSON.stringify(params)
  })
  
  commentForm.classList.add('d-none');
  commentForm.reset();
}


function getShortDateTime(str) {
  const date = new Date(str);
  const year = `${date.getFullYear()}`.slice(2);
  const month = date.getMonth() + 1;
  const day = date.getDate();
  const hours = date.getHours();
  const mins = date.getMinutes();

  return `${day}-${month}-${year} ${hours}:${mins}`;
}
