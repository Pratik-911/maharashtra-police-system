# Database Setup Instructions

## Database Import

1. **Create Database:**
   ```sql
   CREATE DATABASE police_duty_management;
   USE police_duty_management;
   ```

2. **Import Database:**
   ```bash
   mysql -u root -p police_duty_management < database_export.sql
   ```

## Database Configuration

Update your `.env` file with database credentials:

```env
database.default.hostname = localhost
database.default.database = police_duty_management
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi
```

## Test Credentials

### Admin Login
- URL: `/admin/login`
- Username: `admin`
- Password: `admin123`

### Station Login
- URL: `/station/login`
- Station ID: `PS001`, `PS002`, or `PS003`
- Password: `password`

## Database Schema

The database includes the following main tables:
- `officers` - Police officer information
- `points` - Duty points/locations
- `duties` - Duty assignments
- `duty_officers` - Officer-duty relationships
- `location_logs` - GPS tracking data
- `compliance` - Compliance calculations
- `admin_users` - Admin authentication
- `police_stations` - Station information

## Important Notes

- The database export includes sample data for testing
- All passwords are hashed using PHP's password_hash()
- Location tracking requires HTTPS in production
- Google Maps API key needed for map features
