const RELAY_URL = '/.netlify/functions/relay';

async function submitShipment(shipmentData) {
  try {
    const response = await fetch(RELAY_URL, {
      method: 'POST',
      body: JSON.stringify(shipmentData),
      headers: {
        'Content-Type': 'application/json',
      }
    });

    const result = await response.json();

    if (result.success) {
      alert('Shipment saved!');
    } else {
      alert('Failed to save shipment.');
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Failed to submit data.');
  }
}
