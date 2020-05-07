import '../css/batimentAddEdit.css';
import 'bootstrap';
import $ from 'jquery';
import 'popper.js';

$(document).ready(function () {
  $('input[type="file"]').change(function (e) {
    const fileName = e.target.files[0].name;
    $('.custom-file-label').html(fileName);
  });
});
