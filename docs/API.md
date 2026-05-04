# Basic REST API

## Endpoints

- `POST /api/auth/login`
  Validates email/password and returns a bearer token for API use.

- `GET /api/docs`
  Returns basic API metadata and endpoint list.

- `GET /api/trips`
  Returns paginated published trips.

- `GET /api/trips/{id}`
  Returns one trip with computed availability.

- `POST /api/trips`
  Creates a trip. Requires authenticated admin session. Accepts JSON.

- `PUT /api/trips/{id}`
  Updates a trip. Requires authenticated admin session. Accepts JSON.

- `DELETE /api/trips/{id}`
  Deletes a trip. Requires authenticated admin session.

- `GET /api/reservations`
  Returns reservations for the authenticated session user.

- `POST /api/reservations`
  Creates a reservation for the authenticated session user.

- `PUT /api/reservations/{id}`
  Updates one of the authenticated user's reservations.

- `DELETE /api/reservations/{id}`
  Cancels one of the authenticated user's reservations.

## Example response

```json
{
  "data": [
    {
      "id": 1,
      "name": "Madrid to Valencia Weekend Ride",
      "origin": "Madrid",
      "destination": "Valencia"
    }
  ],
  "total": 3,
  "per_page": 50,
  "page": 1
}
```

## Example JSON payloads

```json
{
  "email": "user@travelbooking.local",
  "password": "User123!"
}
```

```json
{
  "name": "Bilbao to San Sebastian Ride",
  "description": "Afternoon shared ride with room for backpacks.",
  "origin": "Bilbao",
  "destination": "San Sebastian",
  "departure_at": "2026-05-20 17:00:00",
  "vehicle": "Ford Focus",
  "available_seats": 3,
  "image_path": "https://example.com/trip.jpg",
  "status": "published"
}
```

```json
{
  "trip_id": 1,
  "reservation_date": "2026-05-01 10:00:00",
  "seats_reserved": 1,
  "travel_role": "passenger",
  "notes": "Carry-on bag only"
}
```

## Authorization

Send the generated token in the header:

```text
Authorization: Bearer YOUR_TOKEN
```
