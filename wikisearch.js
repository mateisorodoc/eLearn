async function handleSubmit(event) {
  event.preventDefault();
  const inputValue = document.querySelector('.js-search-input').value;
  const searchQuery = inputValue.trim();

  const searchResults = document.querySelector('.js-search-results');
  searchResults.innerHTML = '';

  const spinner = document.querySelector('.js-spinner');
  spinner.classList.remove('hidden');

  try {
      const results = await searchWikipedia(searchQuery);
      if (results.query.searchinfo.totalhits === 0) {
          alert('No results found. Try different keywords');
          return;
      }

      displayResults(results);
  } catch (err) {
      console.log(err);
      alert('Failed to search Wikipedia');
  } finally {
      spinner.classList.add('hidden');
  }
}

async function searchWikipedia(searchQuery) {
  const endpoint = `https://en.wikipedia.org/w/api.php?action=query&list=search&prop=info&inprop=url&utf8=&format=json&origin=*&srlimit=20&srsearch=${searchQuery}`;
  const response = await fetch(endpoint);
  if (!response.ok) {
      throw Error(response.statusText);
  }
  const json = await response.json();
  return json;
}

function displayResults(results) {
  const searchResults = document.querySelector('.js-search-results');

  results.query.search.forEach(result => {
      const url = `https://en.wikipedia.org/?curid=${result.pageid}`;

      searchResults.insertAdjacentHTML(
          'beforeend',
          `<div class="result-item">
              <h3 class="result-title">
                  <a href="${url}" rel="noopener" style="color:teal" target="_blank">${result.title}</a>
                  <button class="btn btn-save" data-title="${result.title}" data-url="${url}">
                      <i class="far fa-save"></i>
                  </button>
              </h3>
              <a href="${url}" class="result-link" target="_blank" rel="noopener" style="color:#BBF1F1">${url}</a>
              <span class="result-snippet" style="color:white">${result.snippet}</span><br>
          </div>`
      );
  });

  document.querySelectorAll('.btn-save').forEach(button => {
      button.addEventListener('click', handleSaveButton);
  });
}

async function handleSaveButton(event) {
  const button = event.currentTarget;
  const title = button.getAttribute('data-title');
  const url = button.getAttribute('data-url');

  try {
      const response = await fetch('save_link.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify({ title, url }),
      });

      const result = await response.json();

      if (result.success) {
          button.querySelector('i').classList.toggle('far');
          button.querySelector('i').classList.toggle('fas');
          button.querySelector('i').classList.toggle('fa-save');
          button.querySelector('i').classList.toggle('fa-trash');
      } else {
          alert('Error: ' + result.message);
      }
  } catch (err) {
      console.log(err);
      alert('Failed to save/delete the link.');
  }
}

async function fetchSavedLinks() {
  const savedResults = document.querySelector('.js-saved-results');
  savedResults.innerHTML = '';

  try {
      const response = await fetch('fetch_saved_links.php');
      const results = await response.json();

      if (results.length === 0) {
          savedResults.innerHTML = '<p>No saved links found.</p>';
          return;
      }

      results.forEach(result => {
          savedResults.insertAdjacentHTML(
              'beforeend',
              `<div class="result-item">
                  <h3 class="result-title">
                      <a href="${result.url}" rel="noopener" style="color:teal" target="_blank">${result.title}</a>
                      <button class="btn btn-save" data-title="${result.title}" data-url="${result.url}">
                          <i class="fas fa-trash"></i>
                      </button>
                  </h3>
                  <a href="${result.url}" class="result-link" target="_blank" rel="noopener" style="color:#BBF1F1">${result.url}</a>
              </div>`
          );
      });

      document.querySelectorAll('.btn-save').forEach(button => {
          button.addEventListener('click', handleSaveButton);
      });
  } catch (err) {
      console.log(err);
      alert('Failed to fetch saved links.');
  }
}

const form = document.querySelector('.js-search-form');
form.addEventListener('submit', handleSubmit);

const showSavedLinksButton = document.querySelector('.js-show-saved-links');
showSavedLinksButton.addEventListener('click', fetchSavedLinks);
