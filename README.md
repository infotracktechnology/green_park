# Green Park Educational Management System - Qwen Coder CLI Documentation

## System Overview

This is a Laravel 9.x based educational management platform that handles student lifecycle, examinations, academic resources, and institutional operations. The system integrates Firebase for real-time features and supports PDF generation for reports and documents.

## Codebase Architecture

### Framework Stack
- **Backend**: Laravel 9.x with PHP 8.0+
- **Frontend**: Blade templating with Bootstrap UI
- **Database**: MySQL with Eloquent ORM
- **Real-time**: Firebase Integration
- **PDF Generation**: DomPDF
- **Authentication**: Multi-auth system (admin and student guards)

### Directory Structure
```
├── app/
│   ├── Http/Controllers/           # MVC Controllers
│   │   ├── Auth/                   # Authentication controllers
│   │   ├── HomeController.php      # Dashboard and admin functions
│   │   ├── StudentController.php   # Student management
│   │   ├── ExamController.php      # Examination system
│   │   ├── FinanceController.php   # Financial operations
│   │   ├── HostelController.php    # Hostel management
│   │   ├── ExportController.php    # Data export functionality
│   │   ├── ImportController.php    # Data import functionality
│   │   └── ReportController.php    # Report generation
│   ├── Models/                     # Eloquent Models
│   │   ├── Student.php             # Student entity with SoftDeletes
│   │   ├── Exam.php                # Examination entity
│   │   ├── Attendance.php          # Attendance tracking
│   │   ├── Branch.php              # Branch/Location entity
│   │   ├── Announcement.php        # Announcement system
│   │   └── Hostel.php              # Hostel management
│   └── Providers/                  # Service Providers
│       ├── FcmServiceProvider.php  # Firebase Cloud Messaging
│       └── CsvServiceProvider.php  # CSV processing services (if exists)
├── config/                         # Configuration files
│   ├── firebase.php                # Firebase service configuration
│   └── filesystems.php             # File storage configuration
├── routes/
│   ├── web.php                     # Web routes with middleware
│   ├── api.php                     # API endpoints (v2)
│   └── channels.php                # Broadcasting channels
├── resources/views/                # Blade templates
│   ├── layouts/                    # Main layout templates
│   ├── student/                    # Student-specific views
│   ├── exam/                       # Examination views
│   ├── finance/                    # Financial management views
│   ├── hostel/                     # Hostel management views
│   ├── dashboards/                 # Dashboard views
│   └── pdf/                        # PDF generation templates
├── public/                         # Public assets
│   ├── bundles/                    # JS/CSS libraries (DataTables, etc.)
│   ├── css/                        # Stylesheets
│   ├── js/                         # JavaScript files
│   └── uploads/                    # File uploads
└── storage/                        # File uploads and cache
```

### Core Features
1. **Student Management**: Complete lifecycle including registration, profiles, sections, and academic tracking
2. **Examination System**: Online/offline tests, answer keys, marking, and result analysis
3. **Attendance Tracking**: Both regular and hostel attendance with detailed reporting
4. **Hostel Management**: Room allocation, attendance, sick room entries, and student activities
5. **Finance System**: Fee plans, collection, and tracking
6. **Communication**: Announcements, videos (chairman, class, discussion), and parent concerns
7. **Academic Resources**: Timetables, question papers, answer keys, worksheets, and study materials
8. **Reporting**: Comprehensive reports with analytics and data visualization
9. **API Integration**: v2 API for mobile applications and external integrations
10. **Multi-auth System**: Separate authentication for admin and student users

### Authentication & Middleware
- Admin users (web guard): Full system access
- Student users (student guard): Limited to student-specific functions
- Academic year filtering across the application
- Branch-based access control

### Database Design
- Uses both Eloquent ORM and direct DB queries where needed
- Soft deletes implemented on key models
- JSON fields for flexible data storage (e.g., student menu configuration)
- Foreign key relationships between entities (Student → Branch, etc.)

### API Structure
- v2 API endpoints in routes/api.php
- Student profile, attendance, examination results
- Resource management (videos, documents, announcements)
- Mobile application support with token authentication

### Key Controllers
- HomeController: Main dashboard and administrative functions
- StudentController: Student lifecycle management
- ExamController: Full examination workflow
- FinanceController: Fee management
- ReportController: Analytics and reporting

### Frontend Components
- Bootstrap 4 UI framework
- DataTables for data presentation and sorting
- Select2 for enhanced select inputs
- Summernote for rich text editing
- Custom JavaScript for dynamic form interactions
- PDF generation for reports using DomPDF

### Coding Patterns
- Resource controllers with RESTful routing
- Custom query methods in models (e.g., StudentFilterQuery)
- Reusable components in blade templates
- JavaScript functions for form validation and dynamic updates
- Centralized navigation with sidebar menu

### Security
- Laravel's built-in authentication
- Form request validation
- CSRF protection
- Input sanitization
- Secure file uploads