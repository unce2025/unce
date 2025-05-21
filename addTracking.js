const fs = require('fs');
const path = require('path');

// Path to the JSON data file
const dataFile = path.join(__dirname, 'shipments.json');

exports.handler = async (event) => {
  if (event.httpMethod !== 'POST') {
    return { statusCode: 405, body: 'Method Not Allowed' };
  }

  const data = JSON.parse(event.body);
  const trackingNumber = data.tracking;

  if (!trackingNumber) {
    return {
      statusCode: 400,
      body: JSON.stringify({ message: "Tracking number is required." }),
    };
  }

  try {
    // Load existing data or initialize
    let shipments = {};
    if (fs.existsSync(dataFile)) {
      const content = fs.readFileSync(dataFile);
      shipments = JSON.parse(content);
    }

    // Save or update the shipment
    shipments[trackingNumber] = data;

    // Write back to file
    fs.writeFileSync(dataFile, JSON.stringify(shipments, null, 2));

    return {
      statusCode: 200,
      body: JSON.stringify({ message: "Shipment saved successfully!" }),
    };
  } catch (error) {
    return {
      statusCode: 500,
      body: JSON.stringify({ message: "Failed to save shipment", error: error.message }),
    };
  }
};