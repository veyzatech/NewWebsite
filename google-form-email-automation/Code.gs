function onFormSubmit(e) {
  // Configuration
  const RECIPIENT_EMAIL = "info@veyza.in"; // Change this to the specific person's email
  const FORM_TITLE = "Contact Us"; // Change this to your form title
  
  try {
    // Get form response data
    const itemResponses = e.response.getItemResponses();
    let responseData = "";
    
    // Build email content from form responses
    for (let i = 0; i < itemResponses.length; i++) {
      const itemResponse = itemResponses[i];
      const question = itemResponse.getItem().getTitle();
      const answer = itemResponse.getResponse();
      responseData += `${question}: ${answer}\n`;
    }
    
    // Email details
    const subject = `New ${FORM_TITLE} Response Received`;
    const body = `A new response has been submitted to ${FORM_TITLE}.\n\nDetails:\n${responseData}\n\nTimestamp: ${new Date()}`;
    
    // Send email
    MailApp.sendEmail({
      to: RECIPIENT_EMAIL,
      subject: subject,
      body: body
    });
    
    console.log("Email notification sent successfully");
    
  } catch (error) {
    console.error("Error sending email notification:", error);
  }
}

function setupTrigger() {
  // Get the form (replace with your form ID)
  const form = FormApp.getActiveForm(); // Use this if script is bound to form
  // Or use: const form = FormApp.openById('YOUR_FORM_ID');
  
  // Delete existing triggers
  const triggers = ScriptApp.getProjectTriggers();
  triggers.forEach(trigger => {
    if (trigger.getHandlerFunction() === 'onFormSubmit') {
      ScriptApp.deleteTrigger(trigger);
    }
  });
  
  // Create new trigger
  ScriptApp.newTrigger('onFormSubmit')
    .onFormSubmit()
    .create();
    
  console.log("Trigger setup completed");
}