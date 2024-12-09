// JS kod korišten za dropdown

function show(selectedCategory) {
  const textBox = this.closest('.dropdown-selector').querySelector('.text-box');
  textBox.value = selectedCategory;
}

document.querySelectorAll('.dropdown-selector').forEach(dropdown => {
  dropdown.onclick = function() {
    dropdown.classList.toggle('selectorActive');
  };

  const options = dropdown.querySelectorAll('.category-option div');
  options.forEach(option => {
    option.addEventListener('click', function() {
      show.call(this, this.textContent);
    });
  });
});

// Do ovjde je JS korišten za dropdown

// ##################################################################################################################################

// JS kod odavde je korišten za otvaranje/zatvaranje obrasca/formulara korištenih za dodavanje, uređivanje, brisanje kategorija

const openModalButtons = document.querySelectorAll('[data-modal-target]')
const closeModalButtons = document.querySelectorAll('[data-close-button]')
const overlay = document.getElementById('overlay')

openModalButtons.forEach(button => {
  button.addEventListener('click', () => {
    const modal = document.querySelector(button.dataset.modalTarget)
    openModal(modal)
  })
})

overlay.addEventListener('click', () => {
  const modals = document.querySelectorAll('.modal.active')
  modals.forEach(modal => {
    closeModal(modal)
  })
})

closeModalButtons.forEach(button => {
  button.addEventListener('click', () => {
    const modal = button.closest('.modal')
    closeModal(modal)
  })
})

function openModal(modal) {
  if (modal == null) return
  modal.classList.add('active')
  overlay.classList.add('active')
}


// dropdown.classList.remove('selectorActive'); -- kad zatvorimo modal, dropdown ostane otvoren ako ga nismo zatvorili. Ovo to rješava.
function closeModal(modal) {
  if (modal == null) return
  modal.classList.remove('active')
  overlay.classList.remove('active')

  dropdown.classList.remove('selectorActive');
}

// JS kod do ovdje je korišten za otvaranje/zatvaranje obrasca/formulara korištenih za dodavanje, uređivanje, brisanje kategorija

// ##################################################################################################################################

// AJAX kod korišten za dohvaćanje Naziva i ID-a kategorije prilikom klika na 'Uredi'

$(document).ready(function (){
  $('.edit-category').on('click', function() {
    $tr = $(this).closest('tr');

    var data = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    console.log(data);

    $('#edit_category_id_fetched').val(data[0]);
    $('#edit_category_name_fetched').val(data[1]);
  });
});

// AJAX kod korišten za dohvaćanje Naziva i ID-a kategorije prilikom klika na 'Izbriši'

$(document).ready(function (){
  $('.delete-category').on('click', function() {
    $tr = $(this).closest('tr');

    var data = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    console.log(data);

    $('#delete_category_id_fetched').val(data[0]);
    $('#delete_category_name_fetched').text(data[1]);
  });
});

// ####################################################################################################################################














// ####################################################################################################################################

// JS korišten za dropdown selector

//function showCategory(selected){
//  document.querySelector('.text-box').value = selected;
//}
//dropdown.onclick = function() {
//  dropdown.classList.toggle('selectorActive');
//}

// ####################################################################################################################################

// AJAX kod korišten za dohvaćanje Naziva i ID-a kategorije prilikom klika na 'Uredi'

$(document).ready(function (){
  $('.edit-item').on('click', function() {
    $tr = $(this).closest('tr');

    var data = $tr.children("td").map(function() {
      return $(this).text();
    }).get();
    console.log(data);

    $('#edit_category_id_fetched').val(data[0]);
    $('#edit_category_name_fetched').val(data[1]);
  });
});