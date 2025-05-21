const endpoint = 'https://script.google.com/macros/s/AKfycbw-3HuIdKt0STynAuZZxOC51kAVhd6hBp7kd3UChOX5dN6S2hUqBaRbJW81aOr63nY/exec';

/**
 * Fetch shipment data by tracking number (used in status.html)
 * @param {string} trackingNumber
 * @returns {Promise<object>}
 */
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

/**
 * Save or update a shipment (used in admin.html)
 * @param {object} shipmentData
 * @returns {Promise<object>}
 */
async function saveShipment(shipmentData) {
  try {
    const response = await fetch(endpoint, {
      method: 'POST',
      mode: 'cors',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(shipmentData)
    });

    const result = await response.json();
    if (!result.success) {
      throw new Error(result.message || 'Failed to save shipment.');
    }

    return result;
  } catch (error) {
    console.error('Save error:', error);
    throw error;
  }
}
