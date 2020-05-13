import '../css/contactCategorieForm.css';
import 'bootstrap';
import $ from 'jquery';
import '@fortawesome/fontawesome-free/js/all';

$(document).ready(function () {
    $('.add-action-widget').click(addOnPressed);
    $('.delete-action-widget').click(removeOnPressed);
    $('input[type="file"]').change(function(e){
        const fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
});

const removeOnPressed = function (e) {
    e.preventDefault();

    // Delete the closest wrapper
    $(this).closest('.data-tag')
        .fadeOut()
        .remove();
}

const addOnPressed = function (_) {
    const list = $($(this).attr('data-list-selector'));
    // Try to find the counter of the list or use the length of the list
    let counter = list.data('widget-counter') || list.children().length;

    // grab the prototype template
    let newWidget = list.attr('data-prototype');
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__label__/g, counter);
    newWidget = newWidget.replace(/__name__/g, counter);
    // Increase the counter
    counter++;
    // And store it, the length cannot be used if deleting widgets is allowed
    list.data('widget-counter', counter);

    // create a new list element and add it to the list
    const newElem = $(list.attr('data-widget-tags')).html(newWidget);
    newElem.appendTo(list);
    newElem.find('.delete-action-widget').click(removeOnPressed);
}