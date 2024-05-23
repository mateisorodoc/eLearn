const todoInput = document.querySelector(".todo-input");
const todoButton = document.querySelector(".todo-button");
const todoList = document.querySelector(".todo-list");
const filterOption = document.querySelector(".filter-todo");
const form = document.getElementById("todoForm");

// Fetch todos from server whenever the page is loaded
document.addEventListener("DOMContentLoaded", () => {
    getTodosFromServer();

    // Add event listeners
    todoButton.addEventListener("click", addTodo);
    todoList.addEventListener("click", deleteCheck);
    filterOption.addEventListener("change", filterTodo);
});

function getTodosFromServer() {
    fetch("get_todos.php")
    .then(response => response.json())
    .then(data => {
        if (data.length > 0) {
            todoList.innerHTML = '';
            for (let todo of data) {
                displayTodo(todo);
            }
        } else {
            console.log("No todos retrieved from the server.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function displayTodo(todo) {
    const todoDiv = document.createElement("div");
    todoDiv.classList.add("todo");
    
    const newTodo = document.createElement("li");
    newTodo.innerText = todo.content;
    newTodo.classList.add("todo-item");
    if (todo.status === 1) {
        newTodo.classList.add("completed");
    }
    todoDiv.appendChild(newTodo);

    const completedButton = document.createElement("button");
    completedButton.innerHTML = '<i class="fas fa-check-circle"></i>';
    completedButton.classList.add("complete-btn");
    todoDiv.appendChild(completedButton);

    const trashButton = document.createElement("button");
    trashButton.innerHTML = '<i class="fas fa-trash"></i>';
    trashButton.classList.add("trash-btn");
    todoDiv.appendChild(trashButton);

    todoList.appendChild(todoDiv);
}

function addTodo(event) {
    event.preventDefault();
    const todoDiv = document.createElement("div");
    todoDiv.classList.add("todo");
    const newTodo = document.createElement("li");
    newTodo.innerText = todoInput.value; 
    newTodo.classList.add("todo-item");
    todoDiv.appendChild(newTodo);
    
    const completedButton = document.createElement("button");
    completedButton.innerHTML = '<i class="fas fa-check-circle"></i>';
    completedButton.classList.add("complete-btn");
    todoDiv.appendChild(completedButton);

    const trashButton = document.createElement("button");
    trashButton.innerHTML = '<i class="fas fa-trash"></i>';
    trashButton.classList.add("trash-btn");
    todoDiv.appendChild(trashButton);
    
    todoList.appendChild(todoDiv);
    todoInput.value = "";

    // Determine the status of the todo item
    const status = 0; // Default is uncompleted

    // Add the new todo to local storage
    saveLocalTodos(newTodo.innerText, status);

    // Submit the form using AJAX
    let formData = new FormData(form);
    formData.set("content", newTodo.innerText); // Set the content in the form data
    formData.set("status", status); // Set the status in the form data
    fetch(form.getAttribute("action"), {
        method: form.getAttribute("method"),
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log("Response from server:", data); // Add this line to see the response
        if (data.success) {
            // If task added successfully, update the todo list
            updateTodoList();
            alert('ok')
        } else {
            // If an error occurred, display the error message
            alert(data.message);
        }
    })
    .catch(error => {
        console.log('Error:', error);
    });
    
    // Event listener for trash button
    trashButton.addEventListener("click", function() {
        // Send request to delete todo item from database
        deleteTodoFromDB(newTodo.innerText);
        // Remove todo from UI
        todoDiv.remove();
    });

    // Event listener for complete button
    completedButton.addEventListener("click", function() {
        let todo = newTodo;
        todo.classList.toggle("completed");

        // Update the status
        const updatedStatus = todo.classList.contains("completed") ? 1 : 0;
        updateStatus(newTodo.innerText, updatedStatus); // Set updateCompletionTime to true
    });
}
function updateStatus(content, status) {
    // Create a new FormData object
    const formData = new FormData();
    formData.set("content", content);
    formData.set("status", status);

    // Send a POST request to the PHP file
    fetch("update_status.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Status updated successfully
            if (status === 1 && data.time_taken !== undefined) {
                const timeTaken = data.time_taken;
                const hours = Math.floor(timeTaken / 3600);
                const minutes = Math.floor((timeTaken % 3600) / 60);
                const seconds = timeTaken % 60;
                alert(`Task completed in ${hours}h ${minutes}m ${seconds}s`);
            }
        } else {
            // Error updating status
            console.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}


// Inside the deleteCheck function
function deleteCheck(e) {
    const item = e.target;

    if(item.classList[0] === "trash-btn") {
        const todo = item.parentElement;
        todo.classList.add("slide");

        removeLocalTodos(todo);
        todo.addEventListener("transitionend", function() {
            // Remove todo from UI
            todo.remove();
            // Delete todo item from the database
            deleteTodoFromDB(todo.children[0].innerText);
        });
    }

    if(item.classList[0] === "complete-btn") {
        const todo = item.parentElement;
        todo.classList.toggle("completed");
    }
}

function filterTodo(e) {
    const todos = todoList.childNodes;
    todos.forEach(function(todo) {
        switch(e.target.value) {
            case "all": 
                todo.style.display = "flex";
                break;
            case "completed": 
                if(todo.classList.contains("completed")) {
                    todo.style.display = "flex";
                } else {
                    todo.style.display = "none";
                }
                break;
            case "incomplete":
                if(!todo.classList.contains("completed")) {
                    todo.style.display = "flex";
                } else {
                    todo.style.display = "none";
                }
                break;
        }
    });
}

function saveLocalTodos(todo) {
    let todos;
    if(localStorage.getItem("todos") === null) {
        todos = [];
    } else {
        todos = JSON.parse(localStorage.getItem("todos"));
    }
    todos.push(todo);
    localStorage.setItem("todos", JSON.stringify(todos));
}

function removeLocalTodos(todo) {
    let todos;
    if(localStorage.getItem("todos") === null) {
        todos = [];
    } else {
        todos = JSON.parse(localStorage.getItem("todos"));
    }

    const todoIndex = todo.children[0].innerText;
    todos.splice(todos.indexOf(todoIndex), 1);
    localStorage.setItem("todos", JSON.stringify(todos));
}

function deleteTodoFromDB(todoContent) {
    const formData = new FormData();
    formData.set("content", todoContent);
    fetch("delete_todo.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
