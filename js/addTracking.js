const RELAY_URL = '/.netlify/functions/relay';

async function submitShipment(shipmentData) {
  try {
    const response = await fetch(RELAY_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(shipmentData),
    });

    const result = await response.json();

    if (result.success) {
      alert('Shipment saved successfully!');
    } else {
      alert('Failed to save shipment.');
    }
  } catch (error) {
    console.error('Error saving shipment:', error);
    alert('Error submitting shipment data.');
  }
}
