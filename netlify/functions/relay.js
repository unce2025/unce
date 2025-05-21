
export async function handler(event, context) {
  const TARGET_URL = "https://script.google.com/macros/s/AKfycbz_NrN4dnBf43IRXMw4RjsKff5C5EDbNh9XqCyjsKbyuFTcRWVZE96fGzUDmFbkQ2KW/exec";

  const method = event.httpMethod;
  const headers = {
    'Content-Type': 'application/json'
  };

  let response;
  try {
    if (method === "GET") {
      const query = event.rawQuery || "";
      response = await fetch(`${TARGET_URL}?${query}`, { method, headers });
    } else if (method === "POST") {
      response = await fetch(TARGET_URL, {
        method,
        headers,
        body: event.body
      });
    } else {
      return {
        statusCode: 405,
        body: JSON.stringify({ error: "Method not allowed" })
      };
    }

    const data = await response.text();
    return {
      statusCode: 200,
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "*"
      },
      body: data
    };

  } catch (err) {
    return {
      statusCode: 500,
      body: JSON.stringify({ error: err.message })
    };
  }
}

