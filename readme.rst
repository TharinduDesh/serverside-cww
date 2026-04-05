# Alumni Influencer Platform

Alumni Influencer Platform is a CodeIgniter-based web application developed for **6COSC022C.2 Advanced Server-Side Web Programming** coursework.

The system allows alumni to register, verify their email, build a professional profile, participate in blind bidding for the daily featured slot, and exposes a developer-facing API for retrieving the featured alumnus of the day.

## Features

- University email registration and login
- Email verification and password reset
- Alumni profile management
- Blind bidding system
- Developer API key generation and revocation
- Bearer-token protected API
- Swagger / OpenAPI documentation
- API usage logging

## Tech Stack

- PHP
- CodeIgniter 3
- MySQL
- XAMPP
- Gmail SMTP
- Swagger UI / OpenAPI

## Main Routes

### Web
- `/register`
- `/login`
- `/logout`
- `/dashboard`
- `/profile`
- `/bidding`
- `/developer`

### API
- `GET /api/featured-today` — protected endpoint
- `GET /api/featured-today-public` — optional public demo endpoint

### Docs
- `/api-docs`
- `/api-spec.json`

## Setup

1. Clone the repository
2. Place the project inside your XAMPP `htdocs` folder
3. Create a MySQL database
4. Import the provided SQL schema
5. Configure environment variables / local config for:
   - database connection
   - SMTP settings
6. Start Apache and MySQL in XAMPP
7. Open the application in the browser

