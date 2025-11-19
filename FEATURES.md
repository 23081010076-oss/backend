# Feature Documentation

## 1. User Authentication & Profile Management

### JWT-Based Authentication

The platform uses JWT (JSON Web Tokens) for secure API authentication.

**Features:**

-   Register new users
-   Login with email and password
-   Token-based session management
-   Profile photo upload
-   Profile information management

**Key Functions:**

```php
POST /api/register           // Create new account
POST /api/login              // Get JWT token
POST /api/auth/logout        // Invalidate token
GET /api/auth/profile        // Get user details
PUT /api/auth/profile        // Update profile
POST /api/auth/profile/photo // Upload avatar
```

**User Profile Fields:**

-   Full name and email
-   Phone and address
-   Education details (institution, major, level)
-   Biography and profile photo
-   Role assignment

**User Roles:**

1. **User** - Standard student/member access
2. **Admin** - Full platform management
3. **Corporate** - Partner company access

---

## 2. Course Management System

### Create & Manage Courses

Admins can create and manage online courses with video content.

**Course Properties:**

-   Title and detailed description
-   Course level (beginner, intermediate, advanced)
-   Duration and price
-   Video content (URLs, duration, count)
-   Certificate generation upon completion
-   Access control settings

**Course Features:**

```
✓ Browse all available courses
✓ Search and filter courses
✓ View course details and reviews
✓ Enroll in courses
✓ Track learning progress (0-100%)
✓ Receive certificates upon completion
✓ Download course materials
```

**Key Endpoints:**

```php
GET /api/courses              // List all courses
POST /api/courses             // Create course (Admin)
GET /api/courses/{id}         // Get course details
PUT /api/courses/{id}         // Update course (Admin)
POST /api/courses/{id}/enroll // Enroll in course
GET /api/my-courses          // Get enrolled courses
```

**Enrollment Tracking:**

-   Progress percentage (0-100%)
-   Completion status
-   Certificate URL

---

## 3. Scholarship System

### Manage Scholarships & Applications

**Scholarship Features:**

```
✓ Create and manage scholarship opportunities
✓ Set eligibility criteria and requirements
✓ Define funding amounts and benefits
✓ Set application deadlines
✓ Track scholarship applications
✓ Update application status (approved/rejected/awarded)
✓ Categorize by study field
```

**Scholarship Statuses:**

-   `active` - Open for applications
-   `closed` - No longer accepting applications
-   `filled` - All scholarships awarded
-   `archived` - Past scholarship

**Application Statuses:**

-   `pending` - Under review
-   `approved` - Application accepted
-   `rejected` - Application declined
-   `awarded` - Scholarship granted
-   `completed` - Term completed

**Key Endpoints:**

```php
GET /api/scholarships                           // List scholarships
POST /api/scholarships                          // Create (Admin/Corporate)
GET /api/scholarships/{id}                      // Scholarship details
POST /api/scholarships/{id}/apply              // Apply for scholarship
GET /api/my-applications                        // My applications
PUT /api/scholarship-applications/{id}/status  // Update status (Admin)
```

**Application Process:**

1. User views available scholarships
2. User submits application with motivation letter
3. Admin/Corporate reviews application
4. Status updated (approved/rejected/awarded)
5. Notification sent to applicant

---

## 4. Mentoring Sessions

### One-on-One Coaching Sessions

**Session Features:**

```
✓ Schedule mentoring sessions with mentors
✓ Set meeting time and location/link
✓ Conduct need assessment
✓ Share coaching materials
✓ Track session progress
✓ Make payments for sessions
✓ Update session status
✓ Generate meeting links
```

**Session Types:**

-   Technical mentoring
-   Career coaching
-   Academic guidance
-   Project assistance
-   Interview preparation

**Session Statuses:**

-   `scheduled` - Waiting to begin
-   `in_progress` - Currently running
-   `completed` - Session finished
-   `cancelled` - Session cancelled
-   `rescheduled` - New time set

**Key Components:**

#### Need Assessment

Conducted at the beginning of mentoring relationship:

```php
POST /api/mentoring-sessions/{id}/need-assessments
// Collect:
// - Student goals
// - Current skill level
// - Challenges and obstacles
// - Expected outcomes
// - Time commitment
```

#### Coaching Files

Share resources and materials:

```php
POST /api/mentoring-sessions/{id}/coaching-files
// Upload and share:
// - Study materials
// - Code examples
// - Documentation
// - Reference guides
// - Project files
```

**Key Endpoints:**

```php
POST /api/mentoring-sessions              // Create session
GET /api/my-mentoring-sessions            // View sessions
PUT /api/mentoring-sessions/{id}/status   // Update status
POST /api/mentoring-sessions/{id}/schedule // Schedule session
GET /api/mentoring-sessions/{id}/need-assessments
POST /api/mentoring-sessions/{id}/need-assessments
GET /api/mentoring-sessions/{id}/coaching-files
POST /api/mentoring-sessions/{id}/coaching-files
GET /api/mentoring-sessions/{id}/coaching-files/{fileId}/download
```

---

## 5. User Portfolio & Achievements

### Showcase Skills & Accomplishments

**Achievement Features:**

```
✓ Add certifications
✓ Document awards
✓ Record completed projects
✓ List course completions
✓ Upload achievement proof
✓ Organize by category
✓ Share portfolio with others
```

**Achievement Types:**

-   Certifications and badges
-   Awards and recognition
-   Course completion certificates
-   Project completions
-   Skill validations
-   Competitions

**Portfolio Contents:**

-   Achievements
-   Experiences
-   Completed courses
-   Certificates
-   Skills
-   Projects

**Key Endpoints:**

```php
POST /api/achievements              // Add achievement
GET /api/achievements               // View achievements
PUT /api/achievements/{id}          // Update achievement
DELETE /api/achievements/{id}       // Delete achievement
GET /api/auth/portfolio             // View complete portfolio
```

---

## 6. Professional Experience Management

### Document Career History

**Experience Features:**

```
✓ Add work experience
✓ Record education history
✓ Document volunteer work
✓ Track skills by experience
✓ Set employment dates
✓ Detailed role descriptions
✓ Organize by date
```

**Experience Types:**

-   Professional (paid work)
-   Educational (formal education)
-   Volunteer work
-   Internships
-   Projects
-   Freelance work

**Experience Details:**

-   Position/Title
-   Company/Organization
-   Employment dates (start/end)
-   Description of responsibilities
-   Skills demonstrated
-   Achievements in role

**Key Endpoints:**

```php
POST /api/experiences              // Add experience
GET /api/experiences               // View experiences
PUT /api/experiences/{id}          // Update experience
DELETE /api/experiences/{id}       // Delete experience
GET /api/auth/portfolio            // See in portfolio
```

---

## 7. Organization Management

### Manage Partner Organizations

**Organization Features:**

```
✓ Create organizations
✓ Link to scholarship programs
✓ Add contact information
✓ Define organization type
✓ Manage representatives
✓ Set organizational details
```

**Organization Details:**

-   Organization name
-   Type (Corporate, NGO, Educational, Government)
-   Contact person and email
-   Phone and website
-   Headquarters location
-   Description
-   Years established
-   Employee count

**Key Endpoints:**

```php
POST /api/organizations             // Create organization
GET /api/organizations              // List organizations
GET /api/organizations/{id}         // View details
PUT /api/organizations/{id}         // Update organization
DELETE /api/organizations/{id}      // Delete organization
```

---

## 8. Subscription & Premium Features

### Flexible Subscription Plans

**Subscription Features:**

```
✓ Multiple plan tiers
✓ Flexible billing periods
✓ Course bundling
✓ Plan upgrades
✓ Auto-renewal options
✓ Payment tracking
✓ Access control
```

**Subscription Plans:**

-   **Free** - Limited access to courses
-   **Basic** - Access to select courses
-   **Premium** - Full course catalog
-   **Premium Plus** - Premium + mentoring

**Plan Details:**

-   Duration (months, years)
-   Associated courses
-   Feature access
-   Priority support
-   Pricing

**Subscription Statuses:**

-   `active` - Currently valid
-   `inactive` - Not in use
-   `expired` - Past expiration
-   `cancelled` - User cancelled
-   `paused` - Temporarily paused

**Key Endpoints:**

```php
POST /api/subscriptions              // Create subscription
GET /api/subscriptions               // View subscriptions
PUT /api/subscriptions/{id}          // Update subscription
DELETE /api/subscriptions/{id}       // Cancel subscription
POST /api/subscriptions/{id}/upgrade // Upgrade plan
```

---

## 9. Transaction & Payment Management

### Handle Financial Transactions

**Transaction Features:**

```
✓ Process multiple payment types
✓ Track payment status
✓ Generate transaction codes
✓ Upload payment proof
✓ Set payment expiration
✓ Support multiple payment methods
✓ Transaction history
```

**Transaction Types:**

-   Course enrollment
-   Scholarship applications
-   Subscription purchases
-   Mentoring session payments

**Payment Methods:**

-   Credit card
-   Bank transfer
-   E-wallet
-   Mobile payment
-   Check payment

**Transaction Statuses:**

-   `pending` - Awaiting payment
-   `paid` - Successfully processed
-   `expired` - Payment window closed
-   `failed` - Payment failed
-   `cancelled` - Transaction cancelled
-   `refunded` - Refund processed

**Transaction Details:**

-   Transaction code (unique ID)
-   Amount (decimal format)
-   Payment method
-   Payment proof/receipt
-   Timestamp (created, paid, expired)
-   Status
-   Associated entity (course, subscription, etc.)

**Key Endpoints:**

```php
POST /api/transactions         // Create transaction
GET /api/transactions          // View transactions
GET /api/transactions/{id}     // View details
```

---

## 10. Reviews & Ratings System

### Community Feedback

**Review Features:**

```
✓ Rate courses and scholarships
✓ Write detailed reviews
✓ See community ratings
✓ Update own reviews
✓ Display average ratings
✓ Sort by rating/date
✓ Helpful votes (future feature)
```

**Reviewable Items:**

-   Courses
-   Scholarships
-   Mentoring services

**Rating System:**

-   1-5 star rating
-   Text comment/review
-   Verified user indicator
-   Review date and helpful count

**Review Contents:**

-   Star rating
-   Review text
-   Author name and avatar
-   Timestamp
-   Verified purchase indicator

**Key Endpoints:**

```php
GET /api/reviews                    // View reviews
POST /api/reviews                   // Create review
PUT /api/reviews/{id}               // Update review
DELETE /api/reviews/{id}            // Delete review
```

---

## 11. Blog & Article Management

### Content & Knowledge Base

**Article Features:**

```
✓ Create blog posts and articles
✓ Rich text editing
✓ Featured images
✓ Categorization
✓ Publishing schedule
✓ SEO-friendly slugs
✓ Author information
✓ Drafts and scheduling
```

**Article Status:**

-   `draft` - Work in progress
-   `published` - Live on platform
-   `scheduled` - Scheduled for future
-   `archived` - Older articles

**Article Details:**

-   Title and slug
-   Content (HTML/Markdown)
-   Category
-   Featured image
-   Author
-   Publication date
-   Status

**Article Categories:**

-   Technology tips
-   Career advice
-   Study guides
-   Success stories
-   Platform updates
-   Learning resources

**Key Endpoints:**

```php
GET /api/articles                  // List articles (public)
GET /api/articles/{id}             // View article
POST /api/articles                 // Create (Admin/Corporate)
PUT /api/articles/{id}             // Update (Admin/Corporate)
DELETE /api/articles/{id}          // Delete (Admin/Corporate)
```

---

## 12. Corporate Partnerships

### B2B Collaboration & Inquiries

**Corporate Features:**

```
✓ Submit partnership inquiries
✓ Manage company profiles
✓ Create scholarship programs
✓ Post job opportunities
✓ Host training sessions
✓ Recruit talented students
✓ Admin follow-up system
```

**Inquiry Status:**

-   `new` - Initial submission
-   `reviewing` - Under evaluation
-   `contacted` - Admin reached out
-   `negotiating` - In discussion
-   `closed` - No further action
-   `partnered` - Active partnership

**Corporate Contact Form:**

-   Company name
-   Contact person
-   Email and phone
-   Message/inquiry
-   Company size
-   Industry
-   Website (optional)

**Key Endpoints:**

```php
POST /api/corporate-contact           // Submit inquiry (public)
GET /api/corporate-contacts           // View inquiries (Admin)
GET /api/corporate-contacts/{id}      // View details (Admin)
PUT /api/corporate-contacts/{id}/status // Update status (Admin)
DELETE /api/corporate-contacts/{id}   // Delete (Admin)
```

---

## 13. User Activity History

### Track User Actions

**Activity Tracking:**

```
✓ Track all user actions
✓ View activity timeline
✓ Filter by activity type
✓ Date range filtering
✓ Action descriptions
```

**Activity Types:**

-   Profile updates
-   Course enrollments
-   Course completions
-   Scholarship applications
-   Session scheduling
-   Achievement submissions
-   File uploads
-   Review submissions

**Activity Information:**

-   Activity type
-   Description
-   Timestamp
-   Related entity (course, scholarship, etc.)

**Key Endpoints:**

```php
GET /api/auth/activity-history    // View personal activity
```

---

## 14. Admin Management Features

### Platform Administration

**Admin Capabilities:**

```
✓ User management (create, edit, delete)
✓ Course creation and management
✓ Scholarship management
✓ Application review and approval
✓ Transaction oversight
✓ Content moderation
✓ System configuration
✓ Report generation
```

**Admin Endpoints:**

```php
GET /api/admin/users               // Manage all users
POST /api/admin/users              // Create user
PUT /api/admin/users/{id}          // Update user
DELETE /api/admin/users/{id}       // Delete user
```

**Admin Functions:**

-   User registration and management
-   Role assignment
-   Course publication
-   Scholarship approval
-   Payment processing oversight
-   Content review and approval
-   System monitoring

---

## Feature Integration Example

### Complete User Journey

**1. Registration & Profile Setup**

```
User registers → Completes profile → Uploads photo
```

**2. Course Learning**

```
Browse courses → Enroll → Track progress → Complete & get certificate
```

**3. Scholarship Application**

```
Find scholarship → Apply → Admin reviews → Status update → Notification
```

**4. Mentoring & Coaching**

```
Schedule session → Assessment → Receive materials → Complete session → Payment
```

**5. Profile Development**

```
Add experiences → Achievements → Courses completed → Build portfolio
```

---

## Best Practices

### For Students

-   Complete profile for better recommendations
-   Engage with mentors regularly
-   Provide detailed reviews
-   Keep track of achievements
-   Update experiences after new roles

### For Instructors/Mentors

-   Create detailed course content
-   Respond promptly to inquiries
-   Provide constructive feedback
-   Share relevant materials
-   Update course regularly
-   Monitor student progress

### For Admins

-   Review applications thoroughly
-   Respond to corporate inquiries
-   Monitor payment transactions
-   Moderate content appropriately
-   Update system regularly
-   Generate reports regularly

---

**Last Updated:** November 19, 2025
**Documentation Version:** 1.0.0
