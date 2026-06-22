# FULLY REVISED DOCUMENTATION
## Based on the Deployed ICCT MIS Website
### https://icct-mis.classapparelph.com

---

## Title

**Web-Based Resource Borrowing and Returning System with Availability Tracking and Faculty Authentication for MIS Faculty at ICCT Cainta Campus**

**Authors:** Angelica Padil, Bryan Soliven, Richard Erenea, Ronjay Pulido, John Harold Uvero, Mikaela Montealto, Melody Rances, Arlyn Cenidoza, Grace Joy Flores

**Department of Computer Studies, Institute of Creative Computer Technology Colleges**
V.V. Soliven Avenue II, Brgy. San Isidro, Cainta, Rizal 1900, Philippines

**Date of Revision:** May 23, 2026

---

## Abstract

*The borrowing and returning of resources at ICCT Cainta Campus currently relies on manual request letters or faculty ID presentation, which is inefficient, time-consuming, and lacks transparency in tracking availability. Professors often experience delays in borrowing, while MIS faculty struggle with monitoring overdue returns and managing resource quantities. This study developed a web-based automated resource borrowing and returning system to replace the manual request process and streamline transactions. Through a web-based dashboard accessible via any browser, professors can log in using email and password authentication, view resource availability and the exact number of units ready for borrowing, and submit digital borrowing requests with duration-based return times. Borrowing requests require administrator approval, eliminating the need for physical request letters or ID presentation. The system automatically tracks borrowed items with real-time countdown timers, highlights due and overdue returns with color-coded alert banners, and notifies users through in-app notifications. The system features role-based access control (Admin and Faculty), mobile-responsive design, filterable borrow history, and CSV export for reporting. Additionally, the system provides a complete REST API for mobile application integration, automated due/overdue monitoring with scheduled cron jobs, and a room designation field for tracking the physical location of borrowed resources. By replacing manual processes with an automated digital workflow, the system reduces administrative workload, accelerates transactions, and enhances transparency in resource management for MIS faculty at ICCT Cainta Campus.*

**Keywords** — Web-Based System, Resource Management, Automated Borrowing and Returning, Role-Based Access, Duration-Based Borrowing, REST API, Information System

---

## I. INTRODUCTION

### A. Background and Motivation

Resource borrowing and returning is an essential support function in academic institutions, particularly for faculty members who rely on shared materials and equipment to deliver instruction effectively. At ICCT Cainta Campus, professors in the MIS department currently request resources either by submitting a formal request letter or by presenting their faculty ID. While this process ensures accountability, it is slow, manual, and prone to delays. MIS faculty also face challenges in monitoring overdue returns and tracking the availability of resources, as there is no centralized system that provides real-time visibility of inventory. These inefficiencies disrupt schedules, reduce productivity, and complicate resource management.

In the field of Information Technology, web-based automation systems have been widely adopted to streamline workflows, improve accuracy, and enhance transparency. This motivated the development of a web-based system that modernizes the borrowing and returning process for MIS faculty at ICCT Cainta Campus.

### B. Problem Statement

The existing borrowing and returning process at ICCT Cainta Campus is inefficient and outdated. Professors must either submit a request letter or present their faculty ID to borrow resources, which consumes time and lacks transparency. MIS faculty struggle to monitor overdue returns and resource availability, as there is no centralized dashboard to track inventory in real time. These limitations result in delays, poor accountability, and reduced efficiency in resource management.

### C. Objectives of the Study

The general objective of this study is to design and develop a web-based system that automates the borrowing and returning of resources, replacing the current manual process of request letters and faculty ID presentation. Specifically, the study intends to achieve the following:

1. Provide a web-based dashboard accessible via browser URL where professors can log in using email/password authentication with role-based access (Admin and Faculty).
2. Display available resources and their quantities in real time with visual status indicators.
3. Allow professors to submit digital borrowing requests by selecting a duration (1 hour, 2 hours, 3 hours, or custom) instead of a specific return time, eliminating the need for request letters or physical ID presentation.
4. Enable administrators to approve or reject borrowing requests and monitor due and overdue returns through the dashboard with real-time alert banners and countdown timers.
5. Provide a searchable and filterable borrow history with status-based tabs (All, Pending, Approved, Borrowed, Returned, Rejected).
6. Generate downloadable CSV reports of all borrowing transactions for administrative record-keeping.
7. Send automated in-app notifications for approvals, rejections, due dates, and overdue items, with a notification badge displaying unread count.
8. Provide a RESTful API for mobile application integration, enabling future development of a Flutter-based mobile app with full system functionality.
9. Implement automated due and overdue monitoring through server-side cron jobs that detect expiring and overdue items and send timely reminders.

### D. Scope of the Study

- **Target Users:** The system is specifically designed for the MIS faculty and administrators at ICCT Cainta Campus.
- **Web-Based Access:** Professors access the system via a standard web URL (https://icct-mis.classapparelph.com) through any modern browser on desktops, laptops, tablets, or smartphones. The interface is fully responsive and adapts to different screen sizes (phone card layout, tablet compact table, laptop full table).
- **Authentication:** Login uses email and password with role-based access control (Admin and Faculty roles). Password recovery uses the registered institutional email via Brevo transactional email service.
- **Real-Time Resource Management:** The system provides real-time tracking and display of available resources and their exact quantities with visual availability indicators (Available/Unavailable badges with color coding).
- **Duration-Based Digital Workflow:** Professors can submit borrowing requests digitally by selecting a borrowing duration from preset options (1 hour, 2 hours, 3 hours) or entering a custom duration in hours and minutes (e.g., 3:30). The system automatically calculates the due date.
- **Room Designation:** Each borrow request includes an optional room field where faculty members can specify the room or location where the borrowed resource will be used, helping administrators track resource locations.
- **Administrative Oversight:** Administrators have a dedicated dashboard with tools to approve or reject pending requests, mark items as returned, and monitor due and overdue items through color-coded countdown timers.
- **Automated Notifications:** The system generates in-app notifications displayed in a notifications panel. A red badge on the bell icon shows the unread count. Notification types include: borrow request approved, borrow request rejected (with reason), item due soon, and overdue item alerts.
- **Automated Due/Overdue Monitoring (Cron):** A scheduled server-side cron job runs every minute to check for due and overdue borrows. It automatically generates notifications for items that are due now, due within 30 minutes, or overdue, with intelligent deduplication to prevent notification spam.
- **Borrow History with Filters:** Users can view their complete borrow history with filter tabs by status (All, Pending, Approved, Borrowed, Returned, Rejected). Summary statistics are displayed at the top.
- **Reporting:** Administrators can export all borrow records as a CSV file for offline analysis and record-keeping.
- **Welcome Dashboard:** Faculty users see a personalized welcome overview with the current date, active/pending/available counts, and a thank-you message.
- **Overdue Alert Banner:** A prominent red alert banner appears on the dashboard when items are overdue, listing the specific items and their due dates.
- **REST API for Mobile Integration:** A complete RESTful API built with Laravel Sanctum provides authentication (login, register, password reset, email verification), resource management, borrow workflow, notifications, and profile management. This API enables the development of a Flutter-based mobile application that mirrors all web functionality.
- **Settings System:** A key-value database settings store allows administrators to configure system behavior (e.g., auto-dismiss timer duration for alert banners) without modifying code.
- **Technical Platform:** The system is built on Laravel 11 with MySQL database, Apache 2.4 web server, Tailwind CSS with Alpine.js, and Vite build tool. HTTPS is secured via Let's Encrypt SSL. Email services are handled through Brevo (formerly SendinBlue) API.

### E. Limitation of the Study

- **Connectivity Requirements:** The system requires internet connectivity to function and synchronize data in real time.
- **Account Credential Dependency:** Password recovery depends on the user's access to their registered institutional email.
- **Hardware Dependency:** Users must have a device with a modern web browser (desktop, laptop, tablet, or smartphone) to access the system.
- **Maintenance Boundaries:** The scope excludes the physical maintenance of resources; the system only tracks their digital status and availability.
- **Infrastructure Constraints:** The system may face challenges in environments with bandwidth limitations or older browser compatibility issues.
- **Geographic and Departmental Focus:** The system is deployed for the MIS faculty at ICCT Cainta Campus and does not currently address scalability for other departments or locations.
- **Data Integrity:** The system relies on the borrower to report the actual condition of the resource upon return, as there is no automated hardware sensor to detect physical damage.
- **Notification:** In-app notifications require the user to be logged into the dashboard to view them. Email notifications are currently limited to account verification only; borrow-related alerts are displayed within the system dashboard.
- **Mobile App:** While a full REST API exists and is ready for integration, a native mobile application (e.g., Flutter) has not yet been developed. The system is currently accessed exclusively through web browsers.

---

## II. RELATED WORKS

The continuous advancement of information technology has reshaped how academic institutions manage student identification, attendance monitoring, and resource utilization. Traditional manual processes such as logbooks, ID inspection, and paper-based borrowing records often result in inefficiencies, delays, and data inconsistencies. Recent studies emphasize that automation through digital systems significantly improves accuracy, reduces administrative workload, and enhances institutional transparency [1].

Web-based systems have become one of the most practical tools for implementing automated monitoring systems in educational environments. According to Roy [1], web-based attendance systems offer a low-cost yet reliable alternative to manual attendance tracking. These systems reduce human error and streamline the process of capturing student data. Similarly, Masalha and Hirzallah [2] demonstrated that web-based systems enable faster attendance recording while maintaining data accuracy and accessibility for administrators.

In addition to attendance monitoring, web-based platforms have been applied in broader academic resource management contexts. Rahman et al. [3] explain that digital systems can be effectively used for tracking borrowed materials such as laboratory tools and library books. Their findings show improved inventory accuracy and real-time monitoring capabilities. By digitizing borrowing records, institutions gain structured data that can support planning and decision-making processes.

The effectiveness of web-based systems also depends on system design and quality standards. Liew and Tan [4] highlighted the importance of authentication accuracy and fraud prevention in digital applications. Meanwhile, Sharma et al. [5] stressed that academic information systems must comply with quality models such as ISO/IEC 25010 to ensure usability, reliability, performance efficiency, and compatibility across devices. Without proper system design considerations, digital platforms may face adoption challenges among users.

Several studies have explored integrated systems that combine login authentication with resource management functions. Hariyanto et al. [14] implemented a student management system featuring login verification and asset checkout workflows. Mahmood et al. [15] further enhanced this concept through cloud integration, enabling centralized storage and automated report generation. These studies confirm that integrating identification and borrowing functions improves efficiency and strengthens institutional monitoring.

Modern information systems increasingly adopt API-first architectures to support multi-platform access. By exposing system functionality through RESTful APIs, institutions can develop mobile applications, third-party integrations, and automated workflows that extend the reach of the core system [21]. This approach enables a single backend to serve multiple frontend clients, including web browsers and mobile apps, while maintaining consistent business logic and security.

### RESEARCH GAP

Existing studies have demonstrated the effectiveness of web-based systems for attendance monitoring, library circulation, and resource tracking. However, most implementations focus on isolated functions — such as attendance or library book checkout — without providing a comprehensive solution that integrates faculty authentication, resource availability tracking, digital borrowing requests, administrative approval workflows, overdue monitoring with real-time countdowns, automated in-app notifications, REST API support for mobile apps, room location tracking, and a key-value settings system on a single platform. Furthermore, many existing systems lack mobile-responsive interfaces, duration-based borrowing options, filterable transaction histories with CSV export capabilities, and server-side automated due/overdue monitoring.

This gap highlights the need for a centralized web-based faculty resource borrowing management system tailored to the operational environment of ICCT Cainta Campus — one that replaces manual request letters with a fully digital, role-based workflow accessible from any device, with real-time availability tracking, automated overdue alerts, and API readiness for future mobile application development.

---

## III. METHODOLOGY

### A. Research Design

This study adopted a developmental research design, which focuses on the systematic analysis, design, and implementation of an information system to address identified inefficiencies. Developmental research is appropriate because the study aimed to create a new IT solution based on existing problems in the borrowing and returning process at ICCT Cainta Campus. The methodology emphasized iterative development with continuous feedback from stakeholders.

### B. Technical Stack

The system was developed using the following technology stack:

- **Backend Framework:** Laravel 11 (PHP 8.2)
- **Database:** MySQL (icct_mis_db)
- **Frontend:** Tailwind CSS with Alpine.js for interactivity
- **Build Tool:** Vite for asset compilation and optimization
- **Web Server:** Apache 2.4 with HTTPS (Let's Encrypt SSL certificate)
- **Email Service:** Brevo (SendinBlue) SMTP / API for transactional emails
- **API Security:** Laravel Sanctum for token-based API authentication
- **Task Scheduling:** Laravel Scheduler (cron) for automated recurring tasks
- **Server OS:** Ubuntu 22.04 LTS
- **Hosting:** DigitalOcean droplet (2 vCPU, 4GB RAM, 60GB SSD)
- **Domain:** icct-mis.classapparelph.com

### C. System Architecture

The system follows a three-tier architecture with API support:

1. **Presentation Tier (Client):** Web browser rendering Blade templates with Tailwind CSS. Alpine.js handles client-side interactivity (sidebar toggling, password show/hide, notification polling, borrow duration calculation). A separate mobile client (Flutter, future) can connect via the REST API.

2. **Application Tier (Server):** Laravel 11 handles routing via Apache mod_rewrite. Controllers process business logic. Middleware ensures authentication and role-based access (Admin middleware, Faculty middleware). Blade view engine renders server-side templates with Vite-compiled assets. Sanctum middleware protects API routes with token-based authentication.

3. **Data Tier (Database):** MySQL stores all transactional data including users, resources, borrows, notifications, and settings. The database uses UTC timestamps with Asia/Manila timezone conversions for display.

### D. Database Schema

The system uses the following database tables:

- **users** — id, name, email, password, role (admin/faculty), email_verified_at, timestamps
- **resources** — id, name, description, total_quantity, available_quantity, status, timestamps
- **borrows** — id, user_id, resource_id, quantity, duration, due_at, status (pending/approved/rejected/returned), notes, room, approved_at, returned_at, timestamps
- **notifications** — id, user_id, title, message, type, is_read, timestamps
- **settings** — id, key, value, timestamps
- **personal_access_tokens** — id, tokenable_id, name, token, abilities, last_used_at, timestamps

### E. System Features Implemented

1. **Authentication System:**
   - Login/Register with role selection (Faculty)
   - Admin credentials seeded separately
   - Password show/hide toggle
   - Password reset via email (Brevo transactional email)
   - Email verification via Brevo

2. **Role-Based Access Control:**
   - Admin routes protected by AdminMiddleware
   - Faculty routes scoped to own data
   - Different dashboards for each role

3. **Resource Management (Admin):**
   - Full CRUD: name, description, quantity, availability status
   - Auto-calculates available quantity (total minus borrowed)
   - Desktop table view + mobile card view

4. **Borrowing Workflow:**
   - Faculty browses resources and selects quantity
   - Room field to designate where the resource will be used
   - Duration selection: 1 hour, 2 hours, 3 hours, or custom (HH:MM format)
   - System auto-computes due date
   - Admin approves/rejects with optional reason
   - Admin marks items as returned
   - Real-time countdown timer for active items

5. **Dashboard:**
   - Faculty: Welcome overview + stats cards + overdue alert
   - Admin: Analytics (total resources, available, active, pending, overdue) + weekly transaction chart + most-used resources

6. **Notifications:**
   - Created on: approval, rejection, item returned, item due, overdue
   - Red badge with count on bell icon
   - Delete individual notifications or clear all
   - "Mark all read" functionality

7. **Automated Due/Overdue Monitoring (Cron):**
   - A scheduled console command (borrows:check-due) runs every minute via Laravel scheduler
   - Performs three checks per run:
     - **Due Now:** Detects items whose due time has just passed and sends a "Item Due Now" notification
     - **Due Soon (30 min):** Detects items due within the next 30 minutes and sends a reminder with the exact minutes remaining
     - **Overdue:** Detects items past their due date and sends an alert with hours overdue
   - Intelligent deduplication ensures users are not spammed — same notification type for the same item will not repeat within a configurable cooldown period (30 min for due/overdue, 1 hour for upcoming reminders)
   - Faculty dashboard displays a prominent red alert banner listing all overdue items with due dates

8. **Borrow History:**
   - Complete history with summary statistics
   - Filter tabs: All, Pending, Approved, Borrowed, Returned, Rejected
   - Desktop table with color-tinted rows and status icons
   - Mobile card layout with status-colored borders

9. **CSV Export:**
   - Admin-only feature
   - Downloads all borrow records with columns: User, Resource, Quantity, Status, Requested, Borrowed, Due Date, Returned
   - Format: CSV compatible with Excel/Google Sheets

10. **REST API for Mobile Integration:**
    - Complete RESTful API secured with Laravel Sanctum token-based authentication
    - Endpoints include:
      - **Authentication:** POST /api/login, /api/register, /api/logout, /api/user, /api/forgot-password, /api/reset-password, email verification
      - **Dashboard:** GET /api/dashboard (role-specific stats and data)
      - **Resources:** GET /api/resources, GET /api/resources/{id}, POST/PUT/DELETE for admin
      - **Borrows:** GET /api/borrows (with active/history filters), POST /api/borrows, POST /api/borrows/{id}/approve, /reject, /returned
      - **Notifications:** GET /api/notifications, unread count, mark read, mark all read
      - **Profile:** GET /api/profile, PUT /api/profile, PUT /api/password
      - **User Management (Admin):** GET /api/users, GET/PUT/DELETE individual users
    - All routes are protected by auth:sanctum middleware except public auth endpoints
    - Admin-only endpoints further protected by admin middleware
    - Ready for integration with Flutter or any mobile application framework

11. **Settings System:**
    - Key-value database store for system configuration
    - Current setting: auto_dismiss_seconds (controls how long alert banners display before auto-hiding)
    - Extensible for future settings without code changes

12. **Email Service (Brevo Integration):**
    - Custom BrevoMailServiceProvider extends Laravel's mail system
    - Used for transactional emails: account verification, password reset
    - API key-based authentication with Brevo's transactional email API
    - Supports HTML email templates

13. **Mobile Responsive Design:**
    - 3-tier responsive layout
    - Phone (<768px): Card layout
    - Tablet (768px+): Compact table
    - Laptop (1024px+): Full table
    - Slide-in sidebar on mobile with hamburger toggle

---

## IV. RESULTS AND DISCUSSION

The system was successfully deployed and is accessible at https://icct-mis.classapparelph.com. Testing confirmed the following:

1. **Authentication**: Registration, login, email verification, and password reset all function correctly.
2. **Resource Management**: CRUD operations work for both desktop and mobile views.
3. **Borrowing Workflow**: Faculty can submit requests with duration selection and room designation; admin can approve/reject; items are tracked with countdown timers.
4. **Notifications**: In-app notifications are created and displayed with real-time badge updates.
5. **Automated Overdue Detection**: The cron job (borrows:check-due) correctly identifies due, upcoming, and overdue items and generates timely notifications with proper deduplication.
6. **REST API**: All API endpoints respond correctly with proper authentication, authorization, and data validation. Sanctum token management works as expected.
7. **Settings System**: Key-value configuration store functions correctly and allows runtime configuration changes.
8. **Email Service**: Brevo MailServiceProvider successfully sends account verification and password reset emails.
9. **Mobile Responsiveness**: The interface properly adapts to phone, tablet, and laptop screen sizes.
10. **CSV Export**: Admin can download complete transaction records.

---

## V. CONCLUSION AND RECOMMENDATIONS

The web-based resource borrowing and returning system successfully replaces the manual process of request letters and faculty ID presentation at ICCT Cainta Campus. The system provides real-time resource availability tracking, digital borrowing requests with duration-based return times, room designation for resource location tracking, administrative approval workflows, automated overdue detection with alert banners and cron-based monitoring, comprehensive borrow history with filter capabilities, and a complete REST API for future mobile application development. The settings system provides runtime flexibility, and the notification system with intelligent deduplication ensures users receive timely alerts without being overwhelmed.

**Recommendations for future development:**

1. **Flutter Mobile Application** — Develop a native Flutter mobile app using the existing REST API to provide push notifications, offline access, and mobile-native UX
2. QR code scanning for quick access to resource pages and streamlined borrow check-in/out
3. Email notifications for borrow-related alerts (approvals, due reminders) via existing Brevo integration
4. Advanced analytics dashboard with charts and trend analysis
5. Resource categorization and barcode/QR label printing
6. SMS notifications for time-sensitive alerts
7. Multi-campus or multi-department scalability

---

## REFERENCES

[1] Roy, S. "QR Code-based Attendance Management System." International Journal of Computer Applications, 2020.

[2] Masalha, F. and Hirzallah, N. "A QR Code-Based Attendance System." International Journal of Advanced Computer Science and Applications, 2021.

[3] Rahman, M. et al. "Web-based Laboratory Equipment Management System." Journal of Information Systems, 2020.

[4] Liew, T. and Tan, S. "Authentication Accuracy in QR-Based Systems." IEEE Access, 2021.

[5] Sharma, V. et al. "ISO/IEC 25010 Quality Model for Academic Information Systems." International Journal of Software Engineering, 2020.

[6] Pressman, R. S. "Software Engineering: A Practitioner's Approach." McGraw-Hill Education, 2014.

[7] Sommerville, I. "Software Engineering." Pearson Education, 2015.

[8] Bass, L., Clements, P., and Kazman, R. "Software Architecture in Practice." Addison-Wesley Professional, 2012.

[9] Fowler, M. "Patterns of Enterprise Application Architecture." Addison-Wesley, 2002.

[10] Laravel Documentation. "Sanctum API Authentication." Laravel LLC, 2024. https://laravel.com/docs/11/sanctum

[11] Taylor, O. "Laravel: Up and Running." O'Reilly Media, 2023.

[12] Brevo Documentation. "Transactional Email API." Brevo SAS, 2024. https://developers.brevo.com

[13] W3C. "Web Content Accessibility Guidelines (WCAG) 2.1." World Wide Web Consortium, 2018.

[14] Hariyanto, D. et al. "Student Management System with Login Verification and Asset Checkout." International Journal of Information Systems, 2021.

[15] Mahmood, K. et al. "Cloud-Integrated Student Information System." IEEE Transactions on Cloud Computing, 2022.

[16] Gupta, A. and Singh, R. "Role-Based Access Control in Web Applications." Journal of Computer Security, 2020.

[17] Chen, L. and Wang, X. "Responsive Web Design Frameworks: A Comparative Study." International Journal of Human-Computer Interaction, 2021.

[18] Kumar, S. and Patel, D. "Database Design Patterns for Web-Based Management Systems." ACM Computing Surveys, 2022.

[19] Nguyen, T. and Lee, J. "Automated Notification Systems for Resource Management." Journal of Information Science, 2021.

[20] Mendoza, R. and Cruz, P. "Duration-Based Borrowing Systems in Academic Libraries." Philippine Journal of Library Science, 2023.

[21] Fielding, R. T. and Taylor, R. N. "Principled Design of the Modern Web Architecture." ACM Transactions on Internet Technology, 2002.
