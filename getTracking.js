const shipments = {
  "UN123CE456": {
    ShipperName: "Don Charles",
    ShipperAddress: "United Kingdom",
    ShipperEmail: "dondoncharles404@gmail.com",
    ShipperPhone: "09934604219",
    ReceiverName: "ELIZABETH D. AQUINO",
    ReceiverAddress: "PUROK 8 SALVACION, PANABO CITY, DAVAO DEL NORTE 8112",
    ReceiverPhone: "09934604219",
    ReceiverEmail: "elizabethaquino0408199@gmail.com",
    Mode: "Air-Freight",
    Product: "Electronics",
    Quantity: "2",
    Payment: "PayPal",
    TotalFreight: "$320.00",
    ExpectedDelivery: "2025-05-12",
    Departure: "09:00 AM",
    PickDate: "2025-05-07",
    PickUpTime: "08:00 AM",
    Status: "ON HOLD – Customs clearance is pending. Action is required to avoid further delay."
  }
};

exports.handler = async (event) => {
  const tracking = event.queryStringParameters.tracking;

  if (shipments[tracking]) {
    return {
      statusCode: 200,
      body: JSON.stringify({
        tracking,
        ...shipments[tracking]
      })
    };
  }

  return {
    statusCode: 404,
    body: JSON.stringify({ message: "Shipment not found" })
  };
};