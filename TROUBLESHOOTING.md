# VEYZA Contact Form - Google Sheets Integration Troubleshooting

## Current Configuration
- **Google Apps Script URL**: `https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec`
- **Google Sheet ID**: `1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o`

## "Failed to fetch" Error Solutions

The "Failed to fetch" error typically indicates one of these issues:

### 1. Google Apps Script Deployment Issues

**Check your deployment:**
1. Open your Google Apps Script project
2. Click "Deploy" → "Manage deployments"
3. Verify the deployment settings:
   - **Type**: Web app
   - **Execute as**: Me (your email)
   - **Who has access**: Anyone
4. If settings are wrong, create a new deployment with correct settings

### 2. CORS (Cross-Origin Resource Sharing) Issues

**Solutions:**
- Make sure your Google Apps Script is deployed as a Web App with "Anyone" access
- The script should automatically handle CORS, but sometimes needs re-deployment

### 3. Google Apps Script Code Issues

**Copy this updated code to your Google Apps Script:**
```javascript
function doPost(e) {
  try {
    var sheetId = '1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o';
    var sheet = SpreadsheetApp.openById(sheetId).getActiveSheet();
    
    var formData = e.parameter;
    console.log('Received form data:', JSON.stringify(formData));
    
    var timestamp = new Date();
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
    
    sheet.appendRow(rowData);
    console.log('Data successfully added to sheet');
    
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'success',
        'message': 'Data saved successfully',
        'timestamp': timestamp.toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error('Error in doPost:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'error',
        'message': error.toString(),
        'timestamp': new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

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
```

### 4. Google Sheet Permissions

**Verify your Google Sheet:**
1. Open your Google Sheet: `https://docs.google.com/spreadsheets/d/1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o/edit`
2. Ensure the sheet has headers in Row 1:
   - A1: Timestamp
   - B1: Company Name
   - C1: Location
   - D1: Fleet Size
   - E1: SPOC Name
   - F1: SPOC Contact
   - G1: SPOC Email
   - H1: Solution Required

### 5. Step-by-Step Fix Process

1. **Update Google Apps Script:**
   - Copy the code from `updated-google-apps-script.js`
   - Save the project
   - Test by running the `testFunction()` manually

2. **Re-deploy the Web App:**
   - Click "Deploy" → "New deployment"
   - Choose "Web app"
   - Set "Execute as" to "Me"
   - Set "Who has access" to "Anyone"
   - Click "Deploy"
   - **Important**: Use the NEW deployment URL, not the old one

3. **Update your website files:**
   - All files now use: `https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec`

4. **Test the integration:**
   - Open `debug-google-sheets.html` in your browser
   - Click "Test Connection" first
   - If connection works, try "Test with Minimal Data"
   - Finally, try the full form submission

### 6. Alternative Testing Method

If the debug page still shows "Failed to fetch", try this:

1. **Test the URL directly in your browser:**
   - Open: `https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec`
   - You should see a JSON response like: `{"status":"success","message":"Google Apps Script is working",...}`

2. **Check Google Apps Script logs:**
   - In your Google Apps Script, click "Executions" on the left sidebar
   - Look for recent executions and any error messages

### 7. Common Issues and Solutions

- **"Script function not found"**: Make sure you have both `doPost` and `doGet` functions
- **"Permission denied"**: Run the script manually once to grant permissions
- **"Spreadsheet not found"**: Verify the Google Sheet ID is correct
- **"Headers already sent"**: Make sure you're not calling multiple `return` statements

### 8. If All Else Fails

1. Create a completely new Google Apps Script project
2. Create a new deployment
3. Update all your files with the new URL
4. Test step by step

## Files Updated
- ✅ `js/google-sheets-form.js` - Updated with correct URL
- ✅ `test-google-sheets.html` - Updated with correct URL  
- ✅ `updated-google-apps-script.js` - New file with correct code and Sheet ID
- ✅ `debug-google-sheets.html` - Already has the correct URL

## Next Steps
1. Copy the code from `updated-google-apps-script.js` to your Google Apps Script
2. Re-deploy the web app
3. Test using `debug-google-sheets.html`
