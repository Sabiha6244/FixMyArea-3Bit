
# FixMyArea: Community Issue Reporting Portal

## Team 3Bit Members
- **Sabiha Akter** (ID: 222014017)  
- **Moitree Mazumder** (ID: 222014011)  
- **Marjanul Jannat Prapti** (ID: 222014082)  

## Problem Statement
Citizens face difficulties reporting local issues like broken streetlights, potholes, or garbage disposal. Often, they don’t know where to report, and the lack of updates makes them feel ignored. Additionally, authorities struggle to assign service providers and track issue resolution efficiently.

This project aims to solve these problems by providing a centralized platform where:
-  Citizens can report issues and track resolutions.
-  Service providers can register, offer services, and get hired by authorities.
-  Government authorities can manage reports, assign tasks, and track service provider performance.

## Objectives
- Create a portal for citizens to report local problems and track resolutions.
- Enable service provider registration, allowing them to offer services.
- Allow government authorities to assign tasks to service providers.
- Use maps for issue geolocation and service provider tracking.
- Provide real-time notifications and internal messaging for status updates and communications.
- Implement secure OTP-based email verification for all users during registration.
- Allow users to edit, delete, or re-report their issues if needed.
- Enable service providers to view assigned tasks and the reporter’s full address.

## Proposed Solution
### Technology Stack
- **Frontend**: React (for dynamic UI and interactive dashboards)
- **Backend**: PHP (for authentication, service provider management, messaging)
- **Database**: MySQL (for storing users, reports, service providers, messages)
- **APIs**: Google Maps API (for issue location tracking)
- **Tools**: XAMPP (local development), Visual Studio Code

### Core Features
#### 1️⃣ Issue Reporting & Tracking
- Citizens can report community issues by filling out a form with details (description, category, photo).
- Issues are geotagged using an interactive map.
- Users can track issue progress (e.g., submitted, in progress, resolved).
- Citizens can Edit, Delete, or Report Again based on previous reports.

#### 2️⃣ Service Provider Registration & Management
- Service providers sign up, list services, and get verified by the government.
- Government authorities review and approve (or decline) service providers before assigning jobs.
- Providers receive job notifications and can accept assigned tasks.
- Service provider dashboard shows assigned issues, including full reporter addresses (Division, District, City Corporation, Upazila, Postcode).

#### 3️⃣ Admin Dashboard for Authorities
- Authorities can view, prioritize, and assign issues to relevant service providers.
- Track ongoing work, verify or decline service provider applications.
- Generate reports on issue resolution and service provider performance.

#### 4️⃣ Internal Messaging System
- Real-time messaging between admins and service providers linked to specific issues.
- Helps in fast communication regarding job assignments and updates.

#### 5️⃣ Notifications & Alerts
- Citizens receive real-time updates on reported issues.
- Service providers get alerts for new job assignments.
- Authorities receive performance reports and system updates.

#### 6️⃣ Profile Setup for Users
- After registration, users must complete a profile setup page selecting their Division, District, City Corporation, Upazila, and Postcode using cascading dropdowns.

#### 7️⃣ Email OTP Verification
- All users (citizens, service providers, admins) must verify their email using a one-time password (OTP) sent during the registration phase to ensure account authenticity.

### User Roles
- **Citizens**: Report issues, track progress, edit/delete/re-report issues, and communicate with authorities.
- **Service Providers**: Register, complete profile, get verified, accept jobs, communicate with authorities, and view assigned jobs with location details.
- **Government Authorities**: Manage reports, approve/decline service providers, assign tasks, track service performance, and oversee user messaging.

## Methodology
### Development Process
1. **Planning** – Gather user needs and define system requirements.
2. **Design** – Create UI/UX layouts for issue reporting, provider dashboards, admin controls, and messaging.
3. **Development**
   - **Frontend**: React, HTML, CSS, Bootstrap, JavaScript (with AJAX for dynamic interactions)
   - **Backend**: PHP (for authentication, issue management, service provider management, messaging)
   - **Database**: MySQL to store user data, issue reports, messages, and provider details.
4. **Testing** – Verify issue submission, provider registration, profile setup, email verification, and messaging functionality.
5. **Deployment** – Launch the platform on a secure web server.

### System Workflow
1. **Issue Reporting Flow:**
   - Citizens report an issue → Admin reviews → Assigns a provider → Provider fixes the issue → Citizen gets notified.
2. **Service Provider Flow:**
   - Service Providers sign up → Admin approves/declines → Providers complete profile setup → Assigned tasks → Task completion → Admin reviews.
3. **Messaging Flow:**
   - Admin and service provider exchange messages regarding tasks.

## Tools & Resources Needed
- **Development Tools**: Visual Studio Code, XAMPP
- **APIs**: Google Maps API
- **Hosting**: A secure web hosting service
- **Testing**: Cross-browser and mobile responsiveness testing

