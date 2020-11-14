export function displayUploadedImages(files, container, className = '') {
  container.innerHTML = '';
  
  for (const file of files) {

    if (!file.type.startsWith('image/')) { 
      continue;
    }

    container.innerHTML += `<img src=${URL.createObjectURL(file)} class=${className} alt='image'>`; 

    /*
    const reader = new FileReader();

    reader.onload = e => { 
      container.innerHTML += `<img src=${e.target.result} class=${className} alt='image'>`; 
    };

    // reader.addEventListener('loadend', () => { 
    //   container.innerHTML += `<img src=${reader.result} class=${className} alt='image'>`; 
    // });

    reader.readAsDataURL(file);
    */
  }
}