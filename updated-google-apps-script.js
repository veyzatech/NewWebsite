/**
 * Google Apps Script for VEYZA Contact Form
 * This code should be deployed as a Web App in Google Apps Script
 * 
 * Google Sheet ID: 1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o
 * Web App URL: https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec
 */

function doPost(e) {
  try {
    // Your Google Sheet ID
    var sheetId = '1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o';
    var sheet = SpreadsheetApp.openById(sheetId).getActiveSheet();
    
    // Parse the form data
    var formData = e.parameter;
    
    // Log the received data for debugging
    console.log('Received form data:', JSON.stringify(formData));
    
    // Get current timestamp
    var timestamp = new Date();
    
    // Prepare the row data
    var rowData = [
      timestamp,
      formData['company-name'] || '',
      formData['location'] || '',
      formData['fleet-size'] || '',
      formData['spoc-name'] || '',
      formData['spoc-contact'] || '',
      formData['spoc-email'] || '',
      formData['solution-required'] || ''
    ];
    
    // Add the data to the sheet
    sheet.appendRow(rowData);
    
    // Log success
    console.log('Data successfully added to sheet');
    
    // Return success response
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'success',
        'message': 'Data saved successfully',
        'timestamp': timestamp.toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    // Log the error
    console.error('Error in doPost:', error);
    
    // Return error response
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'error',
        'message': error.toString(),
        'timestamp': new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Also handle GET requests for connection testing
function doGet(e) {
  try {
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'success',
        'message': 'Google Apps Script is working',
        'timestamp': new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  } catch (error) {
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'error',
        'message': error.toString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Test function to verify the script works
function testFunction() {
  var testData = {
    parameter: {
      'company-name': 'Test Company',
      'location': 'Test Location',
      'fleet-size': 'less-than-50',
      'spoc-name': 'Test Name',
      'spoc-contact': '1234567890',
      'spoc-email': 'test@example.com',
      'solution-required': 'unified-view'
    }
  };
  
  var result = doPost(testData);
  console.log('Test result:', result.getContent());
}
