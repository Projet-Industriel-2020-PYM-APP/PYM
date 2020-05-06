import '../css/serviceCategorieForm.css';
import 'bootstrap';
import 'jquery';
import $ from "jquery";

$(document).ready(function () {
    $('input[type="file"]').change(function(e){
        const fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
});