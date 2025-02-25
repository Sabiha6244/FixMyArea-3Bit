# FixMyArea: Community Issue Reporting & Paid Service Portal

## Team 3Bit Members
- **Sabiha Akter** (ID: 222014017)  
- **Moitree Mazumder** (ID: 222014011)  
- **Marjanul Jannat Prapti** (ID: 222014082)  

## Problem Statement
Citizens face difficulties reporting local issues like broken streetlights, potholes, or garbage disposal. Often, they don’t know where to report, and the lack of updates makes them feel ignored. Additionally, authorities struggle to assign service providers and track issue resolution efficiently.

This project aims to solve these problems by providing a centralized platform where:
-  Citizens can report issues and track resolutions.
-  Service providers can register, offer services, and get hired by authorities.
-  Government authorities can manage reports, assign tasks, and track payments for services.

## Objectives
- Create a portal for citizens to report local problems and track resolutions.
- Enable service provider registration, allowing them to offer paid services.
- Allow government authorities to assign tasks to service providers.
- Integrate a secure payment system for paid services.
- Use maps for issue geolocation and service provider tracking.
- Provide real-time notifications for status updates.

## Proposed Solution
### Technology Stack
- **Frontend**: React (for dynamic UI and interactive dashboards)
- **Backend**: PHP (for authentication, service provider management, payments)
- **Database**: MySQL (for storing users, reports, service providers, transactions)
- **APIs**: Google Maps API (for issue location tracking)
- **Tools**: XAMPP (local development), Visual Studio Code

### Core Features
#### 1️⃣ Issue Reporting & Tracking
- Citizens can report community issues by filling out a form with details (description, category, photo).
- Issues are geotagged using an interactive map.
- Users can track issue progress (e.g., submitted, in progress, resolved).

#### 2️⃣ Service Provider Registration & Hiring
- Service providers sign up, list services, and get verified by the government.
- Government authorities review and approve service providers before assigning jobs.
- Providers receive job notifications and can accept/reject tasks.

#### 3️⃣ Admin Dashboard for Authorities
- Authorities can view, prioritize, and assign issues to relevant service providers.
- Track ongoing work, budget allocations, and payments.
- Generate reports on issue resolution and service provider performance.

#### 4️⃣ Notifications & Alerts
- Citizens receive real-time updates on reported issues.
- Service providers get alerts for new job assignments and payments.
- Authorities receive performance reports and system updates.

### User Roles
- **Citizens**: Report issues, track progress, and hire providers for personal needs.
- **Service Providers**: Register, list services, accept jobs, and receive payments.
- **Government Authorities**: Manage reports, approve service providers, assign tasks, and oversee payments.

## Methodology
### Development Process
1. **Planning** – Gather user needs and define system requirements.
2. **Design** – Create UI/UX layouts for issue reporting, provider dashboards, and admin controls.
3. **Development**
   - **Frontend**: React components for interactive dashboards and forms,html,CSS,JS.
   - **Backend**: PHP for managing user authentication, job assignments, and payments.
   - **Database**: MySQL to store user data, issue reports, payments, and provider details.
4. **Testing** – Verify issue submission, provider hiring, and payment functionality.
5. **Deployment** – Launch the platform on a secure web server.

### System Workflow
1. **Issue Reporting Flow:**
   - Citizens report an issue → Admin reviews → Assigns a provider → Provider fixes the issue → Citizen gets notified.
2. **Service Provider Flow:**
   - Service Providers sign up → Admin approves → Providers list services → They get hired → They complete tasks → They get paid.

## Tools & Resources Needed
- **Development Tools**: Visual Studio Code, XAMPP
- **APIs**: Google Maps API,Bkash 
- **Hosting**: A secure web hosting service
- **Testing**: Cross-browser and mobile responsiveness testing




