const GAS_URL = 'https://script.google.com/macros/s/AKfycbz_NrN4dnBf43IRXMw4RjsKff5C5EDbNh9XqCyjsKbyuFTcRWVZE96fGzUDmFbkQ2KW/exec';

exports.handler = async function(event) {
  try {
    if (event.httpMethod === 'GET') {
      const tracking = event.queryStringParameters.tracking;

      if (!tracking) {
        return {
          statusCode: 400,
          body: JSON.stringify({ success: false, message: 'Tracking number required' }),
        };
      }

      const response = await fetch(`${GAS_URL}?tracking=${encodeURIComponent(tracking)}`);
      const data = await response.json();

      return {
        statusCode: 200,
        headers: { 'Access-Control-Allow-Origin': '*' },
        body: JSON.stringify(data),
      };
    }

    if (event.httpMethod === 'POST') {
      const body = JSON.parse(event.body);
      const response = await fetch(GAS_URL, {
        method: 'POST',
        body: JSON.stringify(body),
        headers: { 'Content-Type': 'application/json' },
      });

      const data = await response.json();
      return {
        statusCode: 200,
        headers: { 'Access-Control-Allow-Origin': '*' },
        body: JSON.stringify(data),
      };
    }

    return {
      statusCode: 405,
      body: JSON.stringify({ success: false, message: 'Method Not Allowed' }),
    };
  } catch (err) {
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Error processing request' }),
    };
  }
};
