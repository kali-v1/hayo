# Certification Courses Platform

A full-featured web platform dedicated to certification courses and simulated exams (e.g., CCNA, CCNP, Security+).

## Project Overview

This platform consists of two main components:

1. **Main Website (User Panel)**
   - Neo Brutalism design style
   - Fully supports Arabic (RTL) and English (LTR) languages
   - Course browsing and enrollment
   - Practice exams with various question types
   - User profiles and leaderboards

2. **Admin Panel**
   - Modern professional dashboard UI
   - Arabic-only interface with RTL support
   - Role-based access system
   - Course, exam, and question management
   - User management

## Features

### Main Website (User Panel)
- Neo Brutalism design style
- Fully supports Arabic (RTL) and English (LTR) languages
- User authentication (login/register)
- Course pages with detailed information
- Practice exams with various question types:
  - Single-choice questions
  - Multiple-choice questions
  - Drag-and-drop questions
- Free and paid content
- User profile page
- Public leaderboard
- Course ratings and reviews

### Admin Panel
- Modern professional dashboard UI
- Arabic language only (RTL)
- Role-based access system:
  - Admin (مدير): Full access to system management
  - Data Entry (مدخل بيانات): Can only manage exam questions
  - Instructor (مدرب): Can only manage their assigned courses
- Dashboard overview
- Course management
- Question bank management
- User and role management
- Basic analytics/statistics

## Technical Stack

- **Backend**: PHP with Object-Oriented Programming (OOP)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **CSS Framework**: Bootstrap 5 with Neo Brutalism styling for the main site
- **JavaScript Libraries**: Bootstrap JS

## Project Structure

```
/
├── admin/                  # Admin panel files
│   ├── assets/             # Admin-specific assets
│   ├── classes/            # Admin-specific classes
│   ├── controllers/        # Admin controllers
│   ├── includes/           # Admin helper functions
│   ├── templates/          # Admin templates
│   ├── .htaccess           # Admin routing rules
│   └── index.php           # Admin entry point
├── assets/                 # Main site assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   └── img/                # Images
├── classes/                # Core classes
├── config/                 # Configuration files
├── controllers/            # Main site controllers
├── database/               # Database schema and migrations
├── includes/               # Helper functions
├── languages/              # Language files
├── templates/              # Main site templates
├── .htaccess               # Main routing rules
├── index.php               # Main entry point
└── README.md               # Project documentation
```

## Installation

1. Clone the repository
2. Import the database schema from `/database/schema.sql`
3. Configure the database connection in `/config/database.php`
4. Set up a web server (Apache/Nginx) with PHP support
5. Ensure the web server is configured to serve the application from the root directory

## Development

The project follows an MVC-like architecture:

- **Models**: Classes in the `classes` directory
- **Views**: Templates in the `templates` directory
- **Controllers**: Controllers in the `controllers` directory

## Default Admin Credentials

- Username: admin
- Password: admin123

## Default User Credentials

- Username: johndoe
- Password: user123

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

- Neo Brutalism CSS: [Brutopia](https://cdn.jsdelivr.net/gh/rajnandan1/brutopia@latest/dist/assets/compiled/css/app.css)
- Bootstrap: [Bootstrap 5](https://getbootstrap.com/)
- Font Awesome: [Font Awesome 6](https://fontawesome.com/)