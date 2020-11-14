export async function uploadFiles(url, files, inputName, data) {
  try {
    const formData  = new FormData();

    for (const file of files) {
      formData.append(inputName, file); // when inputName has "[]" next to it php will put the data into $_FILES
    }

    for (const key in data) {
      formData.append(key, data[key]);
    }

    let response = await fetch(url, {
      method: 'POST',
      body: formData
    });

    response = await response.json(); 
    return response;

  } catch(error) {
    console.log(error);
  }
}

export async function fetchData(url, data = null) {
  try {

    if (data) {
      const formData = new FormData();
  
      for (const key in data) {
        formData.append(key, data[key]);
      }
  
      let response = await fetch(url, {
        method: 'POST',
        body: formData
      });
  
      response = await response.json();
      return response;

    } else {
      let response = await fetch(url);
      response = await response.json();
      return response;
    }

  } catch(error) {
    console.log(error);
  }
}