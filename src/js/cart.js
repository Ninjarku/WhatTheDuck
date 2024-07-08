// Select all the delete buttons
const deleteButtons = document.querySelectorAll('.delete-btn');

// Add the event listener to your form, assuming it has an id of "yourFormId"
document.getElementById("cartForm").addEventListener("submit", validateAndSubmitForm);

// Add a click event listener to each delete button
deleteButtons.forEach(button => {
  button.addEventListener('click', function() {
      deleteEntry(button);
  });
});


function deleteEntry(button) {
  // Send an AJAX request to the PHP script
  var cartid = button.value;
  var price = button.getAttribute('data-price');

  var totalValueDiv = document.getElementById('subtotal-value').innerText;
  totalValueDiv = totalValueDiv.replace('$', '');

  
  if (totalValueDiv != '0.00') {
    updatePrice(price);
  }

  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          // Remove the entry from the webpage
          var row = button.parentNode.parentNode;
          
          updateCartNo();
          
          row.parentNode.removeChild(row);
      }
  }; 
  xmlhttp.open("GET", "process_cart.php?action=deleteCartItem&cartid=" + cartid, true);
//  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xmlhttp.send();
}

function updateCartNo() {
  var cartCount = document.getElementsByClassName('cartcount');
  
  const xhr = new XMLHttpRequest();
  
  xhr.open('POST', 'process_cart.php?action=updateCartCount', true);
  xhr.setRequestHeader('Content-Type', 'application/json');
  
  // Handle the response
//  xhr.onreadystatechange = function() {
//      if (xhr.readyState === 4 && xhr.status === 200) {
//          cartCount[0].innerHTML = xhr.responseText;
//      }
//  };
  
  xhr.send();
}

function check_uncheck_checkbox(isChecked) {
    if(isChecked) {
        $('input[type="checkbox"]').each(function() { 
                this.checked = true; 
        });
    } else {
        $('input[type="checkbox"]').each(function() {
                this.checked = false;
        });
    }
}

//Need to edit after voucher implementation
function updatePrice(price){
//    var priceElement = document.getElementById('total-value');
//    var oldPrice = priceElement.innerText;
//    var newPrice = parseFloat(oldPrice.replace('$', '')) - parseFloat(price);
//    priceElement.innerText = '$' + newPrice.toFixed(2);

    var priceElement = document.getElementById('subtotal-value');
    var oldPrice = priceElement.innerText;
    var newPrice = parseFloat(oldPrice.replace('$', '')) - parseFloat(price);
    priceElement.innerText = '$' + newPrice.toFixed(2);
}



function validateAndSubmitForm(event) {
  const form = event.target;
  const timeElements = form.querySelectorAll('select.timing-obj');

  for (const timeElement of timeElements) {
    if (timeElement.value === "") {
      event.preventDefault(); // Prevent form submission
      alert("Please select an available time before proceeding.");
      return;
    }
  }
}
