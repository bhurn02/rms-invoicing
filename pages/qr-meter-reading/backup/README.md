# RMS QR Meter Reading System

## 🚀 Executive Professional QR Code Meter Reading System

A modern, sophisticated Progressive Web App designed for non-technical field staff to scan QR codes and input meter readings with zero training required.

## ✨ Features

- **🎯 QR Code Scanning**: Camera-based QR code scanning with instant property/unit identification
- **📱 Mobile-First Design**: Responsive Bootstrap 5 interface optimized for mobile devices
- **🔄 Offline Capability**: Works offline with automatic data synchronization when online
- **🎨 Executive Professional UI**: Modern, sophisticated design that builds user confidence
- **⚡ Progressive Web App**: Installable app-like experience with service worker
- **🔒 Secure Authentication**: Integrated with existing RMS authentication system
- **📊 Real-time Data**: Live updates and recent readings display

## 🔐 Authentication System

### Overview
The QR Meter Reading System integrates with the existing RMS authentication system, requiring users to log in before accessing the scanning functionality.

### Authentication Features
- **Session Management**: Secure PHP sessions with 8-hour timeout
- **RMS Integration**: Uses existing `sp_s_Login` stored procedure
- **User Tracking**: All readings are associated with authenticated users
- **Automatic Logout**: Session expiration and manual logout functionality
- **Security Headers**: XSS protection and secure cookie settings

### Login Process
1. **Access System**: Navigate to `qr-meter-reading/`
2. **Redirect to Login**: Automatically redirected to `auth/login.php` if not authenticated
3. **Enter Credentials**: Username, password, and company code
4. **Authentication**: Validated against existing RMS user database
5. **Session Creation**: Secure session established with user information
6. **Access Granted**: Redirected to main scanning interface

### User Interface
- **Header Display**: Shows current user and company information
- **Logout Button**: Prominent logout option in navigation
- **Session Status**: Automatic session validation on each request
- **User Feedback**: Clear indication of logged-in user throughout interface

## 🏗️ Architecture

### Technology Stack
- **Frontend**: Bootstrap 5.3+ with Executive Professional styling
- **QR Scanning**: html5-qrcode.min.js library
- **Backend**: PHP 7.2 with MSSQL 2019
- **Database**: Existing RMS `t_tenant_reading` table
- **Authentication**: Integrated with RMS authentication system
- **PWA**: Service Worker for offline functionality
- **Deployment**: IIS (Windows Server 2019)

### System Components
```
qr-meter-reading/
├── assets/
│   ├── css/
│   │   ├── custom-theme.css          # Executive Professional styling
│   │   └── qr-scanner.css            # QR scanner specific styles
│   ├── js/
│   │   └── app.js                    # Main application logic
│   └── images/                       # Icons and UI graphics
├── auth/
│   ├── auth.php                      # Authentication middleware
│   ├── login.php                     # Login page
│   ├── logout.php                    # Logout handler
│   └── check.php                     # Authentication status check
├── api/
│   ├── save-reading.php              # Save meter readings (authenticated)
│   └── get-recent-readings.php       # Retrieve recent readings (authenticated)
├── config/
│   └── config.php                    # Database configuration
├── index.php                         # Main QR scanner interface (authenticated)
├── manifest.json                     # PWA manifest
├── service-worker.js                 # Offline functionality
└── README.md                         # This file
```

## 🚀 Deployment Options

### Option 1: Standalone IIS Application

1. **Copy Files to IIS**
   ```bash
   # Copy qr-meter-reading folder to IIS web root
   xcopy qr-meter-reading C:\inetpub\wwwroot\qr-meter-reading\ /E /I
   ```

2. **Create IIS Application**
   - Open IIS Manager
   - Right-click on "Sites" → "Add Application"
   - Application Alias: `qr-meter-reading`
   - Physical Path: `C:\inetpub\wwwroot\qr-meter-reading\`
   - Application Pool: Use existing RMS pool or create dedicated

3. **Access URL**
   ```
   http://your-server/qr-meter-reading/
   ```

### Option 2: Integrated with RMS

1. **Copy to RMS Directory**
   ```bash
   # Copy qr-meter-reading folder to RMS directory
   xcopy qr-meter-reading C:\inetpub\wwwroot\rms\qr-meter-reading\ /E /I
   ```

2. **Access URL**
   ```
   http://your-server/rms/qr-meter-reading/
   ```

## ⚙️ Configuration

### Database Configuration

1. **Edit Database Settings**
   ```php
   // File: config/config.php
   $db_server = 'your-mssql-server';     // MSSQL server address
   $db_name = 'RMS';                     // Database name
   $db_user = 'your-db-user';            // Database username
   $db_password = 'your-db-password';    // Database password
   ```

2. **Test Database Connection**
   ```php
   // Test connection by visiting:
   http://your-server/qr-meter-reading/config/test-connection.php
   ```

### Authentication Configuration

1. **Session Settings** (already configured in `auth/auth.php`)
   ```php
   // Session lifetime: 8 hours
   $session_lifetime = 8 * 60 * 60;
   
   // Secure cookie settings
   ini_set('session.cookie_httponly', 1);
   ini_set('session.use_only_cookies', 1);
   ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
   ```

2. **User Permissions**
   - Users must have access to the existing RMS system
   - Authentication uses the same `sp_s_Login` stored procedure
   - Company codes must match existing RMS company configuration

## 🔧 Installation Steps

### Prerequisites
- Windows Server 2019 with IIS
- PHP 7.2+ with MSSQL drivers
- MSSQL 2019 database
- Existing RMS system with user authentication

### Step 1: File Deployment
1. Copy the `qr-meter-reading` folder to your web server
2. Ensure proper file permissions (read/execute for web server)
3. Verify all files are accessible via web browser

### Step 2: Database Configuration
1. Update `config/config.php` with your database credentials
2. Test database connection
3. Verify access to existing RMS tables

### Step 3: Authentication Setup
1. Ensure existing RMS authentication system is working
2. Test login with existing RMS credentials
3. Verify company codes are available

### Step 4: Testing
1. Access the system via web browser
2. Test login functionality
3. Verify QR code scanning works
4. Test offline functionality

## 📱 User Guide

### First-Time Setup
1. **Access System**: Navigate to the QR Meter Reading System URL
2. **Login**: Enter your existing RMS username, password, and company code
3. **Grant Camera Permissions**: Allow camera access when prompted
4. **Start Scanning**: Click "Start Scanner" to begin QR code scanning

### Daily Usage
1. **Login**: Authenticate with your RMS credentials
2. **Scan QR Code**: Point camera at meter QR code
3. **Verify Data**: Check that property/unit information is correct
4. **Enter Reading**: Input current meter reading
5. **Submit**: Save reading to database
6. **Continue**: Scan next meter or logout

### Offline Mode
- **Automatic**: System works offline automatically
- **Data Storage**: Readings saved locally when offline
- **Sync**: Data automatically syncs when connection restored
- **Status**: Offline status displayed in interface

## 🔒 Security Features

### Authentication Security
- **Session Management**: Secure PHP sessions with timeout
- **Password Protection**: Uses existing RMS password system
- **HTTPS Support**: Secure cookie settings for HTTPS
- **Session Validation**: Automatic session checks on all requests

### Data Security
- **Input Validation**: All user input sanitized and validated
- **SQL Injection Protection**: Prepared statements used throughout
- **XSS Protection**: Output encoding and security headers
- **Access Control**: Authentication required for all API endpoints

### Privacy Protection
- **User Tracking**: All actions logged with user identification
- **Data Encryption**: Sensitive data handled securely
- **Audit Trail**: Complete audit trail of all readings
- **Session Cleanup**: Automatic session cleanup on logout

## 🐛 Troubleshooting

### Common Issues

#### Authentication Problems
- **Issue**: Cannot login
  - **Solution**: Verify RMS credentials and company code
  - **Check**: Ensure `sp_s_Login` stored procedure exists and works

- **Issue**: Session expires quickly
  - **Solution**: Check session configuration in `auth/auth.php`
  - **Check**: Verify server time settings

#### Camera Issues
- **Issue**: Camera not working
  - **Solution**: Ensure HTTPS or localhost for camera access
  - **Check**: Grant camera permissions in browser

- **Issue**: QR codes not scanning
  - **Solution**: Check QR code format and quality
  - **Check**: Ensure good lighting and camera focus

#### Database Issues
- **Issue**: Cannot save readings
  - **Solution**: Check database connection and permissions
  - **Check**: Verify `t_tenant_reading` table structure

### Error Logs
- **PHP Errors**: Check web server error logs
- **Database Errors**: Check database error logs
- **Application Logs**: Check `logs/activity.log` for user actions

## 🔄 Updates and Maintenance

### Regular Maintenance
- **Session Cleanup**: Automatic session cleanup (handled by PHP)
- **Log Rotation**: Monitor and rotate log files
- **Database Backup**: Regular database backups
- **Security Updates**: Keep PHP and dependencies updated

### System Updates
- **Backup**: Always backup before updates
- **Test**: Test updates in development environment
- **Deploy**: Deploy during maintenance windows
- **Verify**: Verify functionality after updates

## 📞 Support

### Technical Support
- **Documentation**: This README and inline code comments
- **Logs**: Check application and server logs
- **Database**: Verify database connectivity and permissions
- **Authentication**: Test with existing RMS credentials

### Contact Information
- **System Administrator**: Contact your RMS system administrator
- **Database Issues**: Contact database administrator
- **Network Issues**: Contact network administrator

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: RMS System, PHP 7.2+, MSSQL 2019, IIS
