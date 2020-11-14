/* dropdown */
const dropdownTogglers = document.querySelectorAll('.dropdown');

if (dropdownTogglers) {
  dropdownTogglers.forEach(dropdownToggler => {
    const dropdwonMenu = dropdownToggler.querySelector('.dropdown-menu');
    
    dropdownToggler.addEventListener('mouseover', () => {
      dropdwonMenu.classList.add('show');
    })
  
    dropdownToggler.addEventListener('mouseleave', () => {
      dropdwonMenu.classList.remove('show');
    })
  })
}


/* card hover */
const cards = document.querySelectorAll('.card');

if (cards) {
  cards.forEach(card => {
    card.addEventListener('mouseover', () => {
      card.classList.add('shadow');
    });

    card.addEventListener('mouseout', () => {
      card.classList.remove('shadow');
    })
  })
}


/* show more */
const showMoreBtns = document.querySelectorAll('.show-more');

if (showMoreBtns) {
  showMoreBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const content = btn.parentNode.querySelector('.long-content');
      content.classList.toggle('show');

      if (content.classList.contains('show')) {
        btn.querySelector('span').innerHTML = 'Show less';
      } else  {
        btn.querySelector('span').innerHTML = 'Show more';
      }
    });
  })
}
