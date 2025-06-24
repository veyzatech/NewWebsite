# Google Sheets Integration Troubleshooting Guide

## Current Status
- **Web App URL**: https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec
- **Google Sheet ID**: 1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o
- **Error**: "Failed to fetch" when testing locally

## Why "Failed to fetch" Occurs

The "Failed to fetch" error is **NORMAL** when testing locally from a `file://` URL due to browser CORS (Cross-Origin Resource Sharing) restrictions. This does NOT mean your Google Apps Script is broken.

## Step-by-Step Verification Process

### Step 1: Test Google Apps Script Directly
1. Open this URL in your browser:
   ```
   https://script.google.com/macros/s/AKfycbzYYoMq4lKyyE-Gj5Hteh_yWeuEodq-BakbIxQnmowod9tTZBD7wX6nOPeQAZ4OH6Vn/exec
   ```
2. **Expected Result**: You should see JSON response like:
   ```json
   {"status":"success","message":"Google Apps Script is working","timestamp":"..."}
   ```
3. **If you see an error**: Your Google Apps Script needs to be fixed or redeployed.

### Step 2: Verify Google Apps Script Code
Make sure your Google Apps Script contains this code:

```javascript
function doPost(e) {
  try {
    var sheetId = '1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o';
    var sheet = SpreadsheetApp.openById(sheetId).getActiveSheet();
    
    var formData = e.parameter;
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
    
    return ContentService
      .createTextOutput(JSON.stringify({
        'status': 'success',
        'message': 'Data saved successfully',
        'timestamp': timestamp.toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
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
  return ContentService
    .createTextOutput(JSON.stringify({
      'status': 'success',
      'message': 'Google Apps Script is working',
      'timestamp': new Date().toISOString()
    }))
    .setMimeType(ContentService.MimeType.JSON);
}
```

### Step 3: Check Deployment Settings
1. In Google Apps Script, click **Deploy** → **Manage deployments**
2. Verify settings:
   - **Type**: Web app
   - **Execute as**: Me
   - **Who has access**: Anyone
3. If you made changes, click **New deployment** with these settings

### Step 4: Verify Google Sheet Setup
1. Open your sheet: https://docs.google.com/spreadsheets/d/1Hco3edhwO9w7wpNDu1Wl9pDmAlwRSmQz2BcpfkjSD4o/edit
2. Make sure Row 1 has these headers:
   - A1: Timestamp
   - B1: Company Name
   - C1: Location
   - D1: Fleet Size
   - E1: SPOC Name
   - F1: SPOC Contact
   - G1: SPOC Email
   - H1: Solution Required

### Step 5: Test Form Submission (Despite CORS Error)

Even if you get "Failed to fetch" locally, the form might still work. Try this:

1. Open `simple-test.html` in your browser
2. Click "Test Form Submission"
3. Check your Google Sheet - data might appear even if the test shows an error

### Step 6: Test on a Web Server (Not Locally)

The CORS issue only happens when testing locally. To properly test:

1. **Option A**: Use the local server I started:
   - Open: http://localhost:8000/debug-google-sheets.html
   - This should avoid CORS issues

2. **Option B**: Deploy to your actual website and test there

3. **Option C**: Use Live Server extension in VS Code

### Step 7: Test the Real Form

Your main form is in `index.html`. Test it by:
1. Opening the website normally (not locally)
2. Filling out the contact form
3. Checking if data appears in your Google Sheet

## Common Issues and Solutions

### Issue: "Script not found" error
**Solution**: Redeploy your Google Apps Script as a new deployment

### Issue: "Permission denied" error
**Solution**: 
1. Run the script manually once in Google Apps Script editor
2. Grant all requested permissions
3. Redeploy the script

### Issue: Data not appearing in sheet
**Solution**:
1. Check the Google Sheet ID in your Apps Script code
2. Verify the sheet has proper headers
3. Check the Apps Script execution logs

### Issue: Form works but no response
**Solution**: This is normal due to CORS. Check the Google Sheet directly.

## Testing Checklist

- [ ] Google Apps Script URL works when opened directly
- [ ] Google Apps Script is deployed with correct permissions
- [ ] Google Sheet has proper headers
- [ ] Form field names match the Apps Script code
- [ ] Test data appears in Google Sheet (even if error shown)

## Files to Test

1. **simple-test.html** - Basic testing
2. **debug-google-sheets.html** - Advanced debugging
3. **test-google-sheets.html** - Form testing
4. **index.html** - Your actual website form

## Next Steps

1. First, test the Google Apps Script URL directly in browser
2. If that works, test form submission and check Google Sheet
3. If data appears in sheet, your integration is working (ignore CORS errors)
4. Deploy to actual website for final testing

Remember: "Failed to fetch" when testing locally ≠ broken integration!
