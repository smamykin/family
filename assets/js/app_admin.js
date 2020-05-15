require('../css/dashboard.css');
$ = require('jquery');

$('input[type="file"]').change(function (e) {
    if (e.target.files && e.target.files[0]) {
        let fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    }
});
