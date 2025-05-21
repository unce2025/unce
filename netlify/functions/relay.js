const GAS_URL = 'https://script.google.com/macros/s/AKfycbz_NrN4dnBf43IRXMw4RjsKff5C5EDbNh9XqCyjsKbyuFTcRWVZE96fGzUDmFbkQ2KW/exec';

exports.handler = async function(event) {
  const tracking = event.queryStringParameters.tracking;

  if (!tracking) {
    return {
      statusCode: 400,
      body: JSON.stringify({ success: false, message: 'Tracking number required' }),
    };
  }

  try {
    const response = await fetch(`${GAS_URL}?tracking=${encodeURIComponent(tracking)}`);
    const data = await response.json();

    return {
      statusCode: 200,
      body: JSON.stringify(data),
      headers: {
        'Access-Control-Allow-Origin': '*',
      },
    };
  } catch (err) {
    return {
      statusCode: 500,
      body: JSON.stringify({ success: false, message: 'Error fetching data' }),
    };
  }
};
