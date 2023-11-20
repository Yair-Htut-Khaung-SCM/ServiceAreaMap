// function roundAndDivide(value, interval) {
//     // Round up to the nearest multiple of the interval
//     let roundedValue = Math.ceil(value / interval) * interval;

//     // Subtract the interval to get the correct result
//     let result = Math.floor((roundedValue - interval) / interval);

//     return result;
// }

// function meshcode2adjust(value, interval) {
//     // Round up to the nearest multiple of the interval
//     let roundedValue = Math.ceil(value / interval) * interval;

//     // Check if the second digit is 5 or above
//     let secondDigit = Math.floor((roundedValue % 100) / 10);
    
//     if (secondDigit >= 5) {
//         // If yes, adjust the tens place
//         return Math.floor((roundedValue - interval) / interval);
//     } else {
//         // Otherwise, use the regular result
//         return Math.floor(roundedValue / interval);
//     }
// }



function latlonToMesh(lat, lng) {

    let mesh1lat = Math.floor(lat);
    let mesh2lat = Math.floor((lat % 1) * 1000 / 125);
    let mesh3lat = Math.floor(( Math.floor((lat % 1) * 1000 % 125) * 10) / 125);


    let mesh1lng = Math.floor(lng);
    let mesh2lng = Math.floor((lng % 1) * 1000 / 125);
    let mesh3lng = Math.floor(( Math.floor((lng % 1) * 1000 % 125) * 10) / 125);


    let meshcode = `${mesh1lat}${mesh1lng}${mesh2lat}${mesh2lng}${mesh3lat}${mesh3lng}`;

    return meshcode;
}




// One point lat lng
function convertToMeshCode() {

    let addListBtn = document.getElementById('addListBtn');
    let latitudeInput = document.getElementById('latitude');
    let longitudeInput = document.getElementById('longitude');
    let meshCodeResult = document.getElementById('meshCodeResult');
    let invalid = document.getElementById('invalid-onepoint');
    let copyText = document.getElementById('copy-text');




    if (latitudeInput.checkValidity() && longitudeInput.checkValidity()) {
        addListBtn.style.backgroundColor = '#00ada9';
        addListBtn.disabled = false;
        let latitude = parseFloat(latitudeInput.value);
        let longitude = parseFloat(longitudeInput.value);


        let meshCode = latlonToMesh(latitude, longitude);

        meshCodeResult.textContent = `${meshCode}`;
        copyText.value = `${meshCode}`;
    } else {
        // If inputs are not valid, show an error message
        copyText.value = '';
        invalid.textContent = 'Invalid latitude or longitude. (format : 00.000)';
    }
}

// Multi point lat and lng
function convertMultiToMeshCode() {
    let addMultListBtn = document.getElementById('addMultListBtn');
    let invalid = document.getElementById('invalid-mulpoint');
    let yLat = document.getElementById('y-lat');
    let yLon = document.getElementById('y-lon');
    let xLat = document.getElementById('x-lat');
    let xLon = document.getElementById('x-lon');
    let yEndLat = document.getElementById('y-end-lat');
    let yEndLon = document.getElementById('y-end-lon');
    let xEndLat = document.getElementById('x-end-lat');
    let xEndLon = document.getElementById('x-end-lon');
    let multiMeshCodeResult = document.getElementById('multiMeshCodeResult');
    let copyText = document.getElementById('copy-text');

    if (yLat.checkValidity() && yLon.checkValidity() &&
    xLat.checkValidity() && xLon.checkValidity() &&
    yEndLat.checkValidity() && yEndLon.checkValidity() &&
    xEndLat.checkValidity() && xEndLon.checkValidity()) {
 
    let yStartMesh = latlonToMesh(yLat.value, yLon.value);
    let yEndMesh = latlonToMesh(yEndLat.value, yEndLon.value);
    let xStartMesh = latlonToMesh(xLat.value, xLon.value);
    let xEndMesh = latlonToMesh(xEndLat.value, xEndLon.value);

    let x = [];
    let x_end = [];
    let y = [];
    let y_end = [];
    let multiOffset = [];


    // start x point 
    x[0] = `${parseInt(xStartMesh.substring(0,2))}${parseInt(xStartMesh.substring(4,5))}${parseInt(xStartMesh.substring(6,7))}`;
    x[1] = `${parseInt(xStartMesh.substring(2,4))}${parseInt(xStartMesh.substring(5,6))}${parseInt(xStartMesh.substring(7,8))}`;

    // end x point
    x_end[0] = `${parseInt(xEndMesh.substring(0,2))}${parseInt(xEndMesh.substring(4,5))}${parseInt(xEndMesh.substring(6,7))}`;
    x_end[1] = `${parseInt(xEndMesh.substring(2,4))}${parseInt(xEndMesh.substring(5,6))}${parseInt(xEndMesh.substring(7,8))}`;

    // start y point
    y[0] = `${parseInt(yStartMesh.substring(0,2))}${parseInt(yStartMesh.substring(4,5))}${parseInt(yStartMesh.substring(6,7))}`;
    y[1] = `${parseInt(yStartMesh.substring(2,4))}${parseInt(yStartMesh.substring(5,6))}${parseInt(yStartMesh.substring(7,8))}`;

    // end y point 
    y_end[0] = `${parseInt(yEndMesh.substring(0,2))}${parseInt(yEndMesh.substring(4,5))}${parseInt(yEndMesh.substring(6,7))}`;
    y_end[1] = `${parseInt(yEndMesh.substring(2,4))}${parseInt(yEndMesh.substring(5,6))}${parseInt(yEndMesh.substring(7,8))}`;



    function generatePointsWithinRectangle(vertexA, vertexB, vertexC, vertexD) {
        const points = [];
    
        for (let x = Math.min(vertexA[0], vertexC[0]); x <= Math.max(vertexB[0], vertexD[0]); x++) {
            for (let y = Math.min(vertexA[1], vertexB[1]); y <= Math.max(vertexC[1], vertexD[1]); y++) {
                points.push([x, y]);
            }
        }
    
        return points;
    }
//   C --------------------------- D
//   |                             |
//   A --------------------------- B
    // Example vertices
    const vertexA = [x[0], x[1]]; // x
    const vertexB = [x_end[0], x_end[1]]; // x_end
    const vertexC = [y[0], y[1]]; // y
    const vertexD = [y_end[0], y_end[1]]; // y_end
    
    const pointsWithinRectangle = generatePointsWithinRectangle(vertexA, vertexB, vertexC, vertexD);

    let count = 0;
    pointsWithinRectangle.forEach(function (item) {

        multiOffset[count] = `${parseInt((item[0].toString()).substring(0,2))}${parseInt((item[1].toString()).substring(0,2))}${parseInt((item[0].toString()).substring(2,3))}${parseInt((item[1].toString()).substring(2,3))}${parseInt((item[0].toString()).substring(3,4))}${parseInt((item[1].toString()).substring(3,4))}`;
        count++;

    });
    
    addMultListBtn.style.backgroundColor = '#00ada9';
    addMultListBtn.disabled = false;
    let allMeshCodes = multiOffset.join('\n');
    multiMeshCodeResult.textContent = allMeshCodes;
    copyText.value = `${allMeshCodes}`;


    } else {
        // If inputs are not valid, show an error message
        copyText.value = '';
        invalid.textContent = 'Invalid latitude or longitude. (format : 00.000)';
    }


 
}

// Center point lat and lng 
function convertCenterToMeshCode() {
    let addCenterListBtn = document.getElementById('addCenterListBtn');
    let invalid = document.getElementById('invalid-centerpoint');
    let centerLat = document.getElementById('center-lat');
    let centerLon = document.getElementById('center-lon');
    let outerLat = document.getElementById('outer-lat');
    let outerLon = document.getElementById('outer-lon');
    let circleOffset = [];
    let centerMeshCodeResult = document.getElementById('centerMeshCodeResult');
    let copyText = document.getElementById('copy-text');

    if (centerLat.checkValidity() && centerLon.checkValidity() &&
    outerLat.checkValidity() && outerLon.checkValidity()) {

    let centerMesh = latlonToMesh(centerLat.value, centerLon.value);
    let outerMesh = latlonToMesh(outerLat.value, outerLon.value);

    let centerOffsetx = `${parseInt(centerMesh.substring(0,2))}${parseInt(centerMesh.substring(4,5))}${parseInt(centerMesh.substring(6,7))}`;
    let centerOffsety = `${parseInt(centerMesh.substring(2,4))}${parseInt(centerMesh.substring(5,6))}${parseInt(centerMesh.substring(7,8))}`;

    let outerOffsetx = `${parseInt(outerMesh.substring(0,2))}${parseInt(outerMesh.substring(4,5))}${parseInt(outerMesh.substring(6,7))}`;
    let outerOffsety = `${parseInt(outerMesh.substring(2,4))}${parseInt(outerMesh.substring(5,6))}${parseInt(outerMesh.substring(7,8))}`;


    function generatePointsWithinCircle(center, outerPoint) {
        const points = [];
        
        const radius = Math.sqrt((outerPoint[0] - center[0]) ** 2 + (outerPoint[1] - center[1]) ** 2);
        const diameter = radius * 2;
    
        for (let x = Math.floor(center[0] - radius); x <= Math.ceil(center[0] + radius); x++) {
            for (let y = Math.floor(center[1] - radius); y <= Math.ceil(center[1] + radius); y++) {
                if (Math.sqrt((x - center[0]) ** 2 + (y - center[1]) ** 2) <= radius) {
                    points.push([x, y]);
                }
            }
        }
    
        return points;
    }
 
    
    // Example usage
    const center = [parseInt(centerOffsetx), parseInt(centerOffsety)];
    const pointOnCircumference = [parseInt(outerOffsetx), parseInt(outerOffsety)];

    
    const pointsWithinCircle = generatePointsWithinCircle(center, pointOnCircumference);

    let count = 0;
    pointsWithinCircle.forEach(function (item) {

        circleOffset[count] = `${parseInt((item[0].toString()).substring(0,2))}${parseInt((item[1].toString()).substring(0,2))}${parseInt((item[0].toString()).substring(2,3))}${parseInt((item[1].toString()).substring(2,3))}${parseInt((item[0].toString()).substring(3,4))}${parseInt((item[1].toString()).substring(3,4))}`;
        count++;

    });

    addCenterListBtn.style.backgroundColor = '#00ada9';
    addCenterListBtn.disabled = false;
    let allMeshCodes = circleOffset.join('\n');
    centerMeshCodeResult.textContent = allMeshCodes;
    copyText.value = `${allMeshCodes}`;

    } else {
        // If inputs are not valid, show an error message
        copyText.value = '';
        invalid.textContent = 'Invalid latitude or longitude. (format : 00.000)';
    }
    

}

$(document).ready(function() {
    
    $('#addMultListBtn').css('background-color', '#00ada9');
    $('#addMultListBtn').prop('disabled', true);
    $('#addCenterListBtn').css('background-color', '#00ada9');
    $('#addCenterListBtn').prop('disabled', true);
    $('#addListBtn').css('background-color', '#00ada9');
    $('#addListBtn').prop('disabled', true);
    $('#exportBtn').css('background-color', '#00ada9');
    $('#exportBtn').prop('disabled', true);

    // delete table cell
    $('#deleteAllBtn').on('click', function() {
        // Remove all rows in the table
        $('#meshCodeList').empty();
      });


    // Event listener for Add to List button
    $('#addListBtn').on('click', function() {
      // Get the values from input fields
      $(this).css('background-color', '#00ada9');
      $(this).prop('disabled', true);
      $('#exportBtn').prop('disabled', false);
      let meshCode = $('#meshCodeResult').text();
      let locationType = $('#locationType').val();
      let colorPicker = $('#colorPicker').val();

      let locationTypeValue = 0;
      if (locationType === 'landStable') locationTypeValue = 2;
      else if (locationType === 'landNearLimit') locationTypeValue = 1;
      else if (locationType === 'seaStable') locationTypeValue = 4;
      else if (locationType === 'seaNearLimit') locationTypeValue = 3;

      // Validate lat and long (You can add your validation logic here)

      // Append a new row to the table
      $('#meshCodeList').append(`
        <tr>
          <td class='col-4 text-center'>${meshCode}</td>
          <td class='col-4 text-center'>${locationType}</td>
          <td class='col-4 text-center ${locationType}'>${colorPicker}</td>
        </tr>
      `);

      $(`.${locationType}`).css({
        'background-color': colorPicker,
        'color': colorPicker 
      });

      // Clear input fields and mesh code result
      $('#latitude, #longitude').val('');
      $('#meshCodeResult').text('');
    });

    // Add multiple mesh code to csv
    $('#addMultListBtn').on('click', function() {
        $(this).css('background-color', '#00ada9');
        $(this).prop('disabled', true);
        $('#exportBtn').prop('disabled', false);
        let meshCodesTextarea = $('#multiMeshCodeResult').text();
        let locationType = $('#multLocationType').val();
        let meshCodes = meshCodesTextarea.split('\n');
        let colorPicker = $('#colorPickerMultiLocation').val();

        let locationTypeValue = 0;
        if (locationType === 'landStable') locationTypeValue = 2;
        else if (locationType === 'landNearLimit') locationTypeValue = 1;
        else if (locationType === 'seaStable') locationTypeValue = 4;
        else if (locationType === 'seaNearLimit') locationTypeValue = 3;

        meshCodes.forEach(function (meshCode) {
            // Skip empty mesh codes
            if (meshCode.trim() !== '') {
                // Append a new row to the table
                $('#meshCodeList').append(`
                    <tr>
                    <td class='col-4 text-center'>${meshCode}</td>
                    <td class='col-4 text-center'>${locationType}</td>
                    <td class='col-4 text-center ${locationType}'>${colorPicker}</td>
                    </tr>
                `);

                $(`.${locationType}`).css({
                    'background-color': colorPicker,
                    'color': colorPicker 
                });
            }
        });
    });

    // Add center mesh code to csv
    $('#addCenterListBtn').on('click', function() {
        $(this).css('background-color', '#00ada9');
        $(this).prop('disabled', true);
        $('#exportBtn').prop('disabled', false);
        let centerMeshCodeResult = $('#centerMeshCodeResult').text();
        let locationType = $('#centerLocationType').val();
        let meshCodes = centerMeshCodeResult.split('\n');
        let colorPicker = $('#colorPickerCenterLocation').val();

        let locationTypeValue = 0;
        if (locationType === 'landStable') locationTypeValue = 2;
        else if (locationType === 'landNearLimit') locationTypeValue = 1;
        else if (locationType === 'seaStable') locationTypeValue = 4;
        else if (locationType === 'seaNearLimit') locationTypeValue = 3;

        meshCodes.forEach(function (meshCode) {
            // Skip empty mesh codes
            if (meshCode.trim() !== '') {
                // Append a new row to the table
                $('#meshCodeList').append(`
                    <tr>
                    <td class='col-4 text-center'>${meshCode}</td>
                    <td class='col-4 text-center'>${locationType}</td>
                    <td class='col-4 text-center ${locationType}'>${colorPicker}</td>
                    </tr>
                `);

                $(`.${locationType}`).css({
                    'background-color': colorPicker,
                    'color': colorPicker 
                });
            }
        });
    });

    // Event listener for Export to CSV button
    $('#exportBtn').on('click', function() {
    exportToCSV();
    });

    function exportToCSV() {
        // Prepare CSV content
        let csvContent = '' ;
        
        // Iterate through table rows and append data to CSV content
        $('#meshCodeList tr').each(function(index, row) {
  
            const columns = $(row).find('td');
            const locationType = columns.eq(2).text();
            const meshCode = columns.eq(0).text();

            csvContent += `${meshCode},${locationType}\n`;
        });

        // Create a data URI for the CSV content
        const csvDataUri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvContent);

        // Create a download link with the download attribute
        const link = document.createElement('a');
        link.href = csvDataUri;
        link.setAttribute('download', 'mesh_code_data.csv');

        // Append the link to the document and trigger a click event
        document.body.appendChild(link);
        link.click();

        // Remove the link from the document
        document.body.removeChild(link);
      }

  });

//   copy mesh code result 
function copy() {

    let copyText = document.getElementById("copy-text");
    let copySuccess = document.getElementById("copied-success");
    copyText.select();
    copyText.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(copyText.value);
    
   copySuccess.style.opacity = "1";
   setTimeout(function(){ copySuccess.style.opacity = "0" }, 500);
  }



  