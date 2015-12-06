SELECT DISTINCT GPSLatitude, GPSLongitude, p.Name, TotalSlotQty, Tyyppi 
FROM PARK_LOT AS p INNER JOIN (SEGMENT AS s, PaymentType as pt) 
ON p.Name = s.Name AND pt.Segment = s.Segment
WHERE GPSLatitude >= 60 AND GPSLatitude <= 61 AND GPSLongitude >= 22.25 AND GPSLongitude <= 24;
