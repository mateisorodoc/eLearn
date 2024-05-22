const todoInput = document.querySelector(".todo-input");
const todoButton = document.querySelector(".todo-button");
const todoList = document.querySelector(".todo-list");
const filterOption = document.querySelector(".filter-todo");
const form = document.getElementById("todoForm");

document.addEventListener("DOMContentLoaded", getLocalTodos);
todoButton.addEventListener("click", addTodo);
todoList.addEventListener("click", deleteCheck);
filterOption.addEventListener("change", filterTodo);

createFlashcardForm.addEventListener('submit', (event) => {
    event.preventDefault();
    const frontInput = document.getElementById('front');
    const backInput = document.getElementById('back');
    const frontText = frontInput.value;
    const backText = backInput.value;
    frontInput.value = '';
    backInput.value = '';

    // Create the flashcard
    const flashcard = document.createElement('div');
    flashcard.classList.add('flashcard');
    const front = document.createElement('div');
    front.classList.add('flashcard-front');
    const frontContent = document.createElement('p');
    frontContent.textContent = frontText;
    front.appendChild(frontContent);
    const back = document.createElement('div');
    back.classList.add('flashcard-back');
    const backContent = document.createElement('p');
    backContent.textContent = backText;
    back.appendChild(backContent);
    flashcard.appendChild(front);
    flashcard.appendChild(back);

    // Create the delete button
    const deleteButton = document.createElement('button');
    deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
    deleteButton.classList.add('delete_flashcard');
    flashcard.appendChild(deleteButton);

    flashcardsContainer.appendChild(flashcard);

    // Add event listener to the delete button
    deleteButton.addEventListener('click', () => {
        flashcardsContainer.removeChild(flashcard);

        // Create FormData object to send form data
        const formData = new FormData();
        formData.append('front', frontText);
        formData.append('back', backText);

        // Send delete request to the server
        fetch('delete_flashcard.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Flashcard deleted successfully');
            } else {
                console.error('Error deleting flashcard:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Create FormData object to send form data
    const formData = new FormData();
    formData.append('front', frontText);
    formData.append('back', backText);

    // Send form data to the server using fetch
    fetch('flashcard.php', {
        method: 'POST',
        body: formData
    });
});

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
            if (updateCompletionTime) {
                console.log("Completion time and status updated successfully.");
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



function deleteCheck(e) {
    const item = e.target;

    if(item.classList[0] === "trash-btn") {
        const todo = item.parentElement;
        todo.classList.add("slide");

        removeLocalTodos(todo);
        todo.addEventListener("transitionend", function() {
            todo.remove();
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