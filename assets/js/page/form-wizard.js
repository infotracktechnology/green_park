'use strict';

$(function () {
    // Horizontal form basic
    $('#wizard_horizontal').steps({
        headerTag: 'h2',
        bodyTag: 'section',
        transitionEffect: 'slideLeft',
        onInit: function (event, currentIndex) {
            setButtonWavesEffect(event);
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            setButtonWavesEffect(event);
        }
    });


    // Vertical form basic
    $('#wizard_vertical').steps({
        headerTag: 'h2',
        bodyTag: 'section',
        transitionEffect: 'slideLeft',
        stepsOrientation: 'vertical',
        onInit: function (event, currentIndex) {
            setButtonWavesEffect(event);
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            setButtonWavesEffect(event);
        }
    });
    // Advanced form with validation
    var form = $('#wizard_with_validation').show();

    form.steps({
        headerTag: 'h3',
        bodyTag: 'fieldset',
        transitionEffect: 'slideLeft',
        onInit: function (event, currentIndex) {
            // Set tab width
            var $tab = $(event.currentTarget).find('ul[role="tablist"] li');
            var tabCount = $tab.length;
            $tab.css('width', (100 / tabCount) + '%');

            // Set button waves effect
            setButtonWavesEffect(event);
        },
        onStepChanging: function (event, currentIndex, newIndex) {
            if (currentIndex > newIndex) { return true; }

            if (currentIndex < newIndex) {
                form.find('.body:eq(' + newIndex + ') label.error').remove();
                form.find('.body:eq(' + newIndex + ') .error').removeClass('error');
            }

            // Apply custom validation rules
            form.validate().settings.ignore = ':disabled,:hidden';
            return form.valid();
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
            setButtonWavesEffect(event);
        },
        onFinishing: function (event, currentIndex) {
            form.validate().settings.ignore = ':disabled';
            return form.valid();
        },
        onFinished: function (event, currentIndex) {
            alert("Thank you for your submission!");
            form.submit();
        }
    });

    // Apply custom validation rules
    form.validate({
        errorElement: 'div',
        highlight: function (input) {
            $(input).parents('.form-line').addClass('text-danger');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('text-danger');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.form-group').append(error);
        },
        rules: {
            'aadhar_card_no': {
                required: true,
                minlength: 12,
                maxlength: 12,
                digits: true
            },
            'student_name': {
                required: true,
                alphabetsOnly: true,
            },
            'father_name': {
                required: true,
                alphabetsOnly: true,
            },
            'mother_name': {
                required: true,
                alphabetsOnly: true,
            },
            'ph_no1': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true
            },
            'ph_no2': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true
            },
            'father_ph_no': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true
            },
            'mother_ph_no': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true
            },
            'parent_whatsapp_no': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true
            },
            'student_whatsapp_no': {
                required: true,
                minlength: 10,
                maxlength: 10,
                digits: true

            }

            // Add additional rules for other fields here if needed
        },

    });

    // Prevent form submission if invalid
    $('#wizard_with_validation').on('submit', function (e) {
        var invalidFields = $('.is-invalid');
        if (invalidFields.length > 0) {
            e.preventDefault();
            alert('Please correct the errors before submitting the form.');
        }
    });
});

function setButtonWavesEffect(event) {
    $(event.currentTarget).find('[role="menu"] li a').removeClass('waves-effect');
    $(event.currentTarget).find('[role="menu"] li:not(.disabled) a').addClass('waves-effect');
}
$.validator.addMethod("alphabetsOnly", function (value, element) {
    return this.optional(element) || /^[a-zA-Z .]+$/.test(value);
}, "Please enter alphabets, spaces, and dots only");

$.validator.addMethod("digits", function (value, element) {
    return this.optional(element) || /^\d+$/.test(value);
}, "Please enter digits only");