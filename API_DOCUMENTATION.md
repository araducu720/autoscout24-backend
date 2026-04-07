# AutoScout24 Clone - API Documentation

Base URL: `http://localhost:8000/api/v1`

## Authentication

The API uses Laravel Sanctum for authentication. Protected endpoints require a Bearer token in the Authorization header.

### Register
```http
POST /register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1234567890" // optional
}

Response:
{
  "message": "User registered successfully",
  "user": { ... },
  "token": "1|..."
}
```

### Login
```http
POST /login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "message": "Login successful",
  "user": { ... },
  "token": "2|..."
}
```

### Logout
```http
POST /logout
Authorization: Bearer {token}

Response:
{
  "message": "Logged out successfully"
}
```

### Get Current User
```http
GET /user
Authorization: Bearer {token}

Response:
{
  "user": { ... }
}
```

---

## Vehicles

### List Vehicles
```http
GET /vehicles

Query Parameters:
- page: int (pagination, default: 1)
- per_page: int (default: 15, max: 100)
- sort: string (price, year, mileage, created_at, default: created_at)
- order: string (asc, desc, default: desc)
- search: string (search in title and description)
- make_id: int
- model_id: int
- price_min: decimal
- price_max: decimal
- year_min: int
- year_max: int
- mileage_max: int
- fuel_type: enum (petrol, diesel, electric, hybrid, lpg)
- transmission: enum (manual, automatic)
- body_type: enum (sedan, suv, coupe, hatchback, convertible, wagon, van, truck)
- condition: enum (new, used)
- country: string
- is_featured: boolean (0 or 1)

Example:
GET /vehicles?make_id=1&price_min=20000&price_max=30000&fuel_type=diesel&sort=price&order=asc

Response:
{
  "data": [
    {
      "id": 1,
      "make": { "id": 1, "name": "Audi", "slug": "audi", "logo": null },
      "model": { "id": 2, "name": "A4", "slug": "a4" },
      "title": "Audi A4 2.0 TDI - Excellent Condition",
      "description": "...",
      "price": "25900.00",
      "year": 2020,
      "mileage": 45000,
      "fuel_type": "diesel",
      "transmission": "automatic",
      "body_type": "sedan",
      "color": "Black",
      "doors": 4,
      "seats": 5,
      "engine_size": 2000,
      "power": 190,
      "country": "Germany",
      "city": "Berlin",
      "condition": "used",
      "status": "active",
      "views_count": 5,
      "is_featured": true,
      "images": [
        { "id": 1, "path": "placeholder/car-1.jpg", "is_primary": true, "order": 1 }
      ],
      "primary_image": "placeholder/car-1.jpg",
      "created_at": "2026-01-31T15:44:35.000000Z",
      "updated_at": "2026-01-31T15:44:35.000000Z"
    },
    ...
  ],
  "links": { ... },
  "meta": { ... }
}
```

### Get Single Vehicle
```http
GET /vehicles/{id}

Response:
{
  "data": {
    "id": 1,
    "make": { ... },
    "model": { ... },
    ...
  }
}

Note: This endpoint increments the views_count for the vehicle.
```

---

## Vehicle Makes

### List All Makes
```http
GET /makes

Response:
{
  "data": [
    {
      "id": 1,
      "name": "Audi",
      "slug": "audi",
      "logo": null,
      "type": "car",
      "models_count": 7
    },
    ...
  ]
}
```

### Get Single Make
```http
GET /makes/{id}

Response:
{
  "data": {
    "id": 1,
    "name": "Audi",
    "slug": "audi",
    "logo": null,
    "type": "car",
    "models_count": 7
  }
}
```

---

## Vehicle Models

### List All Models
```http
GET /models

Query Parameters:
- make_id: int (filter by make)

Example:
GET /models?make_id=1

Response:
{
  "data": [
    {
      "id": 1,
      "make_id": 1,
      "name": "A3",
      "slug": "a3"
    },
    {
      "id": 2,
      "make_id": 1,
      "name": "A4",
      "slug": "a4"
    },
    ...
  ]
}
```

### Get Single Model
```http
GET /models/{id}

Response:
{
  "data": {
    "id": 1,
    "make_id": 1,
    "name": "A3",
    "slug": "a3"
  }
}
```

---

## Favorites (Protected)

All favorites endpoints require authentication.

### List User Favorites
```http
GET /favorites
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "make": { ... },
      "model": { ... },
      "title": "...",
      ...
    },
    ...
  ]
}
```

### Add to Favorites
```http
POST /favorites
Authorization: Bearer {token}
Content-Type: application/json

{
  "vehicle_id": 1
}

Response:
{
  "message": "Vehicle added to favorites",
  "favorite": {
    "id": 1,
    "user_id": 1,
    "vehicle_id": 1,
    "created_at": "...",
    "updated_at": "..."
  }
}

Error (if already exists):
{
  "message": "Vehicle already in favorites"
}
```

### Remove from Favorites
```http
DELETE /favorites/{vehicleId}
Authorization: Bearer {token}

Response:
{
  "message": "Vehicle removed from favorites"
}

Error (if not found):
{
  "message": "Favorite not found"
}
```

---

## Contact Messages

### Send Contact Message
```http
POST /contact-messages
Content-Type: application/json

{
  "vehicle_id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "message": "Hi, I am interested in this vehicle. Is it still available?"
}

Response:
{
  "message": "Message sent successfully",
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "message": "...",
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

## Rate Limiting

The API is rate-limited to **60 requests per minute** per IP address (or per user for authenticated requests).

If you exceed the rate limit, you'll receive a `429 Too Many Requests` response with headers:
- `X-RateLimit-Limit`: 60
- `X-RateLimit-Remaining`: 0
- `Retry-After`: seconds until reset

---

## Error Responses

All errors follow this format:

```json
{
  "message": "Error message here",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

Common HTTP Status Codes:
- `200` OK - Request successful
- `201` Created - Resource created successfully
- `400` Bad Request - Invalid request data
- `401` Unauthorized - Authentication required or failed
- `403` Forbidden - Authenticated but not authorized
- `404` Not Found - Resource not found
- `422` Unprocessable Entity - Validation failed
- `429` Too Many Requests - Rate limit exceeded
- `500` Internal Server Error - Server error

---

## CORS Configuration

CORS is enabled for the following origins:
- `http://localhost:3000`
- `http://localhost:3001`
- `http://localhost:3002`
- `http://localhost:3003`
- `http://localhost:3004`

Credentials (cookies/auth headers) are supported.

---

## Testing the API

### Using cURL

```bash
# Register
curl -X POST http://localhost:8000/api/v1/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Test User","email":"test@test.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'

# List vehicles
curl http://localhost:8000/api/v1/vehicles \
  -H "Accept: application/json"

# List vehicles with filters
curl "http://localhost:8000/api/v1/vehicles?make_id=1&price_min=20000&price_max=30000" \
  -H "Accept: application/json"

# Add to favorites (replace TOKEN)
curl -X POST http://localhost:8000/api/v1/favorites \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"vehicle_id":1}'
```

### Using JavaScript (Fetch)

```javascript
// Register
const register = async () => {
  const response = await fetch('http://localhost:8000/api/v1/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({
      name: 'Test User',
      email: 'test@test.com',
      password: 'password123',
      password_confirmation: 'password123'
    })
  });
  return await response.json();
};

// List vehicles
const getVehicles = async () => {
  const response = await fetch('http://localhost:8000/api/v1/vehicles?make_id=1', {
    headers: {
      'Accept': 'application/json',
    }
  });
  return await response.json();
};

// Add to favorites
const addToFavorites = async (vehicleId, token) => {
  const response = await fetch('http://localhost:8000/api/v1/favorites', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify({ vehicle_id: vehicleId })
  });
  return await response.json();
};
```

---

## Admin Panel

Access the Filament admin panel at: `http://localhost:8000/admin`

Default credentials:
- Email: `admin@autoscout24.local`
- Password: `password123`

Admin features:
- Manage vehicles (CRUD operations)
- Manage vehicle makes and models
- Manage users
- View contact messages
- Dashboard with statistics

---

## Database Seeded Data

The database comes pre-seeded with:
- **26 vehicle makes** (18 cars + 8 motorcycles)
- **38 vehicle models** (Audi, BMW, Mercedes, Toyota, VW, Honda)
- **2 demo vehicles** (Audi A4, BMW 320d)

You can add more data through the admin panel.
