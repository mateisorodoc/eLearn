const flashcardsContainer = document.getElementById('flashcards-container');
const createFlashcardForm = document.getElementById('create-flashcard-form');

createFlashcardForm.addEventListener('submit', (event) => {
  event.preventDefault();
  const frontInput = document.getElementById('front');
  const backInput = document.getElementById('back');
  const frontText = frontInput.value;
  const backText = backInput.value;
  frontInput.value = '';
  backInput.value = '';
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
  flashcardsContainer.appendChild(flashcard);
});
