# PHP_Laravel12_Snooze
====================================================================
Laravel 12 Snooze Reminder System – Full Working Project
========================================================

Project Name
Laravel12_Snooze_System

Project Overview
This project is a complete Snooze Reminder System built using Laravel 12. It allows users to create reminders with date and time, automatically detect when the reminder time is reached, show real-time notifications, and provide snooze or complete options.

The system includes:

* Reminder creation
* Snooze functionality (default and custom minutes)
* Auto-detection of due reminders
* SweetAlert popup notifications
* Notification bell counter
* Real-time checking every 5 seconds
* Responsive Bootstrap 5 UI

---

## STEP 1: Create Laravel Project

composer create-project laravel/laravel Laravel12_Snooze_System
cd Laravel12_Snooze_System

---

## STEP 2: Configure Database (.env)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_snooze
DB_USERNAME=root
DB_PASSWORD=

Create database manually:
CREATE DATABASE laravel_snooze;

---

## STEP 3: Create Model and Migration

php artisan make:model Reminder -m

Migration Fields:

* id
* title
* message
* remind_at (timestamp)
* is_snoozed (boolean)
* snooze_minutes (integer nullable)
* timestamps

Run:
php artisan migrate

---

## STEP 4: Reminder Model Logic

Features in Model:

* Fillable fields
* Date casting
* isOverdue() method
* time_remaining accessor
* Carbon date comparison

Core Logic:

* Checks if current time >= remind_at
* Calculates minutes or hours remaining

---

## STEP 5: Reminder Controller

Controller Handles:

* index() – show reminders
* store() – create reminder
* snooze() – default 5 min snooze
* snoozeCustom() – custom snooze (1–60 min)
* delete() – delete reminder
* markAsCompleted() – complete reminder
* checkDue() – API endpoint returning due reminders JSON

Real-Time Detection:
checkDue() returns:
{
count: number,
reminders: [
id,
title,
message
]
}

---

## STEP 6: Routes

/
/store
/snooze/{id}
/snooze-custom/{id}
/delete/{id}
/complete/{id}
/check-due-reminders

---

## STEP 7: Frontend UI

Technologies Used:

* Bootstrap 5
* Bootstrap Icons
* SweetAlert2
* Fetch API

UI Includes:

* Reminder creation form
* Reminders table
* Status badges (Waiting / Time Reached)
* Snooze modal
* Notification bell dropdown
* Statistics cards

---

## STEP 8: Auto Notification System

JavaScript Logic:

* setInterval every 5 seconds
* Fetch /check-due-reminders
* Show SweetAlert popup
* Prevent duplicate alerts using Set()
* Update notification bell counter
* Update table row status dynamically

SweetAlert Features:

* Snooze button
* Complete button
* Close button
* Timer progress bar

---

## STEP 9: Test Application

php artisan serve

Visit:
[http://localhost:8000](http://localhost:8000)
<img width="1644" height="859" alt="image" src="https://github.com/user-attachments/assets/9c99fd87-c7d9-4045-b5b6-913b30fc3f9a" />


Test Steps:

1. Create reminder 1–2 minutes ahead
2. Wait until time reaches
3. SweetAlert popup appears
4. Notification bell shows count
5. Choose Snooze or Complete

---

## Features Included

* Full CRUD operations
* Snooze (default + custom)
* Real-time checking
* Auto popup alerts
* Notification bell counter
* Dynamic UI updates
* Responsive design
* Statistics summary cards
* No duplicate alerts

---

## How It Works Internally

1. User creates reminder
2. Reminder saved with remind_at timestamp
3. Frontend polls server every 5 seconds
4. Backend returns due reminders
5. JavaScript triggers SweetAlert
6. User snoozes or completes reminder
7. Database updates accordingly

---

## Future Enhancements

* Email notifications
* SMS integration
* WebSocket real-time updates
* User authentication
* Recurring reminders
* Mobile app integration

---

## End of Laravel 12 Snooze Reminder System Documentation

