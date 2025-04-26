# Todo AJAX Application

A simple Todo application built with HTML, CSS, JavaScript, and PHP that demonstrates AJAX functionality for seamless client-server interaction without page refreshes.

## Features

- Create new todo items
- View a list of all todos
- Mark todos as completed
- Delete existing todos
- Persistent data storage using server-side JSON file

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Data Storage**: JSON file
- **Communication**: AJAX (Asynchronous JavaScript and XML)

## Project Structure

- `index.html` - The main HTML file containing the user interface
- `index.php` - PHP server that handles both HTML content and API requests
- `todos.json` - JSON file that stores the todo items data

## Setup Instructions

### Prerequisites

- PHP 7.0+ installed on your system
- A web browser

### Running the Application

1. Clone or download this repository to your local machine
2. Navigate to the project directory
3. Start the PHP development server:
   ```
   php -S localhost:8000
   ```
4. Open your browser and go to http://localhost:8000

## API Endpoints

The application provides a RESTful API for managing todos:

- `GET /index.php/api` - Get all todos
- `GET /index.php/api?id={id}` - Get a specific todo by ID
- `POST /index.php/api` - Create a new todo
  - Required JSON body: `{ "todo": "Your todo text", "completed": false }`
- `PUT /index.php/api` - Update an existing todo
  - Required JSON body: `{ "id": 1, "completed": true }` or `{ "id": 1, "todo": "Updated text" }`
- `DELETE /index.php/api?id={id}` - Delete a todo

## How It Works

The application uses AJAX (XMLHttpRequest object) to communicate with the PHP backend without refreshing the page. When you perform actions like adding, updating, or deleting todos, JavaScript sends requests to the PHP server, which updates the JSON file and returns appropriate responses.

## Future Improvements

- User authentication
- Todo categories or tags
- Due dates and reminders
- Filtering and searching todos
- Mobile-responsive design improvements

## License

This project is open-source and available under the MIT License.