function checkData() {
  var p_id = document.getElementById('product_id').value;

  if (p_id.trim() === '') {
    alert('Please enter Product ID.');
    document.getElementById('product_id').focus();
    return false;
  }

  return true;
}

document.getElementById('add-product-form').addEventListener('submit', function(event) {
  if (!checkData()) {
    event.preventDefault();
  }
});
