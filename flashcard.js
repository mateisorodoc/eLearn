document.addEventListener('DOMContentLoaded', () => {
    const flashcardsContainer = document.getElementById('flashcards-container');
    const createFlashcardForm = document.getElementById('create-flashcard-form');
  
    // Fetch flashcards for the logged-in user
    fetch('get_flashcards.php')
      .then(response => response.json())
      .then(data => {
        data.flashcards.forEach(flashcard => {
          createFlashcardElement(flashcard.front_content, flashcard.back_content);
        });
      });
  
    createFlashcardForm.addEventListener('submit', (event) => {
      event.preventDefault();
      const frontInput = document.getElementById('front');
      const backInput = document.getElementById('back');
      const frontText = frontInput.value;
      const backText = backInput.value;
      frontInput.value = '';
      backInput.value = '';
  
      createFlashcardElement(frontText, backText);
  
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
  
    function createFlashcardElement(frontText, backText) {
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
        });
      });
    }
  });
  