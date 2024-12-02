# Laravel Event Calendar Application

This Laravel application allows users to add, edit, and manage events on a calendar. The calendar is implemented using [FullCalendar](https://fullcalendar.io/)

## Features

-   **User Authentication**: Users can register, log in, and manage their sessions.
-   **Event Management**:
    -   Add new events with details such as title, description, start and end times, and a color label.
    -   Edit existing events.
    -   Delete events from the calendar.
-   **Interactive Calendar**: Real-time updates on the calendar using FullCalendar.
-   **Responsive Design**: Fully functional on mobile, tablet, and desktop devices.

## Technologies Used

-   **Backend**: Laravel (PHP Framework)
-   **Frontend**: FullCalendar, Bootstrap
-   **Database**: MySQL (or any other database supported by Laravel)

## Installation

Follow these steps to set up the application on your local environment:

1. **Clone the repository**:

    ```bash
    git clone https://github.com/ctAnsaf/Calendar-app
    cd event-calendar-app
    ```

2. **Install dependencies**:

    ```bash
    composer install
    npm install
    npm run dev
    ```

3. **Start the application**:
    ```bash
    php artisan serve
    ```

## Usage

1. Navigate to `http://localhost:8000` in your web browser.
2. Register or log in to access the calendar.
