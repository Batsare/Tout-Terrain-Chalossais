
require('../css/app.scss');


require('jquery');
require('popper.js');
require('lightgallery');
require('quill');





var postGuestbook = $("#posts_guestbook");
var formGuestbook = $("#form_guestbook");

$( "#messages_guestbook" ).click(function() {
    if( postGuestbook.hasClass('enabled')){
    }else{
        formGuestbook.toggleClass('disabled enabled');
        postGuestbook.toggleClass('enabled disabled');
    }

});

$( "#post_message_guestbook" ).click(function() {
    if( formGuestbook.hasClass('enabled')){
    }else{
        postGuestbook.toggleClass('disabled enabled');
        formGuestbook.toggleClass('enabled disabled');
    }

});

$(document).ready(function () {
    $(".summernote").summernote({
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('img').on('click', function () {
        $("#lightgallery").lightGallery({
            thumbnail:true
        });
        $("#lightgalleryArticle").lightGallery({
            selector:'this'
        });

        // var image = $(this).attr('src');
        // $('#myModal').css("display", "block");
        // $(".img-responsive").attr("src", image);
        // $("#lien_image_article").attr("href", image);
        //
        // console.log(image);
        // $('#myModal').on('show.bs.modal', function () {
        //     $(".img-responsive").attr("src", image);
        // });
    });

//     if(document.getElementsByClassName("close")[0]){
//         var span = document.getElementsByClassName("close")[0];
//
// // When the user clicks on <span> (x), close the modal
//         span.onclick = function() {
//             $('#myModal').css("display", "none");
//         };
//         $('#myModal').click(function(){
//             $('#myModal').css("display", "none");
//         });
//     }



    /*
    $(window).scroll(function () {

        console.log($(window).scrollTop());

        if ($(window).scrollTop() > 70) {
            $('#nav_bar').addClass('fixed-top');
        }

        if ($(window).scrollTop() < 71) {
            $('#nav_bar').removeClass('fixed-top');
        }
    });
     */
});

