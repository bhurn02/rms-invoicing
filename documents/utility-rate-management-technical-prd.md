# Utility Rate Management Enhancement - Technical PRD

## Overview

This document outlines the technical implementation of two major enhancements to the RMS (Real Estate Management System) to address manual utility rate management and meter reading processes for properties in the Commonwealth of the Northern Mariana Islands (CNMI). The system is built on PHP 7.2 with MSSQL 2019 database, running on Windows Server 2019 with IIS.

**Problem Statement**: Current manual processes for utility rate updates and meter readings are time-consuming, error-prone, and inefficient for managing 100+ tenants across CNMI properties.

**Target Users**: RMS administrators, property management staff, and field technicians operating in CNMI.

**Value Proposition**: Automate repetitive manual processes, reduce errors by 95%, and save 20+ hours per month in operational efficiency for CNMI property management.

## Core Features

### Feature 1: Automated Utility Rate Management

**What it does:**
- Single-point entry interface for Electric/CNMI and LEAC rates
- Automatic classification of units as residential/commercial
- Bulk update functionality for all active tenants
- Real-time rate application and validation

**Why it's important:**
- Eliminates 4-6 hours of manual data entry per month
- Reduces human error in rate calculations
- Ensures consistency across all tenant accounts
- Improves billing accuracy and compliance

**How it works:**
- New database column `is_residential` in `m_units` table (default: 1)
- Rate management interface with residential/commercial rate inputs
- Automated update process for charge codes CUCNF (Electric/CNMI) and CUCF (LEAC)
- Integration with existing tenant charge management system

### Feature 2: Mobile QR Code Meter Reading System

**What it does:**
- Web-based mobile application for meter reading
- QR code generation and scanning capability
- Real-time data entry and validation
- Integration with existing tenant reading system

**Why it's important:**
- Reduces meter reading time by 70%
- Eliminates transcription errors
- Provides real-time data access
- Modernizes field operations

**How it works:**
- QR codes contain property ID, unit number, and optional meter ID
- Mobile web interface with camera access for QR scanning
- Auto-populated forms based on scanned data
- Direct integration with `t_tenant_reading` table

## User Experience

### User Personas

**RMS Administrator**
- Primary user for utility rate management
- Needs efficient bulk update capabilities
- Requires audit trail and validation
- Values accuracy and compliance

**Field Technician**
- Primary user for meter reading system
- Needs mobile-friendly interface
- Requires offline capability for poor connectivity
- Values speed and ease of use

**Property Manager**
- Oversees both systems
- Needs reporting and monitoring capabilities
- Requires data accuracy and timeliness
- Values operational efficiency

### Key User Flows

**Utility Rate Management Flow:**
1. Admin logs into RMS system
2. Navigates to Utility Rate Management page
3. Enters new Electric/CNMI and LEAC rates for residential/commercial
4. System validates rates and shows preview of affected tenants
5. Admin confirms and submits
6. System automatically updates all active tenant charges
7. Confirmation and audit trail generated

**Meter Reading Flow:**
1. Technician opens mobile browser and navigates to RMS Meter Reading page
2. Technician logs in (if not already authenticated)
3. Technician clicks "Scan Meter" to activate camera
4. Technician scans QR code on meter
5. System parses QR data and auto-populates form
6. Technician enters current meter reading
7. System validates reading and saves to database
8. Reading appears in real-time on management dashboard

### UI/UX Considerations

**Utility Rate Management:**
- Clean, intuitive interface for rate entry
- Clear distinction between residential and commercial rates
- Preview functionality before applying changes
- Comprehensive audit trail and reporting

**Mobile Meter Reading:**
- Responsive design for various mobile devices
- Large, touch-friendly buttons and inputs
- Offline capability for poor connectivity areas
- Clear visual feedback for successful scans and submissions

## Technical Architecture

### System Components

**Backend Components:**
- PHP 7.2 application layer
- MSSQL 2019 database
- Existing RMS framework integration
- New utility rate management module
- Mobile meter reading API

**Frontend Components:**
- Existing RMS web interface (utility rate management)
- New mobile-responsive web application (meter reading)
- QR code generation and scanning library
- Real-time data synchronization

**Integration Points:**
- Existing tenant management system
- Current billing and invoicing system
- User authentication and authorization
- Audit logging system

### Data Models

**New Database Schema:**
```sql
-- Add is_residential column to m_units table
ALTER TABLE m_units ADD is_residential TINYINT DEFAULT 1;

-- New utility rate management table
CREATE TABLE m_utility_rates (
    rate_id INT IDENTITY(1,1) PRIMARY KEY,
    rate_type VARCHAR(10) NOT NULL, -- 'ELECTRIC' or 'LEAC'
    rate_category VARCHAR(10) NOT NULL, -- 'RESIDENTIAL' or 'COMMERCIAL'
    rate_amount DECIMAL(18,6) NOT NULL,
    effective_date DATE NOT NULL,
    created_by VARCHAR(50),
    created_date DATETIME DEFAULT GETDATE(),
    is_active TINYINT DEFAULT 1
);

-- QR code mapping table
CREATE TABLE m_meter_qr_codes (
    qr_id INT IDENTITY(1,1) PRIMARY KEY,
    real_property_code CHAR(5) NOT NULL,
    building_code CHAR(10) NOT NULL,
    unit_no CHAR(10) NOT NULL,
    meter_id VARCHAR(20),
    qr_code VARCHAR(255) NOT NULL,
    created_date DATETIME DEFAULT GETDATE(),
    is_active TINYINT DEFAULT 1
);
```

**Existing Tables Integration:**
- `m_units` - Unit information and residential classification
- `m_tenant` - Tenant information and active status
- `m_tenant_charges` - Tenant charge codes and amounts
- `t_tenant_reading` - Meter reading records
- `m_charges` - Charge code definitions

### APIs and Integrations

**Utility Rate Management API:**
- `POST /api/utility-rates/update` - Bulk update utility rates
- `GET /api/utility-rates/history` - Rate change history
- `GET /api/utility-rates/preview` - Preview affected tenants

**Meter Reading API:**
- `POST /api/meter-reading/scan` - Process QR code scan
- `POST /api/meter-reading/submit` - Submit meter reading
- `GET /api/meter-reading/list` - List recent readings
- `GET /api/meter-reading/qr-generate` - Generate QR codes

**Integration Requirements:**
- Existing RMS authentication system
- Current billing and invoicing system
- Audit logging and event tracking
- User permission and access control

### Infrastructure Requirements

**Server Requirements:**
- Windows Server 2019
- IIS 10.0+
- PHP 7.2+
- MSSQL 2019

**Mobile Requirements:**
- Responsive web design
- Camera access for QR scanning
- Offline capability for poor connectivity
- Cross-browser compatibility

**Security Requirements:**
- SSL/TLS encryption
- User authentication and authorization
- Data validation and sanitization
- Audit trail and logging

## Development Roadmap

### Phase 1: Utility Rate Management (MVP)

**MVP Requirements:**
- Database schema updates for `m_units` table
- Basic utility rate management interface
- Bulk update functionality for tenant charges
- Integration with existing charge management system
- Basic validation and error handling
- Audit trail for rate changes

**Core Functionality:**
- Add `is_residential` column to `m_units` table
- Create utility rate management page
- Implement rate entry and validation
- Develop bulk update process for tenant charges
- Integrate with existing RMS authentication
- Add basic reporting and audit trail

### Phase 2: Enhanced Utility Rate Management

**Future Enhancements:**
- Advanced rate scheduling and automation
- Rate change notifications and alerts
- Comprehensive reporting and analytics
- Rate history and trend analysis
- Integration with external rate sources
- Advanced validation and business rules

### Phase 3: Mobile QR Code Meter Reading (MVP)

**MVP Requirements:**
- QR code generation system
- Mobile-responsive web interface
- Camera integration for QR scanning
- Basic meter reading data entry
- Integration with existing tenant reading system
- Real-time data synchronization

**Core Functionality:**
- QR code generation for units and meters
- Mobile web application for meter reading
- Camera access and QR code scanning
- Form auto-population from scanned data
- Data validation and submission
- Integration with `t_tenant_reading` table

### Phase 4: Enhanced Meter Reading System

**Future Enhancements:**
- Offline capability for poor connectivity
- Advanced reporting and analytics
- Integration with IoT meters
- Automated reading validation
- Mobile app development
- Advanced user management and permissions

## Logical Dependency Chain

### Foundation Requirements (Phase 1)

**Database Schema Updates:**
1. Add `is_residential` column to `m_units` table
2. Create utility rate management tables
3. Update existing stored procedures
4. Create new stored procedures for bulk updates

**Core System Integration:**
1. Integrate with existing RMS authentication
2. Connect to existing tenant management system
3. Link with current charge management system
4. Implement audit logging and event tracking

### Usable Frontend Development (Phase 1)

**Utility Rate Management Interface:**
1. Create rate entry form with validation
2. Develop preview functionality for affected tenants
3. Implement bulk update process
4. Add confirmation and success messaging
5. Create basic reporting and audit trail

### Atomic Feature Development

**Phase 1 Features (Utility Rate Management):**
- Database schema updates (Week 1)
- Rate management interface (Week 2)
- Bulk update functionality (Week 2-3)
- Integration and testing (Week 3)

**Phase 2 Features (Meter Reading):**
- QR code generation system (Week 4-5)
- Mobile web interface (Week 5-6)
- Camera integration and scanning (Week 6-7)
- Data integration and testing (Week 7-8)

### Iterative Improvement

**Continuous Enhancement:**
- User feedback integration
- Performance optimization
- Security hardening
- Feature refinement based on usage patterns

## Risks and Mitigations

### Technical Challenges

**Database Schema Changes:**
- **Risk**: Impact on existing data and applications
- **Mitigation**: Comprehensive testing, backup procedures, gradual rollout

**Mobile Compatibility:**
- **Risk**: Device and browser compatibility issues
- **Mitigation**: Responsive design, cross-browser testing, progressive enhancement

**Integration Complexity:**
- **Risk**: Disruption to existing RMS functionality
- **Mitigation**: Modular development, comprehensive testing, rollback procedures

### MVP Development Strategy

**Minimal Viable Product Approach:**
- Focus on core functionality first
- Build upon existing system architecture
- Ensure backward compatibility
- Implement gradual feature rollout

**Risk Mitigation:**
- Comprehensive testing at each phase
- User acceptance testing before deployment
- Rollback procedures for each feature
- Documentation and training materials

### Resource Constraints

**Development Resources:**
- **Risk**: Limited development time and resources
- **Mitigation**: Phased approach, clear priorities, efficient development practices

**User Training:**
- **Risk**: Resistance to new systems and processes
- **Mitigation**: Comprehensive training, user-friendly interfaces, gradual adoption

**System Performance:**
- **Risk**: Impact on existing system performance
- **Mitigation**: Performance testing, optimization, monitoring

## Appendix

### Research Findings

**Current System Analysis:**
- RMS built on PHP 5.3 with PHP 7.2 enhancements in utilities folder
- MSSQL 2019 database with comprehensive schema
- Existing tenant reading system in `tenant_reading.php`
- Charge management through `m_tenant_charges` and `m_unit_charges` tables

**Technical Requirements:**
- Windows Server 2019 environment
- IIS web server configuration
- PHP 7.2 compatibility
- MSSQL 2019 database integration

**User Requirements:**
- Efficient bulk operations for utility rates
- Mobile-friendly meter reading interface
- Real-time data access and validation
- Comprehensive audit trail and reporting

### Technical Specifications

**System Architecture:**
- Web-based application with mobile responsiveness
- Database-driven with stored procedures
- RESTful API for mobile integration
- Event-driven audit logging

**Security Requirements:**
- User authentication and authorization
- Data encryption and validation
- Audit trail and logging
- Access control and permissions

**Performance Requirements:**
- Sub-second response times for rate updates
- Real-time data synchronization
- Mobile-optimized interface
- Scalable architecture for future growth

---

*Technical PRD - Utility Rate Management Enhancement*  
*Version: 1.0*  
*Date: January 2025*  
*System: RMS (Real Estate Management System)*  
*Location: Commonwealth of the Northern Mariana Islands (CNMI)* 