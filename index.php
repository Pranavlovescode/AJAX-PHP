<?php
// Check if this is an API request or a page request
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = dirname($_SERVER['SCRIPT_NAME']);
$relativePath = trim(substr($requestPath, strlen($basePath)), '/');

// If it's a direct access to index.php or the root path, serve the HTML
if ($relativePath === 'index.php' || $relativePath === '') {
    // Serve the HTML content
    include 'index.html';
    exit;
}

// Set headers for JSON response and CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Simple file-based storage for todos
$todosFile = 'todos.json';

// Create the todos file if it doesn't exist
if (!file_exists($todosFile)) {
    $initialTodos = [
        [
            'id' => 1,
            'todo' => 'Complete project assignment',
            'completed' => false
        ],
        [
            'id' => 2,
            'todo' => 'Buy groceries',
            'completed' => true
        ],
        [
            'id' => 3,
            'todo' => 'Learn AJAX',
            'completed' => false
        ]
    ];
    file_put_contents($todosFile, json_encode($initialTodos, JSON_PRETTY_PRINT));
}

// Read all todos from file
function getTodos() {
    global $todosFile;
    $content = file_get_contents($todosFile);
    return json_decode($content, true);
}

// Save todos to file
function saveTodos($todos) {
    global $todosFile;
    file_put_contents($todosFile, json_encode($todos, JSON_PRETTY_PRINT));
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all todos or a single todo by ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $todos = getTodos();
            $found = false;
            
            foreach ($todos as $todo) {
                if ($todo['id'] == $id) {
                    echo json_encode($todo);
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                http_response_code(404);
                echo json_encode(['error' => 'Todo not found']);
            }
        } else {
            // Return all todos
            echo json_encode(getTodos());
        }
        break;
        
    case 'POST':
        // Add a new todo
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['todo'])) {
            $todos = getTodos();
            
            // Find the highest ID to create a new unique ID
            $maxId = 0;
            foreach ($todos as $todo) {
                if ($todo['id'] > $maxId) {
                    $maxId = $todo['id'];
                }
            }
            
            // Create new todo
            $newTodo = [
                'id' => $maxId + 1,
                'todo' => $input['todo'],
                'completed' => isset($input['completed']) ? $input['completed'] : false
            ];
            
            $todos[] = $newTodo;
            saveTodos($todos);
            
            http_response_code(201); // Created
            echo json_encode($newTodo);
        } else {
            http_response_code(400); // Bad request
            echo json_encode(['error' => 'Todo content is required']);
        }
        break;
        
    case 'PUT':
        // Update an existing todo
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['id'])) {
            $id = $input['id'];
            $todos = getTodos();
            $found = false;
            
            foreach ($todos as &$todo) {
                if ($todo['id'] == $id) {
                    // Update todo fields if provided
                    if (isset($input['todo'])) {
                        $todo['todo'] = $input['todo'];
                    }
                    if (isset($input['completed'])) {
                        $todo['completed'] = $input['completed'];
                    }
                    
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                saveTodos($todos);
                echo json_encode(['message' => 'Todo updated successfully']);
            } else {
                http_response_code(404); // Not found
                echo json_encode(['error' => 'Todo not found']);
            }
        } else {
            http_response_code(400); // Bad request
            echo json_encode(['error' => 'Todo ID is required']);
        }
        break;
        
    case 'DELETE':
        // Delete a todo
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $todos = getTodos();
            $found = false;
            $updatedTodos = [];
            
            foreach ($todos as $todo) {
                if ($todo['id'] == $id) {
                    $found = true;
                } else {
                    $updatedTodos[] = $todo;
                }
            }
            
            if ($found) {
                saveTodos($updatedTodos);
                echo json_encode(['message' => 'Todo deleted successfully']);
            } else {
                http_response_code(404); // Not found
                echo json_encode(['error' => 'Todo not found']);
            }
        } else {
            http_response_code(400); // Bad request
            echo json_encode(['error' => 'Todo ID is required for deletion']);
        }
        break;
        
    default:
        http_response_code(405); // Method not allowed
        echo json_encode(['error' => 'Method not allowed']);
}
?>