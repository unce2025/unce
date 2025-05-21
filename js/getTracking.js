const RELAY_URL = '/.netlify/functions/relay';

async function getTracking(trackingNumber) {
  try {
    const response = await fetch(`${RELAY_URL}?tracking=${encodeURIComponent(trackingNumber)}`);
    const result = await response.json();
    displayShipment(result.shipment); // Your display function
  } catch (error) {
    console.error('Error:', error);
    alert('Failed to fetch tracking data.');
  }
}
