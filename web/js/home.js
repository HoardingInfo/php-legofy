$(document).ready(function() {
    Dropzone.options.dropzoneImage = {
        paramName: "file",
        maxFilesize: 2,
        accept: function(file, done) {
            var patt = /\.(jpg|png|jpeg)\b/;
            if (patt.test(file.name)) {
                done();
            }
            else { done('Not allowed format.'); }
        },
        complete: function(data) {
            if (data.xhr.status == 200) {
                var response = $.parseJSON(data.xhr.response);
                window.location.href = response.url;
            } else {
                $('.alert.alert-danger').html(data.xhr.response).show();
            }
        }
    };
});
