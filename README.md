# CSE370
AidShare is a website that functions as a lending library for assistive devices, such as wheelchairs, crutches, and walkers, allowing users to borrow mobility and accessibility equipment directly from the platform's inventory. A user can send a borrow request stating how long they will need a device, and the system matches it against real-time availability; if the device is currently unavailable, the user joins a waitlist and is automatically notified once the device of their choice is back in the inventory. Devices are tracked individually with updated condition notes and maintenance information.Overdue borrows are flagged, and users are notified with a gentle reminder,which escalates in cases of serious overdues. Coordinators oversee the process of maintenance before a device is lent out again as well as  personally follow up with users,addressing the overdue cases . Additionally, anyone with a device they no longer need has the option to donate it, which is then added to the platform's inventory for future lending. 
Frontend Development : 
Worked on index.php, signup.php, home.php (role aware display), coordinator.php (device inventory) , request_device.php, request_result.php
Sign in card with box for entering email(username) and password and enter
Sign up card requiring email, password, confirm password(must match password), a drop down box to select one of 3 roles(Donor, Borrower, Coordinator), missing any required fields displays error message
After login, the nav bar shows only the links relevant to the logged-in role: Borrow + Return for borrowers, Donate for donors, Coordinator for coordinators. Log Out is always present. 
Device table shows a fully rendered table with device ID, type, description, condition and status, available to only Coordinator
Requesting a device shows a form card with dropdown for device type, and a number input for estimated days the device is needed, only Borrowers can view this page. 
After that borrower can view one of 3 outcome messages: Approved (along with device and its due date to be returned on), Waitlisted (their position on the list, expected return date), No Matching Device (there are no devices in inventory for the request)

Worked on coordinator.php (overdue panel and waitlisted panel) , request_result.php(waitlisted view), home.php (overdue status)
Coordinator view of the Waitlist with rendered table displaying position, waitlist ID, device ID, type, borrower, request date, estimated date, expected available date and notified
Coordinator view of the Overdue panel  with a rendered table listing overdue loans and their severity displayed with colored tabs.
Borrower view of the Waitlist with their queue position, and the expected return date for the specific device they have been waitlisted for. 
Home page with overdue status, with three visual queues: green for soft reminder (1-4 days), amber for strong reminder (5-13 days), red for strong reminder (14+ days), when due date has been exceeded by 15 days the Borrower is temporarily blocked with the homepage showing a large Blocked message, allowing only return device or log out panels.

Worked on donate.php, donate_success.php, coordinator.php(maintenance panel), return_device.php
Return device form with three fields: dropdown of the borrower’s active loans, text input of the condition, and another dropdown cleared for lending.
Coordinator view of the maintenance panel, with a rendered table listing date, device ID, type, current condition, notes and cleared for lending.
Device donation form with fields for device type, optional description, and condition. Only Donors can view this page. Redirected to the donate success tab with simple text confirming your donation.

Backend Development 
Contributed to signup.php, signup_process.php, auth.php, show_devices.php, coordinator.php, request_device.php, request_result.php
Auth.php defines require_login(), require_role($flag), require_not_role($flag), and show_no_access(). Every protected page calls one of these at the top before any output and allows access only based on the role assigned to the user.
Created signup features that compares raw password and email. On success, stores user_id, email, donor_flag, borrower_flag, coordinator_flag in $_SESSION and redirects to home.php. For signup validates password match, if account already exists for that email checks whether that role has been signed up for, rejects and updates accordingly. New users get all three flags defaulting to NO, with only chosen roles to YES. Redirects to index.php on success.
Worked on coordinator access to show_devices table where every physical device is entered as its own individual record. A condition note is updated every time the device is returned so borrowers know the current state of what they are receiving.
 When a borrower requests a device they state how many days they need it. The system first looks for a free device; if none exists, it finds the one whose current loan ends soonest and places the borrower on a waitlist for it.

Contributed to coordinator.php, request_result.php(waitlisted view), home.php
Waitlist logic: If a device is on loan when someone requests it, they join a queue. The moment that device is returned and cleared, the first person in the waitlist is automatically promoted to a new borrower — no coordinator action required.
Overdue tracking with escalation: The system flags any device past its expected return date. Borrowers see a colour-coded toast notification fixed to the bottom-right of the home page. Coordinators see all overdue loans in a dedicated nav panel with severity badges.
After closing a returned loan, if it is cleared for lending it queries the first waitlist entry for the device ordered by QUEUE_POSITION, if found it updates device status to donated and deletes the user’s waitlist position and then decrements everyone else’s position by 1.

Contributed to donate.php, donate_success.php, coordinator.php(maintenance panel), return_device.php
Maintenance logic: Every time a device is returned, the borrower logs its condition and states whether it is ready to lend again. This creates a permanent service history per device visible to the coordinator. It verifies that the LOAN_ID belongs to a request made by the logged-in borrower and updates the return date, overdue status, device_condition, status (availability) for the device. It also triggers waitlist auto-promotion.
Any user registered as a Donor can submit a device directly into the inventory through a simple form. The donation is immediately available for borrowers to request. It asks the donor to enter device type, description text (optional), and its condition, then applies a donation ID and device ID. Redirects to donate_success.php afterwards or shows error message if required fields are empty

Conclusion: This project helped us understand how a website collects, stores, and displays data for its users, particularly how borrower requests, device availability, and coordinator actions are all connected through the underlying database. Working on features like the waitlist and overdue tracking system gave us a clearer picture of how queries are used to pass data between the database and the frontend in real time. Additionally, this project helped us build stronger teamwork habits, as we learned to collaborate using GitHub.

