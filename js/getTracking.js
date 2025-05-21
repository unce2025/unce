const RELAY_URL = '/.netlify/functions/relay';

async function getTracking(trackingNumber) {
  try {
    const response = await fetch(`${RELAY_URL}?tracking=${encodeURIComponent(trackingNumber)}`);
    const result = await response.json();

    if (result.success === false || !result.shipment) {
      alert('Tracking not found.');
      return;
    }

    displayShipment(result.shipment);
  } catch (error) {
    console.error('Fetch error:', error);
    alert('Failed to fetch tracking data.');
  }
}
