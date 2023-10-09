<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.6.0/jszip.min.js"></script>



<input type="file" id="pdfInput" accept=".pdf">
<button onclick="extractImages()">Extract Images</button>


<script>

function extractImages() {
  const pdfInput = document.getElementById('pdfInput');
  const file = pdfInput.files[0];

  if (!file) {
    alert('Please select a PDF file.');
    return;
  }

  const reader = new FileReader();

  reader.onload = function (event) {
    const arrayBuffer = event.target.result;

    // Load the PDF using pdf.js
    pdfjsLib.getDocument({ data: arrayBuffer }).promise.then(function (pdf) {
      const zip = new JSZip();

      // Helper function to convert image data to a Blob

      function dataURLToBlob(dataURL) {
        const parts = dataURL.match(/^data:(.*?)(;base64)?,(.*)$/);

        if (!parts) {
          console.error('Invalid data URL:', dataURL);
          return null; // Return null on invalid data URLs
        }

        const contentType = parts[1];
        const isBase64 = !!parts[2];
        const data = parts[3];

        const byteCharacters = isBase64 ? atob(data) : decodeURIComponent(data);
        const byteNumbers = new Array(byteCharacters.length);

        for (let i = 0; i < byteCharacters.length; i++) {
          byteNumbers[i] = byteCharacters.charCodeAt(i);
        }

        const byteArray = new Uint8Array(byteNumbers);

        return new Blob([byteArray], { type: contentType });
      }



     // function dataURLToBlob(dataURL) {
     //    const parts = dataURL.split(',');

     //    if (parts.length !== 2) {
     //      console.error('Invalid data URL:', dataURL);
     //      return null; // Return null on invalid data URLs
     //    }

     //    const contentType = parts[0].split(':')[1].split(';')[0];
     //    const byteCharacters = atob(parts[1]);
     //    const byteNumbers = new Array(byteCharacters.length);

     //    for (let i = 0; i < byteCharacters.length; i++) {
     //      byteNumbers[i] = byteCharacters.charCodeAt(i);
     //    }

     //    const byteArray = new Uint8Array(byteNumbers);

     //    return new Blob([byteArray], { type: contentType });
     //  }

      // Iterate through each page of the PDF
        const pagePromises = [];
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
          pagePromises.push(pdf.getPage(pageNum).then(function (page) {
            // Extract images from the page
            return page.getOperatorList().then(function (ops) {
              const pageImages = [];

              for (let i = 0; i < ops.fnArray.length; i++) {
                if (ops.fnArray[i] === pdfjsLib.OPS.paintImageXObject) {
                  const imgData = ops.argsArray[i][0].data;


                  console.log(imgData);

                  const imgName = `image_page${pageNum}_${i}.png`;
                  pageImages.push({ name: imgName, data: imgData });
                }
              }

              return pageImages;
            });
          }));
        }


      Promise.all(pagePromises).then(function (pageImagesArray) {
        // Flatten the array of arrays
        const allImages = [].concat(...pageImagesArray);

        // Add images to the ZIP file
        allImages.forEach(function (image) {
          const blob = dataURLToBlob(image.data);
          zip.file(image.name, blob);
        });

        // Generate and download the ZIP file
        zip.generateAsync({ type: 'blob' }).then(function (content) {
          const zipFileName = 'images.zip';
          const blob = new Blob([content]);
          const a = document.createElement('a');
          a.href = window.URL.createObjectURL(blob);
          a.download = zipFileName;
          a.style.display = 'none';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
        });
      });
    });
  };

  reader.readAsArrayBuffer(file);
}

</script>
