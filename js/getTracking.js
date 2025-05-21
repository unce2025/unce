const endpoint = '/.netlify/functions/relay';

async function fetchShipment(trackingNumber) {
  const url = `${endpoint}?tracking=${encodeURIComponent(trackingNumber)}`;

  try {
    const response = await fetch(url);
    const result = await response.json();
    if (!result.success || !result.shipment) {
      throw new Error('Shipment not found.');
    }
    return result.shipment;
  } catch (error) {
    console.error('Fetch error:', error);
    throw error;
  }
}
