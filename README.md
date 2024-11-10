# Virtual Study Space

A web-based platform designed to facilitate virtual classrooms, where teachers and students can collaborate, communicate in real time, and manage assignments, quizzes, and learning resources efficiently.

## Features

### User Roles
- **Admin**: 
  - Manages teachers and students.
  - Creates, updates, and deletes classrooms.
  - Receives and manages feedback from users.
  - Sends notifications to specific users, classrooms, or all users.
  
- **Teacher**: 
  - Creates, updates, and deletes classrooms.
  - Manages classroom-specific chats.
  - Uploads assignments, quizzes, videos, and notes.
  - Grades assignments and provides feedback to admins.
  - Can remove students from classrooms.
  - Can view classroom overviews.
  - Sends notifications to students or all users.

- **Student**: 
  - Joins classrooms.
  - Submits assignments and takes quizzes.
  - Accesses uploaded notes and videos.
  - Tracks personal progress.
  - Sends feedback to their classroom teacher or about the overall platform to the admin.
  - Receives notifications from teachers and admins but cannot receive direct feedback.

### Core Functionalities
- **Classroom Management**: Admins and teachers can create, update, and delete virtual classrooms.
- **Real-time Chat**: Firebase-based chat feature for real-time communication between students and teachers within a classroom.
- **Assignments & Quizzes**: Teachers can upload assignments and quizzes. Students submit their work, and teachers review, grade, and provide feedback.
- **Resource Upload**: Teachers can upload notes and videos for students to access.
- **Notifications**: 
  - **Admin and Teachers**: Can send notifications to specific users or classrooms or to all users.
  - **Students**: Receive notifications from admins and teachers.
- **Feedback System**: 
  - **Students**: Can send feedback either to their classroom teacher or to the admin regarding the platform.
  - **Teachers**: Can send platform-related feedback directly to the admin.
  - **Admins**: Can review, manage, and delete feedback received from students and teachers.

### Progress Tracking
- **Student Progress**: Students can track their own progress through submitted assignments and quizzes across different classrooms.
- **Teacher Overview**: Teachers can view a classroom overview, track, and monitor assignment submissions and quiz results.

## Tech Stack

- **Backend**: PHP (with MySQL for database management)
- **Frontend**: HTML, CSS, JavaScript (with jQuery)
- **Database**: MySQL
- **Real-time Communication**: Firebase
- **Version Control**: Git

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/virtual-study-space.git
    ```
2. Navigate to the project directory:
    ```bash
    cd virtual-study-space
    ```
3. Set up your local environment:
   - Ensure you have a local server environment (e.g., XAMPP or MAMP) to run PHP.
   - Set up a MySQL database and import the provided SQL file:
     - **Database Structure**: The project includes an SQL file containing the database structure for the application. You can find it at `database/virtual_study_space.sql`.
     - **Importing the SQL File**:
       - Open your MySQL client (like phpMyAdmin).
       - Create a new database (e.g., `virtual_study_space`).
       - Import the SQL file into the newly created database.
   - Configure your database connection in `config/database.php`.

4. Run the project on `localhost` through your server environment.

## Usage

- **Admin**: Manages teachers and students, creates and manages classrooms, receives and manages feedback, and sends notifications.
- **Teacher**: Creates classrooms, uploads notes and videos, manages assignments and quizzes, reviews student work, provides feedback, sends notifications, and manages classroom chats.
- **Student**: Joins classrooms, submits assignments, takes quizzes, accesses uploaded resources, receives notifications, sends feedback, and tracks personal progress.

## Project Demo
Check out a video demo of this project [here](https://drive.google.com/file/d/1ffxTOnGTmnTj6G5n1wsQEWEwXQjIEyhL/view?usp=sharing).

## Security Notice

To use the Firebase real-time chat functionality, follow these steps:

1. **Set Up Firebase**:
   - Go to the [Firebase Console](https://console.firebase.google.com/).
   - Create a new Firebase project.

2. **Configure the Real-time Database**:
   - In the Firebase console, navigate to the **Database** section.
   - Click on **Create Database** and select the appropriate settings (e.g., start in test mode for development).
   - Set up rules to secure your database according to your requirements.

3. **Get Your Firebase Credentials**:
   - In your Firebase project settings, find your Web API Key and other required credentials.
   - Add these credentials to the public-facing `chat.php` file. Replace the placeholder keys with your actual Firebase configuration.

4. **Database Security**:
   - Ensure you implement proper security rules in your Firebase database to restrict access to authorized users only.
   - Do not expose sensitive API keys in public repositories.

## License
This project is not currently licensed.
