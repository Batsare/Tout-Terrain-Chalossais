postGuestbook = $("#posts_guestbook");
formGuestbook = $("#form_guestbook");

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
    $('img').on('click', function () {
        var image = $(this).attr('src');
        $('#myModal').css("display", "block");
        $(".img-responsive").attr("src", image);
        $("#lien_image_article").attr("href", image);

        console.log(image);
        $('#myModal').on('show.bs.modal', function () {
            $(".img-responsive").attr("src", image);
        });
    });

    var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        $('#myModal').css("display", "none");
    }
});

